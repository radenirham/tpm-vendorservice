<?php

namespace App\MKILib;

use Exception;
use Grpc\ChannelCredentials;
use Illuminate\Support\Facades\Config;

class GrpcClient
{
    protected string $service = '';
    protected BaseStubWrapper $client;

    /**
     * @throws Exception
     */
    public function __construct($service)
    {
        $this->service = $service;
        $host = env(strtoupper($service).'_HOST');
        $authentication = env(strtoupper($service).'_AUTHENTICATION');
        $cert = env(strtoupper($service).'_CERT');

        if(is_null($host) || is_null($authentication)){
            throw new Exception('Configuration Failed ('.$this->service.')');
        }
        $authenticationMethod = 'create'.ucfirst($authentication).'Credentials';

        $this->client = new BaseStubWrapper($host, [
            'credentials' => $this->{$authenticationMethod}($cert??''),
        ]);
    }

    public function simpleRequest(string $methodName, $request, $response) {
        $this->client->setServiceName($this->service);
        return $this->client->simpleRequest($methodName, $request, $response);
    }

    /**
     * Create tls credential
     * @param string $certPath
     * @return ChannelCredentials
     */
    private function createTlsCredentials(string $certPath): ChannelCredentials
    {
        return ChannelCredentials::createSsl(file_get_contents(base_path($certPath)));
    }

    /**
     * Create insecure credential
     * @param string $certPath
     * @return null
     */
    private function createInsecureCredentials(string $certPath)
    {
        return ChannelCredentials::createInsecure();
    }
}
