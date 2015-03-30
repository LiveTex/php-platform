<?php

/**
 * @author E.Mitroshin <evgeniy.m@livetex.ru>
*/

namespace Platform\Supply;

use Platform\Types\PlatformException;

class Curl
{
    protected $host = '127.0.0.1';
    protected $port = 80;
    protected $path = '/';
    protected $curl = null;

    protected $curl_options = [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_MAXREDIRS => 2,
        CURLOPT_HEADER => 0,
        CURLOPT_CONNECTTIMEOUT => 2,
        CURLOPT_TIMEOUT => 2,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
    ];


    /**
     * @param string $host - host with protocol, ex. http://host
     * @param int $port - http port, default 80
     * @param string $path - uri
     */
    public function __construct($host = '127.0.0.1', $port = 80, $path = '/')
    {
        /**
         *  Если $host заканчивается на '/', убираем его
         */
        if ( substr($host, -1) == "/" )
            $host = substr( $host, 0, strlen($host) - 1 );

        /**
         * Если $path не начинается на '/', добавляем
         */
        if ( substr($path, 0, 1) != "/" )
            $path = "/" . $path;

        $this->host = $host;
        $this->port = $port;
        $this->path = $path;

        $url = sprintf( "%s:%d%s", $this->host, $this->port, $this->path );
        $this->init( $url );
    }


    /**
     *
     */
    public function __destruct(){
        if ( !is_null($this->curl) ){
            curl_close( $this->curl );
        }

    }


    /**
     * @param string $url - URL адрес
     * @throws PlatformException
     */
    public function init( $url ) {
        $url = trim( $url );
        if ( !strlen($url) )
            throw new PlatformException('Url is empty.');

        $this->curl = curl_init($url);
    }

    /**
     * @param string $data - отправляемые данные
     */
    public function setData( $data ){
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
    }


    /**
     *
     */
    public function exec() {
        return curl_exec($this->curl);
    }


}