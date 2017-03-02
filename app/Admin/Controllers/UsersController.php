<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 17/2/20
 * Time: 18:17
 */

namespace App\Admin\Controllers;

use App\Admin\Model\User;
use DB;
use Illuminate\Http\Request;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Form;
use Encore\Admin\Facades\Admin as AdminManager;

class UsersController extends Controller{

    protected $script,$path;

    public function index(Request $request){
        $params = $request->all();

        $size = isset($params['size'])?$params['size']:$this->configs['page_num'];

        $table = DB::table('user_info')
                ->select(DB::raw('id,name,identity,major,grade,class,id_number,sex,available_num,sum_num,phone,created_at'));
        if(isset($params['id_number'])) $table->where('id_number',$params['id_number']);
        $data = $table->paginate($size);

        return Admin::content(function (Content $content) use($data,$params){

            $content->header(trans('admin::lang.borrow'));
            $content->description(trans('admin::lang.list'));

            $headers = [
                'Id',
                trans('admin::lang.username'),
                trans('admin::lang.identity'),
                trans('admin::lang.major'),
                trans('admin::lang.grade'),
                trans('admin::lang.class'),
                trans('admin::lang.id_number'),
                trans('admin::lang.sex'),
                trans('admin::lang.available_num'),
                trans('admin::lang.sum_num'),
                trans('admin::lang.phone'),
                trans('admin::lang.created_at'),
                trans('admin::lang.operation'),
            ];

            $users = array();
            foreach($data as $v){
                $v->operation = '<a class="btn btn-warning btn-xs" href="/admin/users/'.$v->id.'/edit">编辑</a>&nbsp&nbsp';
                $v->operation .= '<button class="btn btn-danger btn-xs _delete" data-id="'.$v->id.'">删除</button>';
                $users[] = $v;
            }

            //设置必要的script代码
            $this->path = '/admin/users';
            $this->buildupScript();
            AdminManager::script($this->script);

            $params['path'] = '/admin/users';
            $content->row((
            new Box(
                'Table',
                new Table($headers, $users, [],$data,$params),
                ['search_id'=>'search_book_number','params'=>$params]
            )
            )->addUserSelect()->style('info')->solid());
        });
    }

    public function create(){
        return Admin::content(function (Content $content) {
            $content->header(trans('admin::lang.user'));
            $content->description(trans('admin::lang.create'));

            $content->row($this->form());
        });
    }

    public function store(){
        return $this->form()->store();
    }

    public function edit($id){
        return Admin::content(function (Content $content) use($id){
            $content->header(trans('admin::lang.user'));
            $content->description(trans('admin::lang.edit'));

            $content->row($this->form()->edit($id));
        });
    }

    public function update($id){
        return $this->form()->update($id);
    }

    public function destroy($id){
        if ($this->form()->destroy($id)) {
            return redirect('admin/users/');
        }
    }

    public function show(){
        return redirect('admin/users/');
    }

    public function form(){
        return Admin::form(User::class, function (Form $form) {
            $form->display('id', 'ID');

            $id_options = [0=>'学生',1=>'老师'];
            $sex_options = [0=>'女',1=>'男'];

            $form->text('name', trans('admin::lang.username'));
            $form->select('identity', trans('admin::lang.identity'))->options($id_options);
            $form->text('major', trans('admin::lang.major'));
            $form->text('grade', trans('admin::lang.grade'));
            $form->text('class', trans('admin::lang.class'));
            $form->text('id_number', trans('admin::lang.id_number'));
            $form->select('sex', trans('admin::lang.sex'))->options($sex_options);
            $form->text('sum_num', trans('admin::lang.sum_num'))->default($this->configs['borrow_num']);
            $form->text('phone', trans('admin::lang.phone'));

            $form->display('created_at', trans('admin::lang.created_at'));
            $form->display('updated_at', trans('admin::lang.updated_at'));
        });
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
}