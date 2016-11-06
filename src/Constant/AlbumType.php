<?php

namespace SpotifyClient\Constant;

/**
 * Class AlbumType.
 */
class AlbumType
{
    const ALBUM = 'album';
    const APPEARS_ON = 'appears_on';
    const COMPILATION = 'compilation';
    const SINGLE = 'single';
    /**
     * @var array
     */
    public static $all = [
        self::ALBUM,
        self::SINGLE,
        self::APPEARS_ON,
        self::COMPILATION,
    ];
}
