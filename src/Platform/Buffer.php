<?php
/**
 * @author E.Mitroshin <evgeniy.m@livetex.ru>
 */

namespace Platform;

use Platform\Supply\Curl;
use Platform\Types\PlatformException;
use Thrift\Transport\TPhpStream;

class Buffer extends TPhpStream {

    /**
     * $method - вызываемый метод
     */
    public $method = null;


    /**
     * $bufIn - Буфер входящего трифта
     */
    protected $bufIn = null;


    /**
     *
     */
    public function __construct( $mode )
    {
        parent::__construct( $mode );
    }


    /**
     *
     */
    public function __destruct()
    {

    }

    public function flush()
    {

        if ( in_array( \Session::get("thrift_method"), \Config::get('allow_log_methods') ) ) {

            /**
             * Сохраним результат для логирования
             * перед его отдачей
             */
            $buff = ob_get_contents();

            /**
             * Инициализируем curl и пытаемся отправить данные
             */
            try {
                $curl = new Curl(
                    \Config::get( 'eventservice.host' ),
                    \Config::get( 'eventservice.port' ),
                    \Config::get( 'eventservice.path' )
                );

                $curl->setData( $buff );
                $result = $curl->exec();

                if ( (int)$result !== 1 ){
                    throw new PlatformException("Event Service error.");
                }


            } catch ( PlatformException $error ) {

                \Log::error( $error->getMessage() );

            }

        }

        parent::flush();
    }

}