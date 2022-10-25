<?php
namespace App\JavascriptAdaptation\TemplatingClassAdaptor;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ConstraintViolationListAdaptor 
{
    /** @var array */
    private $errors = [];

    /**
     * @param ConstraintViolationList $list
     * @return array [field => message, ...]
     */
    public function adapte($list): array
    {
        foreach($list as $constraintViolation)
        {
            $this->errors[$constraintViolation->getPropertyPath()] = $constraintViolation->getMessage();
        }
        return $this->errors;
    }
}