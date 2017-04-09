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
use Illuminate\Support\Facades\Log;

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
        $book_info = DB::table('books_info')->where('book_number',$params['book_number'])->first();
        if(!$user_info) return redirect()->back()->withErrors('未查询到此用户信息！');
        if(!$book_info) return redirect()->back()->withErrors('未查询到此书籍信息！');
        if($user_info->available_num<=0) return redirect()->back()->withErrors('您的借阅量已达到上限，请先归还已借书籍！');
        if($book_info->cur_total<=0) return redirect()->back()->withErrors('此图书已无库存，请等待！');

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

    public function compensate(Request $request){
        $params = $request->all();

        if(!isset($params['id']) || !$params['id']) return redirect()->back()->withErrors('参数丢失或无效参数！');

        $borrow_info = DB::table('borrow_info')->where('id',$params['id'])->first();
        if(!$borrow_info) return redirect()->back()->withErrors('未查询到有此借阅信息！');

        DB::beginTransaction();

        //更新借阅信息
        $rs_bi = DB::table('borrow_info')->where('id',$params['id'])->update([
            'status'    =>  1
        ]);

        //更新借阅者信息
        $rs_ui = DB::table('user_info')->where('id',$borrow_info->user_id)->increment('available_num');

        if($rs_bi === false || $rs_ui === false){
            DB::rollback();
            return redirect()->back()->withErrors('赔偿失败！');
        }
        else{
            DB::commit();
            return redirect('/admin/borrow');
        }
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
                return $this->statusToHtml($roles);
            });
            $grid->created_at(trans('admin::lang.created_at'));
            $grid->end_time(trans('admin::lang.end_time'));

            $grid->rows(function($row){
                if($row->id<100000000) {
                    $row->actions('delete');
                }

                $row->actions()->add(function ($row) {
                    $str = '';
                    if($this->htmlToStatus($row->status) == 0) {
                        $str = "<a class='btn btn-success btn-xs' href='/admin/borrow/return?id={$row->id}' >归还</a>&nbsp";
                        $str .= "<a class='btn btn-danger btn-xs' href='/admin/borrow/compensate?id={$row->id}'>赔偿</a>";
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

    public function destroy($id){
        //判断借阅信息状态，如果为在借状态则不可删除
        $borrow_info = DB::table('borrow_info')->where('id',$id)->where('status','<>',0)->first();
        if(!$borrow_info){
            return redirect()->back()->withErrors('未找到借阅信息或借阅记录为在借状态！');
        }

        if ($this->form()->destroy($id)) {
            return redirect('admin/books/');
        }
    }

    public function returnBook(Request $request){
        $params = $request->all();

        if(!isset($params['id']) || !$params['id']) return;

        $borrow_info = DB::table('borrow_info')->where(['id'=>$params['id'],'status'=>0])->first();
        if(!$borrow_info) return redirect('/admin/borrow');

        DB::beginTransaction();

        //更新借阅信息状态
        $rs_bi = DB::table('borrow_info')->where('id',$params['id'])->update([
            'status'    =>  2
        ]);

        //更新借阅者信息
        $rs_ui = DB::table('user_info')->where('id',$borrow_info->user_id)->increment('available_num');

        //更新借阅书籍信息
        $rs_bk = DB::table('books_info')->where('id',$borrow_info->book_id)->increment('cur_total');

        if($rs_bi === false || $rs_ui === false || $rs_bk === false){
            DB::rollback();
            return;
        } else{
            DB::commit();
            return redirect('/admin/borrow');
        }
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

    public function statusToHtml($status){
        if($status == 0) return '<span class="label label-warning">在借</span>';
        else if($status == 1) return '<div class="label label-danger">赔偿</div>';
        else if($status == 2) return '<div class="label label-success">归还</div>';
        else return '<div class="label label-danger">未知</div>';
    }

    public function htmlToStatus($html){
        if($html == '<span class="label label-warning">在借</span>') return 0;
        else if($html == '<div class="label label-danger">赔偿</div>') return 1;
        else if($html == '<div class="label label-success">归还</div>') return 2;
        else return 3;
    }
}