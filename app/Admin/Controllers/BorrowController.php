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

        $table = DB::table('borrow_info as bo')
            ->leftjoin('books_info as bi','bi.id','=','bo.book_id')
            ->leftjoin('books_type as bt','bt.id','=','bi.type_id')
            ->leftjoin('user_info as ui','ui.id','=','bo.user_id')
            ->select(DB::raw('bo.id,ui.name as user_name,bi.book_number,bi.name as book_name,bo.status,bo.created_at,bo.updated_at,bo.end_time'));
        if(isset($params['type_id']) && $params['type_id']){
            $this->getLastSons($params['type_id'],$type_ids);
            $table->whereIn('bi.type_id',$type_ids);
        }
        if(isset($params['book_number']) && $params['book_number']) $table->where('bi.book_number',$params['book_number']);
        $data = $table->orderBy('bo.created_at','desc')
            ->paginate($size);

        return Admin::content(function (Content $content) use($data,$params){

            $content->header(trans('admin::lang.borrow'));
            $content->description(trans('admin::lang.list'));

            $headers = [
                'Id',
                trans('admin::lang.username'),
                trans('admin::lang.book_number'),
                trans('admin::lang.book_name'),
                trans('admin::lang.borrow_status'),
                trans('admin::lang.created_at'),
                trans('admin::lang.updated_at'),
                trans('admin::lang.end_time'),
                trans('admin::lang.operation'),
            ];

            $borrows = array();
            foreach($data as $v){
                if($v->status == 0) {
                    $v->operation = '<a class="btn btn-warning btn-xs" href="/admin/borrow/return?id=' . $v->id . '">归还</a>&nbsp&nbsp';
                    $v->operation .= '<a class="btn btn-danger btn-xs" href="/admin/borrow/compensate?id=' . $v->id . '">赔偿</a>';
                }else{
                    $v->operation = '无';
                }
                $v->status = $this->statusToString($v->status);
                $borrows[] = $v;
            }

            $params['path'] = '/admin/borrow';
            $options = [0 => 'Root'] + BooksType::buildSelectOptions([],0,'',2);
            $content->row((
                new Box(
                    'Table',
                    new Table($headers, $borrows, [],$data,$params),
                    ['select_id'=>'select_book_type','search_id'=>'search_book_number','options'=>$options,'params'=>$params]
                )
            )->addBookTypeSelect()->style('info')->solid());
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

    public function statusToString($status){
        if($status == 0) return '<div class="btn btn-xs btn-warning">在借</div>';
        else if($status == 1) return '<div class="btn btn-xs btn-danger">赔偿</div>';
        else if($status == 2) return '<div class="btn btn-xs btn-success">归还</div>';
        else return '<div class="btn btn-xs btn-danger">未知</div>';
    }
}