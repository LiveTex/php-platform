<?php

/**
 * @author Tebryaev Oleg <oleg@tebryaev.com>
 */

namespace Platform;

use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\TPhpStream;

class Endpoint
{
    protected $config;
<<<<<<< HEAD
    protected $transport = null;
=======
>>>>>>> 6b5ff2def6c283cea27ce08a23e75051c535f3a2

    public function __construct(EndpointConfig $config)
    {
        $this->config = $config;
<<<<<<< HEAD

        /**
         * Подключаем сгенерированные трифтом файлы
         */

        set_include_path(get_include_path() . ':./app/thrift');

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
=======
>>>>>>> 6b5ff2def6c283cea27ce08a23e75051c535f3a2
    }

    public function process()
    {
        try {
            /**
<<<<<<< HEAD
             * Инициализируем трифт транспорт
=======
             * Thrift Transport
>>>>>>> 6b5ff2def6c283cea27ce08a23e75051c535f3a2
             */

            $transport = new TBufferedTransport(
                new TPhpStream(TPhpStream::MODE_R | TPhpStream::MODE_W)
            );

            /**
<<<<<<< HEAD
             * Инициализация бинарного прототокола
=======
             * Initialize protocol
>>>>>>> 6b5ff2def6c283cea27ce08a23e75051c535f3a2
             */

            $protocol = new TBinaryProtocol($transport, true, true);
            $transport->open();

            /**
<<<<<<< HEAD
             * Создаем хэндлел для обеспечения методов логикой
=======
             * Initialize service handler
>>>>>>> 6b5ff2def6c283cea27ce08a23e75051c535f3a2
             */

            $handlerClassName = "Handler{$this->config->service}";

            if (!class_exists($handlerClassName)) {
<<<<<<< HEAD
                throw new \Exception('Handler for service not found "' . $handlerClassName . '"');
=======
                throw new \Exception('Handler for service not found (' . $handlerClassName . ')');
>>>>>>> 6b5ff2def6c283cea27ce08a23e75051c535f3a2
            }
            $handler = new $handlerClassName();

            /**
<<<<<<< HEAD
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
        } catch (\Exception $exception) {
            echo $exception->getMessage();
            exit(1);
        }
    }

    public function getClient($host = '127.0.0.1', $port = 80, $path = '/')
    {
        try {
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
            $this->transport &= $transport;

        } catch (\Exception $exception) {
            echo $exception->getMessage();
            exit(1);
        }

        return $client;
    }

=======
             * Required thrift generated files
             */

            set_include_path(get_include_path().':thrift');

            $required_files = [
                "{$this->config->namespace}/{$this->config->service}.php",
                "{$this->config->namespace}/Types.php"
            ];

            foreach ($required_files as $file) require_once "{$file}";

            /**
             * Initialize service processor
             */

            $serviceProcessorClassName = "\\{$this->config->namespace}\\{$this->config->service}ServiceProcessor";

            if (!class_exists($serviceProcessorClassName)) {
                throw new \Exception('Service processor not found (' . $serviceProcessorClassName . ')');
            }

            $processor = new $serviceProcessorClassName($handler);

            /**
             * Process command
             */
            $processor->process($protocol, $protocol);

            $transport->close();
        } catch (\Exception $exception) {
            /**
             * Throw exception
             */

            echo $exception->getMessage();
            exit(1);
        }
    }
>>>>>>> 6b5ff2def6c283cea27ce08a23e75051c535f3a2
}


