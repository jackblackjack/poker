<h3>
    <a href="/">
        Home
    </a>
</h3>
<form method="post">
    <ul>
    <?php  foreach($items as $item): ?>
        <?php $name = 'reset['.$item.']' ;?>
        <li>
            <input name="<?php echo $name; ?>" id="<?php echo $name; ?>" type="checkbox" value=1 />   
            <label for="<?php echo $name ;?>"><?php echo $item;?></label> 
        </li>
    <?php  endforeach; ?>
    </ul>
    <button>reset</button>
</form>