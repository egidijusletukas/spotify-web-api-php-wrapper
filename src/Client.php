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
            $this->getQuery(['ids' => implode(',', $ids), 'market' => $market])
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
        $response = $this->request(
            Request::GET,
            Endpoint::ARTISTS,
            $this->getQuery(['ids' => implode(',', $artistIds)])
        );

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
            $this->getQuery(['ids' => implode(',', $trackIds)])
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
     * @param int|null $limit
     * @param int|null $offset
     * @param string   $market
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getMeAlbums(int $limit = null, int $offset = null, string $market = '') : array
    {
        $options = ['limit' => $limit, 'offset' => $offset, 'market' => $market];
        $response = $this->request(Request::GET, Endpoint::ME_ALBUMS, $this->getQuery($options));

        return $this->decode($response);
    }

    /**
     * @param string   $type
     * @param int|null $limit
     * @param string   $after
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getMeFollowedArtists(string $type = 'artist', int $limit = null, string $after = '') : array
    {
        $options = ['type' => $type, 'limit' => $limit, 'after' => $after];
        $response = $this->request(Request::GET, Endpoint::ME_FOLLOWED_ARTISTS, $this->getQuery($options));

        return $this->decode($response);
    }

    /**
     * @param string $type
     * @param array  $ids
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getMeFollowedContains(string $type, array $ids) : array
    {
        $options = ['type' => $type, 'ids' => implode(',', $ids)];
        $response = $this->request(Request::GET, Endpoint::ME_FOLLOWED_CONTAINS, $this->getQuery($options));

        return $this->decode($response);
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @param string   $market
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getMeSavedTracks(int $limit = null, int $offset = null, string $market = '') : array
    {
        $options = ['limit' => $limit, 'offset' => $offset, 'market' => $market];
        $response = $this->request(Request::GET, Endpoint::ME_SAVED_TRACKS, $this->getQuery($options));

        return $this->decode($response);
    }

    /**
     * @param array $ids
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getMeSavedTracksContains(array $ids) : array
    {
        $options = ['ids' => implode(',', $ids)];
        $response = $this->request(Request::GET, Endpoint::ME_SAVED_TRACKS_CONTAINS, $this->getQuery($options));

        return $this->decode($response);
    }

    /**
     * @param string   $type
     * @param int|null $limit
     * @param int|null $offset
     * @param string   $timeRange
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getMeTop(string $type, int $limit = null, int $offset = null, string $timeRange = '') : array
    {
        $uri = $this->getUri(Endpoint::ME_TOP, ['type' => $type]);
        $options = ['limit' => $limit, 'offset' => $offset, 'time_range' => $timeRange];
        $response = $this->request(Request::GET, $uri, $this->getQuery($options));

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
        $response = $this->request(Request::GET, Endpoint::NEW_RELEASES, $this->getQuery($options));

        return $this->decode($response);
    }

    /**
     * @param int|null $limit
     * @param string   $market
     * @param array    $max
     * @param array    $min
     * @param array    $seedArtists
     * @param array    $seedGenres
     * @param array    $seedTracks
     * @param array    $target
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getRecommendations(int $limit = null, string $market = '', array $max = [], array $min = [], array $seedArtists = [], array $seedGenres = [], array $seedTracks = [], array $target = []) : array
    {
        $options = [
            'limit' => $limit,
            'market' => $market
        ];
        foreach ($max as $key => $item) {
            $options['max_'.$key] = $item;
        }
        foreach ($min as $key => $item) {
            $options['min_'.$key] = $item;
        }
        if ($seedArtists) {
            $options['seed_artists'] = implode(',', $seedArtists);
        }
        if ($seedGenres) {
            $options['seed_genres'] = implode(',', $seedGenres);
        }
        if ($seedTracks) {
            $options['seed_tracks'] = implode(',', $seedTracks);
        }
        foreach ($target as $key => $item) {
            $options['target_'.$key] = $item;
        }
        $response = $this->request(Request::GET, Endpoint::RECOMMENDATIONS, $this->getQuery($options));

        return $this->decode($response);
    }

    /**
     * @param array    $query
     * @param array    $types
     * @param string   $market
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getSearchResult(array $query, array $types, string $market = '', int $limit = null, int $offset = null) : array
    {
        $options = [
            'q' => implode('+', $query),
            'type' => implode(',', $types),
            'market' => $market,
            'limit' => $limit,
            'offset' => $offset
        ];
        $response = $this->request(Request::GET, Endpoint::SEARCH, $this->getQuery($options));

        return $this->decode($response);
    }

    /**
     * @param string $id
     * @param string $market
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getTrack(string $id, string $market = '') : array
    {
        $uri = $this->getUri(Endpoint::TRACK, ['id' => $id]);
        $options = ['market' => $market];
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
    public function getTracks(array $ids, string $market = '') : array
    {
        $options = ['ids' => implode(',', $ids), 'market' => $market];
        $response = $this->request(Request::GET, Endpoint::TRACKS, $this->getQuery($options));

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
     * @param string   $userId
     * @param string   $playlistId
     * @param string   $fields
     * @param int|null $limit
     * @param int|null $offset
     * @param string   $market
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getUserPlaylistTracks(string $userId, string $playlistId, string $fields = '', int $limit = null, int $offset = null, string $market = '') : array
    {
        $uri = $this->getUri(Endpoint::USER_PLAYLIST_TRACKS, ['user_id' => $userId, 'playlist_id' => $playlistId]);
        $options = [
            'fields' => $fields,
            'limit' => $limit,
            'offset' => $offset,
            'market' => $market
        ];
        $response = $this->request(Request::GET, $uri, $this->getQuery($options));

        return $this->decode($response);
    }

    /**
     * @param string   $id
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getUserPlaylists(string $id, int $limit = null, int $offset = null) : array
    {
        $uri = $this->getUri(Endpoint::USER_PLAYLISTS, ['id' => $id]);
        $options = ['limit' => $limit, 'offset' => $offset];
        $response = $this->request(Request::GET, $uri, $this->getQuery($options));

        return $this->decode($response);
    }

    /**
     * @param string $id
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function getUserProfile(string $id) : array
    {
        $uri = $this->getUri(Endpoint::USER_PROFILE, ['id' => $id]);
        $response = $this->request(Request::GET, $uri);

        return $this->decode($response);
    }

    /**
     * @param string $ownerId
     * @param string $playlistId
     * @param array  $ids
     *
     * @return array
     * @throws SpotifyAPIException
     */
    public function isUserFollowingPlaylist(string $ownerId, string $playlistId, array $ids) : array
    {
        $uri = $this->getUri(Endpoint::USER_FOLLOWING_PLAYLIST, ['owner_id' => $ownerId, 'playlist_id' => $playlistId]);
        $options = ['ids' => implode(',', $ids)];
        $response = $this->request(Request::GET, $uri, $this->getQuery($options));

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

        return empty($result) ? $result : [RequestOptions::QUERY => $result];
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
