<?php

class configure{
    private $life_array;
    private $task_devide;
    private $work_schedule;
    private $conf_file_path;
    
    function __get($name){
	return $this->$name;
    }

    function __construct(){
	$this->conf_file_path = dirname(__FILE__) . "/../configure/configure.txt";
	$this->read_configure();
    }

    private function read_configure(){
	$all_conf = file($this->conf_file_path);
	$line_num = count($all_conf);
	$task_devide = "";
	$work_schedule = "";

	if($line_num == 0){
	    echo "ERROR!!! Please note that you've not add the configure information, so, the work clock will be disabled.";
	}

	for($i=0; $i<$line_num; $i++){
	    $temp = trim($all_conf[$i]);
	    $temp = ereg_replace("#.*", "", $temp);
	    if($temp == "")
		continue;
	    else{
		$temp = explode('=', $temp); 
		if(trim(strtolower($temp[0]))=="life")
		    $life = trim($temp[1]);
		elseif(trim(strtolower($temp[0]))=="time")
		    $task_devide[$i] = trim($temp[1]);
		else
		    $work_schedule[$i] = array(trim($temp[0]), trim($temp[1]));
	    }
	}
	$this->set_life_array($life);
	$this->set_task_devide($task_devide);
	$this->set_work_schedule($work_schedule);
    }

    private function set_life_array($life){
	$i = 0;
	$person = explode('||', $life);
	foreach($person as $current){
	    $current_p = explode(' ', trim($current));
	    $this->life_array[$i] = array('born_year' => $current_p[0], 'life_length' => $current_p[1]);
	    $i++;
	}
    }

    private function set_task_devide($task_devide){
	$i=0;
	foreach($task_devide as $current){
	    $this->task_devide[$i] = explode(' ', trim($current));
	    $i++;
	}
    }

    private function set_work_schedule($work_schedule){
	$this->work_schedule = array();
	foreach($work_schedule as $current){
	    $temp = ereg_replace(" *([0-9]*)-([0-9]*)-([0-9]*) ([0-9]*)-([0-9]*)-([0-9]*) (.*)", "\\1:\\2:\\3:\\4:\\5:\\6:\\7", $current[1]);
	    $temp_array = explode(':', $temp);
	    $isnew = true;

	    foreach($this->work_schedule as $key=>$value){
		if($key == $current[0]){
		    $index = count($this->work_schedule[$current[0]]);
		    $this->work_schedule[$key][$index] = $temp_array;
		    $isnew = false;
		    break;
		}
	    }
	    if($isnew)
		$this->work_schedule[$current[0]][0] = $temp_array;
	}
    }

}

/*
$conf = new configure();
echo "$conf->born_year $conf->life_length \n";
foreach($conf->task_devide as $value)
{
    
    $i=0;
    foreach($value as $temp){
	echo $i."-".$value[$i]."==";
	$i++;
    }
    echo "\n";
}
foreach($conf->work_schedule as $key=>$value)
{
    echo "$key => ";
    echo "\n";

    foreach($value as $temp){
	foreach($temp as $aa)
	    echo $aa."::";
	echo "\t\n";
    }
    echo "\n";
}

 */
?>
