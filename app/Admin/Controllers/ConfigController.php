<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 17/3/3
 * Time: 13:17
 */

namespace App\Admin\Controllers;

use App\Admin\Model\Config;
use DB;
use Encore\Admin\Facades\Auth;
use Illuminate\Http\Request;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class ConfigController extends Controller{

    public function index(Request $request){
        $params = $request->all();

        $size = isset($params['size'])?$params['size']:$this->configs['page_num'];

//        $table = DB::table('config')
//            ->select(DB::raw('id,key,value,remarks,status,created_at'));
//        if(isset($params['key']) && $params['key']!='') $table->where('key',$params['key']);
//        $data = $table->paginate($size);

        return Admin::content(function (Content $content) use($size){

            $content->header(trans('admin::lang.config'));
            $content->description(trans('admin::lang.list'));
            $content->body($this->grid($size)->render());
        });
    }

    public function create(){
        return Admin::content(function (Content $content) {
            $content->header(trans('admin::lang.config'));
            $content->description(trans('admin::lang.create'));
            $content->body($this->form());
        });
    }

    public function store(){
        return $this->form()->store();
    }

    public function edit($id){
        return Admin::content(function (Content $content) use($id){
            $content->header(trans('admin::lang.config'));
            $content->description(trans('admin::lang.create'));
            $content->body($this->form()->edit($id));
        });
    }

    public function update($id){
        return $this->form()->update($id);
    }

    protected function grid($size){
        return Admin::grid(Config::class, function (Grid $grid) use($size){
            $grid->id('ID')->sortable();
            $grid->key(trans('admin::lang.key'))->sortable();
            $grid->value(trans('admin::lang.value'));
            $grid->remarks(trans('admin::lang.remarks'));

            $grid->status(trans('admin::lang.enable_status'))->value(function ($roles) {
                if($roles) return "<span class='label label-success'>启用</span>";
                else return "<span class='label label-danger'>禁用</span>";
            });

            $grid->created_at(trans('admin::lang.created_at'));

            $grid->rows(function($row){
                $row->actions()->add(function ($row) {
                    return "<a href='/url/{$row->id}'><i class='fa fa-eye'></i></a>";
                });
            });

            $grid->filter(function($filter){

                $filter->like('key',trans('admin::lang.key'));
            });

            $grid->paginate($size);

            $grid->disableBatchDeletion();
        });
    }

    public function form(){
        return Admin::form(Config::class, function (Form $form){
            $form->display('id', 'ID');

            $form->text('key', trans('admin::lang.key'))->rules('required');
            $form->text('value', trans('admin::lang.value'));
            $form->text('remarks', trans('admin::lang.remarks'));
            $form->text('status', trans('admin::lang.enable_status'));

            $form->display('created_at', trans('admin::lang.created_at'));
            $form->display('updated_at', trans('admin::lang.updated_at'));
        });
    }
}