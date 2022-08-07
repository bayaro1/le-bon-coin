<?php
namespace App\Exception\AuthenticationException;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class Authentication2FAException extends AuthenticationException
{
    public const ERROR_CODE = 2;


    public function __construct(string $message, int $code)
    {
        parent::__construct($message, $code);
    }
    


}