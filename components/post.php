<?php
include "../cfg/paths.php";
//debugPre($_POST);

// THIS WORKS
function moveUploadedImages($pathPhotos, $reportId){
    // Find image count

    //debugPre($_FILES);
    
    if(is_array($_POST["attached"]["images"])){
        end($_POST["attached"]["images"]);
        $lastimage = key($_POST["attached"]["images"]);
        $lastimage = substr($lastimage, 5)  + 1;
        echo "<br>Last Image: $lastimage<br>";
    }
    else{
        $lasimage = 0;
        echo "<br>Last Image: 0<br>";
    }

    $imageArray = $_POST['attached']['images'];

    //Sift through files
    foreach($_FILES as $key => $value){
        
        // Catch irrelevant uploads
        if($value["error"] !== 4){

            // Create easy pathname
            $pathname = PATHPHOTOS . $reportId . "/";
            $target = $pathname . str_replace(' ', '', $_FILES[$key]["name"]);
            echo "<br>Target: $target<br>";
            
            // Make directory for images
            if( !file_exists($pathname) ){
                echo "Making directory";
                mkdir($pathname, 0770, true);
            }

            // Determine file extension
            $imageFileType = pathinfo($target,PATHINFO_EXTENSION);
            
            // Get tmp location, move file.
            $tmpName = $_FILES[$key]["tmp_name"];
            move_uploaded_file($tmpName, $target);

            //$index = substr($index,-1);
            //echo "<br>$index<br>";
            // print_r($tmp_name . " " . $target . "<br>");
            // $test_name = $target_dir . $target;
            //$array[$index] = $target;
            //return $target;
            $imageArray[$key] = $target;  
            //debugPre($imageArray);
        }

    }
  
    return $imageArray;
}

// THIS WORKS
function uniqueIdNumber(){
    echo "<br>-----ID GENERATION-----<br>";
    $idDatabase = json_decode(file_get_contents(PATHIDNUMBER), true);
    echo "<pre>";
    print_r($idDatabase);
    echo "</pre>";
    $newId = end($idDatabase) + 1;
    print_r($newId);
    array_push($idDatabase, $newId);
    print_r($idDatabase);
    file_put_contents(PATHIDNUMBER,json_encode($idDatabase));
    return $newId;
    echo "<br>-----END ID GENERATION-----<br>";
}

// THIS WORKS
function updateReport($emailToPush, $pathReports, $pathPhotos, $imageArray){
    // A little housekeeping
    unset($_POST['email']);
    unset($_POST['eSend']);
    unset($_POST['submit']);
    unset($_POST['noteAdd']);

    $file = PATHREPORTS . htmlspecialchars($_GET['report']);
    //echo $file;
    
    foreach($_POST as $key => $value){
        if(is_array($value)){
            foreach($value as $k => $v){
                if(is_array($v)){
                    foreach($v as $p => $q){
                        if($p == "data"){
                            //echo "[P] $p [Q] $q <br>";
                            $unserial = unserialize($q);
                            $_POST[$key][$k][$p] = $unserial;
                            // print_r($_POST[$key][$k][$p]);
                        }
                    }
                }
            }
        }
    }

    $formAndFile = $_POST;

    //debugPre($imageArray);
    
    // Clean out old image data, then add new list of images
    unset($formAndFile["attached"]["images"]);
    $formAndFile["attached"]["images"] = $imageArray;
    
    if(isset($emailToPush)){
        $formAndFile = $formAndFile + $emailToPush;
    }

    $json = json_encode( $formAndFile );
    file_put_contents( $file, $json );
    //debugPre($formAndFile);
    if(!isset($emailToPush)){
        $redirectTo = "Location: " . PATHHREF . "office/?confirm=".$formAndFile['info']['idNumber']['value'];
        echo $redirectTo;
        header($redirectTo); /* Redirect browser */
    }
    else{
        // This should redirect the user back to the note add on the same form
        $returnToReport = "Location: " . PATHHREF . "office/?report=" . htmlspecialchars($_GET['report']) . "#noteAdd";
        echo $returnToReport;
        header($returnToReport);
    }
    exit();
}

