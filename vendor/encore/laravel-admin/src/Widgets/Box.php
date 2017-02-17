<?php

namespace Encore\Admin\Widgets;

use Illuminate\Contracts\Support\Renderable;
use Encore\Admin\Facades\Admin as AdminManager;

class Box extends Widget implements Renderable
{
    protected $attributes = [
        'class'     => [],
        'tools'     => [],
        'title'     => 'Box header',
        'content'   => 'here is the box content.',
    ];

    protected $toolsParams = [];

    public function __construct($title = '', $content = '', $toolsParams = [])
    {
        if ($title) {
            $this->title($title);
        }

        if ($content) {
            $this->content($content);
        }

        if ($toolsParams) {
            $this->toolsParams($toolsParams);
        }
    }

    public function content($content)
    {
        if ($content instanceof Renderable) {
            $this->attributes['content'] = $content->render();
        } else {
            $this->attributes['content'] = (string) $content;
        }

        return $this;
    }

    public function title($title)
    {
        $this->attributes['title'] = $title;
    }

    public function toolsParams($toolsParams){
        $this->toolsParams = $toolsParams;
    }

    public function addSelete(){
        $select_id = $this->toolsParams['select_id'];
        $search_id = $this->toolsParams['search_id'];
        $options = $this->toolsParams['options'];

        $str = '<div class="form-group"><div class="col-sm-6"><select class="form-control " style="width: 100%;height:30px;" id="'.$select_id.'" name="'.$select_id.'">';
        foreach($options as $select => $option){
            $str .= '<option value="'.$select.'">'.$option.'</option>';
        }
        $str .= '</select></div><div class="col-sm-6" style="padding-left:0px;">';
        $str .= '<input id="'.$search_id.'" type="text" name="book_number" placeholder="'.trans('admin::lang.book_number').'" style="height:30px;color:black;">';
        $str .= '</div></div>';

        $this->attributes['tools'][] =  $str;

        $script = $this->buildupScript();
        AdminManager::script($script);

        return $this;
    }

    public function buildupScript(){
        $select_id = $this->toolsParams['select_id'];
        $search_id = $this->toolsParams['search_id'];

        return <<<SCRIPT

        $('#$select_id').change(function(){
            var type_id = $('#$select_id option:selected') .val();
            window.location.href = '/admin/books/index?type_id=' + type_id;
        });

        $('#$search_id').keyup(function(e){
			if(e.keyCode === 13){
                var book_number = $('#$search_id') .val();
                window.location.href = '/admin/books/index?book_number=' + book_number;
			}
		});

SCRIPT;

    }

    public function collapsable()
    {
        $this->attributes['tools'][] =
            '<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>';

        return $this;
    }

    public function removable()
    {
        $this->attributes['tools'][] =
            '<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>';

        return $this;
    }

    public function style($styles)
    {
        if (is_string($styles)) {
            return $this->style([$styles]);
        }

        $styles = array_map(function ($style) {
            return 'box-'.$style;
        }, $styles);

        $this->attributes['class'] = array_merge($this->attributes['class'], $styles);

        return $this;
    }

    public function solid()
    {
        $this->attributes['class'][] = 'box-solid';

        return $this;
    }

    public function render()
    {
        return view('admin::widgets.box', $this->attributes)->render();
    }
}
