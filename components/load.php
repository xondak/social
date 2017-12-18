<?php
include "../cfg/paths.php";

if(isset($_GET['report'])){ // Checks if the report variable is set in URL
    $reportFile = PATHREPORTS . $_GET['report']; // Concats file name with path
    if(is_readable($reportFile)){ // Checks if file is readable
        $reportArray = json_decode(file_get_contents($reportFile),true);
        print $reportArray[$nameKey1][$nameKey2]["label"];
    }
}
else{
    echo "Exception Reports";
}
?>