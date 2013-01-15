<?php

# get current time, then do a infinate loop, until the value of the time() changed.
function deci_timer($all_second){
    static $last_deci_micro = 0;

    # recheck the seconds of now time.
    $now_time = microtime(true);

    # current second start from 1970-01-01 00:00
    $now_second = $now_time;
    settype($now_second, 'integer');
    $added_second = $now_second - $all_second;

    # current deci second of this second
    $this_micro = $now_time * 10000 % 10000;
    $added_deci_sec = floor($this_micro / 1000);

    # current micro second of this second
    $this_deci_micro = $this_micro / 1000;


    # condition 1: do a loop while $last_micro is little than $this_micro, because it means the value of time() is not changed.
    while($last_deci_micro <= $this_deci_micro){
	$last_deci_micro = $this_deci_micro;

	$this_micro = microtime(true) * 10000 % 10000;
	$this_deci_sec = floor($this_micro / 1000);
	$this_deci_micro = $this_micro / 1000;

	if($this_deci_sec != $added_deci_sec){
	    $added_deci_sec = $this_deci_sec;
	    break;
	}else{
	    if($this_deci_micro > 999 || $last_deci_micro > $this_deci_micro)
		continue;
	    elseif($this_deci_micro > 990)
		usleep(100);
	    elseif($this_deci_micro > 900)
		usleep(1000);
	    else
		usleep(10000);
	}
    }
    # condition 2: add 1 to $added_second, and then save $this_micro to $last_micro, it will used at next cycle.
    $last_deci_micro = $this_deci_micro;
    $added_deci_sec += 1;
    echo microtime()."\n";

    return $added_second.".".$added_deci_sec;
}

?>
