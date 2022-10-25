<?php
namespace App\Exception\AuthenticationException;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AuthenticationNotVerifiedException extends AuthenticationException
{
    public const ERROR_CODE = 1;


    public function __construct(string $message, int $code)
    {
        parent::__construct($message, $code);
    }
    


}