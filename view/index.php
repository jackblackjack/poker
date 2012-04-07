<link rel="stylesheet" type="text/css" href="/assets/style.css" />
<menu>
    <ul>
        <li>
            <a href="/default/sql">ResetDB</a>
        </li>
    </ul>
</menu>
<h2><?php echo $round->name.' ('.$round->bank.'$)'; ?></h2>
<?php
    foreach($round->players as $player){
        $this->render('_player', array('player'=>$player));
    };
?>
<div class="board">
    <?php  if($round->board):?>
        <?php  foreach($round->board as $card): ?>
             <img src="/files/deck/<?php echo strtolower($card['suit']) . '_' . strtolower($card['value']) ; ?>.png"/>
        <?php  endforeach; ?>
    <?php  endif;?>
    <button onclick="window.location = '/';">Next >></button>
</div>