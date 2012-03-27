<?php
    foreach($dealer->players as $player){
        $this->render('_player', array('player'=>$player));
    };
?>