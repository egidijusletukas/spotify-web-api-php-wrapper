<?php

namespace tests\SpotifyClient;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;
use SpotifyClient\Client;
use SpotifyClient\Constant\AlbumType;
use SpotifyClient\Constant\Endpoint;
use SpotifyClient\DataType\AccessTokens;
use SpotifyClient\Exceptions\SpotifyAPIException;

/**
 * Class ClientTest.
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    const ACCESS_TOKEN = 'xyz';
    const ID = 'xyz';
    const IDS = ['xyz', 'zyx'];

    /**
     * @test
     */
    public function exceptionOnInvalidJson()
    {
        $clientGuzzle = $this->getGuzzleClientMock();
        $clientGuzzle
            ->expects(static::once())
            ->method('request')
            ->willReturn(new Response(200, [], ''));

        $clientAPI = new Client($this->getAccessTokens(), $clientGuzzle);
        $this->expectException(SpotifyAPIException::class);
        $this->expectExceptionMessage(SpotifyAPIException::INVALID_JSON);
        $clientAPI->getMe();
    }

    /**
     * @test
     */
    public function getAlbum()
    {
        $response = $this->getResponseJSON('album');
        $options = ['query' => ['market' => 'FR']];
        $uri = '/albums/'.self::ID;
        $client = $this->getClientMock('GET', $uri, $options, $response);
        $album = $client->getAlbum(self::ID, 'FR');
        static::assertArrayHasKey('album_type', $album);
    }

    /**
     * @test
     */
    public function getAlbumTracks()
    {
        $response = $this->getResponseJSON('album_tracks');
        $options = ['query' => ['limit' => 10, 'offset' => 1, 'market' => 'FR']];
        $uri = '/albums/'.self::ID.'/tracks';
        $client = $this->getClientMock('GET', $uri, $options, $response);
        $tracks = $client->getAlbumTracks(self::ID, 10, 1, 'FR');
        static::assertArrayHasKey('items', $tracks);
    }

    /**
     * @test
     */
    public function getAlbums()
    {
        $response = $this->getResponseJSON('albums');
        $options = ['query' => ['ids' => self::IDS, 'market' => 'FR']];
        $uri = '/albums';
        $client = $this->getClientMock('GET', $uri, $options, $response);
        $albums = $client->getAlbums(self::IDS, 'FR');
        static::assertArrayHasKey('albums', $albums);
    }

    /**
     * @test
     */
    public function getArtist()
    {
        $response = $this->getResponseJSON('artist');
        $uri = '/artists/'.self::ID;
        $client = $this->getClientMock('GET', $uri, [], $response);
        $artist = $client->getArtist(self::ID);
        static::assertArrayHasKey('followers', $artist);
    }

    /**
     * @test
     */
    public function getArtistAlbums()
    {
        $response = $this->getResponseJSON('artist_albums');
        $uri = '/artists/'.self::ID.'/albums';
        $options = [
            'query' => [
                'album_type' => 'album,single,appears_on,compilation',
                'market' => 'SE',
                'limit' => 10,
                'offset' => 1,
            ]
        ];
        $client = $this->getClientMock('GET', $uri, $options, $response);
        $artistAlbums = $client->getArtistAlbums(self::ID, AlbumType::$all, 'SE', 10, 1);
        static::assertArrayHasKey('items', $artistAlbums);
    }

    /**
     * @test
     */
    public function getArtistRelated()
    {
        $response = $this->getResponseJSON('artist_related');
        $uri = '/artists/'.self::ID.'/related-artists';
        $client = $this->getClientMock('GET', $uri, [], $response);
        $artists = $client->getArtistRelated(self::ID);
        static::assertArrayHasKey('artists', $artists);
    }

    /**
     * @test
     */
    public function getArtistTopTracks()
    {
        $response = $this->getResponseJSON('artist_top_tracks');
        $uri = '/artists/'.self::ID.'/top-tracks';
        $options = ['query' => ['country' => 'FR']];
        $client = $this->getClientMock('GET', $uri, $options, $response);
        $artistTopTracks = $client->getArtistTopTracks(self::ID, 'FR');
        static::assertArrayHasKey('tracks', $artistTopTracks);
    }

    /**
     * @test
     */
    public function getArtists()
    {
        $response = $this->getResponseJSON('artists');
        $uri = '/artists';
        $options = ['query' => ['ids' => self::IDS]];
        $client = $this->getClientMock('GET', $uri, $options, $response);
        $artists = $client->getArtists(self::IDS);
        static::assertArrayHasKey('artists', $artists);
    }

    /**
     * @test
     */
    public function getAudioAnalysis()
    {
        $response = $this->getResponseJSON('audio_analysis');
        $uri = '/audio-analysis/'.self::ID;
        $client = $this->getClientMock('GET', $uri, [], $response);
        $audioAnalysis = $client->getAudioAnalysis(self::ID);
        static::assertArrayHasKey('bars', $audioAnalysis);
    }

    /**
     * @test
     */
    public function getAudioFeaturesForTrack()
    {
        $response = $this->getResponseJSON('audio_features_for_track');
        $uri = '/audio-features/xyz';
        $client = $this->getClientMock('GET', $uri, [], $response);
        $audioAnalysis = $client->getAudioFeaturesForTrack(self::ID);
        static::assertArrayHasKey('danceability', $audioAnalysis);
    }

    /**
     * @test
     */
    public function getAudioFeaturesForTracks()
    {
        $response = $this->getResponseJSON('audio_features_for_tracks');
        $uri = '/audio-features';
        $options = ['query' => ['ids' => self::IDS]];
        $client = $this->getClientMock('GET', $uri, $options, $response);
        $audioAnalysis = $client->getAudioFeaturesForTracks(self::IDS);
        static::assertArrayHasKey('audio_features', $audioAnalysis);
    }

    /**
     * @test
     */
    public function getQuery()
    {
        $clientGuzzle = $this->getGuzzleClientMock();
        $expectedOptions = [
            'query' => ['limit' => 10, 'offset' => 10],
            'headers' => ['Authorization' => 'Bearer '.self::ACCESS_TOKEN],
        ];
        $clientGuzzle
            ->expects(static::once())
            ->method('request')
            ->with('GET', Client::API_BASE_URL.Endpoint::NEW_RELEASES, $expectedOptions)
            ->willReturn(new Response(200, [], '{}'));

        $clientAPI = new Client($this->getAccessTokens(), $clientGuzzle);
        $clientAPI->getNewReleases('', 10, 10);
    }

    /**
     * @test
     */
    public function getUri()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|Client $clientAPI */
        $clientAPI = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();
        $clientAPI
            ->expects(static::once())
            ->method('request')
            ->with('GET', '/users/1/playlists/2')
            ->willReturn(new Response(200, [], '{}'));
        $clientAPI->getUserPlaylist('1', '2');
    }

    /**
     * @test
     */
    public function handledRequestException()
    {
        $clientGuzzle = $this->getGuzzleClientMock();
        $clientGuzzle->expects(static::once())->method('request')->willThrowException(new \Exception());
        $clientAPI = new Client($this->getAccessTokens(), $clientGuzzle);
        $this->expectException(SpotifyAPIException::class);
        $clientAPI->request('GET');
    }

    /**
     * @test
     */
    public function requestingWithAuthHeaders()
    {
        $clientGuzzle = $this->getGuzzleClientMock();
        $expectedOptions = [
            'headers' => [
                'header-x' => 'x',
                'Authorization' => 'Bearer '.self::ACCESS_TOKEN,
            ],
        ];
        $clientGuzzle
            ->expects(static::once())
            ->method('request')
            ->with('GET', Client::API_BASE_URL, $expectedOptions)
            ->willReturn(new Response());
        $clientAPI = new Client($this->getAccessTokens(), $clientGuzzle);
        $clientAPI->request('GET', '', ['headers' => ['header-x' => 'x']]);
    }

    /**
     * @test
     */
    public function responseIsArray()
    {
        $clientGuzzle = $this->getGuzzleClientMock();
        $clientGuzzle
            ->expects(static::once())
            ->method('request')
            ->willReturn(new Response(200, [], $this->getResponseJSON('me')));
        $clientAPI = new Client($this->getAccessTokens(), $clientGuzzle);
        $response = $clientAPI->getMe();

        static::assertTrue(is_array($response));
        static::assertArrayHasKey('birthdate', $response);
    }

    /**
     * @return AccessTokens
     */
    private function getAccessTokens()
    {
        return (new AccessTokens())
            ->setAccessToken(self::ACCESS_TOKEN);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $options
     * @param string $response
     * @param null   $matcher
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|Client
     */
    private function getClientMock(string $method, string $uri, array $options, string $response, $matcher = null)
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();
        $client
            ->expects(null === $matcher ? static::once() : $matcher)
            ->method('request')
            ->with($method, $uri, $options)
            ->willReturn(new Response(200, [], $response));

        return $client;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getGuzzleClientMock()
    {
        return $this->getMockBuilder(GuzzleClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function getResponseJSON(string $name)
    {
        return file_get_contents(__DIR__.'/Data/'.$name.'.json');
    }
}
