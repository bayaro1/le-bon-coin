<?php

namespace App\DataModel;

use App\Entity\Category;


class SearchFilter
{
    /**
     * @var Category|null
     */
    public $category;

    /** 
     * @var string|null
     */
    public $qSearch;

    /** 
     * @var string|null
     */
    public $city;


}
