<?php

namespace SpotifyClient;

use GuzzleHttp\Client as ClientGuzzle;
use GuzzleHttp\Exception\ClientException;
use SpotifyClient\Constant\Endpoint;
use SpotifyClient\Constant\Request;
use SpotifyClient\Exceptions\SpotifyClientException;

/**
 * Class Client.
 */
class Client extends ClientGuzzle
{
    const API_BASE_URI = 'https://api.spotify.com/'.self::API_VERSION;
    const API_VERSION = 'v1';

    /**
     * @var Authorization
     */
    private $auth;

    /**
     * Client constructor.
     *
     * @param Authorization $auth
     */
    public function __construct(Authorization $auth)
    {
        $this->auth = $auth;
        $config = ['base_uri' => self::API_BASE_URI];
        parent::__construct($config);
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
        $options = $this->getOptions($country, $limit, $offset);
        $response = $this->request(Request::GET, Endpoint::NEW_RELEASES, $options);
        var_dump(__METHOD__, $response);
        die;
    }

    /**
     * {@inheritdoc}
     * @throws SpotifyClientException
     */
    public function request($method, $uri = '', array $options = [])
    {
        $options['headers'] = $this->auth->getHeaders();
        try {
            $response = parent::request($method, $uri, $options);
        } catch (ClientException $ex) {
            throw new SpotifyClientException($ex->getCode());
        } catch (\Exception $ex) {
            throw new SpotifyClientException(null);
        }

        var_dump(__METHOD__, 'RESPONSE GET');
        die;
    }

    /**
     * @return array
     */
    protected function getOptions() : array
    {
        $options = [];

        return $options;
    }
}