function debugPre($value){
    echo "<pre>$value<br>";
    print_r($value);
    echo "<br></pre>";
}

function sendEmail(){
    require_once "Mail.php";
    require_once "Mail/mime.php";
    // see http://pear.php.net/manual/en/package.mail.mail-mime.php
    // for further extended documentation on Mail_Mime

    $from = "info@perrysfurniture.com";
    $to = $_POST['email']['send'];
    $subject = $_POST['email']['subject'] ." #".$_POST['info']['idNumber']['value']." ".$_POST['info']['manufacturer']['value'];
    $text = $_POST['email']['body'];
    $html = $_POST['email']['body'] . "<br><br><hr>This is an automated email sent from <a href='" . EMAILURL . "?report=" . htmlspecialchars($_GET['report']) . "'>Exception Report #" . $_POST['info']['idNumber']['value'] . "</a><br><br><b>Do not reply to this email</b>";
    $crlf = "\n";
    // create a new Mail_Mime for use
    $mime = new Mail_mime($crlf);
    // define body for Text only receipt
    $mime->setTXTBody($text);
    // define body for HTML capable recipients
    $mime->setHTMLBody($html);

    // specify a file to attach below, relative to the script's location
    // if not using an attachment, comment these lines out
    // set appropriate MIME type for attachment you are using below, if applicable
    // for reference see http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types

    //$file = "attachment.jpg";
    //$mimetype = "image/jpeg";
    //$mime->addAttachment($file, $mimetype); 

    // specify the SMTP server credentials to be used for delivery
    // if using a third party mail service, be sure to use their hostname
    $host = "gator3323.hostgator.com";
    $username = "info@perrysfurniture.com";
    $password = "20Lily17";

    $headers = array ('From' => $from,
        'To' => $to,
        'Subject' => $subject);
    $smtp = Mail::factory('smtp',
        array ('host' => $host,
        'auth' => true,
        'username' => $username,
        'password' => $password));


    $body = $mime->get();
    $headers = $mime->headers($headers); 

    $mail = $smtp->send($to, $headers, $body);

    if (PEAR::isError($mail)) {
        echo("

    " . $mail->getMessage() . "
    ");
    } else {
        echo("Message successfully sent!");
    }
}

// Check if the submit button has been pressed
if( isset( $_POST['submit'] ) ){
    
    // Check if ?report= is set or not
    if ($_GET['report'] == null){
        
        echo "<h1>Submitting report...</h1>";

        $reportId = uniqueIdNumber();
        //$reportId = "1002";
        
        //debugPre($_POST);
        foreach($_POST as $key => $value){
            if(is_array($value)){
                foreach($value as $k => $v){
                    if(is_array($v)){
                        foreach($v as $p => $q){
                            if($p == "data"){
                                //echo "[P] $p [Q] $q <br>";
                                $unserial = unserialize($q);
                                $_POST[$key][$k][$p] = $unserial;
                                // print_r($_POST[$key][$k][$p]);
                            }
                        }
                    }
                }
            }
        }
        
        // Merge the two filename arrays
        $imageArray = moveUploadedImages(PATHPHOTOS, $reportId);

        // Clean $_POST data
        unset($_POST['submit']);
        unset($_POST['email']);

        // Change from a new form to completed form
        $_POST["meta"]["button"] = "update";

        // Convert $_POST to malliable array
        $formAndFile = $_POST;

        // Enforce ID number
        $formAndFile["info"]["idNumber"]["value"] = $reportId;
        
        // Generate filename
        $fileNameConcat = $reportId .",". date("Y-m-d-h-i-sa") . ".json";        
        
        // Clean out old image data, then add new list of images
        unset($formAndFile["attached"]["images"]);
        $formAndFile["attached"]["images"] = $imageArray;

        // Concat directory and filename
        $handle = PATHREPORTS . $fileNameConcat;
        echo "<br>$handle<br>";

        // Make a new file
        touch($handle);

        // Encode and save file
        $json = json_encode( $formAndFile );
        file_put_contents( $handle, $json);

        // Do a little error testing
        if(is_readable($handle)){
            echo "Created JavaScript Object<br>";
        }
        else{
            echo "<h2>Submission failed.</h2>";
            die("Failed to create " . $handle . " Javascript Object.<br>Please press the back button and try again.");
        }

        // Offer a helpful link
        echo "<h2>This report is number <a href='" . PATHHREF . "office/?report=" . $fileNameConcat . "'>$reportId.</a></h2><p>Please use this number to <em>keep track<em> of the item in this report.</em></p><p>Or you can <a href='/exception-reports'>file another report</a>.</p>";
    }
    else{
        echo "<h2>Updating report...</h2>";        
        // Merge the two filename arrays
        $imageArray = moveUploadedImages(PATHREPORTS, $_POST["info"]["idNumber"]["value"]);
        
        //debugPre($formAndFile);

        updateReport(null, PATHREPORTS, PATHPHOTOS, $imageArray);

        //debugPre($_POST);

    }
    // echo $handle." Success!";
    $redirectTo = "Location: ". PATHHREF . "office/";
    //header($redirectTo); /* Redirect browser */
    exit();
}
else if(isset($_POST['eSend']) || isset($_POST['noteAdd'])) {
    function died($error) {
        // your error code can go here
        echo "<h1>Oops, something went wrong</h1><div>This email wasn't sent.<br /><br />";
        echo $error."<br /><br />";
        echo "Please go back and fix these errors.<br /><br />";
        die();
    }
    $_POST['office']['read']['value'] = "notRead";
    if($_POST["email"]["subject"] == "null"){
        died("You didn't specify a recipient.");
    }
    //Prints -- not code
    echo "<h1>Preparing to send email...</h1>";
    
    $emailTo = $_POST["email"]["send"]; // Creates variable that contains email address
    //echo "<h1>Hey there, " . $_POST["email"]["send"] . "</h1>";
    $email_subject = $_POST['email']['subject']; // Creates subject line
    $emailsSent = $_POST["meta"]["notes"]; // Pushes the variable to the array
    //echo $email_to;

    if(empty($emailsSent)){
        echo "Counter is null";
        $emailsSent = 1; // Begins counter
    }
    else{
        $emailsSent++;
        echo "Counter is adding $emailsSent";
    }
    
    //Composes email message:
    $email_message = $_POST['email']['body'];
    // . " " . "<br><br><hr>This email is in regard to <a href='/exception-reports/office/?report=" . htmlspecialchars($_GET['report']) . "'>Exception Report #" . $_POST['idNumber'] . "</a>"
    $headers = 'From: '.$email_from."\r\n".
    'Reply-To: '.$email_from."\r\n" .
    'X-Mailer: PHP/' . phpversion();
    //mail($emailTo, $email_subject, $email_message, $headers);
    // Logic to add archive of email:
    echo "$email_message<br>"; // Writes preview to screen

    $emailChain = "email" . $emailsSent; // Create key 'email' with number appended.
    $date = date('m-d-Y h:i:s a', time());
    $_POST["meta"]["notes"] = $emailsSent;
    $finalEmail = array('meta' => array(
                            'notes' => $emailsSent
                        ),
                        $emailChain => array(
                            'emailDate' => $date,
                            'emailTo' => $emailTo,
                            'emailTopic' => $email_subject,
                            'emailSubject' => $email_subject ." #".$_POST['idNumber']['value']." ".$_POST['info']['manufacturer']['value'],
                            'emailMessage' => $email_message
                        )
                    );
    //debugPre($finalEmail);
    if($_POST['email']['do']=="on"){
        sendEmail();
    }
    $imageArray = moveUploadedImages(PATHPHOTOS, $_POST["info"]["idNumber"]["value"]);
    
    //debugPre($_POST);
    updateReport($finalEmail, PATHREPORTS, PATHPHOTOS, $imageArray);
    //debugPre($_POST);
}
else{
    $redirectTo = "Location: " . PATHHREF;
    //header($redirectTo); /* Redirect browser */
    exit();
}


?>
</body>