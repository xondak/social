<meta charset="utf-8">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<title>
<?php
    if(isset( $_GET['report']) ){ // Checks if the report variable is set in URL
        $reportFile = $pathReports . $_GET['report']; // Concats file name with path
        if(is_readable($reportFile)){ // Checks if file is readable
            $reportArray = json_decode(file_get_contents($reportFile),true);
            print $reportArray["customer"]["name"]["value"] . " $titleShort";
        }
    }
    else{
        echo FULLTITLE;
    }
?>
</title>
<meta name="description" content="">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="apple-touch-icon" href="apple-touch-icon.png">
<!-- Place favicon.ico in the root directory -->

<!-- <link rel="stylesheet" href="css/normalize.css"> -->
<link rel="stylesheet" href="/<?php echo PATHCSSCOMMON; ?>">
<link rel="stylesheet" href="<?php echo PATHCSSFORM; ?>?v=.1">
<link rel="stylesheet" href="/css/ionicons.min.css">
<!-- <script src="js/vendor/modernizr-2.8.3.min.js"></script> -->