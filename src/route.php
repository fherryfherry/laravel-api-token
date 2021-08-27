<?php

\Illuminate\Support\Facades\Route::middleware(["api"])->prefix("api")->group(function() {
    \Illuminate\Support\Facades\Route::post("auth/request-token",[\FherryFherry\LaravelApiToken\Api\ApiAuthController::class,"postRequestToken"]);
    \Illuminate\Support\Facades\Route::post("auth/refresh-token",[\FherryFherry\LaravelApiToken\Api\ApiAuthController::class,"postRefreshToken"]);
});