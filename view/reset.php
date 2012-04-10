<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <ul>
        <li>
            <a href="/">Home</a>            
        </li>
        <li>
            <a class="newPlayers" href="/default/players">
                New Players
            </a>            
        </li>
    </ul>

<form method="post">
    <ul>
    <?php  foreach($items as $item): ?>
        <?php $name = 'reset['.$item.']' ;?>
        <?php $checked = in_array($item, array('Game', 'Round', 'Move')) ? 'checked="checked"' : '';?>
        <li>
            <input name="<?php echo $name; ?>" <?php echo $checked; ?> id="<?php echo $name; ?>" type="checkbox" value=1 />   
            <label for="<?php echo $name ;?>"><?php echo $item;?></label> 
        </li>
    <?php  endforeach; ?>
    </ul>
    <button>reset</button>
</form>

<script>
    $('.newPlayers').click(function(){
        $.get(this.href);
        return false;
    });
</script>