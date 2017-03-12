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

            $content->header(trans('admin::lang.index'));
            $content->description(trans('admin::lang.overview'));

            $content->row(function ($row) {
                $headers = [trans('admin::lang.no'),trans('admin::lang.username'),trans('admin::lang.borrow_num')];

                $row->column(4,function(Column $column) use($headers){

                    $personal_tabs = new Tab();
                    $personal_year = $personal_month = $personal_week = [];
                    if(isset($this->configs['index_false'])){
                        $personal_year = $personal_month = $personal_week = [
                            [1,'000','999'],
                            [2,'111','888'],
                            [3,'222','777'],
                            [4,'333','666'],
                            [5,'444','555'],
                            [6,'555','444'],
                            [7,'666','333'],
                            [8,'777','222'],
                            [9,'888','111'],
                            [10,'999','000']
                        ];
                        $personal_month = [
                            [1,'999','000'],
                            [2,'888','111'],
                            [3,'777','222'],
                            [4,'666','333'],
                            [5,'555','444'],
                            [6,'444','555'],
                            [7,'333','666'],
                            [8,'222','777'],
                            [9,'111','888'],
                            [10,'000','999']
                        ];
                    }else{
                        $start_time = date('Y-m',time());
                        $personal_month = DB::table('borrow_info as bi')
                            ->join('user_info as ui','ui.id','=','bi.user_id')
                            ->select(DB::raw('ui.name,sum(bi.id) as borrow_num'))
                            ->where('bi.created_at','>',$start_time)
                            ->groupBy('bi.user_id')
                            ->orderBy('borrow_num','desc')
                            ->take(10)
                            ->get();
                        $personal_month = $this->object_array($personal_month);
                        $js = 10;
                        $cnt = count($personal_month);
                        for($i = $js;$i>0;$i--){
                            if($i <= $js-$cnt) {
                                $one = array();
                                $one['name'] = $one['borrow_num'] = '-';
                                $personal_month[] = $one;
                            }
                            $personal_month[$js-$i] = [$js+1-$i] + $personal_month[$js-$i];
                        }
                        $start_time = date('Y',time());
                        $personal_year = DB::table('borrow_info as bi')
                            ->join('user_info as ui','ui.id','=','bi.user_id')
                            ->select(DB::raw('ui.name,sum(bi.id) as borrow_num'))
                            ->where('bi.created_at','>',$start_time)
                            ->groupBy('bi.user_id')
                            ->orderBy('borrow_num','desc')
                            ->take(10)
                            ->get();
                        $cnt = count($personal_year);
                        for($i = $js;$i>0;$i--){
                            if($i <= $js-$cnt) {
                                $one = array();
                                $one['name'] = $one['borrow_num'] = '-';
                                $personal_year[] = $one;
                            }
                            $personal_year[$js-$i] = [$js+1-$i] + $personal_year[$js-$i];
                        }
                    }
                    $personal_tabs->add(trans('admin::lang.year_list'),new Table($headers,$personal_year));
                    $personal_tabs->add(trans('admin::lang.month_list'),new Table($headers,$personal_month));
                    $personal_tabs->title(trans('admin::lang.personal_lending_list'));
                    $column->append($personal_tabs);


                    $rows = [];
                    if(isset($this->configs['index_false'])){
                        $rows = [
                            [1,'000','999'],
                            [2,'111','888'],
                            [3,'222','777'],
                            [4,'333','666'],
                            [5,'444','555'],
                            [6,'555','444'],
                            [7,'666','333'],
                            [8,'777','222'],
                            [9,'888','111'],
                            [10,'999','000']
                        ];
                    }else{
                        $rows = DB::table('borrow_info as bi')
                            ->join('user_info as ui','ui.id','=','bi.user_id')
                            ->select(DB::raw('ui.class,sum(bi.id) as borrow_num'))
                            ->groupBy('ui.class')
                            ->orderBy('borrow_num','desc')
                            ->take(10)
                            ->get();
                        $js = 10;
                        $cnt = count($rows);
                        for($i = $js-$cnt;$i>0;$i--){
                            if($i <= $js-$cnt) {
                                $one = array();
                                $one['class'] = $one['borrow_num'] = '-';
                                $rows[] = $one;
                            }
                            $rows[$js-$i] = [$js+1-$i] + $rows[$js-$i];
                        }
                    }
                    $column->append((new Box(trans('admin::lang.class_lending_list'), new Table($headers, $rows)))->style('info')->solid());
                });

                $rows = [];
                if(isset($this->configs['index_false'])){
                    $rows = [
                        [1,'000','999'],
                        [2,'111','888'],
                        [3,'222','777'],
                        [4,'333','666'],
                        [5,'444','555'],
                        [6,'555','444'],
                        [7,'666','333'],
                        [8,'777','222'],
                        [9,'888','111'],
                        [10,'999','000']
                    ];
                }else{
                    $rows = DB::table('borrow_info as bi')
                        ->join('user_info as ui','ui.id','=','bi.user_id')
                        ->select(DB::raw('ui.major,sum(bi.id) as borrow_num'))
                        ->groupBy('ui.major')
                        ->orderBy('borrow_num','desc')
                        ->take(10)
                        ->get();
                    $js = 10;
                    $cnt = count($rows);
                    for($i = $js;$i>0;$i--){
                        if($i <= $js-$cnt) {
                            $one = array();
                            $one['major'] = $one['borrow_num'] = '-';
                            $rows[] = $one;
                        }
                        $rows[$js-$i] = [$js+1-$i] + $rows[$js-$i];
                    }
                }
                $row->column(4,(new Box(trans('admin::lang.major_lending_list'), new Table($headers, $rows)))->style('info')->solid());

                $rows = [];
                if(isset($this->configs['index_false'])){
                    $rows = [
                        [1,'000','999'],
                        [2,'111','888'],
                        [3,'222','777'],
                        [4,'333','666'],
                        [5,'444','555'],
                        [6,'555','444'],
                        [7,'666','333'],
                        [8,'777','222'],
                        [9,'888','111'],
                        [10,'999','000']
                    ];
                }else{
                    $rows = DB::table('borrow_info as bi')
                        ->join('user_info as ui','ui.id','=','bi.user_id')
                        ->select(DB::raw('ui.grade,sum(bi.id) as borrow_num'))
                        ->groupBy('ui.grade')
                        ->orderBy('borrow_num','desc')
                        ->take(10)
                        ->get();
                    $js = 10;
                    $cnt = count($rows);
                    for($i = $js-$cnt;$i>0;$i--){
                        if($i <= $js-$cnt) {
                            $one = array();
                            $one['grade'] = $one['borrow_num'] = '-';
                            $rows[] = $one;
                        }
                        $rows[$js-$i] = [$js+1-$i] + $rows[$js-$i];
                    }
                }
                $row->column(4,(new Box(trans('admin::lang.grade_lending_list'), new Table($headers, $rows)))->style('info')->solid());
            });
        });
    }
}
