<link rel="stylesheet" type="text/css" href="/assets/style.css" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<menu>
    <ul>
        <li>
            <a href="/default/sql">ResetDB</a>
        </li>
        <li>
            <a href="/default/replay">Replayer</a>
        </li>
    </ul>
</menu>
<h2><?php echo $round->name.' ('.$round->bank.'$)'; ?></h2>
<div class="players">
<?php
    foreach($round->players as $player){
        $this->render('_player', array('player'=>$player, 'round'=>$round));
    };
?>
</div>
<div class="board">
    <?php  if($round->board):?>
        <?php  foreach($round->board as $card): ?>
             <img src="/files/deck/<?php echo strtolower($card['suit']) . '_' . strtolower($card['value']) ; ?>.png"/>
        <?php  endforeach; ?>
    <?php  endif;?>
    <button onclick="window.location = '/';">Next >></button>
    <button class="play_button">Play >></button>
</div>
<?php if($round->winners): ?>
<div class="winners">
    <?php foreach($round->winners as $player): ?>
        <div class="winner">
            <span class="player_name"><?php echo $player->name; ?></span>
            <span class="player_hand_value"> <?php echo $player->handValue['combinationValue']['name'].': '; ?></span>
            <?php if($player->handValue['combinationHeight']['value']):?>
            <span class="player_hand_value"> <?php echo$player->handValue['combinationHeight']['name'].'; Kicker:'; ?></span>
            <?php endif;?>
            <span class="player_hand_value"> <?php echo $player->handValue['handHeight']['name']; ?></span>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
<script>
    $('.play_button').click(function(){
        var play = setInterval(function(){
            $.get(
                '/', function(data){
                    $('body').html(data);
                    if($('body h2').text().search('showDown')>-1){
                        window.history.go(0);
                    }
                }
            )
        },10);
    })
</script>