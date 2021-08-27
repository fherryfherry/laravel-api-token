<?php

namespace FherryFherry\LaravelApiToken\Helper;

use FherryFherry\Models\LaravelApiToken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LaravelSimpleApiToken
{
    private static $tokenData;


    public static function getBearerToken(Request $request)
    {
        $authorization = $request->header('Authorization');
        return str_replace("Bearer ", "", $authorization);
    }

    public static function getBasicToken(Request $request)
    {
        $authorization = $request->header('Authorization');
        return str_replace("Basic ", "", $authorization);
    }

    public static function validateBasicToken(Request $request)
    {
        return !empty(static::getBasicToken($request));
    }

    public static function validateBearerToken(Request $request)
    {
        $bearer = static::getBearerToken($request);
        $validationLevel = config("laravel_simple_api_token.validation_level", 1);
        if($bearer) {
            if($cache = cache()->get(md5("laravel_api_token".$bearer))) {
                $tokenData = $cache;
            } else {
                $tokenData = LaravelApiToken::query()->where("token",$bearer)
                    ->where("expired_at",">",now()->format("Y-m-d H:i:s"))
                    ->first();
                if($tokenData) {
                    cache()->put(md5("laravel_api_token".$bearer), $tokenData, 120);
                }
            }

            switch ($validationLevel) {
                default:
                case 1:
                    if($tokenData->expired_at > now()->format("Y-m-d H:i:s")) {
                        return true;
                    } else {
                        return false;
                    }
                    break;
                case 2:
                    if($tokenData->expired_at > now()->format("Y-m-d H:i:s") && $tokenData->ip_address == $request->ip()) {
                        return true;
                    } else {
                        return false;
                    }
                    break;
                case 3:
                    if($tokenData->expired_at > now()->format("Y-m-d H:i:s") && $tokenData->ip_address == $request->ip() && $tokenData->user_agent == $request->userAgent()) {
                        return true;
                    } else {
                        return false;
                    }
                    break;
            }

        } else {
            return false;
        }
    }

    /**
     * @param Request $request
     * @return LaravelApiToken
     */
    public static function generateToken(Request $request)
    {
        $token = Str::random(config("laravel_simple_api_token.token_length", 128));
        $token = base64_encode($token);

        $expiryUnit = config("laravel_simple_api_token.expiry_unit", 3);
        $expiryDuration = config("laravel_simple_api_token.expiry_duration", 60);

        $tokenModel = new LaravelApiToken();

        switch ($expiryUnit) {
            case "day":
                $tokenModel->expired_at = now()->addDays($expiryDuration);
                break;
            case "hour":
                $tokenModel->expired_at = now()->addHours($expiryDuration);
                break;
            case "minute":
                $tokenModel->expired_at = now()->addMinutes($expiryDuration);
                break;
        }

        $tokenModel->ip_address = $request->ip();
        $tokenModel->user_agent = $request->userAgent();
        $tokenModel->token = $token;
        $tokenModel->save();

        return $tokenModel;
    }

    public static function saveLoginData(Request $request, string $id, string $name, string $role = null)
    {
        $bearer = static::getBearerToken($request);
        if($bearer) {
            $data = LaravelApiToken::query()->where("token", $bearer)->first();
            $data->users_id = $id;
            $data->users_name = $name;
            $data->users_role = $role;
            $data->save();
            return true;
        } else {
            return false;
        }
    }

    public static function destroy(Request $request)
    {
        $bearer = static::getBearerToken($request);
        if($bearer) {
            cache()->forget(md5("laravel_api_token".$bearer));
            LaravelApiToken::query()->where("token", $bearer)->delete();
            return true;
        } else {
            return false;
        }
    }

    public static function getTokenData(Request $request)
    {
        $bearer = static::getBearerToken($request);
        if($bearer) {
            return LaravelApiToken::query()->where("token", $bearer)->first();
        } else {
            return null;
        }
    }

    public static function getUserId(Request $request)
    {
        static::$tokenData = (static::$tokenData) ?: static::getTokenData($request);
        return (static::$tokenData) ? static::$tokenData->users_id : null;
    }

    public static function getUserName(Request $request)
    {
        static::$tokenData = (static::$tokenData) ?: static::getTokenData($request);
        return (static::$tokenData) ? static::$tokenData->users_name : null;
    }

    public static function getUserRole(Request $request)
    {
        static::$tokenData = (static::$tokenData) ?: static::getTokenData($request);
        return (static::$tokenData) ? static::$tokenData->users_role : null;
    }
}