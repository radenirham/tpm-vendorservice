<?php

namespace GrpcServerService\Sample;

use GrpcServerModel\Sample\ConnectionInterface;
use GrpcServerModel\Sample\PingRequest;
use GrpcServerModel\Sample\PingResponse;

use Spiral\RoadRunner\GRPC\ContextInterface;

class ConnectionService implements ConnectionInterface
{
    
    public function Ping(ContextInterface $ctx, PingRequest $in): PingResponse
    {
        $out = new PingResponse();

        $signer = array('status'=>$in->getRequest() . ' - PONG');

        return $out->setResponse(json_encode($signer));
    }
}
