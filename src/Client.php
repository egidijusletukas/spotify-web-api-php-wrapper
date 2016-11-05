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
     * @param string $id
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getAlbum(string $id)
    {
        $uri = $this->getUri(Endpoint::ALBUM, ['id' => $id]);
        $response = $this->request(Request::GET, $uri);

        return $this->decode($response);
    }

    /**
     * @param string $albumId
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getAlbumTracks(string $albumId)
    {
        $uri = $this->getUri(Endpoint::ALBUM_TRACKS, ['id' => $albumId]);
        $response = $this->request(Request::GET, $uri);

        return $this->decode($response);
    }

    /**
     * @param array $ids
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getAlbums(array $ids)
    {
        $response = $this->request(Request::GET, Endpoint::ALBUMS, $this->getQuery(['ids' => $ids]));

        return $this->decode($response);
    }

    /**
     * @param string $artistId
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getArtist(string $artistId)
    {
        $uri = $this->getUri(Endpoint::ARTIST, ['id' => $artistId]);
        $response = $this->request(Request::GET, $uri);

        return $this->decode($response);
    }

    /**
     * @param string $artistId
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getArtistAlbums(string $artistId)
    {
        $uri = $this->getUri(Endpoint::ARTIST_ALBUMS, ['id' => $artistId]);
        $response = $this->request(Request::GET, $uri);

        return $this->decode($response);
    }

    /**
     * @param string $artistId
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getArtistRelated(string $artistId)
    {
        $uri = $this->getUri(Endpoint::ARTIST_RELATED, ['id' => $artistId]);
        $response = $this->request(Request::GET, $uri);

        return $this->decode($response);
    }

    /**
     * @param string $artistId
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getArtistTopTracks(string $artistId)
    {
        $uri = $this->getUri(Endpoint::ARTIST_TOP_TRACKS, ['id' => $artistId]);
        $response = $this->request(Request::GET, $uri);

        return $this->decode($response);
    }

    /**
     * @param array $artistIds
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getArtists(array $artistIds)
    {
        $response = $this->request(Request::GET, Endpoint::ARTISTS, $this->getQuery(['ids' => $artistIds]));

        return $this->decode($response);
    }

    /**
     * @param string $id
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getAudioAnalysis(string $id)
    {
        $uri = $this->getUri(Endpoint::AUDIO_ANALYSIS, ['id' => $id]);
        $response = $this->request(Request::GET, $uri);

        return $this->decode($response);
    }

    /**
     * @param string $id
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getAudioFeaturesById(string $id)
    {
        $uri = $this->getUri(Endpoint::AUDIO_FEATURES_BY_ID, ['id' => $id]);
        $response = $this->request(Request::GET, $uri);

        return $this->decode($response);
    }

    /**
     * @param array $ids
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getAudioFeaturesByIds(array $ids)
    {
        $response = $this->request(Request::GET, Endpoint::AUDIO_FEATURES_BY_IDS, $this->getQuery(['ids' => $ids]));

        return $this->decode($response);
    }

    /**
     * @return array
     * @throws SpotifyAPIException
     */
    public function getCategories()
    {
        $response = $this->request(Request::GET, Endpoint::CATEGORIES);

        return $this->decode($response);
    }

    /**
     * @param string $id
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getCategory(string $id)
    {
        $uri = $this->getUri(Endpoint::CATEGORY, ['id' => $id]);
        $response = $this->request(Request::GET, $uri);

        return $this->decode($response);
    }

    /**
     * @param string $id
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getCategoryPlaylists(string $id)
    {
        $uri = $this->getUri(Endpoint::CATEGORY_PLAYLISTS, ['id' => $id]);
        $response = $this->request(Request::GET, $uri);

        return $this->decode($response);
    }

    /**
     * @return array
     * @throws SpotifyAPIException
     */
    public function getFeaturedPlaylists()
    {
        $response = $this->request(Request::GET, Endpoint::FEATURED_PLAYLISTS);

        return $this->decode($response);
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
     * @param string $userId
     * @param string $playlistId
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getUserPlaylist(string $userId, string $playlistId)
    {
        $uri = $this->getUri(Endpoint::USER_PLAYLIST, ['user_id' => $userId, 'playlist_id' => $playlistId]);
        $response = $this->request(Request::GET, $uri);

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
            throw SpotifyAPIException::create(SpotifyAPIException::INVALID_JSON);
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

    /**
     * @param string $endpoint
     * @param array  $variables
     *
     * @return string
     */
    protected function getUri(string $endpoint, array $variables) : string
    {
        $flipped = array_flip($variables);
        $flipped = array_map(
            function ($variable) {
                return '{'.$variable.'}';
            },
            $flipped
        );
        $variables = array_flip($flipped);

        return str_replace(array_keys($variables), array_values($variables), $endpoint);
    }
}
