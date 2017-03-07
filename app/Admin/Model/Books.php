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

        parent::__construct($attributes);
    }

    public function borrow(){
        return $this->belongsTo(Borrow::class,'id');
    }

    public function booksType()
    {
        return $this->hasOne(BooksType::class,'id','type_id');
    }

}