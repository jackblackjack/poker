<?php
    foreach($round->players as $player){
        $this->render('_player', array('player'=>$player));
    };
?>