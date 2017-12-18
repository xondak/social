<!doctype html>
<html class="no-js" lang="">
    <head>
        <?php
            include "../cfg/paths.php";
            include PATHHEADER;
        ?>
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
        <?php $i=0;

        function renderDirectory($entry){
            if ($handle = opendir(PATHREPORTS)) {

                // Calls the directory.json file which defines how many columns and which data from those columns to display
                $dir = json_decode(file_get_contents(PATHDIRECTORY), true);

                // Begin the table
                echo "<table class='reports' id='office-reports'><tbody>";
                
                // Render the table header
                foreach($dir as $key => $value){
                    if($key == "header"){ // Render Row
                        echo "<tr>";
                        foreach($value as $k => $v){
                            echo "<th>$v</th>";
                        }
                        echo "</tr>";
                    }
                    while (false !== ($entry = readdir($handle))) {
                        // Ignore . and .. directory
                        if ($entry != "." && $entry != "..") {
                            // Set working directory
                            chdir(PATHREPORTS);
                            
                            // Decode JSON object to array
                            $tmpDecode = json_decode(file_get_contents($entry), true);

                            // If the report has been completed, move it to the completed archive.
                            if($tmpDecode["office"]["completed"]["value"] === "completed"){
                                $oldPath = PATHPRIVATE . $entry;
                                $newPath = PATHPRIVATE . "completed/" . $entry;
                                rename($oldPath, $newPath);
                            }
                            else{

                                // Just added the project to completed bin.
                                foreach($dir as $key => $value){
                                    if($key == "header"){
                                        // Ignore this key since we already rendered the header
                                    }
                                    else if($key === "state"){
                                        // Write the beginning row
                                        echo "<tr class='";
                                        foreach($value as $k => $v){
                                            extract($v);
                                            if($trueA == "meta" && isset($tmpDecode[$trueA][$trueB])){
                                                // This is sloppy, but it works
                                                echo $trueB . " ";
                                            }
                                            else if(isset($tmpDecode[$trueA][$trueB]["value"])){
                                                echo $tmpDecode[$trueA][$trueB]["value"] . " ";
                                            }
                                        }
                                        echo "'";
                                        echo ">";
                                    }
                                    else if($key == "row"){
                                        foreach($value as $k => $v){
                                            echo "<td class='" . $v["trueB"] . "'>";
                                            $link = null;
                                            extract($v);
                                            if( isset($v["link"]) ){
                                                // Generate link with variables
                                                echo "<a href='" . $link . $entry . "'>#" . $tmpDecode[$trueA][$trueB]["value"] . "</a>";// . $tmpDecode[$trueA][$trueB]["value"] . "</a>";
                                            }
                                            else {//if( isset($tmpDecode[$trueA][$trueB]["value"]) ){
                                                if(empty($tmpDecode[$trueA][$trueB]["value"])){
                                                    echo "<span class='default'>$default</span>";
                                                }
                                                else{
                                                    echo $tmpDecode[$trueA][$trueB]["value"];
                                                }
                                            }
                                            echo "</td>";
                                            // else{
                                            //     echo $v[$default];
                                            // }
                                        }
                                    }
                                    else if($key == "end"){
                                        echo "$value";
                                    }
                                }
                            }
                        }
                    }
                }
                echo "</table>";
                closedir($handle);
            }
            return $tmpDecode;
        }
        
        if(isset($_GET['report'])){ // Checks if the report variable is set in URL
            if(is_readable($reportFile)){ // Checks if file is readable
                include "../components/form.php"; // Calls the form that writes out the contents of the JSON object
            }
            else{ // Handles unreadable file error gracefully
                echo "<h1>That report is not available.</h1>";
                echo "<p>That report should be located at $reportFile but for some reason, I can't read that location.</p>";
            }
        }
        else{
            echo "<h1 id='directoryHeader'>" . FULLTITLE . "</h1>";
            // Call the renderDirectory functoin that outputs the reports in the directory
            
            
            renderDirectory($entry);
            
        }
        echo "<div class='completed-reports'>";
        echo "<a href='../completed'><span class='ion-ios-copy-outline'></span> See completed reports</a>";
        echo "</div>";

        ?>
        
        
        <script>
        
        var confirmElements = getParameterByName('confirm');
        console.log(confirmElements);
        var elementsToAdd = document.getElementsByClassName(confirmElements);
        console.log(elementsToAdd);
        elementsToAdd.classList.add("confirm");

        function getParameterByName(name, url) {
            if (!url) url = window.location.href;
            name = name.replace(/[\[\]]/g, "\\$&");
            var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, " "));
        }
        var sortType = 0;

        function sortTable() {
            var table, rows, switching, i, x, y, shouldSwitch;
            table = document.getElementById("office-reports");
            // Make a loop that will continue until no switching has been done:
            switching = true;
            // Start by saying: no switching is done:
            while (switching) {
                switching = false;
                rows = table.getElementsByTagName("TR"); /* Loop through all table rows (except the first, which contains table headers): */
                
                for (i = 1; i < (rows.length - 1); i++) {
                    // Start by saying there should be no switching:
                    shouldSwitch = false; /* Get the two elements you want to compare, one from current row and one from the next: */
                    x = rows[i].getElementsByTagName("TD")[0];
                    y = rows[i + 1].getElementsByTagName("TD")[0]; // Check if the two rows should switch place:
                    if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                        // I so, mark as a switch and break the loop:
                        shouldSwitch= true;
                        break;
                    }
                }
                if (shouldSwitch) {
                    /* If a switch has been marked, make the switch
                    and mark that a switch has been done: */
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                }
            }
        }

        function sortTableByCell(className) {
            var table, rows, switching, i, x, y, shouldSwitch;
            table = document.getElementById("office-reports");
            switching = true; /* Make a loop that will continue until no switching has been done: */
            while (switching) { // Start by saying: no switching is done:
                switching = false;
                rows = table.getElementsByTagName("TR"); /* Loop through all table rows (except the first, which contains table headers): */
                
                for (i = 1; i < (rows.length - 1); i++) {
                    // Start by saying there should be no switching:
                    shouldSwitch = false; /* Get the two elements you want to compare, one from current row and one from the next: */
                    x = rows[i].getElementsByClassName(className)[0];
                    // console.log(x);
                    y = rows[i + 1].getElementsByClassName(className)[0]; // Check if the two rows should switch place:
                    if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                        // I so, mark as a switch and break the loop:
                        shouldSwitch= true;
                        break;
                    }
                }
                if (shouldSwitch) {
                    /* If a switch has been marked, make the switch
                    and mark that a switch has been done: */
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                }
            }
        }

        

        </script>
        <!-- <script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.12.0.min.js"><\/script>')</script>
        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>-->
    </body>
</html>