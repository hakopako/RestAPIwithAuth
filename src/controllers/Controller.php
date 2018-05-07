<?php

namespace App\Controllers;

abstract class Controller {

    protected function response(array $data, int $status=200, string $message="")
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        if($status == 200){
            return @json_encode(['status' => $status, 'data' => $data]);
        } else {
            return @json_encode(['status' => $status, 'data' => $data, 'message' => $message]);
        }
    }
}
