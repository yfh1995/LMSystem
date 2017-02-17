<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 17/2/16
 * Time: 10:45
 */

namespace App\Admin\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Admin\Model\BooksType;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Form;

class BooksController extends Controller{

    public function index(Request $request){
        $params = $request->all();

//        $size = isset($params['size'])?$params['size']:$this->configs['page_num'];
        $size = 1;
        $table = DB::table('books_info as bi')
            ->leftjoin('books_type as bt','bt.id','=','bi.type_id')
            ->select(DB::raw('bi.id,bi.book_number,bt.type_name,bi.name,bi.press,bi.publication_year,bi.author,bi.price,bi.cur_total,bi.total,bi.created_at'));
        if(isset($params['type_id'])) $table->where('bi.type_id',$params['type_id']);
        if(isset($params['book_number'])) $table->where('bi.book_number',$params['book_number']);
        $data = $table->orderBy('bi.name')
            ->paginate($size);


        return Admin::content(function (Content $content) use($data){

            $content->header(trans('admin::lang.books'));
            $content->description(trans('admin::lang.list'));

            $headers = [
                'Id',
                trans('admin::lang.book_number'),
                trans('admin::lang.classification'),
                trans('admin::lang.book_name'),
                trans('admin::lang.press'),
                trans('admin::lang.publication_year'),
                trans('admin::lang.author'),
                trans('admin::lang.price'),
                trans('admin::lang.cur_total'),
                trans('admin::lang.total'),
                trans('admin::lang.created_at')
            ];

            $books = array();
            foreach($data as $v){
                $books[] = $v;
            }
            $options = BooksType::buildSelectOptions([],0,'',2);

            $content->row((
                new Box(
                    'Table',
                    new Table($headers, $books, [],$data),
                    ['select_id'=>'select_book_type','search_id'=>'search_book_number','options'=>$options]
                )
            )->addSelete()->style('info')->solid());
        });
    }


    /**
     * 获取图书分类视图
     * @param Request $request
     * @return array
     */
    public function getBookType(Request $request){
        $params = $request->all();
//        $params['parent_id'] = 0;
//        $params['name'] = 'type_1';
        $params['id'] = isset($params['id'])?$params['id']:'';

        if(!isset($params['parent_id']) || $params['parent_id']<0) return [];
        if(!isset($params['name']) || !$params['name']) return [];

        $_list = DB::table('books_type')->where('parent_id',$params['parent_id'])->get();
        $_other = $params;
        return view('Widget.books_type')->with('_list',$_list)->with('_other',$_other);
    }

}