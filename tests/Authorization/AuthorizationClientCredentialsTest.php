<?php

namespace tests\SpotifyClient\Authorization;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use SpotifyClient\Authorization\AuthorizationClientCredentials;
use SpotifyClient\Exceptions\SpotifyAccountsException;

/**
 * Class AuthorizationCodeTest.
 */
class AuthorizationClientCredentialsTest extends \PHPUnit_Framework_TestCase
{
    const CLIENT_ID = 'xyz';
    const CLIENT_SECRET = 'xyz';
    const URL = 'https://accounts.spotify.com';

    /**
     * @test
     */
    public function exceptionOnServerFailure()
    {
        $client = $this->getClientMock();
        $response = new Response(500);
        $client->expects(static::once())->method('request')->willReturn($response);
        $authorization = new AuthorizationClientCredentials($client);

        $this->expectException(SpotifyAccountsException::class);
        $authorization->getAccessTokens(self::CLIENT_ID, self::CLIENT_SECRET);
    }

    /**
     * @test
     */
    public function getAccessTokens()
    {
        $client = $this->getClientMock();
        $body = ['access_token' => 'access token xyz', 'token_type' => 'token type xyz', 'expires_in' => 3600];
        $response = new Response(200, [], json_encode($body));
        $client->expects(static::once())->method('request')->willReturn($response);
        $authorization = new AuthorizationClientCredentials($client);

        $accessTokens = $authorization->getAccessTokens(self::CLIENT_ID, self::CLIENT_SECRET);
        
        static::assertEquals($body['access_token'], $accessTokens->getAccessToken());
        static::assertEquals($body['token_type'], $accessTokens->getTokenType());
        static::assertSame($body['expires_in'], $accessTokens->getExpiresIn());
    }

    /**
     * @test
     */
    public function handledRequestException()
    {
        $client = $this->getClientMock();
        $client->expects(static::once())->method('request')->willThrowException(new \Exception());
        $authorization = new AuthorizationClientCredentials($client);
        $this->expectException(SpotifyAccountsException::class);
        $authorization->getAccessTokens(self::CLIENT_ID, self::CLIENT_SECRET);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Client
     */
    private function getClientMock()
    {
        return $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();
    }
}
