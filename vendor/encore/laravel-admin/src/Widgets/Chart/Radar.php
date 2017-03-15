<?php

namespace Encore\Admin\Widgets\Chart;

class Radar extends Chart
{
    protected $labels = [],$data1 = '[]',$data2 = '[]',$label = '[]';

    public function __construct($data1,$data2,$label){
        $this->data1 = '['.implode(',',$data1).']';
        $this->data2 = '['.implode(',',$data2).']';
        $this->label = '["'.str_replace(',','","',implode(',',$label)).'"]';
    }

    public function labels($labels)
    {
        $this->labels = $labels;
    }

    public function script()
    {
        $options = json_encode($this->options);

        return <<<EOT

(function(){

    var data = {
        labels: $this->label,
        datasets: [
            {
                label: "My First dataset",
                fillColor: "rgba(220,220,220,0.2)",
                strokeColor: "rgba(220,220,220,1)",
                pointColor: "rgba(220,220,220,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",
                data: $this->data1
            },
            {
                label: "My Second dataset",
                fillColor: "rgba(151,187,205,0.2)",
                strokeColor: "rgba(151,187,205,1)",
                pointColor: "rgba(151,187,205,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(151,187,205,1)",
                data: $this->data2
            }
        ]
    };

    var canvas = $("#{$this->elementId}").get(0).getContext("2d");
    var chart = new Chart(canvas).Radar(data, $options);

})();
EOT;
    }
}
