<?php

class ui_show{
    private $line_after;
    private $header;
    private $line_before;

    private $ui_lwts_life;
    private $ui_lwts_work;
    private $ui_utc_time;

    function __construct($lwts_time, $lwts_life, $lwts_work){
	$this->lwts_time = $lwts_time;
	$this->lwts_life = $lwts_life;
	$this->lwts_work = $lwts_work;
	$this->init();
    }

    public function update($utc_time){
	$this->update_life();
	$this->update_work();
	$this->update_utc($utc_time);
	$this->show();
    }

    private function init(){
	$this->line_before = "\t\t\t\t\t\t\t\t";
	$this->line_after  = "\n";

	$this->header = "\t\t\t\t\tLWTS CLOCK\n\n"
	    . "This is a L.W.T.S. clock, which means Life, Years, Weeks, Days, Tasks, and Seconds.\n"
	    . "It assumes that there are 60 years the life can existing(The first year of life is not included in it).\n"
	    . "There 60 years for life, 365 or 366 days per year, and 86400 seconds per day. To make it easier to understand, the years have been devided to weeks, there are 7 days per week, and 50 weeks per year whereas there are 2 special weeks added to the beginning and end of every year. Similarly, the day have been devided to quarters, thare are 900 secons per Quarter, and 96 Tasks per day.\n\n"
	    . "\n\n\n\n\n\n\n\n\n\n\n";
    }


    private function update_life(){
	$lwts_format = "Life %s: %02d-%02s   %02d-%02d   %03d";
	$i = 0;
	foreach($this->lwts_life->life as $current){

	    $this->ui_lwts_life[$i] = array(
		'pass_life' => sprintf($lwts_format,'pass', $current['pass_life'], $this->lwts_time->pass_week, $this->lwts_time->pass_day, $this->lwts_time->pass_task, $this->lwts_time->pass_second),
		'this_life' => sprintf($lwts_format,'this', $current['this_life'], $this->lwts_time->this_week, $this->lwts_time->this_day, $this->lwts_time->this_task, $this->lwts_time->this_second),
		'left_life' => sprintf($lwts_format,'left', $current['left_life'], $this->lwts_time->left_week, $this->lwts_time->left_day, $this->lwts_time->left_task, $this->lwts_time->left_second));
	    $i += 1;

	}
    }

    private function update_work(){
	$work_format = "\t%s %s: %02d-%02d   %02d-%02d   %03d   %s";
	$i = 0;
	if($this->lwts_work->lwts_work!=null)
	    foreach($this->lwts_work->lwts_work as $current){
		$this->ui_lwts_work[$i] = array(
		    'work_pass' => sprintf($work_format, $current['type'], 'pass', $current['pass_life'], $current['pass_week'], $current['pass_day'], $this->lwts_work->lwts_task['pass_task'], $this->lwts_work->lwts_task['pass_second'],$current['description']),

		    'work_this' => sprintf($work_format, $current['type'], 'this', $current['this_life'], $current['this_week'], $current['this_day'], $this->lwts_work->lwts_task['this_task'], $this->lwts_work->lwts_task['this_second'], $current['description']),

		    'work_left' => sprintf($work_format, $current['type'], 'left', $current['left_life'], $current['left_week'], $current['left_day'], $this->lwts_work->lwts_task['left_task'], $this->lwts_work->lwts_task['left_second'], $current['description']));

		$i += 1;
	    }
    }

    private function update_utc($utc_time){
	$this->ui_utc_time = "UTC  time: ". $utc_time;
    }

    private function show(){
	echo $this->header;

	$line_number = 24;
	foreach($this->ui_lwts_life as $current){
	    echo $this->line_before . $current['left_life'] . $this->line_after;
	    echo $this->line_before . $current['this_life'] . $this->line_after;
	    echo $this->line_before . $current['pass_life'] . $this->line_after;
	    echo "\n";
	    $line_number -= 4; 
	}
	echo $this->line_before . $this->ui_utc_time . $this->line_after;
	echo "\n";
	if($this->ui_lwts_work == null){
	    $no_task_string = "There is no task defined this moment!!!";
	    echo $this->line_before . $no_task_string . $this->line_after;
	}
	else
	    foreach($this->ui_lwts_work as $current){
		echo $this->line_before . $current['work_left'] . $this->line_after;
		echo $this->line_before . $current['work_this'] . $this->line_after;
		echo $this->line_before . $current['work_pass'] . $this->line_after;
		echo "\n";
		$line_number -= 4; 
	    }
	for(; $line_number >=1; $line_number--)
	    echo "\n";
	echo "\n\n\n\n";
    }


}
?>
