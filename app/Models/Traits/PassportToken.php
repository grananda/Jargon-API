<?php

namespace App\Models\Traits;

use Carbon\Carbon;
use GuzzleHttp\Psr7\Response;
use Laravel\Passport\Passport;
use Illuminate\Events\Dispatcher;
use League\OAuth2\Server\CryptKey;
use Laravel\Passport\Bridge\Client;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\Bridge\AccessToken;
use Laravel\Passport\Bridge\AccessTokenRepository;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResponseTypes\BearerTokenResponse;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;

/**
 * Trait PassportToken
 * Trait taken from https://github.com/laravel/passport/issues/71#issuecomment-330506407
 *
 * @package App\Models\Traits
 */
trait PassportToken
{
    /**
     * Generate the OAuth 2 access and refresh token for the user.
     *
     * @param int $clientId
     *
     * @return mixed
     * @throws \League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException
     */
    public function generateOAuthToken(int $clientId)
    {
        $encryptionKey = app('encrypter')->getKey();

        $accessToken = $this->generateAccessToken($clientId);

        $refreshToken = $this->generateRefreshToken($accessToken);

        $privateKey = new CryptKey('file://' . Passport::keyPath('oauth-private.key'));

        $bearerResponse = new BearerTokenResponse();
        $bearerResponse->setPrivateKey($privateKey);
        $bearerResponse->setAccessToken($accessToken);
        $bearerResponse->setRefreshToken($refreshToken);
        $bearerResponse->setEncryptionKey($encryptionKey);

        $response = $bearerResponse->generateHttpResponse(new Response());

        return json_decode((string)$response->getBody(), true);
    }

    /**
     * Generate a new unique OAuth identifier.
     *
     * @param int $length
     *
     * @return string
     */
    private function generateUniqueOAuthIdentifier(int $length = 40)
    {
        try {
            return bin2hex(random_bytes($length));
        } catch (\TypeError | \Error | \Exception $e) {
            throw OAuthServerException::serverError('Could not generate a random string');
        }
    }

    /**
     * Generates the token expiration timestamp.
     *
     * @return \Carbon\Carbon
     */
    private function generateTokenExpiration()
    {
        $expiresIn = Passport::tokensExpireIn();

        return Carbon::now()->add($expiresIn);
    }

    /**
     * Generate a new access token.
     *
     * @param int $clientId
     *
     * @return string
     * @throws \League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException
     */
    private function generateAccessToken(int $clientId)
    {
        $expiresAt = $this->generateTokenExpiration();

        $accessToken = new AccessToken($this->id);
        $accessToken->setIdentifier($this->generateUniqueOAuthIdentifier());
        $accessToken->setClient(new Client($clientId, null, null));
        $accessToken->setExpiryDateTime($expiresAt);

        $accessTokenRepository = new AccessTokenRepository(new TokenRepository(), new Dispatcher());
        $accessTokenRepository->persistNewAccessToken($accessToken);

        return $accessToken;
    }

    /**
     * Generate a refresh token.
     *
     * @param \League\OAuth2\Server\Entities\AccessTokenEntityInterface $accessToken
     *
     * @return string
     * @throws \League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException
     */
    private function generateRefreshToken(AccessTokenEntityInterface $accessToken)
    {
        $maxGenerationAttempts = 10;

        $expiresAt = $this->generateTokenExpiration();

        $refreshTokenRepository = app(RefreshTokenRepository::class);

        $refreshToken = $refreshTokenRepository->getNewRefreshToken();
        $refreshToken->setExpiryDateTime($expiresAt);
        $refreshToken->setAccessToken($accessToken);

        while ($maxGenerationAttempts-- > 0) {
            $refreshToken->setIdentifier($this->generateUniqueOAuthIdentifier());

            try {
                $refreshTokenRepository->persistNewRefreshToken($refreshToken);

                return $refreshToken;
            } catch (UniqueTokenIdentifierConstraintViolationException $e) {
                if ($maxGenerationAttempts === 0) {
                    throw $e;
                }
            }
        }

        return null;
    }
}
