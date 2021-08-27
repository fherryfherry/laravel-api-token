<?php

return [

    "expiry_unit"=> "day", // day, hour, minute
    "expiry_duration" => 3,

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

    "basic_auth_user" => env("BASIC_AUTH_USER"),
    "basic_auth_pass" => env("BASIC_AUTH_PASS")
];