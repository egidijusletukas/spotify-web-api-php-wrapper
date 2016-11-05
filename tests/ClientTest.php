<?php

namespace tests\SpotifyClient;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;
use SpotifyClient\Client;
use SpotifyClient\Constant\Endpoint;
use SpotifyClient\DataType\AccessTokens;
use SpotifyClient\Exceptions\SpotifyAPIException;

/**
 * Class ClientTest.
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    const ACCESS_TOKEN = 'xyz';

    /**
     * @test
     */
    public function exceptionOnInvalidJson()
    {
        $clientGuzzle = $this->getClientMock();
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
    public function getQuery()
    {
        $clientGuzzle = $this->getClientMock();
        $expectedOptions = [
            'query' => ['limit' => 10, 'offset' => 10],
            'headers' => ['Authorization' => 'Bearer '.self::ACCESS_TOKEN],
        ];
        $clientGuzzle
            ->expects(static::once())
            ->method('request')
            ->with('GET', Client::API_BASE_URI.Endpoint::NEW_RELEASES, $expectedOptions)
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
        $clientGuzzle = $this->getClientMock();
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
        $clientGuzzle = $this->getClientMock();
        $expectedOptions = [
            'headers' => [
                'header-x' => 'x',
                'Authorization' => 'Bearer '.self::ACCESS_TOKEN,
            ],
        ];
        $clientGuzzle
            ->expects(static::once())
            ->method('request')
            ->with('GET', Client::API_BASE_URI, $expectedOptions)
            ->willReturn(new Response());
        $clientAPI = new Client($this->getAccessTokens(), $clientGuzzle);
        $clientAPI->request('GET', '', ['headers' => ['header-x' => 'x']]);
    }

    /**
     * @test
     */
    public function responseIsArray()
    {
        $responseJson = file_get_contents(__DIR__.'/Data/me.json');
        $clientGuzzle = $this->getClientMock();
        $clientGuzzle
            ->expects(static::once())
            ->method('request')
            ->willReturn(new Response(200, [], $responseJson));
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
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getClientMock()
    {
        return $this->getMockBuilder(GuzzleClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();
    }
}
