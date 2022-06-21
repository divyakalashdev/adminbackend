<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


@ini_set('upload_max_size', '4000M');
@ini_set('post_max_size', '4000M');
@ini_set('max_execution_time', '30000');

include 'DB.class.php';
$db = new DB();

/* ============================== Videos Operations Starts here ==============================*/
if (isset($_POST['submit_new_video'])) {
    $title = trim(strip_tags($_POST['title']));
    $mediatype = trim(strip_tags($_POST['videotype']));
    $tags = trim(strip_tags($_POST['tags']));
    $profile_id = trim(strip_tags($_POST['profile_id']));

    if (isset($_POST['subcatselect']) && !empty($_POST['subcatselect'])) {
        $parentid = trim(strip_tags($_POST['subcatselect']));
    } else if (isset($_POST['parentselect']) && !empty($_POST['parentselect'])) {
        $parentid = trim(strip_tags($_POST['parentselect']));
    }


    $video = trim(strip_tags($_FILES['video']['name']));
    $audio = trim(strip_tags($_FILES['audio']['name']));
    $livelink = trim(strip_tags($_POST['livelink']));
    $image = trim(strip_tags($_FILES['image']['name']));
    $description = trim(strip_tags($_POST['description']));

    // Fields validation
    $errorMsg = '';

    if (empty($parentid)) {
        $errorMsg .= '<li>Select a category.</li>';
    }
    if (empty($mediatype)) {
        $errorMsg .= '<li>Select video type.</li>';
    }
    if (empty($title)) {
        $errorMsg .= '<li>Enter title name.</li>';
    }
    if (empty($image)) {
        $errorMsg .= '<li>Select an image for video thumbnail.</li>';
    }
    if (empty($video) && empty($audio) && empty($livelink)) {
        $errorMsg .= '<li>Select an audio/video/live url.</li>';
    }
    $imgsuccess = false;
    $vidsuccess = false;
    // Submit the form data
    if (empty($errorMsg)) {

        $data = array();
        $data['profile_id'] = $profile_id;
        $data['title'] = $title;
        $data['type'] = $mediatype;
        $data['catid'] = $parentid;
        if ((isset($_FILES['image']['name']) && $_FILES['image']['name'] != '')) {
            $maxsize = 5242880; // 5MB
            $name = $_FILES['image']['name'];
            $target_image_dir = "video_thumbnail/";
            $extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            $target_image_file = $target_image_dir . time() . '.' . $extension;
            // Valid file extensions
            $extensions_arr = array("jpg", "jpeg", "png");

            // Check extension
            if (in_array($extension, $extensions_arr)) {

                // Check file size
                if (($_FILES['image']['size'] >= $maxsize) || ($_FILES["image"]["size"] == 0)) {
                    $errorMsg = "File too large. File must be less than 5MB.";
                    $imgsuccess = false;
                    $type = "failed";
                } else {
                    // Upload
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_image_file)) {
                        $data['thumbnail'] = $target_image_file;
                        $imgsuccess = true;
                        $type = "ok";
                    }
                }
            } else {
                $errorMsg = "Invalid file extension.";
                $imgsuccess = false;
                $type = "failed";
            }
        }
        if ($mediatype == "live" || $mediatype == 'youtube') {
            $data['video_url'] = $livelink;
            $vidsuccess = true;
            $type = "failed";
        } else {
            if (((isset($_FILES['video']['name']) && $_FILES['video']['name'] != '') ||
                (isset($_FILES['audio']['name']) && $_FILES['audio']['name'] != ''))) {
                $maxsize = 3221225472; // 500MB
                if (!empty($_FILES['video']['name'])) {
                    $name = $_FILES['video']['name'];
                    $target_dir = "videos/";
                    $extension = strtolower(pathinfo($_FILES["video"]["name"], PATHINFO_EXTENSION));
                    $target_file = $target_dir . time() . '.' . $extension;
                    // Valid file extensions
                    $extensions_arr = array("mp4", "avi", "3gp", "mov", "mpeg");

                    // Check extension
                    if (in_array($extension, $extensions_arr)) {

                        // Check file size
                        if (($_FILES['video']['size'] >= $maxsize) || ($_FILES["video"]["size"] == 0)) {
                            $errorMsg = "File too large. File must be less than 500MB.";
                            $vidsuccess = false;
                        } else {
                            // Upload
                            if (move_uploaded_file($_FILES['video']['tmp_name'], $target_file)) {
                                $data['video_url'] = $target_file;
                                $vidsuccess = true;
                            }
                        }
                    } else {
                        $vidsuccess = false;
                        $errorMsg = "Invalid file extension.";
                        $type = "failed";
                    }
                } else if (!empty($_FILES['audio']['name'])) {
                    $name = $_FILES['audio']['name'];
                    $target_dir = "audio/";
                    $extension = strtolower(pathinfo($_FILES["audio"]["name"], PATHINFO_EXTENSION));
                    $target_file = $target_dir . time() . '.' . $extension;
                    // Valid file extensions
                    $extensions_arraudio = array(/*"mp3","mp4",*/"wav", "aac", "mp3");
                    // Check extension
                    if (in_array($extension, $extensions_arraudio)) {

                        // Check file size
                        if (($_FILES['audio']['size'] >= $maxsize) || ($_FILES["audio"]["size"] == 0)) {
                            $errorMsg = "File too large. File must be less than 500MB.";
                            $vidsuccess = false;
                            $type = "failed";
                        } else {
                            // Upload
                            if (move_uploaded_file($_FILES['audio']['tmp_name'], $target_file)) {
                                $data['audio_url'] = $target_file;
                                $vidsuccess = true;
                            }
                        }
                    } else {
                        $vidsuccess = false;
                        $errorMsg = "Invalid file extension.";
                        $type = "failed";
                    }
                }
            } else {
                $errorMsg = "Please upload any video or audio.";
                $type = "failed";
            }
        }


        $data['description'] = $description;
        $data['created_at'] = date("Y-m-d H:i:s");
        $data['updated_at'] = date("Y-m-d H:i:s");
        $mid = $db->insert('videos', $data);
        if (empty($errorMsg) && $imgsuccess && $vidsuccess && ($mid > 0)) {
            $mid = $db->insert('tags', array('video_id' => $mid, 'tags' => $tags));
            $errorMsg = 'Video uploaded successfully.';
            $class = 'alert alert-success  text-center';
            //header('Location: /title-list.php');
            //echo '<script type="text/javascript">window.location = "'.DB::getBasePath().'/videos.php'.$redirect_url.'";</script>';
            $type = "ok";
        } else {
            //$errorMsg = 'Unable to upload new video. Please try later';
            $class = 'alert alert-danger text-center';
            $type = "failed";
        }
    } else {
        $class = 'alert alert-danger';
        $type = "failed";
    }
    echo json_encode(array('msg' => $errorMsg, 'class' => $class, 'type' => $type));
}
/* ============================== Videos Operations Ends here ==============================*/


