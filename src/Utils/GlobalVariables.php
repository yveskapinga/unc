<?php

namespace App\Utils;

use GuzzleHttp\RetryMiddleware;

class GlobalVariables
{

    public const SOME_CONSTANT = 'some_value';
    public const ANOTHER_CONSTANT = 'another_value';

    public static $someVariable = 'some_value';
    public static $anotherVariable = 'another_value';

    private static $fonctionsForMembership = [
        'Secrétaire Interfédéral'=>'sif',
        'Secrétaire interfédéral Adjoint'=>'SIFA',
        'Président interfédéral des jeunes' =>'pdt jeunes',
        'Présidente interfédérale des femmes'=>'pdt femmes', 
        'Secrétaire exécutif ubain'=>'secexurbain', 
        'Président Fédéral'=>'pdt federal', 
    ];

    public static function getFonctionsForMembership(){
        return self::$fonctionsForMembership;
    }

}

