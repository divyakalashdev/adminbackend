<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'DB.class.php';
$db = new DB();

/* ============================== Profile Operations Starts here ==============================*/
if(isset($_POST['save_profile']) && $_POST['save_profile'] == "save"){
    $catid = trim(strip_tags($_POST['catid']));
    $tags = trim(strip_tags($_POST['tags']));
    $name = trim(strip_tags($_POST['name']));
    $description = trim(strip_tags($_POST['description']));
    
    $data = array();
    $data['profile_type'] = $catid;
    $data['name'] = $name;
    $data['description'] = $description;
    
    if((isset($_FILES['profilepic']['name']) && $_FILES['profilepic']['name'] != '')){
        $maxsize = 5242880; // 5MB
        $name = $_FILES['profilepic']['name'];
        $target_image_dir = "profiles/pics/";
        $extension = strtolower(pathinfo($_FILES["profilepic"]["name"], PATHINFO_EXTENSION));
        $target_image_file = $target_image_dir . time().'.'.$extension;
        // Valid file extensions
        $extensions_arr = array("jpg","jpeg","png");
        
        // Check extension
        if( in_array($extension,$extensions_arr) ){
        
            // Check file size
            if(($_FILES['profilepic']['size'] >= $maxsize) || ($_FILES["profilepic"]["size"] == 0)) {
                $errorMsg = "File too large. File must be less than 5MB.";
                $imgsuccess = false;
            }else{
                // Upload
                if(move_uploaded_file($_FILES['profilepic']['tmp_name'], $target_image_file)){
                    $data['avatar'] = $target_image_file;
                    $imgsuccess = true;
                }
            }
        
        }else{
            $errorMsg = "Invalid file extension.";
            $imgsuccess = false;
        }
    }
    
    if((isset($_FILES['poster']['name']) && $_FILES['poster']['name'] != '')){
        $maxsize = 5242880; // 5MB
        $name = $_FILES['poster']['name'];
        $target_image_dir = "profiles/posters/";
        $extension = strtolower(pathinfo($_FILES["poster"]["name"], PATHINFO_EXTENSION));
        $target_file = $target_image_dir . time().'.'.$extension;
        // Valid file extensions
        $extensions_arr = array("jpg","jpeg","png");
        
        // Check extension
        if( in_array($extension,$extensions_arr) ){
        
            // Check file size
            if(($_FILES['poster']['size'] >= $maxsize) || ($_FILES["poster"]["size"] == 0)) {
                $errorMsg = "File too large. File must be less than 5MB.";
                $imgsuccess = false;
            }else{
                // Upload
                if(move_uploaded_file($_FILES['poster']['tmp_name'], $target_file)){
                    $data['poster'] = $target_file;
                    $imgsuccess = true;
                }
            }
        
        }else{
            $errorMsg = "Invalid file extension.";
            $imgsuccess = false;
        }
    }
    
    $pid = $db->insert('profiles', $data);
    if($pid){
        $db->insert('tags', array('profile_id' => $pid, 'tags' => $tags));
        echo "OK";
    }else{
        echo "FAILED";
    }
}else if(isset($_POST['update_profile']) && $_POST['update_profile'] == "update"){
    $pid = trim(strip_tags($_POST['pid']));
    $tags = trim(strip_tags($_POST['tags']));
    $catid = trim(strip_tags($_POST['catid']));
    $name = trim(strip_tags($_POST['name']));
    $description = trim(strip_tags($_POST['description']));
    $con['id'] = $pid;
    
    $conditions['where'] = array('id' => $pid);
    $conditions['return_type'] = 'single';
    $profile = $db->getRows('profiles', $conditions);
    if($profile != null){
    
        $data = array();
        $data['profile_type'] = $catid;
        $data['name'] = $name;
        $data['description'] = $description;
        
        if((isset($_FILES['profilepic']['name']) && $_FILES['profilepic']['name'] != '')){
            $maxsize = 5242880; // 5MB
            $name = $_FILES['profilepic']['name'];
            $target_image_dir = "profiles/pics/";
            $extension = strtolower(pathinfo($_FILES["profilepic"]["name"], PATHINFO_EXTENSION));
            $target_image_file = $target_image_dir . time().'.'.$extension;
            // Valid file extensions
            $extensions_arr = array("jpg","jpeg","png");
            
            // Check extension
            if( in_array($extension,$extensions_arr) ){
            
                // Check file size
                if(($_FILES['profilepic']['size'] >= $maxsize) || ($_FILES["profilepic"]["size"] == 0)) {
                    $errorMsg = "File too large. File must be less than 5MB.";
                    $imgsuccess = false;
                }else{
                    // Upload
                    if(move_uploaded_file($_FILES['profilepic']['tmp_name'], $target_image_file)){
                        if(file_exists("./".$profile['avatar'])){
                            unlink("./".$profile['avatar']);
                        }
                        $data['avatar'] = $target_image_file;
                        $imgsuccess = true;
                    }
                }
            
            }else{
                $errorMsg = "Invalid file extension.";
                $imgsuccess = false;
            }
        }
        
        if((isset($_FILES['poster']['name']) && $_FILES['poster']['name'] != '')){
            $maxsize = 5242880; // 5MB
            $name = $_FILES['poster']['name'];
            $target_image_dir = "profiles/posters/";
            $extension = strtolower(pathinfo($_FILES["poster"]["name"], PATHINFO_EXTENSION));
            $target_file = $target_image_dir . time().'.'.$extension;
            // Valid file extensions
            $extensions_arr = array("jpg","jpeg","png");
            
            // Check extension
            if( in_array($extension,$extensions_arr) ){
            
                // Check file size
                if(($_FILES['poster']['size'] >= $maxsize) || ($_FILES["poster"]["size"] == 0)) {
                    $errorMsg = "File too large. File must be less than 5MB.";
                    $imgsuccess = false;
                }else{
                    // Upload
                    if(move_uploaded_file($_FILES['poster']['tmp_name'], $target_file)){
                        if(!empty($profile['poster']) && file_exists("./".$profile['poster'])){
                            unlink("./".$profile['poster']);
                        }
                        $data['poster'] = $target_file;
                        $imgsuccess = true;
                    }
                    
                }
            
            }else{
                $errorMsg = "Invalid file extension.";
                $imgsuccess = false;
            }
        }
    
        if($db->update('profiles', $data, $con)){
            $checkcon = array(
                'where' => array('profile_id' => $pid),
                'return_type' => 'single'
                );
            $tag = $db->getRows('tags', $checkcon);
            $tcon['profile_id'] = $pid;
            if(!empty($tag)){
                $db->update('tags', array('tags' => $tags), $tcon);
            }else{
                $db->insert('tags', array('profile_id' => $pid, 'tags' => $tags));
            }
            echo "OK";
        }else{
            echo "FAILED";
        }
    }else{
        echo "FAILED";
    }
} else if(isset($_POST['delete_profile']) && $_POST['delete_profile'] == "delete"){
    $pid = trim(strip_tags($_POST['delete_profile_id']));
    
    $conditions['where'] = array('id' => $pid);
    $conditions['return_type'] = 'single';
    $profile = $db->getRows('profiles', $conditions);
    if($profile != null){
        $con['id'] = $pid;
        if(file_exists("./".$profile['avatar']) || file_exists("./".$profile['poster'])){
            $d = false;
            if(!empty($profile['avatar'])){
                unlink("./".$profile['avatar']);
                $d = true;
            }
            if(!empty($profile['poster'])){
                unlink("./".$profile['poster']);
                $d = true;
            }
            if($d && $db->delete('profiles', $con)){
                $con['profile_id'] = $pid;
                $db->delete('profiles', $con);
                echo "OK";
            }else{
                echo "FAILED";
            }
        }else{
            echo "FAILED";
        }
    }else{
        echo "FAILED";
    }
} else if(isset($_POST['profile_posters']) && $_POST['profile_posters'] == "posters" && isset($_POST['pid'])){
    $pid = trim(strip_tags($_POST['pid']));
    
    $conditions['where'] = array('profile_id' => $pid);
    $posters = $db->getRows('profiles_images', $conditions);
    if($posters != null){
        $str = '';
        foreach($posters as $p){
            $str .= '<div class="img-wraps" id="imgdiv'.$p['id'].'">
                        <span class="closes" title="Delete" onclick="deletePoster(\''.$p['id'].'\');">×</span>
                        <img class="img-responsive" src="'.$p['poster'].'">
                    </div>';
        }
        echo $str;
    }else{
        echo "No posters available";
    }
} else if(isset($_POST['delete_poster']) && $_POST['delete_poster'] == "delete" && isset($_POST['posterid'])){
    $pid = trim(strip_tags($_POST['posterid']));
    
    $conditions['where'] = array('id' => $pid);
    $conditions['return_type'] = 'single';
    $poster = $db->getRows('profiles_images', $conditions);
    if($poster != null){
        $con['id'] = $poster['id'];
        
        if(!empty($poster['poster'])/* && file_exists('./'.$poster['poster'])*/){
            if(unlink("./".$poster['poster'])){
                $db->delete('profiles_images', $con);
                getPosters($pid, $db);
            }else{
                echo "FAILED";
            }
        }else{
            echo "FAILED";
        }
    }else{
        echo "FAILED";
    }
} else if(isset($_POST['upload_poster']) && $_POST['upload_poster'] == "save" && isset($_FILES['posterimage']['name']) && isset($_POST['profileid'])){
    
    if((isset($_FILES['posterimage']['name']) && $_FILES['posterimage']['name'] != '')){
        $pid = $_POST['profileid'];

        $data['profile_id'] = $pid;
        $maxsize = 5242880; // 5MB
        $name = $_FILES['posterimage']['name'];
        $target_image_dir = "profiles/posters/";
        $extension = strtolower(pathinfo($_FILES["posterimage"]["name"], PATHINFO_EXTENSION));
        $target_file = $target_image_dir . time().'.'.$extension;
        // Valid file extensions
        $extensions_arr = array("jpg","jpeg","png");
        
        // Check extension
        if( in_array($extension,$extensions_arr) ){
        
            // Check file size
            if(($_FILES['posterimage']['size'] >= $maxsize) || ($_FILES["posterimage"]["size"] == 0)) {
                $errorMsg = "File too large. File must be less than 5MB.";
                $imgsuccess = false;
            }else{
                // Upload
                if(move_uploaded_file($_FILES['posterimage']['tmp_name'], $target_file)){
                    $data['poster'] = $target_file;
                    $id = $db->insert('profiles_images', $data);
                    if($id){
                        getPosters($pid, $db);
                    }else{
                        echo "FAILED";
                    }
                }
            }
        
        }else{
            echo "FAILED";
        }
    }
}
/* ============================== Profile Operations Ends here ==============================*/

else{
    echo "Invalid request";
}

function getPosters($pid, $db){
    $conditions['where'] = array('profile_id' => $pid);
    $posters = $db->getRows('profiles_images', $conditions);
    if($posters != null){
        $str = '';
        foreach($posters as $p){
            $str .= '<div class="img-wraps" id="imgdiv'.$p['id'].'">
                        <span class="closes" title="Delete" onclick="deletePoster(\''.$p['id'].'\');">×</span>
                        <img class="img-responsive" src="'.$p['poster'].'">
                    </div>';
        }
        echo $str;
    }else{
        echo "No posters available";
    }
}