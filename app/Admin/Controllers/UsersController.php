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
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin as AdminManager;

class UsersController extends Controller{

    protected $script,$path;

    public function index(Request $request){
        $params = $request->all();

        $size = isset($params['size'])?$params['size']:$this->configs['page_num'];

        return Admin::content(function (Content $content) use($size){

            $content->header(trans('admin::lang.user'));
            $content->description(trans('admin::lang.list'));
            $content->body($this->grid($size));
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

    public function grid($size){
        return Admin::grid(User::class, function (Grid $grid) use($size){
            $grid->id('ID')->sortable();

            $grid->name(trans('admin::lang.username'))->sortable();
            $grid->identity(trans('admin::lang.identity'))->value(function ($roles) {
                if($roles) return '教师';
                else return '学生';
            });
            $grid->major(trans('admin::lang.major'));
            $grid->grade(trans('admin::lang.grade'));
            $grid->class(trans('admin::lang.class'));
            $grid->id_number(trans('admin::lang.id_number'))->sortable();
            $grid->sex(trans('admin::lang.sex'))->value(function ($roles) {
                if($roles) return '男';
                else return '女';
            });
            $grid->available_num(trans('admin::lang.available_num'));
            $grid->sum_num(trans('admin::lang.sum_num'));
            $grid->phone(trans('admin::lang.phone'));
            $grid->created_at(trans('admin::lang.created_at'));

            $grid->rows(function($row){
                $row->actions()->add(function ($row) {
                    return "<a href='/admin/borrow?key_word={$row->id_number}'><i class='fa fa-eye'></i></a>";
                });
            });

            $grid->filter(function($filter){

                $filter->like('id_number',trans('admin::lang.id_number'));
            });

            $grid->paginate($size);

            $grid->disableBatchDeletion();
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