<?php

namespace App\Admin\Controllers;


use DB;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Chart\Bar;
use Encore\Admin\Widgets\Chart\Doughnut;
use Encore\Admin\Widgets\Chart\Line;
use Encore\Admin\Widgets\Chart\Pie;
use Encore\Admin\Widgets\Chart\PolarArea;
use Encore\Admin\Widgets\Chart\Radar;
use Encore\Admin\Widgets\Collapse;
use Encore\Admin\Widgets\InfoBox;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Widgets\Table;

class HomeController extends Controller
{
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('index');
            $content->description('ranking_list');

            $content->row(function ($row) {
                $headers = [trans('admin::lang.username'),trans('admin::lang.borrow_num')];
                $rows = [];
                if(isset($this->configs['index_false'])){
                    $rows = [
                        ['000','999'],
                        ['111','888'],
                        ['222','777'],
                        ['333','666'],
                        ['444','555'],
                        ['555','444'],
                        ['666','333'],
                        ['777','222'],
                        ['888','111'],
                        ['999','000']
                    ];
//                    $rows['month'] = [
//                        ['000','999'],
//                        ['111','888'],
//                        ['222','777'],
//                        ['333','666'],
//                        ['444','555'],
//                        ['555','444'],
//                        ['666','333'],
//                        ['777','222'],
//                        ['888','111'],
//                        ['999','000']
//                    ];
//                    $rows['year'] = [
//                        ['000','999'],
//                        ['111','888'],
//                        ['222','777'],
//                        ['333','666'],
//                        ['444','555'],
//                        ['555','444'],
//                        ['666','333'],
//                        ['777','222'],
//                        ['888','111'],
//                        ['999','000']
//                    ];
                }else{
                    $start_time = date('Y-m',time());
                    $rows = DB::table('borrow_info as bi')
                        ->join('user_info as ui','ui.id','=','bi.user_id')
                        ->select(DB::raw('ui.name,sum(bi.id) as borrow_num'))
                        ->where('bi.created_at','>',$start_time)
                        ->groupBy(DB::raw('bi.user_id'))
                        ->orderBy(DB::raw('borrow_num','desc'))
                        ->take(10)
                        ->get();
                    $js = 10;
                    $cnt = count($rows);
                    for($i = $cnt-$js;$i>0;$i--){
                        $one = array();
                        $one['name'] = $one['borrow_num'] = '-';
                        $rows = $one;
                    }

//                    $start_time = date('Y-m',time());
//                    $rows['month'] = DB::table('borrow_info as bi')
//                        ->json('user_info as ui','ui.id','=','bi.user_id')
//                        ->select(DB::raw('ui.name,sum(bi.id) as borrow_num'))
//                        ->where('bi.created_at','>',$start_time)
//                        ->groupBy('bi.user_id')
//                        ->orderBy('bi.borrow_num','desc')
//                        ->take(10);
//                    $js = 10;
//                    $cnt = count($rows['month']);
//                    for($i = $cnt-$js;$i>0;$i--){
//                        $one = array();
//                        $one['name'] = $one['borrow_num'] = '-';
//                        $rows['month'] = $one;
//                    }
//                    $start_time = date('Y',time());
//                    $rows['year'] = DB::table('borrow_info as bi')
//                        ->json('user_info as ui','ui.id','=','bi.user_id')
//                        ->select(DB::raw('ui.name,sum(bi.id) as borrow_num'))
//                        ->where('bi.created_at','>',$start_time)
//                        ->groupBy('bi.user_id')
//                        ->orderBy('bi.borrow_num','desc')
//                        ->take(10);
//                    $js = 10;
//                    $cnt = count($rows['year']);
//                    for($i = $cnt-$js;$i>0;$i--){
//                        $one = array();
//                        $one['name'] = $one['borrow_num'] = '-';
//                        $rows['year'] = $one;
//                    }
                }dd([$headers,$rows]);
                $row->column(4,(new Box(trans('admin::lang.personal_lending_list'), new Table($headers, $rows)))->style('info')->solid());

                $rows = [];
                if(isset($this->configs['index_false'])){
                    $rows = [
                        ['000','999'],
                        ['111','888'],
                        ['222','777'],
                        ['333','666'],
                        ['444','555'],
                        ['555','444'],
                        ['666','333'],
                        ['777','222'],
                        ['888','111'],
                        ['999','000']
                    ];
                }else{
                    $rows = DB::table('borrow_info as bi')
                        ->join('user_info as ui','ui.id','=','bi.user_id')
                        ->select(DB::raw('ui.major,sum(bi.id) as borrow_num'))
                        ->groupBy('ui.major')
                        ->orderBy('bi.borrow_num','desc')
                        ->take(10)
                        ->get();
                    $js = 10;
                    $cnt = count($rows);
                    for($i = $cnt-$js;$i>0;$i--){
                        $one = array();
                        $one['major'] = $one['borrow_num'] = '-';
                        $rows = $one;
                    }
                }
                $row->column(4,(new Box(trans('admin::lang.major_lending_list'), new Table($headers, $rows)))->style('info')->solid());

                $rows = [];
                if(isset($this->configs['index_false'])){
                    $rows = [
                        ['000','999'],
                        ['111','888'],
                        ['222','777'],
                        ['333','666'],
                        ['444','555'],
                        ['555','444'],
                        ['666','333'],
                        ['777','222'],
                        ['888','111'],
                        ['999','000']
                    ];
                }else{
                    $rows = DB::table('borrow_info as bi')
                        ->join('user_info as ui','ui.id','=','bi.user_id')
                        ->select(DB::raw('ui.grade,sum(bi.id) as borrow_num'))
                        ->groupBy('ui.grade')
                        ->orderBy('bi.borrow_num','desc')
                        ->take(10)
                        ->get();
                    $js = 10;
                    $cnt = count($rows);
                    for($i = $cnt-$js;$i>0;$i--){
                        $one = array();
                        $one['grade'] = $one['borrow_num'] = '-';
                        $rows = $one;
                    }
                }
                $row->column(4,(new Box(trans('admin::lang.grade_lending_list'), new Table($headers, $rows)))->style('info')->solid());

                $rows = [];
                if(isset($this->configs['index_false'])){
                    $rows = [
                        ['000','999'],
                        ['111','888'],
                        ['222','777'],
                        ['333','666'],
                        ['444','555'],
                        ['555','444'],
                        ['666','333'],
                        ['777','222'],
                        ['888','111'],
                        ['999','000']
                    ];
                }else{
                    $rows = DB::table('borrow_info as bi')
                        ->join('user_info as ui','ui.id','=','bi.user_id')
                        ->select(DB::raw('ui.class,sum(bi.id) as borrow_num'))
                        ->groupBy('ui.class')
                        ->orderBy('bi.borrow_num','desc')
                        ->take(10)
                        ->get();
                    $js = 10;
                    $cnt = count($rows);
                    for($i = $cnt-$js;$i>0;$i--){
                        $one = array();
                        $one['class'] = $one['borrow_num'] = '-';
                        $rows = $one;
                    }
                }
                $row->column(4,(new Box(trans('admin::lang.class_lending_list'), new Table($headers, $rows)))->style('info')->solid());
            });
        });
    }
}
