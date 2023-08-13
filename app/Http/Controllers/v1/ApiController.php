<?php

namespace App\Http\Controllers\v1;

use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;

use Spatie\ArrayToXml\ArrayToXml;
use App\Models\Base\Userdata;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ApiController extends BaseController
{
    private $startTime;
    private $version = 'v1';
    private $outputType = 'json';
    private $request = 'get';
    public $rules = [];
    public $tokenExpire = null;
    public $param = [];
    public $file = [];
    public $userdata;
    public $accessId = null;
    private $noAccessTokenNeeded = array(
        'auth/connect',
        'auth/getAccessToken'
    );
    private $noTokenIdNeeded = array(
        'auth/getAuth',
        'auth/getOtp',
        'auth/verifyOtp'
    );

    public function startProcess($request, $allowedMethod, $rules=array()) {
        $time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];
		$start = $time;
        $this->startTime = $start;
        $this->request = strtolower($request);
        $this->rules = $rules;

        if(strtolower($allowedMethod)=='get') {
            $this->param = $request->query->all();
        } else if(strtolower($allowedMethod)=='post') {
            $this->param = $request->request->all();
            $this->file = $request->allFiles();
        } else {
            $this->param = [];
        }

        $this->accessId = $request->header('accessId');
        $this->userdata = new Userdata();

        $isError = false;
        $result_api = array(
                    'status'        => false,
                    'message'       => '',
                    'response'      => array(),
                    'generated'     => 0,
                    'serverTime'    => time(),
                    'version'       => $this->version
        );
        $output_status = 200;

        $segment = explode('/', $request->path());
        $methodPath = $segment[1].'/'.$segment[2];

        $this->outputType = $segment[count((array)$segment)-1];

        if (!$request->isMethod($allowedMethod)) {

            $isError = true;
            $result_api['message'] = 'Method tidak diizinkan';
            $output_status = 405;

        } else {
            if (!$request->header('AppCode')) {
                $isError = true;
                $result_api['message'] = 'AppCode harus dikirim';
                $output_status = 403;
            } else {
                if (!in_array($methodPath, $this->noAccessTokenNeeded)) {
                    if(!$request->header('AppCode') || !$request->header('accessToken')) { //check appcode in header

                        $isError = true;
                        $result_api['message'] = 'AccessToken harus dikirim';
                        $output_status = 403;

                    } else {
                        if (!in_array($methodPath, $this->noTokenIdNeeded)) {
                            if ($request->header('tokenId')) {

                                $decode = $request->header('tokenId');
                                try {
                                    $jwtData = (array)JWT::decode($decode, new Key(env('SERVICE_HASH'), 'HS256'));
                                    if(count((array)$jwtData) > 0) {
                                        $this->userdata->vendorUserId = $jwtData['vendor_user_id'];
                                        $this->userdata->vendorUserUuid = $jwtData['vendor_user_uuid'];
                                        $this->userdata->vendorUserName = $jwtData['vendor_user_name'];
                                        $this->userdata->vendorUserPassword = $jwtData['vendor_user_password'];
                                        $this->userdata->vendorUserFullname = $jwtData['vendor_user_fullname'];
                                        $this->userdata->vendorUuid = $jwtData['vendor_uuid'];
                                        $this->userdata->vendorCode = $jwtData['vendor_code'];
                                        $this->userdata->vendorName = $jwtData['vendor_name'];
                                        $this->userdata->vendorTaxNumber = $jwtData['vendor_tax_number'];
                                        $this->userdata->vendorCompanyType = $jwtData['vendor_company_type'];
                                        $this->userdata->vendorCompanyTypeName = $jwtData['vendor_company_type_name'];
                                        $this->userdata->vendorArea = $jwtData['vendor_area'];
                                        $this->userdata->vendorAreaName = $jwtData['vendor_area_name'];
                                        $this->userdata->vendorCountryCode = $jwtData['vendor_country_code'];
                                        $this->userdata->vendorCountryName = $jwtData['vendor_country_name'];
                                        $this->userdata->vendorStatus = $jwtData['vendor_status'];
                                        $this->userdata->vendorFilledStatus = $jwtData['vendor_filled_status'];
                                    } else {
                                        $isError = true;
                                        $result_api['message'] = 'TokenId tidak sesuai';
                                        $output_status = 403;
                                    }
                                } catch (\Throwable $th) {
                                    $isError = true;
                                    $result_api['message'] = 'TokenId tidak sesuai';
                                    $output_status = 403;
                                }
                            } else {
                                $isError = true;
                                $result_api['message'] = 'TokenId harus dikirim';
                                $output_status = 403;
                            }

                        }

                        $client = app('db')
                            ->table('sys_clients')
                            ->join('sys_client_tokens', 'client_token_client_uuid', '=', 'client_uuid')
                            ->select('client_uuid')
                            ->where('client_appcode', $request->header('AppCode'))
                            ->where('client_token_access', $request->header('accessToken'))
                            ->where('client_service', env('SERVICE_INITIAL'))
                            ->where('client_date_start', '<=', date('Y-m-d', time()))
                            ->where('client_date_end', '>=', date('Y-m-d', time()))
                            ->where('client_token_expire', '>=', date('Y-m-d', time()))
                            ->where('client_status', 1)
                            ->first();

                        if(!$client) { //check active appCode

                            $isError = true;
                            $result_api['message'] = 'AccessToken tidak sesuai';
                            $output_status = 401;

                        } else {
                            $timeAdd = time()+(3600*(substr(base64_decode($request->header('accessToken')), -1)==1?8600:1));
                            $timeText = date('Y-m-d H:i:s', $timeAdd);

                            app('db')
                                ->table('sys_client_tokens')
                                ->where('client_token_access', $request->header('accessToken'))
                                ->update(['client_token_expire' => $timeText]);

                            $this->tokenExpire = $timeAdd;

                            if(env('SERVICE_ACTIVITY', true)) {
                                $dataInsert = array(
                                    'client_activity_client_uuid'   => $client->client_uuid,
                                    'client_activity_ipv4'          => $request->server('REMOTE_ADDR'),
                                    'client_activity_port'          => $request->server('REMOTE_PORT'),
                                    'client_activity_method'        => $request->server('REQUEST_METHOD'),
                                    'client_activity_uri'           => $request->server('REQUEST_URI'),
                                    'client_activity_user_agent'    => $request->server('HTTP_USER_AGENT'),
                                    'client_activity_request_time'  => $request->server('REQUEST_TIME'),
                                    'client_activity_create_date'   => date('Y-m-d H:i:s', time())
                                );
                                app('db')
                                    ->table('sys_client_activities')
                                    ->insert([$dataInsert]);
                            }
                        }
                    }
                }
            }
        }

        //start of filter
        if(!$isError) {
            $validate = Validator::make($this->param, $this->rules, [
                'required'  => 'Parameter :attribute tidak boleh kosong',
                'between'   => 'Jumlah karakter parameter :attribute harus diantara :min - :max karakter',
                'boolean'   => 'Parameter :attribute harus berisi true atau false',
                'date'      => 'Parameter :attribute harus diisi tanggal yang benar',
                'email'     => 'Parameter :attribute harus diisi email yang benar',
                'numeric'   => 'Parameter :attribute harus berupa angka',
                'max'       => 'Jumlah karakter parameter :attribute tidak boleh lebih dari :max karakter',
                'min'       => 'Jumlah karakter parameter :attribute tidak boleh kurang dari :min karakter',
                'string'    => 'Parameter :attribute harus berupa angka',
                'url'       => 'Parameter :attribute harus diisi url yang benar',
                'uuid'      => 'Parameter :attribute harus diisi uuid yang benar',
            ]);

            if ($validate->fails()) {
                $isError = true;
                $errorData = array();
                foreach($validate->messages()->get('*') as $errorField => $errorMessage) {
                    $errorItem = array();
                    foreach($errorMessage as $error) {
                        $errorItem[] = $error;
                    }
                    $errorData[] = array(
                        'field'     => $errorField,
                        'message'   => $errorItem,
                    );
                }
                $result_api['message'] = "Parameter yang dikirim tidak sesuai";
                $result_api['errors'] = $errorData;

                $output_status = 403;
            }
        }

        //end of filter

        if($isError) {
            $output = $this->renderOutput($result_api, $this->outputType, $output_status);
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Headers: *');
            switch ($this->outputType) {
                case 'json':
                    header('Content-Type: application/json');
                    break;
                case 'xml':
                    header('Content-Type: application/xml; charset=utf-8');
                    break;
                case 'jsonp':
                    header('Content-Type: application/javascript');
                    break;
                case 'serialize':
                    header('Content-Type: text/plain');
                    break;
                default:
                    header('Content-Type: application/json');
                    break;
            }
            header('HTTP/1.1 '.$output_status, true, $output_status);
            echo $output->original;
            die();
        }
    }

    private function generatedTime($start) {
        $time = microtime();
        $time = explode(' ', $time);
        $time = $time[1] + $time[0];
        $finish = $time;
        return round(($finish - $start), 4);
    }

    public function output($status=true, $message='', $response=array(), $output_status=200) {

        if($this->startTime =='') {
            $result_api = array(
                            'status'        => false,
                            'message'       => 'Missing startProcess',
                            'response'      => array(),
                            'generated'     => 0,
                            'tokenExpire'   => $this->tokenExpire
            );
            $output_status = 203;
        } else {
            $result_api = array(
                            'status'        => $status,
                            'message'       => $message,
                            'response'      => $response,
                            'generated'     => $this->generatedTime($this->startTime),
                            'tokenExpire'   => $this->tokenExpire
            );
        }
        $result_api['serverTime'] = time();
        $result_api['version'] = $this->version;
        return $this->renderOutput($result_api, $this->outputType, $output_status);
    }

    private function renderOutput($result_api, $output_type='json', $output_status) {
        switch ($output_type) {
            case 'json':
                return response(
                    json_encode($result_api),
                    $output_status
                )->header('Content-Type', 'application/json');
                break;
            case 'xml':
                return response(
                    ArrayToXml::convert($result_api, 'mki-microservices', true, 'UTF-8', '1.1', [], true),
                    $output_status
                )->header('Content-Type', 'text/xml');
                break;
            case 'jsonp':
                return response(
                    json_encode($result_api),
                    $output_status
                )->header('Content-Type', 'application/javascript');
                break;
            case 'serialize':
                return response(
                    serialize($result_api),
                    $output_status
                )->header('Content-Type', 'text/plain');
                break;
            default:
                return response(
                    json_encode($result_api),
                    $output_status
                )->header('Content-Type', 'application/json');
                break;
        }
	}

    function uuid()
    {
        $data = random_bytes(16);
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    function encrypt($plainText, $key)
    {
        $secretKey = md5($key);
        $iv = substr(hash('sha256', env('SERVICE_HASH')), 0, 16);
        $encryptedText = openssl_encrypt($plainText, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $iv);

        return base64_encode($encryptedText);
    }

    function decrypt($encryptedText, $key)
    {
        $key = md5($key);
        $iv = substr(hash('sha256', env('SERVICE_HASH')), 0, 16);
        $decryptedText = openssl_decrypt(base64_decode($encryptedText), 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return $decryptedText;
    }

    function setActivity($description='', $menuId=null, $menuUrl=null, $action=0) {
        /* if(count((array)$this->userdata)>0) {
            $dataInsert = array(
                'activity_user_uuid'    => $this->userdata->employeeUuid,
                'activity_menu_id'      => $menuId,
                'activity_action'       => $action,
                'activity_url'          => $menuUrl,
                'activity_description'  => $description,
                'activity_date'         => date('Y-m-d H:i:s', time())
            );
            try {
                app('db')
                    ->table('master.sys_activities')
                    ->insert([$dataInsert]);
                return true;
            } catch (\Throwable $th) {
                return false;
            }
        } else {
            return false;
        } */
    }
    function setNotification($description = '', $menuId = null, $menuUrl = null, $action = 0)
    {
        /* if (count((array)$this->userdata) > 0) {
            $dataInsert = array(
                'activity_user_uuid'    => $this->userdata->employeeUuid,
                'activity_menu_id'      => $menuId,
                'activity_action'       => $action,
                'activity_url'          => $menuUrl,
                'activity_description'  => $description,
                'activity_date'         => date('Y-m-d H:i:s', time())
            );
            try {
                app('db')
                    ->table('master.sys_activities')
                    ->insert([$dataInsert]);
                return true;
            } catch (\Throwable $th) {
                return false;
            }
        } else {
            return false;
        } */
    }
}
