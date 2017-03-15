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

            $content->row(function ($row){

                $row->column(6,function(Column $column) {
                    if (isset($this->configs['index_false'])) {
                        $pie_info = [
                            [trans('admin::lang.xianxia'), 400], [trans('admin::lang.xuanhuan'), 600], [trans('admin::lang.xuanyi'), 600], [trans('admin::lang.dushi'), 600],
                            [trans('admin::lang.qihuan'), 600], [trans('admin::lang.junshi'), 600], [trans('admin::lang.tiyu'), 600], [trans('admin::lang.youxi'), 600]
                        ];
                    } else {
                        //获取顶级分类
                        $pie_info = DB::table('books_type')
                            ->select(DB::raw('id,type_name'))
                            ->where('parent_id', 0)
                            ->get();
                        $pie_info = $pie_info->toArray();

                        //获取顶级分类下的所有分类id
                        foreach ($pie_info as &$v) {
                            $one_ids = [$v['id']];
                            $this->getSonIdById($v['id'], $one_ids);
                            $v['ids'] = $one_ids;
                        }

                        //获取顶级下的书籍数量
                        foreach ($pie_info as &$v) {
                            unset($v['id']);
                            $v['num'] = DB::table('books_info')->whereIn('type_id', $v['ids'])->count();
                            unset($v['ids']);
                        }
                    }//dd($pie_info);
                    $column->append((new Box(trans('admin::lang.books_type_overview'),new Pie($pie_info)))->removable()->collapsable()->style('info'));
                });

                $row->column(6,function(Column $column) {
                    if (isset($this->configs['index_false'])) {
                        $sum = [400,600,600,600,600,600,600,600];
                        $cur_sum = [300,500,500,500,500,500,500,500];
                        $name = [trans('admin::lang.xianxia'),trans('admin::lang.xuanhuan'),trans('admin::lang.xuanyi'),
                            trans('admin::lang.dushi'),trans('admin::lang.qihuan'),trans('admin::lang.junshi'),
                            trans('admin::lang.tiyu'),trans('admin::lang.youxi')];
                    } else {
                        //获取顶级分类
                        $pie_info = DB::table('books_type')
                            ->select(DB::raw('id,type_name'))
                            ->where('parent_id', 0)
                            ->get();
                        $pie_info = $pie_info->toArray();

                        //获取顶级分类下的所有分类id
                        foreach ($pie_info as &$v) {
                            $one_ids = [$v['id']];
                            $this->getSonIdById($v['id'], $one_ids);
                            $v['ids'] = $one_ids;
                        }

                        //获取顶级下的书籍数量
                        $sum = [];
                        $cur_sum = [];
                        $name = [];
                        foreach ($pie_info as &$v) {
                            $name[] = $v['type_name'];
                            $statistics = DB::table('books_info')
                                ->select(DB::raw('sum(cur_total) as cur_sum,sum(total) as sum'))
                                ->whereIn('type_id', $v['ids'])
                                ->get();
                            $cur_sum[] = $statistics[0]->cur_sum;
                            $sum[] = $statistics[0]->sum;
                        }
                    }
                    $column->append((new Box(trans('admin::lang.cur_type_overview'), new Radar($sum,$cur_sum,$name)))->removable()->collapsable()->style('info'));
                });
            });

            $content->row(function ($row) {

                $row->column(12,function(Column $column) {
                    if (isset($this->configs['index_false'])) {
                        $name = ["January", "February", "March", "April", "May", "June",
                            "July","August","September","October","November","December"];
                        $data = [
                                ['First', [40, 56, 67, 23, 10, 45, 78, 99, 80, 70,60,50]],
                            ];
                    }else{

                    }
                    $column->append((new Box(trans('admin::lang.monthly_borrow_num'), new Bar($name,$data)))->removable()->collapsable()->style('info'));
                });
            });

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

    public function getSonIdById($id,&$ids){

        $type_info = DB::table('books_type')->where('parent_id',$id)->get();
        foreach($type_info as $v){
            $ids[] = $v->id;
            $this->getSonIdById($v->id,$ids);
        }
    }
}
