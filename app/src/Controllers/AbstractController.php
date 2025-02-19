<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

abstract class AbstractController{
    abstract public function process(Request $request): Response;

    protected function checkHeader(string $headerKey, Request $request, ?string $headerValue = null): Response | bool {
        if(array_key_exists($headerKey, $request->getHeaders()) === false) {
            return new Response("$headerKey must be defined in request headers", 400, ['Content-Type' => 'application/json']);
        }

        if($headerValue !== null){
            if($request->getHeaders()['Content-Type'] !== $headerValue) {
                return new Response("$headerKey be `$headerValue`", 400, ['Content-Type' => 'application/json']);
            }
        }
        
        return true;
    }
}