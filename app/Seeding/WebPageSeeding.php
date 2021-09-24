<?php

namespace App\Seeding;

class WebPageSeeding
{
    public static function getRandomTextOf($wordsNum = 200, $returnArray = true){
        $words = [];
        for($i = 0; $i < $wordsNum; $i++){
            $index = rand(0,count(Words::WORDS)-1);
            $words[] = Words::WORDS[$index];
        }

        return $returnArray ? $words : implode(' ', $words);
    }

}