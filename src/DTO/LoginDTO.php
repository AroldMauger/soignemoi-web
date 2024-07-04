<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class LoginDTO
{
    public function __construct(
        #[NotBlank]
        public readonly string $lastname,
        #[NotBlank]
        public readonly string $identification
    )
    {

    }
}