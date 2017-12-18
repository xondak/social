<html class="no-js" lang="">
    <head>
        <?php
            include "cfg/paths.php";
            include PATHHEADER;
        ?>
        <style>
            #attached{
                display:none;
            }
            #idNumber{
                display:none;
            }
        </style>
    </head>
    <body>
    <?php
        if(isset($_GET["mattress"])){
            $reportFile = PATHTEMPLATE;
            $reportArray = json_decode(file_get_contents($reportFile),true);
            $reportFile = PATHADDITIONALTEMPLATE;
            $reportArray["mattress"] = json_decode(file_get_contents($reportFile),true);
        }
        else{
            $reportFile = PATHTEMPLATE;
            $reportArray = json_decode(file_get_contents($reportFile),true);
        }
        include PATHFORM;
    ?>
    </body>
</html>