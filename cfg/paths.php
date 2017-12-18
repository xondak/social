<?php

//-- Basic Settings --//

$URL = "http://192.168.1.2";

//  ALWAYS INCLUDE A TRAILING FORWARD SLASH

// Absolute internal paths
$path = "/home/samba/dev/web/perrys-web/";
$pathPub = "basic/";
$pathPriv = $path . $pathPub;

$pubReports = "reports/";
$pubPhotos = "photos/";
$pubComponents = "components/";

$pathCfg = $pathPriv . "cfg/";
$pathReports = $pathPriv . $pubReports;
$pathPhotos = $pathPriv . $pubPhotos;
$pathComp = $pathPriv . $pubComponents;

// Individual config files
$pathTemplate = $pathCfg . "template.json";
$pathAdditionalTemplate = $pathCfg . "mattress-template.json";
$pathDirectory = $pathCfg . "directory.json";
$pathId = $pathCfg . "reportid.db";
$pathEmail = $pathCfg . "emaillist.php";
$pathSubject = $pathCfg . "subject.php";
$pathEmailList = $pathCfg . "emaillist.php";
$pathSubject = $pathCfg . "subject.php";
$pathCommonCSS = $URL . "css/common.css";
$pathFormCSS = $pathPub . "css/style.css";

// Individual component files
$pathForm = $pathComp . "form.php";
$pathDelNote = $pathComp . "deletenote.php";
$pathForm = $pathComp . "form.php";
$pathHeader = $pathComp . "header.php";

// Individual public components
$pathPost = "/" . $pathPub . $pubComponents . "post.php";
$pathExport = "/" . $pathPub . $pubComponents . "export.php";

// Absolute hyperlinks
$pathCount = strlen($path) - 1;
$pathHref = substr($path, $pathCount) . $pathPub;
$pathPhotoHref = $pathHref . "photos/";
$pubOffice = $pathHref . "office/";

$hrefEmail = $URL . $pubOffice . "?report=";

// Variable to access
$titleContent = "Testing";
$titleShort = "Test";
$nameKey1 = "info";
$nameKey2 = "item";

// Deine global internals
define("PATHPUBLIC",$pathPub);
define("PATHPRIVATE",$pathPriv);
define("PATHREPORTS",$pathReports);
define("PATHPHOTOS",$pathPhotos);
define("PATHCONFIG",$pathConfig);
define("PATHCOMONENTS",$pathComp);
define("PATHHEADER",$pathHeader);
define("PATHHREF",$pathHref);

// Define global files
define("PATHFORM",$pathForm);
define("PATHCOUNT",$pathCount);
define("PATHPOST",$pathPost);
define("PATHEXPORT",$pathExport);
define("PATHEMAILLIST",$pathEmailList);
define("PATHDELETENOTE",substr($pathDelNote,$pathCount));

// Set global config files
define("PATHTEMPLATE", $pathTemplate);
define("PATHDIRECTORY",$pathDirectory);
define("PATHADDITIONALTEMPLATE",$pathAdditionalTemplate);
define("PATHOFFICE",$pubOffice);
define("PATHSUBJECT",$pathSubject);
define("PATHIDNUMBER",$pathId);
define("PATHCSSCOMMON",$pathCommonCSS);
define("PATHCSSFORM",$pathFormCSS);

define("EMAILURL",$hrefEmail);
define("FULLTITLE",$titleContent);
define("TITLESHORT",$titleShort);

// EDITABLE changes the rendering behavior.
// If TRUE, the engine displays all values in input fields
// If FALSE, the engine displays all type text, date, and numeric
// values as static text in <p> blocks and generates an 'edit' button
define("EDITABLE",true);

?>