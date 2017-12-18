<?php 
//$reportID = htmlspecialchars($_GET['report']);
include "../cfg/paths.php";
$reportID = PATHPRIVATE . $_GET['report'];
$deleteNote = htmlspecialchars($_GET['delete']);
$workingReport = json_decode(file_get_contents($reportID),true);
unset($workingReport[$deleteNote]);
$json = json_encode($workingReport);
file_put_contents( $reportID, $json);
$goTo = "Location: " . PATHOFFICE . "?report=" . $_GET['report'] . "#noteAdd";
header($goTo); /* Redirect browser */
exit;
?>