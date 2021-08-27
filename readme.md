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
php artisan vendor:publish --provider=LaravelApiToken\LaravelSimpleApiTokenServiceProvider
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

## Save User Data Into Token
You should create your own Login API. Then after the login is succeeded you could call this helper.
For the first, add these bellow to top of the class
```php 
use LaravelApiToken\LaravelSimpleApiToken;
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

## Security Issue
If you found any security issue please contact me at ferdevelop15[at]gmail.com