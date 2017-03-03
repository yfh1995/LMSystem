<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 17/2/20
 * Time: 17:16
 */

namespace App\Admin\Model;

use DB;
use Illuminate\Database\Eloquent\Model;

class Borrow extends Model{

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        $this->table = 'borrow_info';

        parent::__construct($attributes);
    }

    public function profile()
    {
        $this->hasOne(User::class);
    }
}