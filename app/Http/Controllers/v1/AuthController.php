<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\v1\ApiController;
use Illuminate\Support\Facades\DB;

use App\Models\Auth\ViewVendorUser;

class AuthController extends ApiController
{

    public function connect(Request $request) {
        $data = array();
        $this->startProcess($request, 'get');

        return $this->output(true, '', $data );
    }

    public function getAccessToken(Request $request) {
        $data = array();
        $this->startProcess($request, 'post');

        $get = $request->query->all();
        $post = $request->request->all();

        if(!$request->header('appCode')) { //check appcode & npk in header
            return $this->output(false, 'AppCode harus dikirim', $data);
        } else {
            $client = app('db')
                ->table('sys_clients')
                ->select('client_uuid')
                ->where('client_appcode', $request->header('appCode'))
                ->where('client_service', env('SERVICE_INITIAL'))
                ->where('client_date_start', '<=', date('Y-m-d', time()))
                ->where('client_date_end', '>=', date('Y-m-d', time()))
                ->where('client_status', 1)
                ->first();

            if(!$client) { //check active appCode
                return $this->output(false, 'AppCode tidak ditemukan', $data);
            } else {
                $remember = (isset($post['remember']) && $post['remember']==1)?$post['remember']:0;
                $hashtime = md5(time());
                $generated_token = base64_encode($hashtime.md5($hashtime).'-'.base64_encode($request->header('appCode')).'-'.$remember);

                $timeAdd = time()+(3600*($remember==1?8600:1));
                $timeText = date('Y-m-d H:i:s', $timeAdd);
                $this->tokenExpire = $timeAdd;

                $dataInsert = array(
                    'client_token_client_uuid'     => $client->client_uuid,
                    'client_token_access'          => $generated_token,
                    'client_token_expire'          => $timeText
                );
                app('db')
                    ->table('sys_client_tokens')
                    ->insert([$dataInsert]);

                $data['access_token'] = $generated_token;
                $data['expire'] = array(
                    'time'  => $timeAdd,
                    'text'  => $timeText
                );
                return $this->output(true, '', $data);

            }
        }
    }

    public function getAuth(Request $request) {
        $data = array();

        $status = true;
        $message = '';
        $response = 200;

        $rules = array(
            'username'    => 'required',
            'password'    => 'required'
        );
        $this->startProcess($request, 'post', $rules);

        $username = isset($this->param['username']) ? $this->param['username'] : '';
        $password = isset($this->param['password']) ? $this->param['password'] : '';

        $userExist = ViewVendorUser::where('vendor_user_name', $username)
            ->where('vendor_status', 1)
            ->first();

        if($userExist && Hash::check($password, $userExist->vendor_user_password)) {
            $header = array(
                'alg'   => 'HS256',
                'typ'   => 'JWT',
                'time'  => time()
            );

            $payload = $userExist->toArray();

            $data = array(
                'userdata'  => $payload,
                'token'     => JWT::encode($payload, env('SERVICE_HASH'), 'HS256', null, $header)
            );
        } else {
            $status = false;
            $message = 'Username dan Password tidak sesuai';
            $response = 401;
        }

        return $this->output($status, $message, $data, $response);
    }

    public function getUserdata(Request $request) {
        $data = array();

        $status = true;
        $message = '';
        $response = 200;

        $this->startProcess($request, 'post');

        $vendorUuid = $this->userdata->vendorUuid;

        $userdata = ViewVendorUser::where('vendor_uuid', $vendorUuid)
            ->where('vendor_status', 1)
            ->first()
            ->toArray();

        if($status) {
            $header = array(
                'alg'   => 'HS256',
                'typ'   => 'JWT',
                'time'  => time()
            );
            $payload = $userdata;

            $data = array(
                'userdata'  => $payload,
                'token'     => JWT::encode($payload, env('SERVICE_HASH'), 'HS256', null, $header)
            );
        }
        return $this->output($status, $message, $data, $response);
    }
}
