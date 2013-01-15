<?php

class lwts_life{
    private $life;
    private $life_array;

    function __get($name){
	return $this->$name;
    }

    function __construct($pass_year, $life_array)
    {
	$this->life_array = $life_array;
	$this->update_life($pass_year);
    }

    # $this->pass_life, $this->this_life, $this->left_life
    public function update_life($pass_year){
	$i = 0;
	foreach($this->life_array as $current){
	    $pass_life = $pass_year - $current['born_year'] + 1;
	    $this_life = $pass_life + 1;
	    $left_life = $current['life_length'] - $this_life;
	    $this->life[$i] = array('pass_life'=>$pass_life, 'this_life'=>$this_life, 'left_life'=>$left_life);
	    $i += 1;
	}
    }

}

?>

