<?php

/**
 * @author Tebryaev Oleg <oleg@tebryaev.com>
 */

namespace Platform;

use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\TPhpStream;
use Thrift\Transport\THttpClient;

class Endpoint
{
    protected $config;
    protected $transport = null;

    public function __construct(EndpointConfig $config)
    {
        $this->config = $config;

        /**
         * Подключаем сгенерированные трифтом файлы
         */


        set_include_path(get_include_path() . ':' . app_path() . '/thrift');

        $required_files = [
            "{$this->config->namespace}/{$this->config->service}.php",
            "{$this->config->namespace}/Types.php"
        ];

        foreach ($required_files as $file) {
            require_once "{$file}";
        }
    }

    public function __destruct()
    {
        /**
         * Останавливаем транспорт, если он был использован
         */

        if (!is_null($this->transport)) {
            $this->transport->close();
        }
    }

    public function process()
    {
        /**
         * Инициализируем трифт транспорт
         */

        $transport = new TBufferedTransport(
            new TPhpStream(TPhpStream::MODE_R | TPhpStream::MODE_W)
        );

        /**
         * Инициализация бинарного прототокола
         */

        $protocol = new TBinaryProtocol($transport, true, true);
        $transport->open();

        /**
         * Создаем хэндлел для обеспечения методов логикой
         */

        $handlerClassName = "Handler{$this->config->service}";

        if (!class_exists($handlerClassName)) {
            throw new \Exception('Handler for service not found "' . $handlerClassName . '"');
        }
        $handler = new $handlerClassName();

        /**
         * Создаем процессор
         */

        $serviceProcessorClassName = "\\{$this->config->namespace}\\{$this->config->service}Processor";

        if (!class_exists($serviceProcessorClassName)) {
            throw new \Exception('Processor not found (' . $serviceProcessorClassName . ')');
        }

        $processor = new $serviceProcessorClassName($handler);

        /**
         * Запуск для обработки одного запроса
         */

        $processor->process($protocol, $protocol);

        $transport->close();

    }

    public function getClient($host = '127.0.0.1', $port = 80, $path = '/')
    {
        /**
         * HTTP клиент
         */

        $socket = new THttpClient($host, $port, $path);

        /**
         * Инициализцаия транспорта и протокола
         */

        $transport = new TBufferedTransport($socket);
        $protocol = new TBinaryProtocol($transport);

        /**
         * Инициализируем клиент
         */

        $clientClassName = "\\{$this->config->namespace}\\{$this->config->service}Client";

        if (!class_exists($clientClassName)) {
            throw new \Exception('Processor not found "' . $clientClassName . '"');
        }

        $client = new $clientClassName($protocol);

        /**
         * Запускает транспорт
         */

        $transport->open();
        $this->transport = &$transport;

        return $client;
    }

}


