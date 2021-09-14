<?php


namespace App\Services;


use Cocur\Slugify\Slugify;

class Slugger
{
    public static function slug($str): string
    {
        $slugify = new Slugify();
        return $slugify->slugify($str);
    }

}