<?php

namespace SpotifyClient;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use SpotifyClient\Constant\Request;
use SpotifyClient\Constant\Response;
use SpotifyClient\Exceptions\SpotifyAccountsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Authorization.
 */
class Authorization
{
    const URI = 'https://accounts.spotify.com';

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
    private static function getOptionsAuth() : OptionsResolver
    {
        $required = ['client_id', 'client_secret', 'redirect_uri'];
        $defaults = ['state' => null, 'response_type' => 'code', 'scope' => []];

        return (new OptionsResolver())
            ->setRequired($required)
            ->setDefaults($defaults);
    }

    /**
     * @return OptionsResolver
     */
    private static function getOptionsAuthTokens() : OptionsResolver
    {
        $required = [
            'access_token',
            'token_type',
            'scope',
            'expires_in',
            'refresh_token',
        ];

        return (new OptionsResolver())
            ->setRequired($required);
    }

    /**
     * @param array  $config
     * @param string $code
     *
     * @return array
     * @throws SpotifyAccountsException
     */
    public function getAccessTokens(array $config, string $code) : array
    {
        $config = self::getOptionsAuth()->resolve($config);
        $options = [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $config['redirect_uri'],
                'client_id' => $config['client_id'],
                'client_secret' => $config['client_secret'],
            ],
        ];

        try {
            $response = $this->client->request(Request::POST, self::URI.'/api/token', $options);
        } catch (\Exception $ex) {
            throw new SpotifyAccountsException($ex->getMessage());
        }
        if (Response::HTTP_OK !== $response->getStatusCode()) {
            throw new SpotifyAccountsException('Response status code: '.$response->getStatusCode());
        }

        $accessTokens = json_decode($response->getBody()->getContents(), true);

        return self::getOptionsAuthTokens()->resolve($accessTokens);
    }

    /**
     * @param array $config
     *
     * @return string
     */
    public function getAuthorizationURL(array $config) : string
    {
        $config = self::getOptionsAuth()->resolve($config);
        $query = [
            'client_id' => $config['client_id'],
            'response_type' => $config['response_type'],
            'redirect_uri' => $config['redirect_uri'],
            'state' => $config['state'],
            'scope' => $config['scope'],
        ];

        return self::URI.'/authorize?'.http_build_query($query);
    }

    /**
     * Not implemented
     * @param array $accessTokens
     *
     * @return array
     */
    public function refreshAccessTokens(array $accessTokens) : array
    {
        /** @todo implement */
        return $accessTokens;
    }
}
