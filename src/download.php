<?php

/**
 * @param $url to download
 * @param int $timeout in seconds
 * @return string containing HTTP response
 */
function download( $url, $timeout = 4 )
{
    if( !$url) return null;
    $userAgent = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36';
    $parsed = parse_url( $url );

    $info = false;
    if ( function_exists( 'curl_version' ) )
    {
        $ch = curl_init( $url );

        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_HEADER, false );
        curl_setopt( $ch, CURLOPT_USERAGENT, $userAgent );

        curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );

        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

        $info = curl_exec( $ch );
        curl_close( $ch );
    }
    if ( !$info )
    {
        // Make sure to use the old array() syntax to support ancient PHP installations.
        $info = @file_get_contents( $url, null, stream_context_create( array( 'http' => array(
            'timeout'    => $timeout,
            'user_agent' => $userAgent,
        ) ) ) );
    }

    if ( !$info )
    {
        $info = '';

        $fp = fsockopen(
            ( $parsed[ 'scheme' ] == 'http' ? 'http' : 'ssl' )
            . $parsed[ 'host' ] . "://", ( $parsed[ 'scheme' ] == 'http' ? 80 : 443 ), $errno, $errstr, 30 );

        $out = "GET " . str_replace( $parsed[ 'scheme' ] . '://' . $parsed[ 'host' ], '', $url ) . " HTTP/1.1\r\n";
        $out .= "Host: $parsed[host]\r\n";
        $out .= "Connection: Close\r\n\r\n";
        fwrite( $fp, $out );
        while ( !feof( $fp ) )
        {
            $info .= fgets( $fp, 128 );
        }
        fclose( $fp );

        $info = substr( $info, strpos( $info, "\r\n\r\n" ) + 4 );
    }

    return trim( $info );
}
