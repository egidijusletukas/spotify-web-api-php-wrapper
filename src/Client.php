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
    const API_BASE_URL = 'https://api.spotify.com/'.self::API_VERSION;
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
     * @param string $market
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getAlbum(string $id, string $market = '')
    {
        $uri = $this->getUri(Endpoint::ALBUM, ['id' => $id]);
        $response = $this->request(Request::GET, $uri, $this->getQuery(['market' => $market]));

        return $this->decode($response);
    }

    /**
     * @param string   $albumId
     * @param int|null $limit
     * @param int|null $offset
     * @param string   $market
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getAlbumTracks(string $albumId, int $limit = null, int $offset = null, string $market = '')
    {
        $uri = $this->getUri(Endpoint::ALBUM_TRACKS, ['id' => $albumId]);
        $options = [
            'limit' => $limit,
            'offset' => $offset,
            'market' => $market,
        ];
        $response = $this->request(Request::GET, $uri, $this->getQuery($options));

        return $this->decode($response);
    }

    /**
     * @param array  $ids
     * @param string $market
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getAlbums(array $ids, string $market = '')
    {
        $response = $this->request(
            Request::GET,
            Endpoint::ALBUMS,
            $this->getQuery(['ids' => $ids, 'market' => $market])
        );

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
     * @param string   $artistId
     * @param array    $albumTypes
     * @param string   $country
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getArtistAlbums(string $artistId, array $albumTypes = [], string $country = '', int $limit = null, int $offset = null)
    {
        $uri = $this->getUri(Endpoint::ARTIST_ALBUMS, ['id' => $artistId]);
        $options = [
            'album_type' => implode(',', $albumTypes),
            'market' => $country,
            'limit' => $limit,
            'offset' => $offset,
        ];
        $response = $this->request(Request::GET, $uri, $this->getQuery($options));

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
     * @param string $country
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getArtistTopTracks(string $artistId, string $country = '')
    {
        $uri = $this->getUri(Endpoint::ARTIST_TOP_TRACKS, ['id' => $artistId]);
        $response = $this->request(Request::GET, $uri, $this->getQuery(['country' => $country]));

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
     * @param string $trackId
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getAudioFeaturesForTrack(string $trackId)
    {
        $uri = $this->getUri(Endpoint::AUDIO_FEATURES_FOR_TRACK, ['id' => $trackId]);
        $response = $this->request(Request::GET, $uri);

        return $this->decode($response);
    }

    /**
     * @param array $trackIds
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getAudioFeaturesForTracks(array $trackIds)
    {
        $response = $this->request(
            Request::GET,
            Endpoint::AUDIO_FEATURES_FOR_TRACKS,
            $this->getQuery(['ids' => $trackIds])
        );

        return $this->decode($response);
    }

    /**
     * @param string   $country
     * @param string   $locale
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getCategories(string $country = '', string $locale = '', int $limit = null, int $offset = null)
    {
        $options = ['country' => $country, 'locale' => $locale, 'limit' => $limit, 'offset' => $offset];
        $response = $this->request(Request::GET, Endpoint::CATEGORIES, $this->getQuery($options));

        return $this->decode($response);
    }

    /**
     * @param string $id
     * @param string $country
     * @param string $locale
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getCategory(string $id, string $country = '', string $locale = '')
    {
        $uri = $this->getUri(Endpoint::CATEGORY, ['id' => $id]);
        $options = ['country' => $country, 'locale' => $locale];
        $response = $this->request(Request::GET, $uri, $this->getQuery($options));

        return $this->decode($response);
    }

    /**
     * @param string   $id
     * @param string   $country
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getCategoryPlaylists(string $id, string $country = '', int $limit = null, int $offset = null)
    {
        $uri = $this->getUri(Endpoint::CATEGORY_PLAYLISTS, ['id' => $id]);
        $options = ['country' => $country, 'limit' => $limit, 'offset' => $offset];
        $response = $this->request(Request::GET, $uri, $this->getQuery($options));

        return $this->decode($response);
    }

    /**
     * @param string         $locale
     * @param string         $country
     * @param \DateTime|null $dateTime
     * @param int|null       $limit
     * @param int|null       $offset
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getFeaturedPlaylists(string $locale = '', string $country = '', \DateTime $dateTime = null, int $limit = null, int $offset = null)
    {
        $options = ['locale' => $locale, 'country' => $country, 'limit' => $limit, 'offset' => $offset];
        if (null !== $dateTime) {
            $dateTime->setTimezone(new \DateTimeZone('UTC'));
            $options['timestamp'] = $dateTime->format(\DateTime::ISO8601);
        }
        $response = $this->request(Request::GET, Endpoint::FEATURED_PLAYLISTS, $this->getQuery($options));

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
            return $this->client->request($method, self::API_BASE_URL.$uri, $options);
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
