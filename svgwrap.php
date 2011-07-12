<?php require_once('vars.php'); ?>
<div class="SVGWrapper">
<?php if (isset($_GET['node'])) { ?>
        <embed src="<?php echo($base); ?>svg/mapgen.svg.php?node=<?php echo $_GET['node'] ?>"
<?php } else { ?>
        <embed src="<?php echo($base); ?>svg/mapgen.svg.php"
<?php } ?>
                type="image/svg+xml"
                width="100%"
                height="100%">
        </object>
</div>
