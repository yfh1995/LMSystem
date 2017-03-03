<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 17/3/3
 * Time: 14:14
 */

namespace App\Admin\Model;


use Illuminate\Database\Eloquent\Model;

class Config extends Model{


    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        $this->table = 'config';

        parent::__construct($attributes);
    }
}