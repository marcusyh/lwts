<?php

# get current time, then do a infinate loop, until the value of the time() changed.
function timer($all_second){
    static $last_micro = 0;

    # recheck the seconds of now time.
    $now_time = microtime(true);

    # current second start from 1970-01-01 00:00
    $now_second = $now_time;
    settype($now_second, 'integer');
    $added_second = $now_second - $all_second;

    # current micro second of this second
    $this_micro = $now_time * 10000 % 10000;


    # condition 1: do a loop while $last_micro is little than $this_micro, because it means the value of time() is not changed.
    while($last_micro <= $this_micro){
	$last_micro = $this_micro;
	$this_micro = microtime(true) * 10000 % 10000;
	if($this_micro > 9999 || $last_micro > $this_micro)
	    continue;
	elseif($this_micro > 9990)
	    usleep(100);
	elseif($this_micro > 9900)
	    usleep(1000);
	elseif($this_micro > 9000)
	    usleep(10000);
	else
	    usleep(100000);
    }

    # condition 2: add 1 to $added_second, and then save $this_micro to $last_micro, it will used at next cycle.
    $last_micro = $this_micro;
    $added_second += 1;


    return $added_second;
}

?>
