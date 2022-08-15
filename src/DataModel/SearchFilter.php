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


    /** 
     * sort options
     */
    public const DATE_DESC = 'date_desc';

    public const DATE_ASC = 'date_asc';

    public const PRICE_ASC = 'price_asc';

    public const PRICE_DESC = 'price_desc';

    /**
     * fields
     */
    public const DATE = 'createdAt';

    public const PRICE = 'price';

    /**
     * orders
     */
    public const ASC = 'ASC';

    public const DESC = 'DESC';

    /** 
     * SORT
     * @var int|null
     */
    public $sort;

    /** 
     * @return string|null
     */
    public function getSortField()
    {
        if(in_array($this->sort, [self::DATE_ASC, self::DATE_DESC]))
        {
            return self::DATE;
        }
        elseif(in_array($this->sort, [self::PRICE_ASC, self::PRICE_DESC]))
        {
            return self::PRICE;
        }
        return null;
    }
    /** 
     * @return string|null
     */
    public function getSortOrder()
    {
        if(in_array($this->sort, [self::DATE_ASC, self::PRICE_ASC]))
        {
            return self::ASC;
        }
        elseif(in_array($this->sort, [self::DATE_DESC, self::PRICE_DESC]))
        {
            return self::DESC;
        }
        return null;
    }
}
