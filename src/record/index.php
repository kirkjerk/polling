<!doctype html>
<?php
require "../util.php";
printTop();

$filename = makeSafeFilename(grabPost("_id"));

$path = $metadir . "/" . $filename . ".json";

$resultspath = $resultsdir . "/" . $filename;


if (file_exists($path) && is_dir($resultspath)) {
    $meta = json_decode(file_get_contents($path), true);

    if($meta["closed"]) {
        print "Poll is closed!";
        die;
    }


    $entries = $meta["entries"];

    $results = array();

    foreach ($entries as $entry) {
        $id = $entry['id'];
        if (isset($_POST[$id])) {
            $results[$id] = $_POST[$id];
        }
    }

    $results["_time"] = time();

    $fileout = generateNewResponseId($filename);

    $outputpath = $resultspath . "/" . $fileout . ".json";

    file_put_contents($outputpath, json_encode($results));
} else {
    print "Sorry, could not find that poll";
    die;
}
$url = "$APPROOT/?" . $filename;
$editurl = "$APPROOT/edit/?" . $filename;
$viewurl = "$APPROOT/view/?" . $filename;
?>
Thanks for your response!
<br><br>
You can view results for poll at <a href="https://<?php echo $viewurl ?>"><?php echo $viewurl ?></a>