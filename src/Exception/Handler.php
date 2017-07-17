<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/17 10:48
 * @version
 */
namespace LaravelBoot\Foundation\Exception;

use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\ValidationException;
use LaravelBoot\Foundation\Configuration\Repository;
use LaravelBoot\Foundation\Permission\Exceptions\PermissionException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\Debug\ExceptionHandler as SymfonyExceptionHandler;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class Handler.
 */
class Handler implements ExceptionHandlerContract
{
    /**
     * @var \LaravelBoot\Foundation\Configuration\Repository
     */
    protected $configuration;

    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * @var array
     */
    protected $dontReport = [];

    /**
     * @var \Illuminate\Routing\Redirector
     */
    protected $redirector;

    /**
     * @var \Illuminate\Contracts\Routing\ResponseFactory
     */
    protected $response;

    /**
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $view;

    /**
     * @param \Illuminate\Contracts\Container\Container                $container
     * @param \LaravelBoot\Foundation\Configuration\Repository              $configuration
     * @param \Illuminate\Routing\Redirector                           $redirector
     * @param \Illuminate\Contracts\Routing\ResponseFactory            $response
     * @param \Illuminate\Contracts\View\Factory|\Illuminate\View\View $view
     */
    public function __construct(
        Container $container,
        Repository $configuration,
        Redirector $redirector,
        ResponseFactory $response,
        ViewFactory $view
    )
    {
        $this->container = $container;
        $this->configuration = $configuration;
        $this->redirector = $redirector;
        $this->response = $response;
        $this->view = $view;
    }

    /**
     * Report or log an exception. This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $exception
     *
     * @throws \Exception
     * @return void
     */
    public function report(Exception $exception)
    {
        if ($this->shouldntReport($exception)) {
            return;
        }
        try {
            $logger = $this->container->make(LoggerInterface::class);
        } catch (Exception $ex) {
            throw $exception;
        }
        $logger->error($exception);
    }

    /**
     * Determine if the exception should be reported.
     *
     * @param \Exception $exception
     *
     * @return bool
     */
    public function shouldReport(Exception $exception)
    {
        return !$this->shouldntReport($exception);
    }

    /**
     * Determine if the exception is in the "do not report" list.
     *
     * @param \Exception $exception
     *
     * @return bool
     */
    protected function shouldntReport(Exception $exception)
    {
        $dontReport = array_merge($this->dontReport, [HttpResponseException::class]);
        foreach ($dontReport as $type) {
            if ($exception instanceof $type) {
                return true;
            }
        }

        return false;
    }

    /**
     * Prepare exception for rendering.
     *
     * @param \Exception $exception
     *
     * @return \Exception
     */
    protected function prepareException(Exception $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            $exception = new NotFoundHttpException($exception->getMessage(), $exception);
        } else if ($exception instanceof AuthorizationException) {
            $exception = new HttpException(403, $exception->getMessage());
        }

        return $exception;
    }

    /**
     * Render an exception into a response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $exception
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $exception)
    {
        $exception = $this->prepareException($exception);
        if ($exception instanceof HttpResponseException) {
            return $exception->getResponse();
        } else if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        } else if ($exception instanceof PermissionException) {
            return $this->permissionDenied($request, $exception);
        } else if ($exception instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($exception, $request);
        } else if ($exception instanceof ClientException) {
            if ($request->expectsJson()) {
                return $this->response->json(['error' => $exception->getMessage()], $exception->getCode());
            }
        }

        return $this->prepareResponse($request, $exception);
    }

    /**
     * Prepare response containing exception render.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $exception
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function prepareResponse($request, Exception $exception)
    {
        if ($this->isHttpException($exception)) {
            return $this->toIlluminateResponse($this->renderHttpException($exception), $exception);
        } else {
            return $this->toIlluminateResponse($this->convertExceptionToResponse($exception), $exception);
        }
    }

    /**
     * Map exception into an illuminate response.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param \Exception                                 $exception
     *
     * @return \Illuminate\Http\Response
     */
    protected function toIlluminateResponse($response, Exception $exception)
    {
        if ($response instanceof SymfonyRedirectResponse) {
            $response = new RedirectResponse($response->getTargetUrl(), $response->getStatusCode(),
                $response->headers->all());
        } else {
            $response = new Response($response->getContent(), $response->getStatusCode(), $response->headers->all());
        }

        return $response->withException($exception);
    }

    /**
     * Render an exception to the console.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Exception                                        $exception
     *
     * @return void
     */
    public function renderForConsole($output, Exception $exception)
    {
        (new ConsoleApplication())->renderException($exception, $output);
    }

    /**
     * Render the given HttpException.
     *
     * @param \Symfony\Component\HttpKernel\Exception\HttpException $exception
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderHttpException(HttpException $exception)
    {
        $status = $exception->getStatusCode();
        if ($this->view->exists("error::{$status}") && !$this->configuration->get('app.debug')) {
            return $this->response->view("error::{$status}", ['exception' => $exception], $status,
                $exception->getHeaders());
        } else {
            return $this->convertExceptionToResponse($exception);
        }
    }

    /**
     * Create a response object from the given validation exception.
     *
     * @param \Illuminate\Validation\ValidationException $exception
     * @param \Illuminate\Http\Request                   $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $exception, $request)
    {
        if ($exception->response) {
            return $exception->response;
        }
        $errors = $exception->validator->errors()->getMessages();
        if ($request->expectsJson()) {
            return $this->response->json($errors, 422);
        }

        return $this->redirector->back()->withInput($request->input())->withErrors($errors);
    }

    /**
     * Create a Symfony response for the given exception.
     *
     * @param \Exception $exception
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertExceptionToResponse(Exception $exception)
    {
        $exception = FlattenException::create($exception);
        $handler = new SymfonyExceptionHandler($this->configuration->get('app.debug'));

        return SymfonyResponse::create($handler->getHtml($exception), $exception->getStatusCode(),
            $exception->getHeaders());
    }

    /**
     * Determine if the given exception is an HTTP exception.
     *
     * @param \Exception $exception
     *
     * @return bool
     */
    protected function isHttpException(Exception $exception)
    {
        return $exception instanceof HttpException;
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param \Illuminate\Http\Request                 $request
     * @param \Illuminate\Auth\AuthenticationException $exception
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return $this->response->json(['error' => 'Unauthenticated.'], 401);
        }

        return $this->redirector->guest('login');
    }

    /**
     * Convert an permission exception into an permission denied response.
     *
     * @param \Illuminate\Http\Request                                     $request
     * @param \LaravelBoot\Foundation\Permission\Exceptions\PermissionException $exception
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function permissionDenied($request, PermissionException $exception)
    {
        if ($request->expectsJson()) {
            return $this->response->json([
                'code' => 406,
                'message' => 'Permission Denied.',
            ], 406);
        }

        return $this->redirector->guest('login');
    }
}
