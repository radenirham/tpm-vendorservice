<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\v1\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\MKILib\GcsClient;

class StorageController extends ApiController
{

    public function upload(Request $request)
    {
        $data = array();
        $this->startProcess($request, 'post');

        $fileClient = new GcsClient();

        $folder = isset($this->param['folder']) ? $this->param['folder'] : '';
        $rename = isset($this->param['rename']) ? $this->param['rename'] : '';
        $bucket = isset($this->param['bucket']) ? $this->param['bucket'] : '';

        $filename = $this->file['file']->getClientOriginalName();
        if($rename != '') {
            $filename = $rename.'.'.$this->file['file']->getClientOriginalExtension();
        }
        $target = ($folder != '' ? $folder.'/' : '').$filename;

        $data = $fileClient->upload($this->file['file'], $target, $bucket);

        return $this->output(true, '', $data);
    }

    public function download(Request $request)
    {
        $data = array();
        $this->startProcess($request, 'get');

        $path = isset($this->param['path']) ? $this->param['path'] : '';
        $bucket = isset($this->param['bucket']) ? $this->param['bucket'] : '';

        $fileClient = new GcsClient();
        $data = $fileClient->download($path, $bucket);

        return $this->output(true, '', $data);
    }

    public function signed(Request $request)
    {
        $data = array();
        $this->startProcess($request, 'get');

        $folder = isset($this->param['folder']) ? $this->param['folder'] : '';
        $filename = isset($this->param['filename']) ? $this->param['filename'] : '';
        $mime = isset($this->param['mime']) ? $this->param['mime'] : '';
        $bucket = isset($this->param['bucket']) ? $this->param['bucket'] : '';

        $target = ($folder != '' ? $folder.'/' : '').$filename;

        $fileClient = new GcsClient();
        $data = $fileClient->signed($target, $mime, $bucket);

        return $this->output(true, '', $data);
    }

    public function preview(Request $request)
    {
        $data = array();
        $this->startProcess($request, 'get');

        $path = isset($this->param['path']) ? $this->param['path'] : '';
        $bucket = isset($this->param['bucket']) ? $this->param['bucket'] : '';

        $fileClient = new GcsClient();
        $data = $fileClient->preview($path, $bucket);

        return $this->output(true, '', $data);
    }

}
