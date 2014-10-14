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

    public function __construct(EndpointConfig $config)
    {
        $this->config = $config;
    }

    public function process()
    {
        try {
            /**
             * Thrift Transport
             */

            $transport = new TBufferedTransport(
                new TPhpStream(TPhpStream::MODE_R | TPhpStream::MODE_W)
            );

            /**
             * Initialize protocol
             */

            $protocol = new TBinaryProtocol($transport, true, true);
            $transport->open();

            /**
             * Initialize service handler
             */

            $handlerClassName = "Handler{$this->config->service}";

            if (!class_exists($handlerClassName)) {
                throw new \Exception('Handler for service not found (' . $handlerClassName . ')');
            }
            $handler = new $handlerClassName();

            /**
             * Required thrift generated files
             */

            set_include_path(get_include_path().':thrift');

            $required_files = [
                "{$this->config->namespace}/{$this->config->service}Service.php",
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
}


