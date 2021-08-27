# Laravel Simple API Token

This library is to Tokenize your current API Restful very easy. It makes your API more secure. 

How this library works : 
1. It will create table `laravel_api_tokens`
2. Insert the token data into it
3. Validate user request with table `laravel_api_tokens`

## Requirements

- Laravel 6, 7, or 8
- PHP 7.4 or 8.x

## Installation
Run this command on your root project
```bash
composer require fherryfherry/laravel-api-token
```
Run the migration bellow
```bash 
php artisan migrate
```

After installation is done, then run bellow command to export configuration file : 
```bash 
php artisan vendor:publish --provider=FherryFherry\LaravelApiToken\LaravelSimpleApiTokenServiceProvider
```

## Configuration
```php 
<?php

return [

    "expiry_unit"=> "day", // day, hour, minute
    "expiry_duration" => 3, // expiry duration by unit

    "token_length"=> 128, // how long token is

    // VALIDATION LEVEL ============================================ //
    // Level 1 = Validate by token only (default)                    //
    // Level 2 = Validate by token and ip address                    //
    // Level 3 = Validate by token, ip address and user agent        //
    //                                                               //
    // Please be careful with validation 2 and 3 because ip address  //
    // can suddenly change. Usually this because user providers      //
    // ============================================================= //
    "validation_level"=> 1,

    "basic_auth_user" => env("BASIC_AUTH_USER"), // user to request token
    "basic_auth_pass" => env("BASIC_AUTH_PASS") // password to request token
];
```

## Setting .ENV
Open the `.env` file, and paste these bellow on the bottom of file 
```bash 
BASIC_AUTH_USER="example"
BASIC_AUTH_PASS="123456"
```
You could change its value.

## Save User Data Into Token
You should create your own Login API. Then after the login is succeeded you could call this helper.
For the first, add these bellow to top of the class
```php 
use FherryFherry\LaravelApiToken\Helper\LaravelSimpleApiToken;
```
Then in your login method would be like these
```php 
public function postLogin(Request $request) {
    // ...
    
    if(Auth::attempt($request->except("_token"))) {
        // Then after that call this helper
        LaravelSimpleApiToken::saveLoginData($request, $user->id, $user->name);
        
        // Or if you have a role
        LaravelSimpleApiToken::saveLoginData($request, $user->id, $user->name, $user->role);               
    }
       
    // ...
}
```

## Request Token Endpoint
Give this endpoint to your frontend engineer. (I assume you use artisan serve, instead adjust the base domain)
``` 
http://localhost:8080/api/auth/request-token
```
Add header parameter with *Basic Authorization*.

How to use *Basic Authorization* you could refer this document. 
[https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Authorization](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Authorization)

This API will produce like these bellow : 
```json 
{
    "status": 1,
    "message": "success",
    "data": {
        "expired_at": "2013-05-05T16:34:42+00:00",
        "access_token": "bG9yZW0gaXBzdW0=",
        "refresh_token": "bG9yZW0gaXBzdW0="
    }
}
```
Frontend engineer should save the `expired_at`,`access_token`,`refresh_token` value.

## Refresh Token Endpoint
This API is to extend the expired time of `access_token` without request token again. But you will get new `access_token`,`refresh_token`,`expired_at`. 
The difference with Request Token is you don't need to hit the Login API again.
``` 
http://localhost:8080/api/auth/refresh-token
```

Frontend engineer need to add a *Header Parameter* with *Bearer Authorization*
```bash 
Authorization: Bearer {access_token}
```

This API will produce like these bellow :
```json 
{
    "status": 1,
    "message": "success",
    "data": {
        "expired_at": "2013-05-05T16:34:42+00:00",
        "access_token": "bG9yZW0gaXBzdW0=",
        "refresh_token": "bG9yZW0gaXBzdW0="
    }
}
```
Frontend engineer should save the `expired_at`,`access_token`,`refresh_token` value. For next header authorization.

## Secure Your API With Token
To prevent any user hit your API Without token, so you have to add `laravel_api_token` middleware to your API Route. 
Open your API route location (I assume you use routes/api.php)
```php 
Route::middleware(['api','laravel_api_token'])->group(function() {
    // place your all api routes here
    // ...
    
});
```
Frontend engineer need to add a *Header Parameter* with *Bearer Authorization*
```bash 
Authorization: Bearer {access_token}
```

## Get Current User ID
If you would like to get the current user ID, you only need to call this helper
```php 
$currentUserID = LaravelSimpleApiToken::getUserId();
```

## Get Current User Name
If you would like to get the current user name, you only need to call this helper
```php 
$currentUserName = LaravelSimpleApiToken::getUserName();
```

## Get Current User Role
If you would like to get the current user role, you only need to call this helper
```php 
$currentUserRole = LaravelSimpleApiToken::getUserRole();
```

## Get Token Data
For whatever reason sometime you want to see all available column values of current token, you could call this helper
```php 
$tokenData = LaravelSimpleApiToken::getTokenData();
```

## Destroy Token
If the user is logging out, you have to call this helper into your logout method at the bottom line is fine. 
So the frontend is should call the request token API again.
```php 
LaravelSimpleApiToken::destroy($request);
```

## Support & Donation
Hi thanks for using my open source project, you could support me via :
[https://saweria.co/ferryariawan](https://saweria.co/ferryariawan)
or via [https://buymeacoffee.com/ferryariawan](https://buymeacoffee.com/ferryariawan)
## Security Issue
If you found any security issue please contact me at ferdevelop15[at]gmail.com