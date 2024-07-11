<?php

namespace App\Context;

use App\Entity\Secretary;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class SecretaryApiContext
{
    private ?Secretary $secretary = null;
    public function setSecretary(Secretary $secretary):void
    {
        $this->secretary = $secretary;
    }

    public function getSecretary(): Secretary
    {
        if($this->secretary === null) {
            throw new UnauthorizedHttpException("No secretary found in the context");
        }
        return $this->secretary;
    }
}