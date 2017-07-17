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
namespace LaravelBoot\Foundation\Http;

use Illuminate\Container\Container;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\Validation\ValidatesWhenResolved;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\ValidatesWhenResolvedTrait;
use Illuminate\Validation\ValidationException;

/**
 * Class FormRequest.
 */
class FormRequest extends Request implements ValidatesWhenResolved
{
    use ValidatesWhenResolvedTrait;

    /**
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * @var \Illuminate\Routing\Redirector
     */
    protected $redirector;

    /**
     * @var string
     */
    protected $redirect;

    /**
     * @var string
     */
    protected $redirectRoute;

    /**
     * @var string
     */
    protected $redirectAction;

    /**
     * @var string
     */
    protected $errorBag = 'default';

    /**
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function getValidatorInstance()
    {
        $factory = $this->container->make(ValidationFactory::class);
        if (method_exists($this, 'validator')) {
            $validator = $this->container->call([
                $this,
                'validator',
            ], compact('factory'));
        } else {
            $validator = $factory->make($this->validationData(), $this->container->call([
                $this,
                'rules',
            ]), $this->messages(), $this->attributes());
        }
        if (method_exists($this, 'withValidator')) {
            $this->withValidator($validator);
        }

        return $validator;
    }

    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    protected function validationData()
    {
        return $this->all();
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     *
     * @throws \Illuminate\Validation\ValidationException
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, $this->response($this->formatErrors($validator)));
    }

    /**
     * Determine if the request passes the authorization check.
     *
     * @return bool
     */
    protected function passesAuthorization()
    {
        if (method_exists($this, 'authorize')) {
            return $this->container->call([
                $this,
                'authorize',
            ]);
        }

        return false;
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return void
     */
    protected function failedAuthorization()
    {
        throw new HttpResponseException($this->forbiddenResponse());
    }

    /**
     * Get the proper failed validation response for the request.
     *
     * @param array $errors
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function response(array $errors)
    {
        if ($this->expectsJson()) {
            return new JsonResponse($errors, 422);
        }

        return $this->redirector->to($this->getRedirectUrl())->withInput($this->except($this->dontFlash))->withErrors($errors,
            $this->errorBag);
    }

    /**
     * Get the response for a forbidden operation.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function forbiddenResponse()
    {
        return new Response('Forbidden', 403);
    }

    /**
     * Format the errors from the given Validator instance.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     *
     * @return array
     */
    protected function formatErrors(Validator $validator)
    {
        return $validator->getMessageBag()->toArray();
    }

    /**
     * Get the URL to redirect to on a validation error.
     *
     * @return string
     */
    protected function getRedirectUrl()
    {
        $url = $this->redirector->getUrlGenerator();
        if ($this->redirect) {
            return $url->to($this->redirect);
        } elseif ($this->redirectRoute) {
            return $url->route($this->redirectRoute);
        } elseif ($this->redirectAction) {
            return $url->action($this->redirectAction);
        }

        return $url->previous();
    }

    /**
     * Set the Redirector instance.
     *
     * @param \Illuminate\Routing\Redirector $redirector
     *
     * @return $this
     */
    public function setRedirector(Redirector $redirector)
    {
        $this->redirector = $redirector;

        return $this;
    }

    /**
     * Set the container implementation.
     *
     * @param \Illuminate\Container\Container $container
     *
     * @return $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [];
    }
}
