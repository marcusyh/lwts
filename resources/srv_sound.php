<?php

class srv_sound{
    private $sound_player;
    private $sound_path;
    private $message_queue;


    function __construct($message_queue_key){
	$this->sound_path = dirname(__FILE__)."/sounds";
	$this->sound_player = dirname(__FILE__)."/sound_player.sh";
	$this->message_queue = msg_get_queue($message_queue_key, 0600);
	echo ftok(__FILE__, 'a');
    }

    function mainloop(){
	while(true){
	    // read signel
	    msg_receive($this->message_queue, 0, $message_type, 1024, $message, true);
	    // play sound
	    $this->sound($message[0], $message[1]);
	}
    }

    // Sound Type:
    // 0 exit
    // 1 10 seconds
    // 2 100 seconds
    // 3 task
    // 4 work
    public function sound($sound_type, $sound_id){
	switch($sound_type){
	case 0:
	    exit;
	case 1:
	    $this->seconds10();
	    break;
	case 2:
	    $this->seconds100($sound_id);
	    break;
	case 3:
	    $this->task($sound_id);
	    break;
	case 4:
	    $this->work();
	    break;
	default:
	    break;
	}
    }

    private function work(){
	$sound = "$this->sound_path/task_end_music.wav";
	$voice = "$this->sound_path/task_end.wav";
	$this->play_sound($sound);
	$this->play_sound($voice);
    }

    private function seconds10(){
	$sound = "$this->sound_path/10seconds.wav";
	$this->play_sound($sound);
    }

    private function seconds100($hundards_left){
	$sound = "$this->sound_path/100seconds.wav";
	$voice = "$this->sound_path/left/left$hundards_left.wav";
	$this->play_sound($sound);
	$this->play_sound($voice);
    }

    private function task($task_id){
	$sound = "$this->sound_path/task_sound2.wav";
	$voice = "$this->sound_path/task/task$task_id.wav";

	$this->play_sound($sound);
	sleep(2.5);
	$this->play_sound($sound);
	sleep(2.5);
	$this->play_sound($sound);
	sleep(5);
	$this->play_sound($voice);
    }


    private function play_sound($sound_file){
	`$this->sound_player $sound_file`;
    }
}

$message_queue_key = $argv[1];
$srv_sound = new srv_sound($message_queue_key);
$srv_sound->mainloop();

?>

