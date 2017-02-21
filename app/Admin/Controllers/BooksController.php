<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 17/2/16
 * Time: 10:45
 */

namespace App\Admin\Controllers;

use App\Admin\Model\BooksType;
use DB;
use Illuminate\Http\Request;
use App\Admin\Model\Books;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Form;
use Encore\Admin\Facades\Admin as AdminManager;

class BooksController extends Controller{

    protected $script,$path;

    public function index(Request $request){
        $params = $request->all();

        $size = isset($params['size'])?$params['size']:$this->configs['page_num'];

        $table = DB::table('books_info as bi')
            ->leftjoin('books_type as bt','bt.id','=','bi.type_id')
            ->select(DB::raw('bi.id,bi.book_number,bt.type_name,bi.name,bi.press,bi.publication_year,bi.author,bi.price,bi.cur_total,bi.total,bi.created_at'));
        if(isset($params['type_id']) && $params['type_id']){
            $this->getLastSons($params['type_id'],$type_ids);
            $table->whereIn('bi.type_id',$type_ids);
        }
        if(isset($params['book_number']) && $params['book_number']) $table->where('bi.book_number',$params['book_number']);
        $data = $table->orderBy('bi.name')
            ->paginate($size);


        return Admin::content(function (Content $content) use($data,$params){

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
                trans('admin::lang.created_at'),
                trans('admin::lang.operation')
            ];

            $books = array();
            foreach($data as $v){
                $v->operation = '<a class="btn btn-warning btn-xs" href="/admin/books/'.$v->id.'/edit">编辑</a>&nbsp&nbsp';
                $v->operation .= '<button class="btn btn-danger btn-xs _delete" data-id="'.$v->id.'">删除</button>';
                $books[] = $v;
            }

            $this->path = $params['path'] = '/admin/books';

            //设置必要的script代码
            $this->buildupScript();
            AdminManager::script($this->script);

            $options = [0 => 'Root'] + BooksType::buildSelectOptions([],0,'',2);
            $content->row((
                new Box(
                    'Table',
                    new Table($headers, $books, [],$data,$params),
                    ['select_id'=>'select_book_type','search_id'=>'search_book_number','options'=>$options,'params'=>$params]
                )
            )->addBookTypeSelect()->style('info')->solid());
        });
    }

    public function create(){
        return Admin::content(function (Content $content) {
            $content->header(trans('admin::lang.books_type'));
            $content->description(trans('admin::lang.create'));

            $content->row($this->form());
        });
    }

    public function store(){
        return $this->form()->store();
    }

    public function edit($id){
        return Admin::content(function (Content $content) use ($id) {
            $content->header(trans('admin::lang.books'));
            $content->description(trans('admin::lang.edit'));

            $content->row($this->form()->edit($id));
        });
    }

    public function update($id){
        return $this->form()->update($id);
    }

    public function destroy($id){
        if ($this->form()->destroy($id)) {
            return redirect('admin/books/');
        }
    }

    public function show(){
        return redirect('admin/books/');
    }

    /**
     * 创建script代码
     */
    public function buildupScript()
    {
        $confirm = trans('admin::lang.delete_confirm');
        $token = csrf_token();

        $this->script = <<<SCRIPT

        $('._delete').click(function() {
            var id = $(this).data('id');
            if(confirm("{$confirm}")) {
                $.post('{$this->path}/' + id, {_method:'delete','_token':'{$token}'}, function(data){
                    $.pjax.reload('#pjax-container');
                });
            }
        });

SCRIPT;
    }

    public function form(){
        return Admin::form(Books::class, function (Form $form) {
            $form->display('id', 'ID');

            $options = [0 => 'Root'] + BooksType::buildSelectOptions();

            $form->text('book_number', trans('admin::lang.book_number'));
            $form->select('type_id', trans('admin::lang.classification'))->options($options);
            $form->text('name', trans('admin::lang.book_name'));
            $form->text('press', trans('admin::lang.press'));
            $form->text('name', trans('admin::lang.book_name'));
            $form->text('publication_year', trans('admin::lang.publication_year'));
            $form->text('author', trans('admin::lang.author'));
            $form->text('price', trans('admin::lang.price'));
            $form->text('cur_total', trans('admin::lang.cur_total'));
            $form->text('total', trans('admin::lang.total'));

            $form->display('created_at', trans('admin::lang.created_at'));
            $form->display('updated_at', trans('admin::lang.updated_at'));
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