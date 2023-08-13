<?php

namespace App\MKILib;

use Exception;
use Illuminate\Support\Facades\Config;

use Google\Cloud\Storage\StorageClient;

class GcsClient {

    protected string $keyFile;
    protected string $bucketName;
    protected string $projectId;

    public function __construct()
    {
        $this->keyFile = env('GOOGLE_CLOUD_KEY');
        $this->bucketName = env('GOOGLE_CLOUD_BUCKET');
        $this->projectId = env('GOOGLE_CLOUD_PROJECT_ID');
    }

    public function upload($filePath, $fileName='', $bucket='') {
        $fileName = $fileName == '' ? basename($filePath) : $fileName;
        $this->bucketName = $bucket != '' ? $bucket : $this->bucketName;

        try {
            $storage = new StorageClient([
                'keyFilePath' => base_path(). '/'. $this->keyFile,
                'projectId' => $this->projectId
            ]);

            $bucket = $storage->bucket($this->bucketName);

            $bucket->upload(
                fopen($filePath, 'r'),
                [
                    'name' => $fileName
                ]
            );
            return $fileName;
        } catch(Exception $e) {
            return $e;
        }
    }

    public function download($fileName, $bucket='') {
        $this->bucketName = $bucket != '' ? $bucket : $this->bucketName;
        try {
            $storage = new StorageClient([
                'keyFilePath' => base_path(). '/'. $this->keyFile,
                'projectId' => $this->projectId
            ]);

            $bucket = $storage->bucket($this->bucketName);
            $fileTarget = 'temp/downloaded/'.$fileName;
            $pathTarget = str_replace(basename($fileName), '', $fileTarget);
            if (!file_exists($pathTarget)) {
                mkdir($pathTarget, 0777, true);
            }

            $object = $bucket->object($fileName);
            $object->downloadToFile($fileTarget);

            return $this->getBaseUrl().$fileTarget;
        } catch(Exception $e) {
            return null;
        }
    }

    public function signed($filename, $mime, $bucket='')
    {
        $this->bucketName = $bucket != '' ? $bucket : $this->bucketName;

        $storage = new StorageClient([
            'keyFilePath' => base_path(). '/'. $this->keyFile,
            'projectId' => $this->projectId
        ]);

        $bucket = $storage->bucket($this->bucketName);
        $object = $bucket->object($filename);

        $url = $object->signedUrl(
            new \DateTime('tomorrow'),
            [
                'version' => 'v4',
                'action' => 'write',
                'contentType' => $mime,
                'method' => 'PUT'
            ]
        );

        return $url;
    }

    public function preview($fileName, $bucket='') {
        $this->bucketName = $bucket != '' ? $bucket : $this->bucketName;
        try {
            $storage = new StorageClient([
                'keyFilePath' => base_path(). '/'. $this->keyFile,
                'projectId' => $this->projectId
            ]);

            $bucket = $storage->bucket($this->bucketName);
            $object = $bucket->object($fileName);

            $url = $object->signedUrl(
                new \DateTime('tomorrow'),
                [
                    'version' => 'v4',
                    'action' => 'read',
                ]
            );

            return $url;
        } catch(Exception $e) {
            return null;
        }
    }

    private function getBaseUrl()
    {
        $hostName = $_SERVER['HTTP_HOST'];
        $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
        return $protocol.'://'.$hostName.'/';
    }
}
