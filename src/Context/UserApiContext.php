<?php

namespace App\Context;

use App\Entity\Users;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserApiContext
{
    private ? Users $user = null;

    public function setUser(Users $user){
        $this ->user = $user;
    }
    public function getUser(){
        if($this -> user == null){
            throw new UnauthorizedHttpException("Missing token");
        }
        return $this -> user;
    }
}