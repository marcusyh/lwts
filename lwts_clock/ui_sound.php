<?php

class ui_sound{
    private $srv_sound;
    private $lwts_time;
    private $lwts_work;
    private $sound_message;
    private $message_queue_key;


    function __construct($lwts_time, $lwts_work){
	$this->srv_sound = dirname(__FILE__)."/../resources/srv_sound.php";
	$this->lwts_time = $lwts_time;
	$this->lwts_work = $lwts_work;
	$this->sound_message = "";

	$this->message_queue_key = ftok(__FILE__, 'a');
	$this->message_queue = msg_get_queue($this->message_queue_key, 0600);

	// fork a process and run the sound service.
	$temp_pid = pcntl_fork();
	if($temp_pid == 0){
	    $cmd  = "/usr/bin/php";
	    $args = array($this->srv_sound, $this->message_queue_key);
	    pcntl_exec($cmd, $args);
	}
    }

    function update(){
	if($this->lwts_time->left_second % 10 == 0){
	    $this->get_sound_type();
	    msg_send($this->message_queue, 1, $this->sound_message);
	}
    }

    private function get_sound_type(){
	if($this->lwts_time->left_second == 10){
	    $this->sound_message[0] = 3;
	    $this->sound_message[1] = $this->lwts_time->this_task + 1;
	}
	elseif($this->lwts_time->left_second % 100 == 0 && floor($this->lwts_time->left_second / 100) != 0){
	    $this->sound_message[0] = 2;
	    $this->sound_message[1] = $this->lwts_time->left_second / 100;
	}else{
	    $this->sound_message[0] = 1;
	    $this->sound_message[1] = 0;
	}

	if($this->lwts_work->lwts_task['left_task'] ==0 && $this->lwts_work->lwts_task['left_second'] == 200){
	    $this->sound_message[0] = 4;
	    $this->sound_message[1] = 0;
	    return;
	}
    }

}

?>
