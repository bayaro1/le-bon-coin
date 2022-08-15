<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\ParameterBag;

class HttpQueryService 
{
    public static function modify(ParameterBag $query, array $add, array $delete = []):array
    {
        $query = iterator_to_array($query);

        if(!empty($delete))
        {
            foreach($delete as $toDelete)
            {
                unset($query[$toDelete]);
            }
        }
        if(!empty($add))
        {
            foreach($add as $key => $value)
            {
                $query[$key] = $value;
            }
        }
        return $query;
    }
}