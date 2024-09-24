<!doctype html>
<?php
require "../util.php";

printTop();
?>





<h1><?php echo $APPNAME ?></h1>

<?php

$safefilename = getSafeFileNameFromParams();
if ($safefilename == "_") {
    $safefilename = "";
} else {
    $guts = loadGutsFromParams($safefilename, true);
}

$pollToLoad = $guts ? json_encode($guts) : '{entries:[{type:"heading",id:"aaaaaaaa",caption:"Your Title"}]}';

echo <<<HTML
<div class='splitzones'><div id='editorzone'>
<h2>edit poll</h2>
<div id='editor'></div>
<b>add:</b><div id='addNews'></div>
<form action='../save/' method='POST'>
<textarea style='display:none;' id='guts' name='guts'></textarea>
<h2>save poll</h2>
<label>poll name: <input name='name' value='$safefilename' required></label>
<br><label>admin password: <input name='password' type='password' required></label>
<br><br><button>save poll</button>
</form>

HTML;
?>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        window.poll = <?php echo $pollToLoad ?>;

        redrawEditorAndPoll();
        addInAddNews(); // show "new" buttons to add things


    });
</script>
</div>
<div id="previewzone">
    <h2>poll preview</h2>
    <div id='poll' class='preview'></div>
</div>
</div>
<?


?>
</div>
<script>

</script>
</body>

<div id="colorPickerModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:white; border:1px solid #ccc; padding:20px; z-index:1000;">
    <button id="closeModal" style="position:absolute; top:10px; right:10px;">X</button>
    <h3>Pick a Color</h3>
    <div id="colorGrid"></div>
</div>



</html>