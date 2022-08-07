<?php
namespace App\Service;

class TokenGenerator
{
    public function numeric(?int $characters = 6):string
    {
        return substr(str_shuffle(str_repeat('0123456789', 50)), 0, $characters);
    }
}