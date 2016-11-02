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
     * @test
     */
    public function authorizationUrl()
    {
        $config = [
            'client_id' => 'xyz',
            'client_secret' => 'zyx',
            'redirect_uri' => 'yxz',
            'scope' => 'playlist-read-private'
        ];
        $url = (new Authorization())->getAuthorizationURL($config);
        $expectedUrl = '/authorize?client_id=xyz&response_type=code&redirect_uri=yxz&scope=playlist-read-private';
        static::assertEquals(self::URI.$expectedUrl, $url);
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
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();
        $response = new Response(500);
        $client->expects(static::once())->method('request')->willReturn($response);
        $authorization = new Authorization($client);

        $config = ['client_id' => 'xyz', 'client_secret' => 'zyx', 'redirect_uri' => 'yxz'];
        $this->expectException(SpotifyAccountsException::class);
        $authorization->getAccessTokens($config, '');
    }
}
