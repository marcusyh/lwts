<?php

include(dirname(__FILE__)."/header.php");

#========================================
# Parameters analisys.
#========================================
# Detect if it's slience requested.
$slience=false;
if(count($argv)>=2)
    if($argv[1]=="-s" || $argv[1]=="-S") 
	$slience=true;

#========================================
# initlize
#========================================
# get all seconds start from 1970-01-01 00:00:00
$all_second = time();

# Set the original value to 0.
$added_second = 0;

# Create the configure object.
$configure = new configure();

# Create the lwts_time object and lwts_work object, and then do initialization.
$lwts_time = new lwts_time($all_second);
$lwts_life = new lwts_life($lwts_time->pass_year, $configure->life_array);
$lwts_work = new lwts_work($lwts_time, $lwts_life, $configure);

$utc_time  = date('Y-m-d H:i:s');

# Create the objects of ui_show and ui_sound.
$ui_show = new ui_show($lwts_time, $lwts_life, $lwts_work);
if($slience==false)
    $ui_sound = new ui_sound($lwts_time, $lwts_work);

#========================================
# Running.
#========================================
while(true){
    # The process will be hold by timer() function. It will only running again at the beginning of every seconds. 
    # The process will be hold by timer() function again after this cycle, it won't run until next second comes.
    $added_second = timer($all_second);
    $all_second += $added_second;
    $before_calc = microtime()."\n";

    # update $lwts_time object and $lwts_work object.
    $lwts_time -> update_time($added_second, $lwts_life);
    $lwts_work -> update_work();

    $utc_time  =  date('Y-m-d H:i:s');

    # Update the $ui_sound and $ui_show object. 
    # It's these 2 objects' duty to show the update time to end user.
    $ui_show->update($utc_time);

    if($slience==false)
	$ui_sound->update();

    $after_calc = microtime()."\n";
    echo $before_calc;
    echo $after_calc;
}

?>
