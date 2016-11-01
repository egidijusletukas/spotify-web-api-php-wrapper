<?php

namespace SpotifyClient\Exceptions;

/**
 * Class SpotifyAPIException.
 */
class SpotifyAPIException extends \Exception
{
    const DEFAULT = 'Unexpected client exception';

    /**
     * @var array
     */
    private static $messages = [
        400 => 'Bad Request - The request could not be understood by the server due to malformed syntax. The message body will contain more information',
        401 => 'Unauthorized - The request requires user authentication or, if the request included authorization credentials, authorization has been refused for those credentials',
        403 => 'Forbidden - The server understood the request, but is refusing to fulfill it',
        404 => 'Not Found - The requested resource could not be found. This error can be due to a temporary or permanent condition',
        429 => 'Too Many Requests - Rate limiting has been applied',
        500 => 'Client server error',
        502 => 'The server was acting as a gateway or proxy and received an invalid response from the upstream server',
        503 => 'Service Unavailable',
    ];

    /**
     * SpotifyAPIException constructor.
     *
     * @param int|null $responseCode
     */
    public function __construct(int $responseCode = null)
    {
        $message = self::createMessage($responseCode);

        parent::__construct($message);
    }

    /**
     * @param int $responseCode
     *
     * @return string
     */
    public static function createMessage(int $responseCode = null) : string
    {
        if (null === $responseCode) {
            return self::DEFAULT;
        }

        return $responseCode.' '.self::$messages[$responseCode] ?? self::DEFAULT;
    }
}
