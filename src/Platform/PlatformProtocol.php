<?php

namespace Platform;

use Thrift\Protocol\TBinaryProtocol;

class PlatformProtocol extends TBinaryProtocol{
    public $method = null;

    public function __construct($trans, $strictRead=false, $strictWrite=true){
        parent::__construct($trans, $strictRead=false, $strictWrite=true);
    }

    public function writeMessageBegin($name, $type, $seqid)
    {
        parent::writeMessageBegin($name, $type, $seqid);
        \Session::set("thrift_method", $name);
    }

}
