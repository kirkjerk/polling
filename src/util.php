<?php

// Always use the absolute path to ROOTFOLDER/config.json, based on util.php's location
$configFilePath = __DIR__ . '/config.json';
$config = json_decode(file_get_contents($configFilePath), true);

$APPNAME = $config['APPNAME'];
$DATAROOT = $config['DATAROOT'];
$APPROOT = $config['APPROOT'];
$metadir = "$DATAROOT/meta";
$resultsdir = "$DATAROOT/results";



function printTop()
{
    global $APPROOT;
?>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <title><?php echo $APPROOT ?></title>
        <link rel="stylesheet" type="text/css" href="https://<?php echo $APPROOT ?>/css/style.css" />
        <script src="https://<?php echo $APPROOT ?>/js/Sortable.min.js"></script>
        <script src="https://<?php echo $APPROOT ?>/js/polling.js"></script>
        <meta name="viewport" content="width=600, initial-scale=1">


    </head>


    <body>
        <div class="content">
        <?php
    }

    function makeSafeFilename($filename)
    {
        // Replace spaces with hyphens
        $filename = str_replace(' ', '-', $filename);

        // Remove any characters that are not letters, numbers, hyphens, or underscores
        $filename = preg_replace('/[^A-Za-z0-9-_]/', '', $filename);

        // Optionally, convert to lowercase (uncomment the line below if needed)
        // $filename = strtolower($filename);

        return $filename ? $filename : "_";
    }


    function encryptPassword($passwd)
    {
        $passwordhash = crypt($passwd, "so-damn-salty");
        return $passwordhash;
    }


    function hasRequest($key)
    {
        return (!grabRequest($key) == "");
    }

    function grabRequest($key)
    {
        if (isset($_REQUEST[$key])) {
            return $_REQUEST[$key];
        } else {
            return "";
        }
    }

    function grabPost($key)
    {
        if (isset($_POST[$key])) {
            return $_POST[$key];
        } else {
            return "";
        }
    }

    #get the first thing ala dirname/?something
    function getGet()
    {
        if (!empty($_GET)) {
            $keys = array_keys($_GET);
            return $keys[0];
        }
        return "";
    }

    function isRequestEmpty()
    {
        return empty($_REQUEST);
    }

    function generateRandomKey($length = 8)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function generateNewPollId()
    {
        global $metadir;
        do {
            $newKey = generateRandomKey();
            $newPath = $metadir . '/' . $newKey . ".json";
        } while (file_exists($newPath));
        // At this point, $newPath is a directory that does not exist
        return $newKey;
    }
    function generateNewResponseId($filename)
    {
        global $resultsdir;
        do {
            $newKey = generateRandomKey();
            $newPath = $resultsdir . '/' . $filename . "/" . $newKey . ".json";
        } while (file_exists($newPath));
        // At this point, $newPath is a directory that does not exist
        return $newKey;
    }

    function getSafeFileNameFromParams()
    {
        $rawname = getGet();
        return makeSafeFilename($rawname);
    }

    function loadGutsFromParams($safefilename, $maskpassword)
    {
        global $metadir;
        $path = $metadir . "/" . $safefilename . ".json";
        if (!(file_exists($path) && is_file($path))) {
            return false;
        }
        $guts = json_decode(file_get_contents($path), true);
        if ($maskpassword) {
            maskPasswordInGuts($guts);
        }
        return $guts;
    }

    function printFooter()
    {
        global $APPROOT;
        print "<footer><i>Make your own poll or survey at <a href='https://$APPROOT'>$APPROOT</a></i></footer>";
    }

    function maskPasswordInGuts(&$guts)
    {
        $guts["password"] = "";
        return $guts;
    }

        ?>