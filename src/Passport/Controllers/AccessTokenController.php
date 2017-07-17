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

use Illuminate\Http\Response;
use Laravel\Passport\Http\Controllers\HandlesOAuthErrors;
use Laravel\Passport\TokenRepository;
use Lcobucci\JWT\Parser as JwtParser;
use League\OAuth2\Server\AuthorizationServer;
use LaravelBoot\Foundation\Routing\Abstracts\Controller;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response as Psr7Response;

/**
 * Class AccessTokenController.
 */
class AccessTokenController extends Controller
{
    use HandlesOAuthErrors;

    /**
     * @var \Lcobucci\JWT\Parser
     */
    protected $jwt;

    /**
     * @var \League\OAuth2\Server\AuthorizationServer
     */
    protected $server;

    /**
     * @var \Laravel\Passport\TokenRepository
     */
    protected $tokens;

    /**
     * AccessTokenController constructor.
     *
     * @param \League\OAuth2\Server\AuthorizationServer $server
     * @param \Laravel\Passport\TokenRepository         $tokens
     * @param \Lcobucci\JWT\Parser                      $jwt
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(AuthorizationServer $server, TokenRepository $tokens, JwtParser $jwt)
    {
        parent::__construct();
        $this->jwt = $jwt;
        $this->server = $server;
        $this->tokens = $tokens;
    }

    /**
     * Destroy handler.
     *
     * @param $tokenId
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($tokenId)
    {
        if (is_null($token = $this->request->user()->tokens->find($tokenId))) {
            return new Response('', 404);
        }
        $token->revoke();
    }

    /**
     * Index handler.
     *
     * @return mixed
     */
    public function index()
    {
        return $this->request->user()->tokens->load('client')->filter(function ($token) {
            return !$token->client->firstParty() && !$token->revoked;
        })->values();
    }

    /**
     * Issue token handler.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Illuminate\Http\Response
     */
    public function issueToken(ServerRequestInterface $request)
    {
        return $this->withErrorHandling(function () use ($request) {
            return $this->server->respondToAccessTokenRequest($request, new Psr7Response());
        });
    }
}
