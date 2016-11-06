<?php

namespace SpotifyClient\Constant;

/**
 * Class Endpoint.
 */
final class Endpoint
{
    const ALBUM = '/albums/{id}';
    const ALBUMS = '/albums';
    const ALBUM_TRACKS = '/albums/{id}/tracks';
    const ARTIST = '/artists/{id}';
    const ARTISTS = '/artists';
    const ARTIST_ALBUMS = '/artists/{id}/albums';
    const ARTIST_RELATED = '/artists/{id}/related-artists';
    const ARTIST_TOP_TRACKS = '/artists/{id}/top-tracks';
    const AUDIO_ANALYSIS = '/audio-analysis/{id}';
    const AUDIO_FEATURES_FOR_TRACK = '/audio-features/{id}';
    const AUDIO_FEATURES_FOR_TRACKS = '/audio-features';
    const CATEGORIES = '/browse/categories';
    const CATEGORY = '/browse/categories/{id}';
    const CATEGORY_PLAYLISTS = '/browse/categories/{id}/playlists';
    const FEATURED_PLAYLISTS = '/browse/featured-playlists';
    const ME = '/me';
    const ME_ALBUMS = '/me/albums';
    const ME_FOLLOWED_ARTISTS = '/me/following';
    const ME_FOLLOWED_CONTAINS = '/me/following/contains';
    const ME_SAVED_TRACKS = '/me/tracks';
    const ME_SAVED_TRACKS_CONTAINS = '/me/tracks/contains';
    const ME_TOP = '/me/top/{type}';
    const NEW_RELEASES = '/browse/new-releases';
    const USER_PLAYLIST = '/users/{user_id}/playlists/{playlist_id}';
}
