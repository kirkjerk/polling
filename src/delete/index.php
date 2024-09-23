<!doctype html>
<?php
require "../util.php";
printTop();
    
    $filename = makeSafeFilename(grabPost("filename"));
    $password = encryptPassword(grabPost("password"));    
    
    $path = $metadir."/".$filename.".json";    
    $resultspath = $resultsdir."/".$filename;
    
    
    if (file_exists($path) && is_dir($resultspath)) {     
        
        
        
        $data = json_decode(file_get_contents($path), true);
        $oldpassword = $data["password"];
        if(!($password == $oldpassword)) {
            print "Incorrect password";
            die;   
        }
        $files = array_values(array_diff(scandir($resultspath), array('.', '..')));
        foreach($files as $file){
            
            $tocheck = str_replace(".json", "_json", $file);
            
            $check = $_POST[$tocheck];
            
            if($check){
                unlink($resultspath."/".$file);
            }
        }
    } else {
        print "Sorry, could not find that poll";
        die;
        
    }
?>
    Deleted Responses... <a href="../view/?<?php echo $filename ?>">Return to Results</a>
    <br><br>
    