/* ============================== Explore Video Operations Starts here ==============================*/ else if (isset($_POST['add_explore']) && $_POST['add_explore'] == "add") {
    $vid = (int)trim(strip_tags($_POST['videoid']));
    //$explore_videos = array();
    $explore_videos = $db->readExploreVideos();
    //$explore_videos = $explore_videos['explore_list'];
    if (empty($explore_videos)) {
        $explore_videos = array();
    }
    if (empty($explore_videos) || (!empty($explore_videos) && count($explore_videos) <= 20)) {
        array_push($explore_videos, $vid);

        if (file_put_contents("explore.json", json_encode(array('explore_list' => $explore_videos)))) {
            echo "OK";
        } else {
            echo "FAILED";
        }
    } else {
        echo "Cannot add more than 20 videos in explore category";
    }
} else if (isset($_POST['arrange_explore']) && $_POST['arrange_explore'] == "arrange") {
    $arraged_ids = $_POST['item'];
    if (file_put_contents("explore.json", json_encode(array('explore_list' => $arraged_ids)))) {
        echo "OK";
    } else {
        echo "FAILED";
    }
} else if (isset($_POST['arrange_videos']) && $_POST['arrange_videos'] == "arrange") {
    $arraged_ids = $_POST['item'];
    $ids_in = implode(',', $arraged_ids);
    $conditions = '';
    if (!empty($arraged_ids) && count($arraged_ids) > 0) {
        foreach ($arraged_ids as $key => $value) {
            $conditions .= ' when id = ' . $value . ' then ' . $key;
        }
        $query = 'UPDATE videos SET arrange_id = (case ' . $conditions . ' end) WHERE id in (' . $ids_in . ');';
        $update = $db->customeUpdate($query);
        if ($update) {
            echo "OK";
        } else {
            echo "FAILED";
        }
        //echo "OK";
    } else {
        echo "FAILED";
    }
} else if (isset($_POST['remove_explore']) && $_POST['remove_explore'] == "remove") {
    $vid = (int)trim(strip_tags($_POST['videoid']));


    $file = fopen("explore.json", "r");
    // Check extension
    while (!feof($file)) {
        $line = fgets($file);
    }
    fclose($file);

    $explore_videos = json_decode($line, true);
    if (!empty($explore_videos)) {
        $explore_videos = $explore_videos['explore_list'];

        if (($key = array_search($vid, $explore_videos)) !== false) {
            unset($explore_videos[$key]);
        }
    }


    if (file_put_contents("explore.json", json_encode(array('explore_list' => $explore_videos)))) {
        echo "OK";
    } else {
        echo "FAILED";
    }
} else {
    echo "Invalid request";
}
