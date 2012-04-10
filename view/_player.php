<div class="player">
    <span class="player_name"><?php echo $player->name; ?></span>
    <span class="player_stack">(<?php echo $player->stack . '$'; ?>)</span>
    <span class="player_seat"> - <?php echo $player->seat; ?></span>
    <br />
    <?php  foreach($player->cards as $card):?>
            <img src="/files/deck/<?php echo strtolower($card['suit']) . '_' . strtolower($card['value']) ; ?>.png"/>
    <?php endforeach ;?>
    </br />
    <span class="player_move"> <?php echo $player->moveName . "-" . $player->amount; ?></span>
    <div class="winner">
        <span class="player_name"><?php echo $player->name; ?></span>
        <span class="player_hand_value"> <? echo $player->invest($round); ?></span><br />
    </div>
</div>