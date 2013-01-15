<?php

class lwts_work{
    private $lwts_task;
    private $lwts_work;

    private $configure;
    private $lwts_time;
    private $lwts_life;

    private $task_stage;

    function __get($name){
	return $this->$name;
    }

    function __construct($lwts_time, $lwts_life, $configure){
	$this->configure = $configure;
	$this->lwts_time = $lwts_time;
	$this->lwts_life = $lwts_life;

	$this->init_task();
	$this->init_work();
	$this->update_second();
    }


    public function update_work(){
	if($this->lwts_time->pass_second > $this->lwts_task['pass_second']){
	    $this->update_second();
	}elseif($this->lwts_time->pass_second < $this->lwts_task['pass_second']){
	    $this->update_second();
	    $this->update_task();
	}else{
	    if($this->lwts_time->pass_task != $this->pass_task)
		echo "Error happens to update_work() of class lwts_work";
	}
    }

    private function init_task(){
	foreach($this->configure->task_devide as $current){
	    if($current[0] < $current[1]){
		if($current[0] <= $this->lwts_time->this_task && $this->lwts_time->this_task <= $current[1]){
		    $this->lwts_task['pass_task'] = $this->lwts_time->this_task - $current[0];
		    $this->lwts_task['this_task'] = $this->lwts_task['pass_task'] + 1;
		    $this->lwts_task['left_task'] = $current[1] - $this->lwts_time->this_task;   

		    $this->task_stage = $current[2];
		}
	    }
	    else{
		if($current[0] <= $this->lwts_time->this_task && $this->lwts_time->this_task <= 96){
		    $this->lwts_task['pass_task'] = $this->lwts_time->this_task - $current[0];
		    $this->lwts_task['this_task'] = $this->lwts_task['pass_task'] + 1;
		    $this->lwts_task['left_task'] = $current[1] + 96 - $this->lwts_time->this_task;   
		    $this->task_stage = $current[2];
		}elseif(1 <= $this->lwts_time->this_task && $this->lwts_time->this_task <= $current[1]){
		    $this->lwts_task['pass_task'] = $this->lwts_time->this_task + 96 - $current[0];
		    $this->lwts_task['this_task'] = $this->lwts_task['pass_task'] + 1;
		    $this->lwts_task['left_task'] = $current[1] - $this->lwts_time->this_task;   
		    $this->task_stage = $current[2];
		}
	    }
	    $this->update_second();
	}

    }

    private function init_work(){
	$i = 1;
	$work_lists = $this->configure->work_schedule[$this->task_stage];
	$lwts_work  = "";
	foreach($work_lists as $current){
	    $start_day = ($current[0]-1) * 300 + ($current[1]-1) * 6 + $current[2];
	    $end_day   = ($current[3]-1) * 300 + ($current[4]-1) * 6 + $current[5];
	    $current_day = $this->lwts_life->life[0]['pass_life'] * 50 * 6 + $this->lwts_time->pass_week *6 + $this->lwts_time->this_day;

	    if($this->lwts_time->this_day == 7){
		$pass_day = $current_day - $start_day;
		$left_day = $end_day - $current_day + 1;
		$current_day -= 1;
	    }else{
		$pass_day = $current_day - $start_day;
		$left_day = $end_day - $current_day;
	    }

	    if($start_day <= $current_day && $current_day <= $end_day){
		$lwts_work[$i] = array(
		    'type' => $this->task_stage,
		    'pass_life' => floor($pass_day / 300),
		    'pass_week' => floor($pass_day % 300 / 6),
		    'pass_day'  => $pass_day % 300 % 6,
		    'left_life' => floor($left_day / 300),
		    'left_week' => floor($left_day % 300 / 6),
		    'left_day'  => $left_day % 300 % 6,
		    'description' => $current[6]
		);

		if($this->lwts_time->this_day == 7){
		    $lwts_work[$i]['this_life'] = $lwts_work[$i]['pass_life'] + 1;
		    $lwts_work[$i]['this_week'] = $lwts_work[$i]['pass_week'];
		    $lwts_work[$i]['this_day']  = $lwts_work[$i]['pass_day'];
		}else{
		    $lwts_work[$i]['this_life'] = $lwts_work[$i]['pass_life'] + 1;
		    $lwts_work[$i]['this_week'] = $lwts_work[$i]['pass_week'] + 1;
		    $lwts_work[$i]['this_day']  = $lwts_work[$i]['pass_day']  + 1;
		}
	    }
	    $i += 1;
	}
	$this->lwts_work = $lwts_work;
    }


    private function update_second(){
	$this->lwts_task['pass_second'] = $this->lwts_time->pass_second;
	$this->lwts_task['this_second'] = $this->lwts_time->this_second;
	$this->lwts_task['left_second'] = $this->lwts_time->left_second;
    }

    private function update_task(){
	if($this->lwts_task['left_task'] > 0 ){
	    $this->lwts_task['pass_task'] += 1;
	    $this->lwts_task['this_task'] += 1;
	    $this->lwts_task['left_task'] -= 1;   
	}else{
	    $this->init_task();
	    $this->init_work();
	}
    }
}
?>
