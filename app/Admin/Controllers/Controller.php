<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 17/2/15
 * Time: 18:09
 */

namespace App\Admin\Controllers;

use DB;
use Illuminate\Http\Request;

class Controller extends \App\Http\Controllers\Controller{



    public function __construct(Request $request) {

        $config = $this->getConfig();
    }

    public function getConfig(){

    }
}