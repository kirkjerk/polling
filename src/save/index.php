<!doctype html>
<?php
require "../util.php";
printTop();

$name = grabPost("name");
$password = encryptPassword(grabPost("password"));
$guts = json_decode(grabPost("guts"), true);

$filename = makeSafeFilename($name);

$path = $metadir . "/" . $filename . ".json";

$resultspath = $resultsdir . "/" . $filename;


if (file_exists($path)) {
    $data = json_decode(file_get_contents($path), true);
    $oldpassword = $data["password"];
    if ($password == $oldpassword) {
        $guts["password"] = $password;
        file_put_contents($path, json_encode($guts));
        print "<h2>Poll Updated!</h2>";
    } else {
        print "<h2>Can't Update</h2>";
        print "Either another poll exists with that name or you got the wrong password.<br><br>Please go back and try again.";
    }
} else {
    $guts["password"] = $password;
    file_put_contents($path, json_encode($guts));

    if (!is_dir($resultspath)) {
        // Create the directory with appropriate permissions
        mkdir($resultspath, 0755, true);
    }
}
$url = "$APPROOT/?" . $filename;
$editurl = "$APPROOT/edit/?" . $filename;
$viewurl = "$APPROOT/view/?" . $filename;
?>

Share this link for the poll: <a href="https://<?php echo $url ?>"><?php echo $url ?></a> <button onclick='navigator.clipboard.writeText("<?php echo "https://$url" ?>");'>click to copy</button><br>
<br><br>
You can edit this poll at <a href="https://<?php echo $editurl ?>"><?php echo $editurl ?></a>
<br><br>
You can view results for poll at <a href="https://<?php echo $viewurl ?>"><?php echo $viewurl ?></a>