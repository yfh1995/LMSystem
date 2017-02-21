<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 17/2/20
 * Time: 18:17
 */

namespace App\Admin\Controllers;

use DB;
use Illuminate\Http\Request;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Form;

class UsersController extends Controller{

    public function index(Request $request){
        $params = $request->all();

        $size = isset($params['size'])?$params['size']:$this->configs['page_num'];

        $table = DB::table('admin_users as au')
            ->join('user_info as ui','ui.id','=','au.id')
            ->select(DB::raw('ui.*'))
            ->where('au.name','User');
        if(isset($params['id_number'])) $table->where('ui.id_number',$params['id_number']);
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
                trans('admin::lang.id_number'),
                trans('admin::lang.sex'),
                trans('admin::lang.available_num'),
                trans('admin::lang.sum_num'),
                trans('admin::lang.phone'),
            ];

            $users = array();
            foreach($data as $v){
                $users[] = $v;
            }

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
}