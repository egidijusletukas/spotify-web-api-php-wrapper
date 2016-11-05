<?php

namespace SpotifyClient;

use GuzzleHttp\Client as ClientGuzzle;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use SpotifyClient\Constant\Endpoint;
use SpotifyClient\Constant\Request;
use SpotifyClient\DataType\AccessTokens;
use SpotifyClient\Exceptions\SpotifyAPIException;

/**
 * Class Client.
 */
class Client
{
    const API_BASE_URI = 'https://api.spotify.com/'.self::API_VERSION;
    const API_VERSION = 'v1';
    /**
     * @var ClientGuzzle
     */
    protected $client;
    /**
     * @var AccessTokens
     */
    private $accessTokens;
    /**
     * @var array
     */
    private $headersDefault = [];

    /**
     * Client constructor.
     *
     * @param AccessTokens      $accessTokens
     * @param ClientGuzzle|null $client
     */
    public function __construct(AccessTokens $accessTokens, ClientGuzzle $client = null)
    {
        $this->accessTokens = $accessTokens;
        $this->headersDefault['Authorization'] = 'Bearer '.$accessTokens->getAccessToken();
        $this->client = null === $client ? new ClientGuzzle() : $client;
    }

    /**
     * @return array
     * @throws SpotifyAPIException
     */
    public function getMe() : array
    {
        $response = $this->request(Request::GET, Endpoint::ME);

        return $this->decode($response);
    }

    /**
     * @param string   $country
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getNewReleases(string $country = '', int $limit = null, int $offset = null) : array
    {
        $options = ['country' => $country, 'limit' => $limit, 'offset' => $offset];
        $options = $this->getQuery($options);
        $response = $this->request(Request::GET, Endpoint::NEW_RELEASES, $options);

        return $this->decode($response);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $options
     *
     * @return ResponseInterface
     * @throws SpotifyAPIException
     */
    public function request(string $method, string $uri = '', array $options = []) : ResponseInterface
    {
        $options[RequestOptions::HEADERS] = array_key_exists(RequestOptions::HEADERS, $options) ?
            array_merge($options[RequestOptions::HEADERS], $this->headersDefault) :
            $this->headersDefault;
        try {
            return $this->client->request($method, self::API_BASE_URI.$uri, $options);
        } catch (ClientException $ex) {
            throw SpotifyAPIException::createByResponseCode($ex->getCode());
        } catch (\Exception $ex) {
            throw SpotifyAPIException::createUnexpected();
        }
    }

    /**
     * @param ResponseInterface $response
     *
     * @return array
     * @throws SpotifyAPIException
     */
    protected function decode(ResponseInterface $response) : array
    {
        $result = json_decode($response->getBody()->getContents(), true);
        if (!is_array($result)) {
            throw SpotifyAPIException::create('Cannot decode JSON');
        }

        return $result;
    }

    /**
     * @param array $options
     *
     * @return array
     */
    protected function getQuery(array $options) : array
    {
        $result = [];
        foreach ($options as $key => $value) {
            if (!empty($value)) {
                $result[$key] = $value;
            }
        }

        return empty($result) ? $result : ['query' => $result];
    }
}
