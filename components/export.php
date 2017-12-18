<?php
include "../cfg/paths.php";
if( !empty( $_GET['report'] ) ){
    $reportName = PATHREPORTS . htmlspecialchars_decode($_GET['report']);
    $report = json_decode(file_get_contents($reportName), true);

    // Set the directory name to '1001 - Liberty Dresser'
    $dirName = TITLESHORT . " " . $report["info"]["idNumber"]["value"] . " - " . $report["info"]["manufacturer"]["value"] . " " . $report["info"]["item"]["value"];
    $zDriveDir = "/home/share/Documents/Damaged Photos/";
    $handle = $zDriveDir.$dirName;
    // echo "<pre>$zDriveDir<br>$handle<br>";
    // print_r($report);
    // echo "</pre>";
    mkdir($handle);
    chdir($handle);
    $i=0;
    foreach($report["attached"]["images"] as $key => $value)
    {
        $filename= $dirName . " Image " . $i . substr($value, -4); // Sets the file name to '1001 - Liberty Dresser Image 1.jpg'
        symlink($value, $filename);
        $i+=1;
        echo $filename;
        chmod($value, 0666);
        chmod($filename, 0666);
        // echo fileperms($value) . "<br>";
        // echo fileperms($filename) . "<br>";
    }
    echo '<h1>Images exported to <em>Z:\Damaged Photos\\' .$dirName . "</em></h1><br>";
    echo "<a href='" . PATHOFFICE . $_GET['report'] . "'>Go back to this report</a>";
    echo"<pre>";
    print_r($report);
    echo "path $handle";
    echo"</pre>";
    exit();
}
else{
    header("Location: " . PATHPUBLIC); /* Redirect browser */
    exit();
}
?>