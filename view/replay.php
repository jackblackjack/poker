<h3>
    <a href="/">
        Home
    </a>
</h3>
<form method="get">
    <?php if($games): ?>
    <select name="game_id">
    <?php  foreach($games as $item): ?>
            <option value="<?php echo $item->id; ?>" ><?php echo $item->id; ?></option>   
    <?php  endforeach; ?>
    </select>
    <?php endif; ?>
    <?php if($rounds): ?>
    <select name="round_id">
    <?php  foreach($rounds as $item): ?>
            <option value="<?php echo $item->id; ?>" ><?php echo $item->name; ?></option>   
    <?php  endforeach; ?>
    </select>
    <?php endif; ?>
    <button>View</button>
</form>

<?php if($round){
    $this->render('index', array('round'=>$round));
}; ?>