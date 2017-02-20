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

    protected $configs;

    public function __construct(Request $request) {

        $this->getConfig();//dd($request);
    }

    //获取系统配置
    public function getConfig(){

        $config = DB::table('config')->get();
        $config = $this->object_array($config);

        foreach ($config as $v) {
           $this->configs[$v['key']] = json_decode($v['value']);
        }

    }

    public function object_array($array) {
        if(is_object($array)) {
            $array = (array)$array;
        } if(is_array($array)) {
            foreach($array as $key=>$value) {
                $array[$key] = $this->object_array($value);
            }
        }
        return $array;
    }
}