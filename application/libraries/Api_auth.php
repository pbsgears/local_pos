<?php
class Api_auth
{
    public function login($username, $password) {
        if($username == 'hello' && $password == 'world')
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}