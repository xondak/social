<?php

function notArray($key, $value) {
    switch($key){
        case "button":
            echo "<button name='submit' value='$value'>Submit</button>";
            echo "<input type='hidden' name='link' value='Update'></input>";
            break;
        case "images":
            renderImages($key, $value, PATHHREF);
            break;
        default:
            echo "<input type='hidden' name='$key' value='$value'></input>";
            break;
    }
    return true;
}

function renderImages($imgname, $array, $pathCount){
    foreach($array as $index => $path){
        $docType = substr($path, -3);
        $docType = strtolower($docType);
        $thumbArray = array('jpg', 'peg', 'png', 'svg', 'bmp', 'gif');
        $ref = substr($path,PATHCOUNT);
        echo "<input type='hidden' name='attached[$imgname][$index]' value='$path'></input>";
        $imagePath = substr($path,PATHCOUNT);
        if(in_array($docType, $thumbArray)){
            echo "<div class='image-crop' style='background:url(" . $imagePath . "); background-size: cover;'>";
            echo "<a href='". $imagePath ."' target='_blank'></a></div>";
        }
        else{
            echo "<a href='".$imagePath."' class='attached-document' target='_blank'>";
            $docName = explode("/", $path);
            $docName = end($docName);
            echo "<span class='doc ion-document-text'></span><br>" . $docName . '</a>';
            }
        }
    }

echo "<form action='" . PATHPOST;
if($reportArray["meta"]["button"] == "update"){
    echo "?report=" . htmlspecialchars($_GET['report']);
}
echo "' method='post' enctype='multipart/form-data'>";

foreach($reportArray as $key => $value){
    if( !is_array($value) ){
        notArray($key, $value);
    }
    else if($key !="meta"){
        if(preg_match('/^email/', $key)){
            echo "";
        }
        else{
            // echo "<br>Doesn't match email";
            echo "<fieldset id='$key'>";
            foreach($value as $k => $v){
                if($k == "header"){
                    echo "<h1>$v</h1>";
                    echo "<input type='hidden' name='$key"."[$k]' value='$v'></value>";
                }

                else{

                    // Generate easy variables
                    $label = $v['label'];
                    $type = $v['type'];
                    $name = $v['name'];
                    $placeholder = $v['placeholder'];
                    $val = $v['value'];
                    $data = $v['data'];
                    $readonly = $v['readonly'];

                    // Render each input group
                    if($v["type"] == "text" || $v["type"] == "date" || $v["type"] == "tel"){
                        // Render each input group
                        echo "<div class='input-group $k'>";
                        echo "  <label for='$name'>$label</label>";
                        echo "  <input type='$type' id='$k' name='$name" . "[value]' placeholder='$placeholder' value='$val'";
                        if(isset($readonly) || EDITABLE === false && isset($_GET["report"])){
                            echo "readonly";
                        }
                        echo "></input>";
                        echo "</div>";
                    }

                    // Special case for radio inputs since they have multiple options
                    else if($v["type"] == "radio"){
                        echo "<div class='radio-group $k'>";
                        foreach($data as $priv => $pub){
                            echo "  <label for='$priv'>$pub</label>";
                            echo "  <input type='radio' id='$priv' name='$name" . "[value]'";
                            if($val == $priv){
                                echo " checked=''";
                            }
                            echo "value='$priv'></input>";
                        }
                        echo "</div>";
                    }

                    // Special case for checkboxes, though I guess this could be merged into the first input group.
                    else if($v["type"] == "checkbox"){
                        echo "<div class='checkbox-group $k'>";
                        echo "  <label for='$name'>$label</label>";
                        echo "  <input type='checkbox' name='$name" . "[value]' id='$name' ";
                        if(strtolower($val) == strtolower($k)){
                            echo "checked";
                        }
                        echo " value='$k'></input>";
                        echo "</div>";
                    }

                    // Special case for textareas since they're a unique tag.
                    else if($v["type"] == "textarea"){
                        echo "<div class='input-group $k'>";
                        echo "  <label for='$name'>$label</label>";
                        echo "  <textarea id='$k' name='$name" . "[value]' placeholder='$placeholder' rows='4' cols='20'>$val</textarea>";
                        echo "</div>";
                    }

                    // Special case for images.
                    else if($key == "attached"){
                        if( is_array($v) ){
                            echo "<div class='img-wrap'>";
                            renderImages($k, $v,PATHCOUNT);
                            echo "</div>";
                        }
                        else{
                            echo "<p>There are no images attached to this report.</p>";
                        }
                    }

                    // This attaches all metadata to each $key
                    if(is_array($v)){
                    foreach($v as $n => $q){
                        if($n != "value"){
                            echo "<input type='hidden' name='$name" . "[$n]' value='";
                            if(is_array($q)){
                                echo serialize($q);
                            }
                            else{
                                echo $q;
                            }
                            echo "'></input>";

                        }
                    }} // End metadata attachment

                }

            }

        }
        echo "</fieldset>";
    }

}
echo "<div class='input-group meta'>";
foreach ($reportArray['meta'] as $key => $value){
    if($value == "update"){
        echo "  <button id='submit' name='submit' value='update'><span class='doc ion-ios-cloud-upload-outline'></span><br>Update</button>";
        echo "  <input type='hidden' name='meta[button]' value='update'></input>";
        echo "  <a href='" . PATHEXPORT .  "?report=" . $_GET["report"] . "' id='export' class='button'><span class='doc ion-ios-download-outline'></span><br>Export to Z:\ Drive</a>";
    }
    else if($value == "submit"){
        echo "  <button id='submit' name='submit' value='submit'><span class='doc ion-ios-cloud-upload-outline'></span><br>Submit</button>";
        echo "  <input type='hidden' name='meta[button]' value='update'></input>";
    }
    else{
        echo "<input type='hidden' name='meta[$key]' value='$value'></input>";
    }
}
echo "</div>";
?>
<fieldset id="upload">
    <div class='input-group new-upload'>
        <h1>Attachments</h1>
        <?php
            if( is_array( $reportArray["attached"]['images'] )){
                end($reportArray["attached"]['images']);
                $lastimage = key($reportArray["attached"]["images"]);
                $lastimage = substr($lastimage, 5)  + 1;
            }
            else{
                $lastimage = 1;
            }
        ?>
        <label for='image<?php echo $lastimage ?>'>Upload</label>
        <input type='file' id='additionalUploader' name='image<?php echo $lastimage ?>' class='uploader'></input>
        <a onclick='MoreImages();' href='#photos' class='ion-plus' id='addNewFileUpload'></a>
        <script>
            var toggle = 0;
            var x = document.getElementsByClassName("uploader");
            var i;
            var uploadField = document.getElementById("additionalUploader").name;
            var imageIndex = Number(uploadField.substring(5)) + 1;
            //console.log(toggle);
            for (i = 0; i < x.length; i++) {
                if (x[i].type == "file") {
                    let fileUploadNumber = str.charAt(str.length-1);
                }
            }
            function MoreImages(){
                document.getElementById("addNewFileUpload").insertAdjacentHTML('beforebegin','<input type="file" name="image' + imageIndex + '" /><br>');
                imageIndex+=1;
            }
            function enableSelect(){
                var selectionBox = document.getElementById('emailList');
                if(toggle==0){
                    selectionBox.classList.remove("disabled");
                    toggle=1;
                }
                else{
                    selectionBox.classList.add("disabled");
                    toggle=0;
                }
            }
            function deleteEmail($id){
                return confirm('Are you sure?');
            }
        </script>

    </div>
