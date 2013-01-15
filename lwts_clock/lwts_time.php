<?php

class lwts_time{

    private $pass_second;
    private $pass_task;
    private $pass_day;

    private $this_second;
    private $this_task;
    private $this_day;

    private $left_second;
    private $left_task;
    private $left_day;

    private $pass_year;

    private $pass_week;
    private $this_week;
    private $left_week;
    private $this_week_length;

    # These 3 attributes should not be seen outside this class.
    private $all_second;

    function __get($name){
	if($name == "all_second")
	    return;
	else
	    return $this->$name;
    }

    function __construct($all_second)
    {
	$this->all_second  = $all_second;

	$this->init_time();
	$this->init_week_year();

	$this->update_this();
	$this->update_left();
    }



    public function update_time($added_second, $lwts_life){

	$this->pass_second += $added_second;

	if($this->pass_second >= 900){
	    $this->pass_task    += 1;
	    $this->pass_second  %= 900;

	    if($this->pass_task >= 96){
		$this->pass_day  += 1;
		$this->pass_task %= 96;

		if($this->pass_day >= $this->this_week_length){
		    $this->init_week();
		    $lwts_life->update_life($this->$pass_year);
		}
	    }
	}
	$this->update_this();
	$this->update_left();
    }


    private function init_time(){
	# TODAY_SEC contains how many complete seconds have passed since 00:00 of today.
	$today_sec = $this->all_second % 86400; 
	$this->pass_task = floor($today_sec / 900);
	$this->pass_second = $today_sec % 900;
    }


    #=========================================================================
    # functions called by update_time();
    #=========================================================================

    private function update_this(){
	# calculate the current time.
	$this->this_second = $this->pass_second + 1;
	$this->this_task = $this->pass_task + 1;
	$this->this_day = $this->pass_day + 1;
    }

    private function update_left(){
	# calculate the left time.
	$this->left_second = 900 - $this->this_second;
	$this->left_task = 96 - $this->this_task;
	$this->left_day = $this->this_week_length - $this->this_day;
    }


    #=========================================================================
    #week and life related functions.
    #=========================================================================

    private function init_week_year(){
	$all_days;
	$pass_days;
	$this_year_length;

	$all_days = floor($this->all_second / 86400);

	$this->init_year($this_year_length, $all_days, $pass_days);
	$this->init_week($this_year_length, $all_days, $pass_days);
	#echo "all_days:$all_days left_days:$pass_days this_year_length:$this_year_length \n";
    }

    # $this->pass_year
    private function init_year(&$this_year_length, $all_days, &$pass_days){
	# As 3 years have 365 days and one year has 366 days, so treat these 4 years together as a group, the every 4 years group has 1461 days
	# 1970, 1971, 1973 has 365 days each, and 1972 has 366 days. The fllowing 4 years groups just repeat the ones of 1970 - 1973
	# We calculate these groups first, then, deal with the left days which are not enough to contribute to 4 years.
	# PASS_YEAR means how many complete years have passed, thus, in some day of 1970, the value of YEAR is 1969.
	$this->pass_year = 1969 + floor($all_days/1461) * 4;
	# $this->left_days contains the days which are not enough to a 4 year group.
	$pass_days = $all_days % 1461;
	# Since 1970, 1971, 1972, 1973 are contains 365, 365, 366, 365 days, so, if the LEFT_DAY is less or equal 365, it's still the same year as we calculate above.
	$this_year_length = 365;
	if($pass_days >= 1096){
	    $pass_days -= 1096;
	    $this->pass_year += 3;
	}elseif($pass_days >= 730){
	    $pass_days -= 730;
	    $this->pass_year += 2;
	    $this_year_length = 366;
	}elseif($pass_days >= 365){
	    $pass_days -= 365;
	    $this->pass_year += 1;
	}
	#echo "There are $this->pass_year years have been passed. This year has $this_year_length days in total, $pass_days days have been passed\n"; 
    }

    # $this->pass_week, $this->this_week, $this->left_week, $this->this_week_length, $this->pass_day
    private function init_week($this_year_length, $all_days, $pass_days){
	# WEEK_START is used to contains which week day of the first day of this year, is it sunday? Monday? or Friday? Let's calculate it!
	# ALL_DAY - LEFT_DAY means all the days from 1970-01-01 to 12-31 of last year. The meaning is all the days since 1970-01-01 to 01-01 of this year if that value add 1.
	# Since 1970-01-01 is a Thursday, we find that the 1969-12-29 is the Monday of that week.
	# So, the meaing of ALL_DAYS - LEFT_DAYS + 1 + 3 is all the days since 1969-12-29 to 01-01 of this year. 
	$week_start = ($all_days - $pass_days + 1 + 3) % 7;
	if($week_start==0)
	    $week_start=7;
	$bgn_week_length = (7 - $week_start + 1);
	$end_week_length = ($this_year_length - 50 * 7 - $bgn_week_length);

	# We counting from Monday of the week of 01-01 of this year, it maybe shrink back to some day of last year, + WEEK_START - 1 can help us find that day. if 01-01 of this is a friday, that means there are 4 days locating in last year, so we + 5 - 1.
	# And, we calc the number of weeks of this year from 0, the 0th week has 1 to 7 days, it is maybe a part of week as some days located in the last year, or, it's maybe a complete 7 days week from 01-01 to 01-07, we treat the week start from 01-08 is the first one, for example, so we must - 1. The 0th week also named the BGN week. We also can say that we do not calculate the special weeks, and there are only 50 weeks per year.
	# Note: We calc how many weeks have been pass since the first week, we not calc what's the week's number of this week.
	$this->pass_week = floor(($pass_days + $week_start - 1) / 7) - 1;
	$this->this_week = $this->pass_week + 1;
	$this->left_week = 50 - $this->this_week;
	#echo "pass week $this->pass_week \n";
	if($this->pass_week < 0){
	    $this->pass_week = 0;
	    $this->this_week = "BG";
	    $this->left_week = 50;
	    $this->this_week_length = $bgn_week_length;
	    $pass_days = $pass_days;
	}elseif($this->pass_week >= 50){
	    $this->pass_week = 50;
	    $this->this_week = "ED";
	    $this->left_week = 0;
	    $this->this_week_length = $end_week_length;
	    $pass_days = $pass_days - $bgn_week_length - 350;
	    #echo "pass week $this->pass_week \n";
	}else{
	    # What's the week day of today.
	    # Here, we also calc how many complete days have been passed in this week.
	    # By the way, here we treate Monday is the first day, and Sunday is the 7th day.
	    $this->this_week_length = 7;
	    $pass_days = ($pass_days + $week_start - 1 ) % 7;
	}
	$this->pass_day = $pass_days;
    }
}

?>

