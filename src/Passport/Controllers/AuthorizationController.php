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
namespace LaravelBoot\Foundation\Passport\Controllers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Http\Controllers\HandlesOAuthErrors;
use Laravel\Passport\Http\Controllers\RetrievesAuthRequestFromSession;
use Laravel\Passport\Passport;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use LaravelBoot\Foundation\Routing\Abstracts\Controller;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response as Psr7Response;

/**
 * Class AuthorizationController.
 */
class AuthorizationController extends Controller
{
    use HandlesOAuthErrors, RetrievesAuthRequestFromSession;

    /**
     * @var \Illuminate\Contracts\Routing\ResponseFactory
     */
    protected $response;

    /**
     * @var \League\OAuth2\Server\AuthorizationServer
     */
    protected $server;

    /**
     * AuthorizationController constructor.
     *
     * @param \League\OAuth2\Server\AuthorizationServer     $server
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(AuthorizationServer $server, ResponseFactory $response)
    {
        parent::__construct();
        $this->server = $server;
        $this->response = $response;
    }

    /**
     * Deny handler.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deny()
    {
        $redirect = $this->getAuthRequestFromSession($this->request)->getClient()->getRedirectUri();

        return $this->response->redirectTo($redirect . '?error=access_denied&state=' . $this->request->input('state'));
    }

    /**
     * Index handler.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $psrRequest
     * @param \Laravel\Passport\ClientRepository       $clients
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ServerRequestInterface $psrRequest, ClientRepository $clients)
    {
        return $this->withErrorHandling(function () use ($psrRequest, $clients) {
            $this->request->session()->put('authRequest',
                $authRequest = $this->server->validateAuthorizationRequest($psrRequest));
            $scopes = $this->parseScopes($authRequest);

            return $this->response->view('passport::authorize', [
                'client'  => $clients->find($authRequest->getClient()->getIdentifier()),
                'user'    => $this->request->user(),
                'scopes'  => $scopes,
                'request' => $this->request,
            ]);
        });
    }

    /**
     * Parse scopes for authorization request.
     *
     * @param \League\OAuth2\Server\RequestTypes\AuthorizationRequest $authRequest
     *
     * @return array
     */
    protected function parseScopes(AuthorizationRequest $authRequest)
    {
        return Passport::scopesFor(collect($authRequest->getScopes())->map(function ($scope) {
            return $scope->getIdentifier();
        })->all());
    }

    /**
     * Store handler.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->withErrorHandling(function () {
            $authRequest = $this->getAuthRequestFromSession($this->request);

            return $this->server->completeAuthorizationRequest($authRequest, new Psr7Response());
        });
    }
}
