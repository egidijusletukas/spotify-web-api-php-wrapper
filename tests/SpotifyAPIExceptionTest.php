<?php

namespace tests\SpotifyClient;

use SpotifyClient\Exceptions\SpotifyAPIException;

/**
 * Class SpotifyAPIExceptionTest.
 */
class SpotifyAPIExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function create()
    {
        $message = 'xyz';
        $ex = SpotifyAPIException::create($message);
        static::assertEquals($message, $ex->getMessage());
    }

    /**
     * @test
     */
    public function createByResponseCode()
    {
        $responseCode = 500;
        $ex = SpotifyAPIException::createByResponseCode($responseCode);
        $expectedMessage = '500 Client server error';
        static::assertEquals($expectedMessage, $ex->getMessage());
    }

    /**
     * @test
     */
    public function createUnexpected()
    {
        $ex = SpotifyAPIException::createUnexpected();
        static::assertEquals(SpotifyAPIException::DEFAULT, $ex->getMessage());
    }
}
