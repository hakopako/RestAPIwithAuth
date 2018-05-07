<?php

namespace App\Models;

class User extends Model {

    static protected $table = 'users';

    static public function login($username, $password)
    {
        $user = User::where("username = '$username'");
        if($user == []){ return null; }
        return (password_verify($password, $user[0]->password)) ? $user[0] : null;
    }

    static public function findByToken($token)
    {
        $user = User::where("token = '$token' AND expire_at > now()");
        return ($user != []) ? $user[0] : null;
    }

}
