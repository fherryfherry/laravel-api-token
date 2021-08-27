<?php

namespace FherryFherry\LaravelApiToken\Api;

use Illuminate\Routing\Controller;
use FherryFherry\LaravelApiToken\Helper\LaravelSimpleApiToken;

class ApiAuthController extends Controller
{
    public function postRequestToken(\Illuminate\Http\Request $request)
    {
        $authBase64 = LaravelSimpleApiToken::getBasicToken($request);
        $authDecoded = base64_decode($authBase64);
        $authArray = explode(":", $authDecoded);
        if($authArray[0] == config("laravel_simple_api_token.basic_auth_user") && $authArray[1] == config("laravel_simple_api_token.basic_auth_pass")) {

            $tokenData = LaravelSimpleApiToken::generateToken($request);

            return response()->json([
                'status'=>1,
                'message'=>'success',
                'data'=> [
                    'expired_at'=> date("c", strtotime($tokenData->expired_at)),
                    'access_token'=> $tokenData->access_token,
                    'refresh_token'=> $tokenData->refresh_token
                ]
            ]);
        } else {
            return response()->json([
                'status'=>0,
                'message'=>'invalid credential'
            ],400);
        }
    }


    public function postRefreshToken(\Illuminate\Http\Request $request)
    {
        $tokenData = LaravelSimpleApiToken::generateToken($request, true);

        return response()->json([
            'status'=>1,
            'message'=>'success',
            'data'=> [
                'expired_at'=> date("c", strtotime($tokenData->expired_at)),
                'access_token'=> $tokenData->access_token,
                'refresh_token'=> $tokenData->refresh_token
            ]
        ]);
    }


}