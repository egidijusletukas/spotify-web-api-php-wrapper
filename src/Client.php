<?php

namespace SpotifyClient;

use GuzzleHttp\Client as ClientGuzzle;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use SpotifyClient\Constant\Endpoint;
use SpotifyClient\Constant\Request;
use SpotifyClient\Exceptions\SpotifyAPIException;

/**
 * Class Client.
 */
class Client extends ClientGuzzle
{
    const API_BASE_URI = 'https://api.spotify.com/'.self::API_VERSION;
    const API_VERSION = 'v1';
    /**
     * @var array
     */
    private $accessTokens = [];
    /**
     * @var array
     */
    private $headersDefault = [];

    /**
     * Client constructor.
     *
     * @param array $accessTokens
     */
    public function __construct(array $accessTokens)
    {
        $this->accessTokens = $accessTokens;
        $this->headersDefault['Authorization'] = 'Bearer '.$accessTokens['access_token'];
        parent::__construct();
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
     */
    public function getNewReleases(string $country = '', int $limit = null, int $offset = null) : array
    {
        $options = [
            'country' => $country,
            'limit' => $limit,
            'offset' => $offset,
        ];
        $options = $this->getQuery($options);
        $response = $this->request(Request::GET, Endpoint::NEW_RELEASES, $options);

        return $this->decode($response);
    }

    /**
     * {@inheritdoc}
     * @throws SpotifyAPIException
     */
    public function request($method, $uri = '', array $options = [])
    {
        $options[RequestOptions::HEADERS] = array_key_exists(RequestOptions::HEADERS, $options) ?
            array_merge($options[RequestOptions::HEADERS], $this->headersDefault) :
            $this->headersDefault;
        try {
            return parent::request($method, self::API_BASE_URI.$uri, $options);
        } catch (ClientException $ex) {
            throw new SpotifyAPIException($ex->getCode());
        } catch (\Exception $ex) {
            throw new SpotifyAPIException(null);
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
            throw new SpotifyAPIException('Cannot decode JSON');
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
