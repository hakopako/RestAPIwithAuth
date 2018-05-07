<?php

namespace App\Controllers;

use App\Models\User;

class AuthController extends Controller {

    public function login($request)
    {
        $user = User::login($request['header']['username'], $request['header']['password']);
        if(!$user){
            return $this->response([], 400, "Authentication Error.");
        }
        $user->token = bin2hex(openssl_random_pseudo_bytes(16));
        $user->expire_at = (new \Datetime)->modify('+1 day')->format('Y-m-d H:i:s');
        $user->updated_at = (new \Datetime)->format('Y-m-d H:i:s');
        if(!$user->save()){
            return $this->response([], 400, "Failed to generate a token.");
        }
        return $this->response(['token' => $user->token, 'expire' => $user->expire_at]);
    }

}
