<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 17/3/2
 * Time: 17:45
 */

namespace App\Admin\Model;


use Illuminate\Database\Eloquent\Model;

class User extends Model{

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        $this->table = 'user_info';

        parent::__construct($attributes);
    }
}