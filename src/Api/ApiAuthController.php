<?php

namespace LaravelApiToken\Api;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class ApiAuthController extends Controller
{

    public function postRequestToken(\Illuminate\Http\Request $request)
    {
        $authBase64 = \LaravelApiToken\LaravelSimpleApiToken::getBasicToken($request);
        $authDecoded = base64_decode($authBase64);
        $authArray = explode(":", $authDecoded);
        if($authArray[0] == config("laravel_simple_api_token.basic_auth_user") && $authArray[1] == config("laravel_simple_api_token.basic_auth_pass")) {

            $tokenData = \LaravelApiToken\LaravelSimpleApiToken::generateToken($request);

            return response()->json([
                'status'=>1,
                'message'=>'success',
                'data'=> [
                    'expired_at'=> date("c", strtotime($tokenData->expired_at)),
                    'token'=> $tokenData->token
                ]
            ]);
        } else {
            return response()->json([
                'status'=>0,
                'message'=>'invalid credential'
            ],400);
        }
    }


}