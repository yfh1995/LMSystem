<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 17/2/16
 * Time: 12:14
 */

namespace App\Admin\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Admin\Model\BooksType;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use Encore\Admin\Facades\Admin as AdminManager;


class BooksTypeController extends Controller{

    private $elementId,$path,$script;

    public function index(){

        return Admin::content(function (Content $content) {
            $content->header(trans('admin::lang.books_type'));
            $content->description(trans('admin::lang.list'));

            //设置图书分类的树形视图
            $content->body($this->tree());
        });
    }

    public function create(){
        return Admin::content(function (Content $content) {
            $content->header(trans('admin::lang.books_type'));
            $content->description(trans('admin::lang.create'));

            $content->row($this->form());
        });
    }

    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header(trans('admin::lang.books_type'));
            $content->description(trans('admin::lang.edit'));

            $content->row($this->form()->edit($id));
        });
    }

    public function form(){
        return Admin::form(BooksType::class, function (Form $form) {
            $form->display('id', 'ID');

            $options = [0 => 'Root'] + BooksType::buildSelectOptions();

            $form->select('parent_id', trans('admin::lang.parent_id'))->options($options);
            $form->text('type_name', trans('admin::lang.type_name'))->rules('required');

            $form->display('created_at', trans('admin::lang.created_at'));
            $form->display('updated_at', trans('admin::lang.updated_at'));
        });
    }

    /**
     * 创建图书分类
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request){
        $params = $request->all();

        $sort = DB::table('books_type')->max('sort');
        $rs = DB::table('books_type')->insert([
            'parent_id' =>  $params['parent_id'],
            'type_name' =>  $params['type_name'],
            'sort'      =>  $sort+1,
            'created_at'=>  date('Y-m-d H:i:s',time())
        ]);
//        if($rs === false) return redirect()->back()->withErrors('创建失败！');

        return redirect('admin/booksType/');
    }

    public function tree(){
        //如果本次请求为图书分类存储
        if (Request::capture()->has('_tree')) {
            $_tree = Request::capture()->get('_tree');
            $_tree = json_decode($_tree,true);
            $_tree = $this->object_array($_tree);

            //获取图书分类的绝对位置
            $this->saveTree($_tree,$data);

            //拼接批量更新mysql语句
            $sql = 'update books_type SET sort = CASE id';
            $ids = '';
            $cnt = count($data);
            for($i=0;$i<$cnt;$i++){
                $sql .= ' when '.$data[$i].' then '.($i+1);
                $ids .= $data[$i].($i+1==$cnt?'':',');
            }
            $sql .= ' end where id in ('.$ids.')';
            DB::update($sql);
            return;
        }

        //获取图书分类数据，并树形化
        $data = DB::table('books_type')->select('id','type_name as title','parent_id')->orderBy('sort')->get();
        $data = $this->object_array($data);
        $items = array();
        $this->dfsTree($data,0,$items);

        //设置div的id，设置回调地址前缀
        $this->elementId = 'tree-save';
        $this->path = 'admin/booksType';

        //设置必要的script代码
        $this->buildupScript();
        AdminManager::script($this->script);

        view()->share(['path'  => $this->path]);

        $params['items'] = $items;
        $params['id'] = $this->elementId;
        return view('admin::tree', $params)->render();
    }

    public function destroy($id){
        if ($this->form()->destroy($id)) {
            return response()->json(['msg' => 'delete success!']);
        }
    }

    public function update($id){
        return $this->form()->update($id);
    }

    public function show(){
        return redirect('admin/booksType/');
    }

    /**
     * 创建script代码
     */
    public function buildupScript()
    {
        $confirm = trans('admin::lang.delete_confirm');
        $token = csrf_token();

        $this->script = <<<SCRIPT

        $('#{$this->elementId}').nestable({});

        $('._delete').click(function() {
            var id = $(this).data('id');
            if(confirm("{$confirm}")) {
                $.post('/{$this->path}/' + id, {_method:'delete','_token':'{$token}'}, function(data){
                    $.pjax.reload('#pjax-container');
                });
            }
        });

        $('.{$this->elementId}-save').click(function () {
            var serialize = $('#{$this->elementId}').nestable('serialize');
            $.get('/{$this->path}', {'_tree':JSON.stringify(serialize)}, function(data){
                $.pjax.reload('#pjax-container');
            });
        });

        $('.{$this->elementId}-refresh').click(function () {
            window.location.reload();
        });


SCRIPT;
    }

    /**
     * 将书籍分类按照父子关系，组合成树形数组
     * @param $data         //书籍数据
     * @param $parent_id    //当前所要搜索的数据的父亲id
     * @param $items        //存储地址
     */
    public function dfsTree(&$data,$parent_id,&$items){

        foreach($data as &$v){
            if($v['parent_id'] == $parent_id){
                $this->dfsTree($data,$v['id'],$v['children']);
                if(!count($v['children'])) unset($v['children']);
                $items[] = $v;
                unset($v);
            }
        }
    }

    /**
     * 将树形数组，按照展开时的次序从上到下编号
     * @param $_tree    //树形数组
     * @param $data     //存储地址
     */
    public function saveTree(&$_tree,&$data){
        foreach($_tree as $v){
            $data[] = $v['id'];
            if(isset($v['children'])) $this->saveTree($v['children'],$data);
        }
    }

}