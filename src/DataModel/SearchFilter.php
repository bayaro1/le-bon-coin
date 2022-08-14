<?php

namespace App\DataModel;

use App\Entity\User;
use App\Entity\Category;


class SearchFilter
{
    /**
     * @var Category[]|null
     */
    public $categories;

    /** 
     * @var string|null
     */
    public $qSearch;

    /** 
     * @var string|null
     */
    public $city;

    /** 
     * @var User|null
     */
    public $user;

}
