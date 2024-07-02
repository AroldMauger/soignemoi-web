<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class LoginDTO
{
    public function __construct(
        #[Email]
        public readonly string $email,
        #[NotBlank]
        public readonly string $password
    )
    {

    }
}