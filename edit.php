<?php
require_once('vars.php');
include_once("fckeditor/fckeditor.php");
?>
<div id="editFormContainer">
        <form action="<?php echo($base); ?>getparse.php" method="POST">
                Node Title <input type="text" name="titleField" value="<?php echo $_GET['title']; ?>" /><br/>
<?php
$oFCKeditor = new FCKeditor('FCKeditor1') ;
$oFCKeditor->BasePath = './fckeditor/' ;
$oFCKeditor->Value = $_GET['default'] ;
$oFCKeditor->ToolbarSet = 'Basic';
$oFCKeditor->Create() ;
?>
                <br />
                <input type="submit" name="submitButton" value="<?php echo $_GET['op']; ?>" />
        </form>
</div>
