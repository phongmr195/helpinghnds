<?php

namespace App\Grants;

use RuntimeException;
use Illuminate\Http\Request;
use Laravel\Passport\Bridge\User;
use League\OAuth2\Server\RequestEvent;
use Psr\Http\Message\ServerRequestInterface;
use League\OAuth2\Server\Grant\AbstractGrant;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use Illuminate\Contracts\Hashing\Hasher;

class PasswordCustomProvider extends AbstractGrant
{
    protected $hasher;
    /**
     * @param RefreshTokenRepositoryInterface $refreshTokenRepository
     */
    public function __construct(RefreshTokenRepositoryInterface $refreshTokenRepository, Hasher $hasher)
    {
        $this->setRefreshTokenRepository($refreshTokenRepository);
        $this->hasher = $hasher;

        $this->refreshTokenTTL = new \DateInterval('P2D');
    }

    /**
     * {@inheritdoc}
     */
    public function respondToAccessTokenRequest(
        ServerRequestInterface $request,
        ResponseTypeInterface $responseType,
        \DateInterval $accessTokenTTL
    ) {
        // Validate request
        $client = $this->validateClient($request);
        $scopes = $this->validateScopes($this->getRequestParameter('scope', $request));
        $user = $this->validateUser($request, $client);

        // Finalize the requested scopes
        $scopes = $this->scopeRepository->finalizeScopes($scopes, $this->getIdentifier(), $client, $user->getIdentifier());

        // Issue and persist new tokens
        $accessToken = $this->issueAccessToken($accessTokenTTL, $client, $user->getIdentifier(), $scopes);
        $refreshToken = $this->issueRefreshToken($accessToken);

        // Inject tokens into response
        $responseType->setAccessToken($accessToken);
        $responseType->setRefreshToken($refreshToken);

        return $responseType;
    }

    protected function validateUser(ServerRequestInterface $request, ClientEntityInterface $client)
    {
        $params = ['phone', 'password', 'provider'];
        $data = [];
        foreach($params as $param){
            $data[$param] = $this->getRequestParameter($param, $request);
            if (is_null($data[$param])) {
                throw OAuthServerException::invalidRequest($param);
            }
        }

        $user = $this->getEntityByUserCredentials(
            $data['phone'],
            $data['password'],
            $this->getIdentifier(),
            $client,
            $data['provider']
        );

        if ($user instanceof UserEntityInterface === false) {
            $this->getEmitter()->emit(new RequestEvent(RequestEvent::USER_AUTHENTICATION_FAILED, $request));

            throw OAuthServerException::invalidCredentials();
        }

        return $user;
    }

    private function getEntityByUserCredentials($phone, $password, $grantType, ClientEntityInterface $clientEntity, $provider)
    {
        $provider = config('auth.guards.'.$provider.'.provider');


        if (is_null($model = config('auth.providers.'.$provider.'.model'))) {
            throw new RuntimeException('Unable to determine authentication model from configuration.');
        }

        $user = (new $model)->where('phone', $phone)->first();

        if (!$user || !$this->hasher->check($password, $user->getAuthPassword())) {
            return;
        } 

        return new User($user->getAuthIdentifier());
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'password_grant_custom';
    }
}