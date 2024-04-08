<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Schema;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    public function checkEmail($email) {
        // $find1 = strpos($email, '@');
        // $find2 = strpos($email, '.');
        // return ($find1 !== false && $find2 !== false && $find2 > $find1);
        return filter_var($email, FILTER_VALIDATE_EMAIL);
     }

     public function getTableColumns($table)
     {
         return Schema::getColumnListing($table);
     }
}
