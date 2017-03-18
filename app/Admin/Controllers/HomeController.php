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

            //饼状图、雷达图
            $content->row(function ($row){

                //饼状图
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
                        $pie_info = $this->object_array($pie_info);

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

                //雷达图
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
                        $pie_info = $this->object_array($pie_info);

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

            //柱状图
            $content->row(function ($row) {

                $row->column(12,function(Column $column) {

                    $name = ["January", "February", "March", "April", "May", "June",
                        "July","August","September","October","November","December"];
                    if (isset($this->configs['index_false'])) {
                        $data = [
                                ['First', [40, 56, 67, 23, 10, 45, 78, 99, 80, 70,  45, 40]],
                            ];
                    }else{
                        $months[] = date('Y').'-00-00 00:00:00';
                        for($i=1;$i<12;$i++){
                            $months[] = date('Y-m-d H:i:s',strtotime('+1 month',strtotime($months[$i-1])));
                        }

                        $result = [];
                        for($i=0;$i<12;$i++){
                            $table = DB::table('borrow_info')
                                ->where('created_at','>=',$months[$i]);
                            if($i+1 != 12) $table->where('created_at','<',$months[$i+1]);
                            $result[] = $table->count();
                        }

                        $data = [
                            ['First',$result],
                        ];
                    }

                    $column->append((new Box(date('Y').' '.trans('admin::lang.monthly_borrow_num'), new Bar($name,$data)))->removable()->collapsable()->style('info'));
                });
            });

            //排行榜
            $content->row(function ($row) {
                $size = 10;

                //专业、年级、班级借阅榜
                if(isset($this->configs['index_false'])){
                    $major_headers = [trans('admin::lang.no'),trans('admin::lang.major'),trans('admin::lang.borrow_num')];
                    $grade_headers = [trans('admin::lang.no'),trans('admin::lang.grade'),trans('admin::lang.borrow_num')];
                    $class_headers = [trans('admin::lang.no'),trans('admin::lang.class'),trans('admin::lang.borrow_num')];

                    $major_year = $major_month = $major_week =
                    $grade_year = $grade_month = $grade_week =
                    $class_year = $class_month = $class_week = [
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

                    $major_tab = new Tab();
                    $major_tab->add(trans('admin::lang.week_list'),new Table($major_headers,$major_week));
                    $major_tab->add(trans('admin::lang.month_list'),new Table($major_headers,$major_month));
                    $major_tab->add(trans('admin::lang.year_list'),new Table($major_headers,$major_year));
                    $major_tab->title(trans('admin::lang.major_lending_list'));

                    $grade_tab = new Tab();
                    $grade_tab->add(trans('admin::lang.week_list'),new Table($grade_headers,$grade_year));
                    $grade_tab->add(trans('admin::lang.month_list'),new Table($grade_headers,$grade_month));
                    $grade_tab->add(trans('admin::lang.year_list'),new Table($grade_headers,$grade_year));
                    $grade_tab->title(trans('admin::lang.grade_lending_list'));

                    $class_tab = new Tab();
                    $class_tab->add(trans('admin::lang.week_list'),new Table($class_headers,$class_week));
                    $class_tab->add(trans('admin::lang.month_list'),new Table($class_headers,$class_month));
                    $class_tab->add(trans('admin::lang.year_list'),new Table($class_headers,$class_year));
                    $class_tab->title(trans('admin::lang.class_lending_list'));
                }
                else {
                    $major_tab = $this->getMGCRanking($size,'major');
                    $grade_tab = $this->getMGCRanking($size,'grade');
                    $class_tab = $this->getMGCRanking($size,'class');
                }
                $row->column(4,$major_tab);
                $row->column(4,$grade_tab);
                $row->column(4,$class_tab);

                //个人阅读排行榜
                $headers = [trans('admin::lang.no'),trans('admin::lang.username'),trans('admin::lang.borrow_num')];
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
                }else{
                    $personal_year = $this->personal_ranking_data($size,date('Y',time()));
                    $personal_month = $this->personal_ranking_data($size,date('Y-m',time()));
                    $personal_week = $this->personal_ranking_data($size,date('Y-m-d',time()));
                }
                $personal_tabs->add(trans('admin::lang.week_list'),new Table($headers,$personal_week));
                $personal_tabs->add(trans('admin::lang.month_list'),new Table($headers,$personal_month));
                $personal_tabs->add(trans('admin::lang.year_list'),new Table($headers,$personal_year));
                $personal_tabs->title(trans('admin::lang.personal_lending_list'));
                $row->column(4,$personal_tabs);
            });
        });
    }

    public function personal_ranking_data($size,$start_time){

        $start_time = date('Y',time());
        $personal = DB::table('borrow_info as bi')
            ->join('user_info as ui','ui.id','=','bi.user_id')
            ->select(DB::raw('ui.name,sum(bi.id) as borrow_num'))
            ->where('bi.created_at','>',$start_time)
            ->groupBy('bi.user_id')
            ->orderBy('borrow_num','desc')
            ->take($size)
            ->get();
        $personal = $this->object_array($personal);
        $cnt = count($personal);
        for($i = $size;$i>0;$i--){
            if($i <= $size-$cnt) {
                $one = array();
                $one['name'] = $one['borrow_num'] = '-';
                $personal[] = $one;
            }
            $personal[$size-$i] = [$size+1-$i] + $personal[$size-$i];
        }
        return $personal;
    }

    public function getMGCRanking($size,$key){
        $headers = [trans('admin::lang.no'),trans('admin::lang.'.$key),trans('admin::lang.borrow_num')];

        $year_time = date('Y',time());
        $month_time = date('Y-m',time());
        $week_time = date('Y-m-d',time());

        $year_data     =   $this->MGC_ranking_data($size, $key, $year_time);
        $month_data    =   $this->MGC_ranking_data($size, $key, $month_time);
        $week_data     =   $this->MGC_ranking_data($size, $key, $week_time);

        $tab = new Tab();
        $tab->add(trans('admin::lang.week_list'),new Table($headers,$year_data));
        $tab->add(trans('admin::lang.month_list'),new Table($headers,$month_data));
        $tab->add(trans('admin::lang.year_list'),new Table($headers,$week_data));
        $tab->title(trans('admin::lang.'.$key.'_lending_list'));
        return $tab;
    }

    public function MGC_ranking_data($size,$key,$start_time){
        $rows = DB::table('borrow_info as bi')
            ->join('user_info as ui','ui.id','=','bi.user_id')
            ->select(DB::raw('ui.'.$key.',sum(bi.id) as borrow_num'))
            ->where('bi.created_at','>',$start_time)
            ->groupBy('ui.'.$key)
            ->orderBy('borrow_num','desc')
            ->take($size)
            ->get();
        $rows = $this->object_array($rows);

        $cnt = count($rows);
        for($i = $size;$i>0;$i--){
            if($i <= $size-$cnt) {
                $one = array();
                $one[$key] = '-';
                $one['borrow_num'] = '-';
                $rows[] = $one;
            }
            $rows[$size-$i] = [$size+1-$i] + $rows[$size-$i];
        }
        return $rows;
    }

    public function getSonIdById($id,&$ids){

        $type_info = DB::table('books_type')->where('parent_id',$id)->get();
        foreach($type_info as $v){
            $ids[] = $v->id;
            $this->getSonIdById($v->id,$ids);
        }
    }
}
