<?php

namespace tests\SpotifyClient;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use SpotifyClient\Authorization;
use SpotifyClient\Exceptions\SpotifyAccountsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

/**
 * Class AuthorizationTest.
 */
class AuthorizationTest extends \PHPUnit_Framework_TestCase
{
    const URI = 'https://accounts.spotify.com';
    /**
     * @var array
     */
    private static $accessTokensConfig = [
        'client_id' => 'xyz',
        'client_secret' => 'zyx',
        'redirect_uri' => 'yxz'
    ];
    /**
     * @var array
     */
    private static $authUrlConfig = [
        'client_id' => 'xyz',
        'client_secret' => 'zyx',
        'redirect_uri' => 'yxz',
        'scope' => 'playlist-read-private',
    ];

    /**
     * @test
     */
    public function authorizationUrl()
    {
        $url = (new Authorization())->getAuthorizationURL(self::$authUrlConfig);
        $expectedUrl = '/authorize?client_id=xyz&response_type=code&redirect_uri=yxz&scope=playlist-read-private';
        static::assertEquals(self::URI.$expectedUrl, $url);
    }

    /**
     * @test
     */
    public function catchExceptionOnRequest()
    {
        $client = $this->getClientMock();
        $client->expects(static::once())->method('request')->willThrowException(new \Exception());
        $authorization = new Authorization($client);
        $this->expectException(SpotifyAccountsException::class);
        $authorization->getAccessTokens(self::$accessTokensConfig, '');
    }

    /**
     * @test
     */
    public function exceptionGettingAccessTokensWithEmptyConfig()
    {
        $config = [];
        $this->expectException(MissingOptionsException::class);
        (new Authorization())->getAccessTokens($config, '');
    }

    /**
     * @test
     */
    public function exceptionGettingUrlWithEmptyConfig()
    {
        $config = [];
        $this->expectException(MissingOptionsException::class);
        (new Authorization())->getAuthorizationURL($config);
    }

    /**
     * @test
     */
    public function exceptionOnServerFailure()
    {
        $client = $this->getClientMock();
        $response = new Response(500);
        $client->expects(static::once())->method('request')->willReturn($response);
        $authorization = new Authorization($client);

        $this->expectException(SpotifyAccountsException::class);
        $authorization->getAccessTokens(self::$accessTokensConfig, '');
    }

    /**
     * @test
     */
    public function getAccessTokens()
    {
        $client = $this->getClientMock();
        $body = [
            'access_token' => 'access token xyz',
            'token_type' => 'token type xyz',
            'scope' => 'scope xyz',
            'expires_in' => 3600,
            'refresh_token' => 'refresh token xyz',
        ];
        $response = new Response(200, [], json_encode($body));
        $client->expects(static::once())->method('request')->willReturn($response);
        $authorization = new Authorization($client);

        $accessTokens = $authorization->getAccessTokens(self::$accessTokensConfig, '');

        static::assertEquals($body['access_token'], $accessTokens->getAccessToken());
        static::assertEquals($body['token_type'], $accessTokens->getTokenType());
        static::assertEquals($body['scope'], $accessTokens->getScope());
        static::assertSame($body['expires_in'], $accessTokens->getExpiresIn());
        static::assertEquals($body['refresh_token'], $accessTokens->getRefreshToken());
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
