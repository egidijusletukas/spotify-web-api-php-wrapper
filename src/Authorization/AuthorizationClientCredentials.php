<?php

namespace SpotifyClient\Authorization;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use SpotifyClient\Constant\Request;
use SpotifyClient\Constant\Response;
use SpotifyClient\DataType\AccessTokens;
use SpotifyClient\Exceptions\SpotifyAccountsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AuthorizationClientCredentials.
 */
class AuthorizationClientCredentials
{
    const URL = 'https://accounts.spotify.com';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * Authorization constructor.
     *
     * @param ClientInterface|null $client
     */
    public function __construct(ClientInterface $client = null)
    {
        $this->client = null === $client ? new Client() : $client;
    }

    /**
     * @return OptionsResolver
     */
    private static function getOptionsAuthTokens() : OptionsResolver
    {
        return (new OptionsResolver())
            ->setRequired(['access_token', 'token_type', 'expires_in']);
    }

    /**
     * @param string $clientId
     * @param string $clientSecret
     *
     * @return AccessTokens
     * @throws SpotifyAccountsException
     */
    public function getAccessTokens(string $clientId, string $clientSecret) : AccessTokens
    {
        $options = [
            'form_params' => ['grant_type' => 'client_credentials'],
            RequestOptions::HEADERS => ['Authorization' => 'Basic '.base64_encode($clientId.':'.$clientSecret)],
        ];

        try {
            $response = $this->client->request(Request::POST, self::URL.'/api/token', $options);
        } catch (\Exception $ex) {
            throw new SpotifyAccountsException($ex->getMessage());
        }
        if (Response::HTTP_OK !== $response->getStatusCode()) {
            throw new SpotifyAccountsException('Response status code: '.$response->getStatusCode());
        }

        $accessTokens = json_decode($response->getBody()->getContents(), true);
        $accessTokens = self::getOptionsAuthTokens()->resolve($accessTokens);

        return (new AccessTokens())
            ->setAccessToken($accessTokens['access_token'])
            ->setTokenType($accessTokens['token_type'])
            ->setExpiresIn($accessTokens['expires_in']);
    }
}
