<?php

namespace App\Middlewares;

use App\Models\User;

class AuthMiddleware {

    static public function check($request) {
        $user = User::findByToken($request['header']['X-RECIPE-TOKEN']);
        return ($user) ? true : false;
    }
}