</fieldset>
<div id="correspondance" <?php if(isset($_GET['report'])) {echo "class='seen'";} ?>>
    <?php

    // Hard coding for email rendering
    function renderEmail($key, $value){
        $reportID = htmlspecialchars($_GET['report']);             
        echo "<div id='$key' class='email " . substr($value['emailTo'], 0,-20) . " " . $value['emailTopic'] . "'><a href='" . PATHDELETENOTE . "'?report=$reportID&delete=$key' class='delete ion-android-delete' onclick='return confirm(\"Are you sure you want to delete this note?\");'></a>";
        foreach($value as $k => $v){
            echo '<input type="hidden" name="' . $key .'[' . $k . ']" value="' . $v . '"></input>';
        }
        echo "<div class='emailDate'><span class='ion-ios-calendar-outline'></span> ".$value['emailDate']."</div>";
        echo "<div class='emailTo'><span class='ion-ios-people-outline'></span> ".$value['emailTo']."</div>";
        echo "<div class='emailSubject'><span class='ion-ios-pricetag-outline'></span> ".$value['emailSubject']."</div>";
        echo "<div class='emailMessage'>".$value['emailMessage']."</div>";
        echo "</div>";
    }

        // Look at all the entries
        foreach($reportArray as $key => $value){
            // If it starts with 'email'...
            if(preg_match('/^email/', $key)){
                renderEmail($key, $value);
            }
        }

    // echo "<pre>";
    // print_r($entriesSubmitted);
    // echo "</post>";
    ?>
    <div id="noteAdd" class="email">
            <h2>Add a note/send an email:</h2>
            <div id="sendblock"><label for='email[do]'>Send as Email</label>
                <input type="checkbox" name="email[do]" onclick="enableSelect();"></input>
                <select id="emailList" name="email[send]" class="disabled">
                    <?php include PATHEMAILLIST; ?>
                </select>
            </div>
            <!--<input id="noter" type="hidden" name="email[send]" value="note"></input>-->
            <select name="email[subject]">
                <?php include PATHSUBJECT; ?>
            <textarea name="email[body]" placeholder="Add your note here."></textarea><br>
            <input type="text" name="email[trackingNumber]" placeholder="Tracking Number for Returns"></value>
            
            <button name="noteAdd" value="1" type="submit">Send</button>
    </div>
</div>
</form>