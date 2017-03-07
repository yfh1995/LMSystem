<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 17/2/20
 * Time: 15:48
 */

namespace App\Admin\Controllers;

use App\Admin\Model\Borrow;
use DB;
use App\Admin\Model\BooksType;
use Encore\Admin\Grid;
use Illuminate\Http\Request;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Form;

class BorrowController extends Controller{

    public function index(Request $request){
        $params = $request->all();

        $size = isset($params['size'])?$params['size']:$this->configs['page_num'];

        return Admin::content(function (Content $content) use($size){

            $content->header(trans('admin::lang.borrow'));
            $content->description(trans('admin::lang.list'));
            $content->body($this->grid($size));
        });
    }

    public function create(){
        return Admin::content(function (Content $content) {
            $content->header(trans('admin::lang.borrow'));
            $content->description(trans('admin::lang.create'));

            $content->row($this->form());
        });
    }

    public function store(Request $request){
        $params = $request->all();

        if(!isset($params['id_number']) || !isset($params['book_number']) || !$params['id_number'] || !$params['book_number']) return;

        $borrow_term = $this->configs['borrow_term'];
        $user_info = DB::table('user_info')->where('id_number',$params['id_number'])->first();
        $book_info = DB::table('books_info')->where('book_number',$params['book_number'])->where('cur_total','>','0')->first();
        if(!$user_info || !$book_info) return;

        //开启事务
        DB::beginTransaction();

        //插入借书记录
        $rs_bor = DB::table('borrow_info')->insert([
            'user_id'   =>  $user_info->id,
            'book_id'   =>  $book_info->id,
            'status'    =>  0,
            'end_time'  =>  date('Y-m-d H:i:s',time() + $borrow_term),
            'created_at'=>  date('Y-m-d H:i:s',time())
        ]);

        //修改用户数据
        $rs_use = DB::table('user_info')->where('id',$user_info->id)->decrement('available_num');

        //修改书籍数据
        $rs_boo = DB::table('books_info')->where('id',$book_info->id)->decrement('cur_total');

        if(!$rs_bor || !$rs_use || !$rs_boo){
            DB::rollback();
            return;
        }else{
            DB::commit();
            return redirect('/admin/borrow');
        }

    }

    public function returnBook(Request $request){
        $params = $request->all();

        if(!isset($params['id']) || !$params['id']) return;

        $borrow_info = DB::table('borrow_info')->where('id',$params['id'])->first();
        if(!$borrow_info) return;

        $rs = DB::table('borrow_info')->where('id',$params['id'])->update([
            'status'    =>  2
        ]);
        if($rs === false) return;
        else return redirect('/admin/borrow');
    }

    public function compensate(Request $request){
        $params = $request->all();

        if(!isset($params['id']) || !$params['id']) return;

        $borrow_info = DB::table('borrow_info')->where('id',$params['id'])->first();
        if(!$borrow_info) return;

        $rs = DB::table('borrow_info')->where('id',$params['id'])->update([
            'status'    =>  1
        ]);
        if(!$rs) return;
        else return redirect('/admin/borrow');
    }

    public function form(){
        return Admin::form(Borrow::class, function (Form $form) {
            $form->display('id', 'ID');

            $form->text('id_number', trans('admin::lang.id_number'));
            $form->text('book_number', trans('admin::lang.book_number'));

            $form->display('created_at', trans('admin::lang.created_at'));
            $form->display('updated_at', trans('admin::lang.updated_at'));
        });
    }

    public function grid($size){
        return Admin::grid(Borrow::class,function(Grid $grid)use($size){
            $grid->id('ID')->sortable();

            $grid->user()->name(trans('admin::lang.username'))->sortable();
            $grid->books()->book_number(trans('admin::lang.book_number'))->sortable();
            $grid->books()->name(trans('admin::lang.book_name'));
            $grid->status(trans('admin::lang.borrow_status'))->value(function($roles){
                if($roles == 0) return '<span class="label label-warning">在借</span>';
                else if($roles == 1) return '<div class="label label-danger">赔偿</div>';
                else if($roles == 2) return '<div class="label label-success">归还</div>';
                else return '<div class="label label-danger">未知</div>';
            });
            $grid->created_at(trans('admin::lang.created_at'));
            $grid->end_time(trans('admin::lang.end_time'));

            $grid->rows(function($row){
                $row->actions()->add(function ($row) {
                    if($row->status == 0) {
                        $str = "<a class='btn btn-success btn-xs' href='/admin/borrow/return?id={$row->id}' >归还</a>&nbsp";
                        $str .= "<a class='btn btn-danger btn-xs' href='/admin/borrow/compensate?id={$row->id}'>赔偿</a>";
                    }else{
                        $str = '无';
                    }
                    return $str;
                });
            });

            $grid->filter(function($filter){

                $filter->like('user.id_number',trans('admin::lang.id_number'));
                $filter->like('books.book_number',trans('admin::lang.book_number'));
            });

            $grid->paginate($size);

            $grid->disableBatchDeletion();
        });
    }

    public function getLastSons($type_id,&$type_ids){
        $sons = DB::table('books_type')->where('parent_id',$type_id)->get();

        if(!$sons){
            $type_ids[] = $type_id;
            return;
        }

        foreach($sons as $son){
            $this->getLastSons($son->id,$type_ids);
        }
    }
}