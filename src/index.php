<!doctype html>
<?php
require "util.php";
printTop();
?>




<?php
if (empty($_GET)) {
?>
    <h1><?php echo $APPNAME; ?></h1>

    <p>The quickest way to setup an online poll or survey.</p>

    <p>No account or signup needed! Just add the questions, pick a poll name, set an admin password, and get a shareable link. </p>

    <form action="edit/" method="GET">

        <button>make a new poll</button>
    </form>

<?php
    die;
} else {
    $safefilename = getSafeFileNameFromParams();
    $guts = loadGutsFromParams($safefilename, true);

    if (!$guts) {
        print "Can't find the poll $safefilename, sorry";
        die;
    }
    
    
    if($guts["closed"]) {
        print "Poll is closed!";
        die;
    } 
    print "<script>window.poll = " . json_encode($guts) . ";document.addEventListener('DOMContentLoaded', ()=>redrawPoll(true));</script>";
    
}

?>

<form method="POST" action="record/">
    <input type="hidden" name="_id" value="<?php echo $safefilename ?>">
    <div id="poll"></div>
    <br><br><button>Submit Response</button>
</form>
</div>
<?php printFooter() ?>

</body>

</html>