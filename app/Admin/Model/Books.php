<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 17/2/20
 * Time: 13:30
 */

namespace App\Admin\Model;

use DB;
use Illuminate\Database\Eloquent\Model;

class Books extends Model{

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        $this->table = 'books_info';

        $this->hasOne(Borrow::class);

        parent::__construct($attributes);
    }

}