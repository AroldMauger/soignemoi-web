<?php

namespace App\Context;

use App\Entity\Doctors;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserApiContext
{
    private ? Doctors $doctor = null;

    public function setDoctors(Doctors $doctor){
        $this ->doctor = $doctor;
    }
    public function getDoctors(){
        if($this -> doctor == null){
            throw new UnauthorizedHttpException("Missing token");
        }
        return $this -> doctor;
    }
}