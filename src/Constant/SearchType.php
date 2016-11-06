<?php

namespace SpotifyClient\Constant;

/**
 * Class SearchType.
 */
final class SearchType
{
    const ALBUM = 'album';
    const ARTIST = 'artist';
    const PLAYLIST = 'playlist';
    const TRACK = 'track';

    /**
     * @var array
     */
    public static $all = [
        self::ALBUM,
        self::ARTIST,
        self::PLAYLIST,
        self::TRACK,
    ];
}
