<?php
namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;


class PasswordInit
{
    /**
     * @var string|null
     */
    #[Assert\EqualTo(
        propertyPath: 'passwordConfirm',
        message: false
    )]
    #[Assert\Length(
        min: 6,
        minMessage: 'Le mot de passe doit comporter au moins 6 caractères',
        max: 50,
        maxMessage: 'Le mot de passe ne peut comporter plus de 50 caractères'
    )]
    private $password;

    /**
     * @var string|null
     */
    #[Assert\EqualTo(
        propertyPath: 'password',
        message: 'Les deux mots de passe ne correspondent pas'
    )]
    private $passwordConfirm;


    /**
     * @var int|null
     */
    private $userId;

    /**
     * Get the value of passwordConfirm
     *
     * @return  string|null
     */ 
    public function getPasswordConfirm()
    {
        return $this->passwordConfirm;
    }

    /**
     * Set the value of passwordConfirm
     *
     * @param  string|null  $passwordConfirm
     *
     * @return  self
     */ 
    public function setPasswordConfirm($passwordConfirm)
    {
        $this->passwordConfirm = $passwordConfirm;

        return $this;
    }

    /**
     * Get the value of password
     *
     * @return  string|null
     */ 
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @param  string|null  $password
     *
     * @return  self
     */ 
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of userId
     *
     * @return  int|null
     */ 
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set the value of userId
     *
     * @param  int|null  $userId
     *
     * @return  self
     */ 
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }
}