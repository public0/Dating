<?php 

if (!function_exists('response')) {
    function response()
    {
        return \App\Http\Response::getInstance();
    }
}
