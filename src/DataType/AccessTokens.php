<?php

namespace SpotifyClient\DataType;

/**
 * Class AccessTokens.
 */
class AccessTokens
{
    /**
     * @var string
     */
    private $accessToken;
    /**
     * @var int
     */
    private $expiresIn;
    /**
     * @var string
     */
    private $refreshToken;
    /**
     * @var string
     */
    private $scope;
    /**
     * @var string
     */
    private $tokenType;

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     *
     * @return AccessTokens
     */
    public function setAccessToken(string $accessToken) : AccessTokens
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * @return int
     */
    public function getExpiresIn()
    {
        return $this->expiresIn;
    }

    /**
     * @param int $expiresIn
     *
     * @return AccessTokens
     */
    public function setExpiresIn(int $expiresIn) : AccessTokens
    {
        $this->expiresIn = $expiresIn;

        return $this;
    }

    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @param string $refreshToken
     *
     * @return AccessTokens
     */
    public function setRefreshToken(string $refreshToken) : AccessTokens
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @param string $scope
     *
     * @return AccessTokens
     */
    public function setScope(string $scope) : AccessTokens
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * @return string
     */
    public function getTokenType()
    {
        return $this->tokenType;
    }

    /**
     * @param string $tokenType
     *
     * @return AccessTokens
     */
    public function setTokenType(string $tokenType) : AccessTokens
    {
        $this->tokenType = $tokenType;

        return $this;
    }
}
