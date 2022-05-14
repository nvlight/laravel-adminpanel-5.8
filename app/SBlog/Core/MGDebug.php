<?php

namespace App\SBlog\Core;

class MGDebug
{

    public static function dump($value, $type=1){
        ob_start();
        switch ($type){
            case 1:
                    echo "<pre>";
                    print_r($value);
                    echo "</pre>";
                break;
            case 2:
                    echo "<pre>";
                    var_dump($value);
                    echo "</pre>";
                break;
            default:
        }

        $result = ob_get_contents();
        ob_end_clean() ;
        return $result;
    }
}
