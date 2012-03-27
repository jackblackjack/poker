<div class="player">
    <label>Player: </label>
    <span class="player_name"><?php echo $player->name; ?></span>
    <label>Stack: </label>
    <span class="player_stack"><?php echo $player->stack . ' $'; ?></span>
    <?php  foreach($player->cards as $card):?>
        <div class="card <?php echo $card['suit'] . ' ' . $card['value'] ; ?>"></div><br />    
    <?php endforeach ;?>
</div>

