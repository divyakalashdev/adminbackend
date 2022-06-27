<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

@ini_set('upload_max_size', '5000M');
@ini_set('post_max_size', '5000M');
@ini_set('max_execution_time', '50000');

include 'DB.class.php';
$db = new DB();
/* ============================== Category Operations Starts here ==============================*/
if (isset($_POST['submit_parent_category']) && $_POST['submit_parent_category'] == "add") {
    $catname = trim(strip_tags($_POST['category_name']));
    $display_type = trim(strip_tags($_POST['display_type']));
    $display_height = trim(strip_tags($_POST['display_height']));

    $data = array("parent_id" => 0, "category" => $catname, "display_type" => $display_type, 'height' => $display_height, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s"));

    if ($db->insert('categories', $data)) {
        echo "OK";
    } else {
        echo "FAILED";
    }
} else if (isset($_POST['update_parent_category']) && $_POST['update_parent_category'] == "update") {
    $catid = trim(strip_tags($_POST['edit_cat_id']));
    $catname = trim(strip_tags($_POST['edit_category_name']));
    $display_type = trim(strip_tags($_POST['edit_display_type']));
    $display_height = trim(strip_tags($_POST['edit_display_height']));
    $data = array("category" => $catname, "display_type" => $display_type, 'height' => $display_height, 'updated_at' => date("Y-m-d H:i:s"));
    $con['id'] = $catid;
    if ($db->update('categories', $data, $con)) {
        echo "OK";
    } else {
        echo "FAILED";
    }
} else if (isset($_POST['delete_parent_category']) && $_POST['delete_parent_category'] == "delete") {
    $catid = trim(strip_tags($_POST['cat_id']));
    $pvarr = array();
    $subcats = $db->getRows('categories', array('where' => array('parent_id' => $catid)));

    $parentcatsvid = $db->getRows('categories', array('where' => array('id' => $catid)));
    if (!empty($parentcatsvid)) {
        foreach ($parentcatsvid as $pv) {
            array_push($pvarr, $pv['id']);
        }
    }

    $subcatsvid = $db->getRows('categories', array('where' => array('parent_id' => $catid)));
    if (!empty($subcatsvid)) {
        foreach ($subcatsvid as $sv) {
            array_push($pvarr, $sv['id']);
        }
    }
    $pvarr = implode(',', $pvarr);

    if (!empty($subcats)) {
        $subarr = array();
        foreach ($subcats as $s) {
            array_push($subarr, $s['id']);
            if (!empty($s['thumbnail']) && file_exists("./" . $s['thumbnail'])) {
                unlink("./" . $s['thumbnail']);
            }
        }
        $delpostersid = implode(',', $subarr);
        $posters = $db->customQuery("SELECT * FROM category_posters WHERE cat_id IN($delpostersid)");
        if (!empty($posters)) {
            $posterarr = array();
            foreach ($posters as $p) {
                array_push($posterarr, $s['id']);
                if (!empty($p['poster']) && file_exists("./" . $p['poster'])) {
                    unlink("./" . $p['poster']);
                }
            }
            $posterarr = implode(',', $posterarr);
            $db->customDelete("DELETE FROM category_posters WHERE ID IN($posterarr)");
        }
        $subarr = implode(',', $subarr);
        $db->customDelete("DELETE FROM categories WHERE ID IN($subarr)");
    }

    $profile = $db->customQuery("SELECT * FROM profiles WHERE profile_type IN($pvarr)");
    if (!empty($profile)) {
        $profile_ids = array();
        foreach ($profile as $p) {
            array_push($profile_ids, $p['id']);
            if (!empty($po['avatar']) && file_exists("./" . $po['avatar'])) {
                unlink("./" . $po['avatar']);
            }
            if (!empty($po['poster']) && file_exists("./" . $po['poster'])) {
                unlink("./" . $po['poster']);
            }
        }

        if (count($profile_ids) > 0) {
            $profile_ids = implode(',', $profile_ids);

            $poster = $db->customQuery("SELECT * FROM profiles_images WHERE profile_id IN($profile_ids)");
            if (!empty($poster)) {
                foreach ($poster as $po) {
                    if (!empty($po['poster']) && file_exists("./" . $po['poster'])) {
                        unlink("./" . $po['poster']);
                    }
                }
            }

            $db->customDelete("DELETE FROM profiles_images WHERE profile_id IN ($profile_ids)");
            $db->customDelete("DELETE FROM tags WHERE profile_id IN ($profile_ids)");
        }

        $profilecondel['profile_type'] = $catid;
        $db->delete('profiles', $profilecondel);
    }


    $videos = $db->customQuery("SELECT * FROM videos WHERE catid in ($pvarr)");

    $vid = array();
    if (!empty($videos)) {
        $explore_videos = $db->readExploreVideos();

        foreach ($videos as $v) {
            array_push($vid, $v['id']);
            if (!empty($explore_videos) && count($explore_videos) > 0) {
                if (($key = array_search($v['id'], $explore_videos)) !== false) {
                    unset($explore_videos[$key]);
                }
            }
            if (!empty($v['video_url']) && file_exists("./" . $v['video_url']) && $v['type'] = 'recorded') {
                unlink("./" . $v['video_url']);
            }
            if (!empty($v['audio_url']) && file_exists("./" . $v['audio_url']) && $v['type'] = 'recorded') {
                unlink("./" . $v['audio_url']);
            }
            if (!empty($v['thumbnail']) && file_exists("./" . $v['thumbnail'])) {
                unlink("./" . $v['thumbnail']);
            }
        }
        file_put_contents("explore.json", json_encode(array('explore_list' => $explore_videos)));
        $vid = implode(',', $vid);
        $db->customDelete("DELETE FROM videos WHERE id IN ($vid)");
        $db->customDelete("DELETE FROM tags WHERE video_id catid ($vid)");
    }

    $con['id'] = $catid;
    if ($db->delete('categories', $con)) {
        echo "OK";
    } else {
        echo "FAILED";
    }
} else if (isset($_POST['arrange_category']) && $_POST['arrange_category'] == "arrange") {
    //Array ( [item] => Array ( [0] => 1 [1] => 2 [2] => 3 [3] => 6 [4] => 8 [5] => 4 ) [arrange_category] => arrange )
    $cat_ids = $_POST['item'];
    if (count($cat_ids) > 0) {
        for ($i = 0; $i < count($cat_ids); $i++) {
            $data = array("priority" => $i, 'updated_at' => date("Y-m-d H:i:s"));
            $con['id'] = $cat_ids[$i];
            $db->update('categories', $data, $con);
        }
        echo "OK";
    } else {
        echo "FAILED";
    }
} else if (isset($_POST['disable_category']) && $_POST['disable_category'] == "disable") {
    $cat_ids = $_POST['catid'];
    $status = $_POST['status'];

    if ($status == 0) {
        $data = array("status" => 1, 'updated_at' => date("Y-m-d H:i:s"));
        $con['id'] = $cat_ids;
    } else if ($status == 1) {
        $data = array("status" => 0, 'updated_at' => date("Y-m-d H:i:s"));
        $con['id'] = $cat_ids;
    }
    if ($db->update('categories', $data, $con)) {
        echo "OK";
    } else {
        echo "FAILED";
    }
} else if (isset($_POST['disable_ads']) && $_POST['disable_ads'] == "disable") {
    $cat_ids = $_POST['catid'];
    $status = $_POST['status'];

    if ($status == 0) {
        $data = array("ad_status" => 1, 'updated_at' => date("Y-m-d H:i:s"));
        $con['id'] = $cat_ids;
    } else if ($status == 1) {
        $data = array("ad_status" => 0, 'updated_at' => date("Y-m-d H:i:s"));
        $con['id'] = $cat_ids;
    }
    if ($db->update('categories', $data, $con)) {
        echo "OK";
    } else {
        echo "FAILED";
    }
}
/* ============================== Category Operations Ends here ==============================*/

/* ============================== Video Operations Starts here ==============================*/ else if (isset($_POST['delete_video']) && $_POST['delete_video'] == "delete") {
    $vidid = trim(strip_tags($_POST['delete_video_id']));
    $con['id'] = $vidid;

    $conditions['where'] = array('id' => $vidid);
    $conditions['return_type'] = 'single';
    $videos = $db->getRows('videos', $conditions);
    //[id] => 1 [title] => 1st Test Video [video_url] => videos/1648816157.mp4 [thumbnail] => video_thumbnail/1648816157.png [created_at] => 2022-04-01 12:29:17 [updated_at] => 2022-04-01 12:29:17
    if ($videos != null) {
        $con['id'] = $vidid;
        if (file_exists("./" . $videos['video_url']) && file_exists("./" . $videos['thumbnail']) && $videos['type'] != 'live') {
            $d = false;
            if (!empty($videos['audio_url'])) {
                unlink("./" . $videos['audio_url']);
                $d = true;
            } else if (!empty($videos['video_url'])) {
                unlink("./" . $videos['video_url']);
                $d = true;
            }
            if (unlink("./" . $videos['thumbnail'])) {
                if ($db->delete('videos', $con)) {
                    echo "OK";
                }
            } else {
                echo "FAILED";
            }
        } else {
            if (unlink("./" . $videos['thumbnail'])) {
                if ($db->delete('videos', $con)) {
                    $con['video_id'] = $vidid;
                    $db->delete('videos', $con);
                    echo "OK";
                }
            }
        }
    } else {
        echo "FAILED";
    }
} else if (isset($_POST['new_video']) && $_POST['new_video'] == "new") {
    $title = trim(strip_tags($_POST['title']));

    if (isset($_POST['sub_id']) && !empty($_POST['sub_id'])) {
        $parentid = trim(strip_tags($_POST['sub_id']));
    } else if (isset($_POST['parent_id']) && !empty($_POST['parent_id'])) {
        $parentid = trim(strip_tags($_POST['parent_id']));
    }

    $data = array();
    $data['title'] = $title;
    $data['catid'] = $parentid;
    $maxsize = 3221225472; // 3000MB
    if ((isset($_FILES['video']['name']) && $_FILES['video']['name'] != '')) {
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
                $errorMsg = "File too large. File must be less than 3000MB.";
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
        }
    }

    if (!empty($_FILES['audio']['name'])) {
        $name = $_FILES['audio']['name'];
        $target_dir = "audio/";
        $extension = strtolower(pathinfo($_FILES["audio"]["name"], PATHINFO_EXTENSION));
        $target_file = $target_dir . time() . '.' . $extension;
        // Valid file extensions
        $extensions_arraudio = array(/*"mp3","mp4",*/"wav", "aac");
        // Check extension
        if (in_array($extension, $extensions_arraudio)) {

            // Check file size
            if (($_FILES['audio']['size'] >= $maxsize) || ($_FILES["audio"]["size"] == 0)) {
                $errorMsg = "File too large. File must be less than 3000MB.";
                $vidsuccess = false;
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
        }
    }

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
            } else {
                // Upload
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_image_file)) {
                    $data['thumbnail'] = $target_image_file;
                    $imgsuccess = true;
                }
            }
        } else {
            $errorMsg = "Invalid file extension.";
            $imgsuccess = false;
        }
    }

    if ($db->insert('videos', $data)) {
        echo "OK";
    } else {
        echo "FAILED";
    }
} else if (isset($_POST['update_video']) && $_POST['update_video'] == "update") {
    $vidid = trim(strip_tags($_POST['vid']));
    //$profileid = trim(strip_tags($_POST['profileid']));
    $title = trim(strip_tags($_POST['title']));
    $tags = trim(strip_tags($_POST['tags']));
    $videotype = trim(strip_tags($_POST['videotype']));
    $description = trim(strip_tags($_POST['description']));
    $videolink = trim(strip_tags($_POST['livelink']));
    /*if(isset($_POST['parent_id'])){
        $parentid = trim(strip_tags($_POST['parent_id']));
    }
    if(isset($_POST['sub_id'])){
        $parentid = trim(strip_tags($_POST['sub_id']));
    }*/
    if (isset($_POST['sub_id']) && !empty($_POST['sub_id'])) {
        $parentid = trim(strip_tags($_POST['sub_id']));
    } else if (isset($_POST['parent_id']) && !empty($_POST['parent_id'])) {
        $parentid = trim(strip_tags($_POST['parent_id']));
    }

    $con['id'] = $vidid;

    $conditions['where'] = array('id' => $vidid);
    $conditions['return_type'] = 'single';
    $videos = $db->getRows('videos', $conditions);

    $data = array();
    $data['title'] = $title;
    //$data['profile_id'] = $profileid;
    $data['description'] = $description;
    $data['type'] = $videotype;
    $data['catid'] = $parentid;

    if ($videotype == "live") {
        if (!empty($videolink)) {
            $data['video_url'] = $videolink;
        }
    } else {
        $maxsize = 500242880; // 5000MB
        if ((isset($_FILES['video']['name']) && $_FILES['video']['name'] != '')) {
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
                    $errorMsg = "File too large. File must be less than 5000MB.";
                    $vidsuccess = false;
                } else {
                    // Upload
                    if (move_uploaded_file($_FILES['video']['tmp_name'], $target_file)) {
                        if (file_exists("./" . $videos['video_url'])) {
                            unlink("./" . $videos['video_url']);
                        }

                        $data['video_url'] = $target_file;
                        $vidsuccess = true;
                    }
                }
            } else {
                $vidsuccess = false;
                $errorMsg = "Invalid file extension.";
            }
        }

        if (!empty($_FILES['audio']['name'])) {
            $name = $_FILES['audio']['name'];
            $target_dir = "audio/";
            $extension = strtolower(pathinfo($_FILES["audio"]["name"], PATHINFO_EXTENSION));
            $target_file = $target_dir . time() . '.' . $extension;
            // Valid file extensions
            $extensions_arraudio = array(/*"mp3","mp4",*/"wav", "aac");
            // Check extension
            if (in_array($extension, $extensions_arraudio)) {

                // Check file size
                if (($_FILES['audio']['size'] >= $maxsize) || ($_FILES["audio"]["size"] == 0)) {
                    $errorMsg = "File too large. File must be less than 5000MB.";
                    $vidsuccess = false;
                } else {
                    // Upload
                    if (move_uploaded_file($_FILES['audio']['tmp_name'], $target_file)) {
                        if (file_exists("./" . $videos['audio_url'])) {
                            unlink("./" . $videos['audio_url']);
                        }

                        $data['audio_url'] = $target_file;
                        $vidsuccess = true;
                    }
                }
            } else {
                $vidsuccess = false;
                $errorMsg = "Invalid file extension.";
            }
        }
    }


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
            } else {
                // Upload
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_image_file)) {
                    if (file_exists("./" . $videos['thumbnail'])) {
                        unlink("./" . $videos['thumbnail']);
                    }

                    $data['thumbnail'] = $target_image_file;
                    $imgsuccess = true;
                }
            }
        } else {
            $errorMsg = "Invalid file extension.";
            $imgsuccess = false;
        }
    }

    $tcon['video_id'] = $vidid;
    if (empty($errorMsg) && $db->update('videos', $data, $con)) {
        $checkcon = array(
            'where' => array('video_id' => $vidid),
            'return_type' => 'single'
        );
        $tag = $db->getRows('tags', $checkcon);
        if (!empty($tag)) {
            $db->update('tags', array('tags' => $tags), $tcon);
        } else {
            $db->insert('tags', array('video_id' => $vidid, 'tags' => $tags));
        }
        $errorMsg = "Successfully updated.";
        $class = 'alert alert-success';
        $type = "failed";
    } else {
        //$errorMsg = "Invalid Request";
        $class = 'alert alert-danger';
        $type = "failed";
    }
    echo json_encode(array('msg' => $errorMsg, 'class' => $class, 'type' => $type));
}
/* ============================== Video Operations Ends here ==============================*/
/* ============================== Sub Cagtegory Operations Ends here ==============================*/ else if (isset($_POST['get']) && $_POST['get'] == "subcat") {
    $parentid = trim(strip_tags($_POST['parentid']));
    $conditions['where'] = array('parent_id' => $parentid);

    $con['where'] = array('id' => $parentid);
    $con['return_type'] = 'single';
    $parent_category = $db->getRows('categories', $con);

    $categories = $db->getRows('categories', $conditions);
    $subcat = '<option value="">Sub category</option>';
    if ($categories != null) {
        foreach ($categories as $cat) {
            $subcat .= '<option value="' . $cat['id'] . '">' . $cat['category'] . '</option>';
        }
        $length = count($categories);
    } else {
        $length = 0;
    }
    if ($parent_category['display_type'] == "profile") {
        echo json_encode(array("displaytype" => $parent_category['display_type'], 'length' => $length));
    } else {
        echo json_encode(array("option" => $subcat, "displaytype" => $parent_category['display_type'], 'length' => $length));
    }
} else if (isset($_POST['submit_sub_category']) && $_POST['submit_sub_category'] == "add") {
    $catname = trim(strip_tags($_POST['subcategory_name']));
    $parentid = trim(strip_tags($_POST['parent_cat_id']));
    $description = trim(strip_tags($_POST['description']));
    $data = array("parent_id" => $parentid, "description" => $description, "category" => $catname, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s"));

    $maxsize = 5242880; // 5MB
    $insertid = 0;
    if ((isset($_FILES['newimage']['name']) && $_FILES['newimage']['name'] != '')) {
        $name = $_FILES['newimage']['name'];
        $target_image_dir = "category/subcats/";
        $extension = strtolower(pathinfo($_FILES["newimage"]["name"], PATHINFO_EXTENSION));
        $target_image_file = $target_image_dir . time() . '.' . $extension;
        // Valid file extensions
        $extensions_arr = array("jpg", "jpeg", "png");

        if (in_array($extension, $extensions_arr)) {
            if (($_FILES['newimage']['size'] >= $maxsize) || ($_FILES["newimage"]["size"] == 0)) {
                echo "File too large. File must be less than 5MB.";
            } else {
                if (move_uploaded_file($_FILES['newimage']['tmp_name'], $target_image_file)) {
                    $data['thumbnail'] = $target_image_file;
                    $insertid = $db->insert('categories', $data);
                    if ($insertid) {
                        $check = "OK";
                    } else {
                        $check = "FAILED TO SAVE";
                    }
                }
            }
        } else {
            $check = "Invalid file extension.";
        }
    }

    if (isset($_FILES['posters']) && count($_FILES['posters']) > 0 && $insertid > 0) {
        for ($i = 0; $i < count($_FILES['posters']); $i++) {
            $target_image_dir = "category/subcats/posters/";
            @$extension = strtolower(pathinfo($_FILES["posters"]["name"][$i], PATHINFO_EXTENSION));
            $target_image_file = $target_image_dir . time() . $i . '.' . $extension;
            // Valid file extensions
            $extensions_arr = array("jpg", "jpeg", "png");

            if (in_array($extension, $extensions_arr)) {
                if (($_FILES['posters']['size'][$i] <= $maxsize) || ($_FILES["posters"]["size"][$i] != 0)) {
                    if (move_uploaded_file($_FILES['posters']['tmp_name'][$i], $target_image_file)) {
                        $data = array('cat_id' => $insertid, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s"));
                        $data['poster'] = $target_image_file;

                        if ($db->insert('category_posters', $data)) {
                            $check = "OK";
                        } else {
                            $check = "FAILED TO SAVE POSTERS";
                        }
                    }
                }
            }/*  else {
                echo "Invalid file extension.";
            } */
        }
        echo $check;
    }
} else if (isset($_POST['update_sub_category']) && $_POST['update_sub_category'] == "update") {
    $catid = trim(strip_tags($_POST['subcat']));
    $catname = trim(strip_tags($_POST['subcategory_name']));
    $description = trim(strip_tags($_POST['description']));
    $data = array("category" => $catname, "description" => $description, 'updated_at' => date("Y-m-d H:i:s"));
    $con['id'] = $catid;

    $conditions['where'] = array('id' => $catid);
    $conditions['return_type'] = 'single';
    $subcat = $db->getRows('categories', $conditions);

    if (!empty($subcat) && (isset($_FILES['newimage']['name']) && $_FILES['newimage']['name'] != '')) {
        $maxsize = 5242880; // 5MB
        $name = $_FILES['newimage']['name'];
        $target_image_dir = "category/subcats/";
        $extension = strtolower(pathinfo($_FILES["newimage"]["name"], PATHINFO_EXTENSION));
        $target_image_file = $target_image_dir . time() . '.' . $extension;
        // Valid file extensions
        $extensions_arr = array("jpg", "jpeg", "png");

        if (in_array($extension, $extensions_arr)) {
            if (($_FILES['newimage']['size'] >= $maxsize) || ($_FILES["newimage"]["size"] == 0)) {
                echo "File too large. File must be less than 5MB.";
            } else {
                if (move_uploaded_file($_FILES['newimage']['tmp_name'], $target_image_file)) {
                    if (file_exists("./" . $subcat['thumbnail'])) {
                        unlink("./" . $subcat['thumbnail']);
                    }
                    $data['thumbnail'] = $target_image_file;
                }
            }
        } else {
            echo "Invalid file extension.";
        }
    }

    if (!empty($subcat) && $db->update('categories', $data, $con)) {
        $check = "OK";
    } else {
        $check = "FAILED";
    }

    if (isset($_FILES['posters']) && count($_FILES['posters']) > 0) {
        for ($i = 0; $i < count($_FILES['posters']); $i++) {
            $target_image_dir = "category/subcats/posters/";
            @$extension = strtolower(pathinfo($_FILES["posters"]["name"][$i], PATHINFO_EXTENSION));
            $target_image_file = $target_image_dir . time() . $i . '.' . $extension;
            // Valid file extensions
            $extensions_arr = array("jpg", "jpeg", "png");

            if (in_array($extension, $extensions_arr)) {
                if (($_FILES['posters']['size'][$i] <= $maxsize) || ($_FILES["posters"]["size"][$i] != 0)) {
                    if (move_uploaded_file($_FILES['posters']['tmp_name'][$i], $target_image_file)) {
                        $data = array('cat_id' => $catid, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s"));
                        $data['poster'] = $target_image_file;

                        if ($db->insert('category_posters', $data)) {
                            $check = "OK";
                        } else {
                            $check = "FAILED TO SAVE POSTERS";
                        }
                    }
                }
            }/*  else {
                echo "Invalid file extension.";
            } */
        }
    }
    echo $check;
} else if (isset($_POST['delete_sub_category']) && $_POST['delete_sub_category'] == "delete") {
    $catid = trim(strip_tags($_POST['delete_subcat']));
    $con['id'] = $catid;
    $conditions['where'] = array('id' => $catid);
    $conditions['return_type'] = 'single';
    $subcat = $db->getRows('categories', $conditions);

    if (!empty($subcat) && $db->delete('categories', $con)) {
        if (!empty($subcat['thumbnail']) && file_exists("./" . $subcat['thumbnail'])) {
            unlink("./" . $subcat['thumbnail']);
        }
        echo "OK";
    } else {
        echo "FAILED";
    }
} else if (isset($_POST['del_subcat_posters']) && $_POST['del_subcat_posters'] == "delete") {
    $posertid = trim(strip_tags($_POST['posterid']));
    $con['id'] = $posertid;
    $conditions['where'] = array('id' => $posertid);
    $conditions['return_type'] = 'single';
    $poster = $db->getRows('category_posters', $conditions);

    if (!empty($poster) && $db->delete('category_posters', $con)) {
        if (!empty($poster['poster']) && file_exists("./" . $poster['poster'])) {
            unlink("./" . $poster['poster']);
        }
        echo "OK";
    } else {
        echo "FAILED";
    }
} else if (isset($_POST['load_sub_cat']) && $_POST['load_sub_cat'] == "load") {
    $parentid = trim(strip_tags($_POST['parentid']));
    $con['id'] = $parentid;
    $conditions = array(
        'order_by' => 'priority ASC',
        'where' => array('parent_id' => $parentid)
    );

    $subcat = $db->getRows('categories', $conditions);
    $str = '';
    if (!empty($subcat)) {
        $i = 1;
        foreach ($subcat as $cat) {
            $str .= '<li id="item-' . $cat['id'] . '" dir="rtl">' . $cat['category'] . '<i class="tab fa fa-arrows-alt"></i></li>';
            $i++;
        }
        echo $str;
    } else {
        echo "FAILED";
    }
}

/* ============================== Sub Cagtegory Operations Ends here ==============================*/
/* ============================== Advertising Operations Starts here ==============================*/ else if (isset($_POST['save_ad']) && $_POST['save_ad'] == "save") {
    $type = trim(strip_tags($_POST['type']));
    $sequence = trim(strip_tags($_POST['sequence']));
    $category = trim(strip_tags($_POST['category']));
    if (empty($category)) {
        $category = 0;
    }
    $text = trim(strip_tags($_POST['text']));
    $url = trim(strip_tags($_POST['url']));
    $screen_name = trim(strip_tags($_POST['screen_name']));

    $data['text'] = $text;
    $data['type'] = $type;
    $data['sequence'] = $sequence;
    $data['category_id'] = $category;
    $data['url'] = $url;
    $data['screen_name'] = $screen_name;


    if ((isset($_FILES['image']['name']) && $_FILES['image']['name'] != '')) {
        $maxsize = 5242880; // 5MB
        $name = $_FILES['image']['name'];
        $target_image_dir = "ads/";
        $extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $target_image_file = $target_image_dir . time() . '.' . $extension;
        // Valid file extensions
        $extensions_arr = array("jpg", "jpeg", "png");

        // Check extension
        if (in_array($extension, $extensions_arr)) {

            // Check file size
            if (($_FILES['image']['size'] >= $maxsize) || ($_FILES["image"]["size"] == 0)) {
                echo "File too large. File must be less than 5MB.";
                //$imgsuccess = false;
            } else {
                // Upload
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_image_file)) {
                    $data['image'] = $target_image_file;
                    //$imgsuccess = true;

                    if ($db->insert('ads', $data)) {
                        echo "OK";
                    } else {
                        echo "DATA Insert FAILED";
                    }
                }
            }
        } else {
            echo "Invalid file extension.";
        }
    } else {
        echo "FAILED.";
    }
} else if (isset($_POST['update_ad']) && $_POST['update_ad'] == "update") { //print_r($_POST);exit;
    $adid = trim(strip_tags($_POST['adid']));
    $con['id'] = $adid;

    $conditions['where'] = array('id' => $adid);
    $conditions['return_type'] = 'single';
    $ads = $db->getRows('ads', $conditions);
    if ($ads != null) {
        $type = trim(strip_tags($_POST['type']));
        $sequence = trim(strip_tags($_POST['sequence']));
        $category = trim(strip_tags($_POST['category']));
        if (empty($category)) {
            $category = 0;
        }
        $text = trim(strip_tags($_POST['text']));
        $url = trim(strip_tags($_POST['url']));
        $screen_name = trim(strip_tags($_POST['screen_name']));

        $data['text'] = $text;
        $data['sequence'] = $sequence;
        $data['category_id'] = $category;
        $data['type'] = $type;
        $data['url'] = $url;
        $data['screen_name'] = $screen_name;


        if ((isset($_FILES['image']['name']) && $_FILES['image']['name'] != '')) {
            $maxsize = 5242880; // 5MB
            $name = $_FILES['image']['name'];
            $target_image_dir = "ads/";
            $extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            $target_image_file = $target_image_dir . time() . '.' . $extension;
            // Valid file extensions
            $extensions_arr = array("jpg", "jpeg", "png");

            // Check extension
            if (in_array($extension, $extensions_arr)) {

                // Check file size
                if (($_FILES['image']['size'] >= $maxsize) || ($_FILES["image"]["size"] == 0)) {
                    echo "File too large. File must be less than 5MB.";
                } else {
                    // Upload
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_image_file)) {
                        if (file_exists("./" . $ads['image'])) {
                            unlink("./" . $ads['image']);
                        }

                        $data['image'] = $target_image_file;

                        if ($db->update('ads', $data, $con)) {
                            echo "OK";
                        } else {
                            echo "Update FAILED";
                        }
                    }
                }
            } else {
                echo "Invalid file extension.";
            }
        } else {
            if ($db->update('ads', $data, $con)) {
                echo "OK";
            } else {
                echo "FAILED";
            }
        }
    } else {
        echo "Invalid request";
    }
} else if (isset($_POST['delete_ad']) && $_POST['delete_ad'] == "delete") {
    $adid = trim(strip_tags($_POST['delete_ad_id']));

    $con['id'] = $adid;

    $conditions['where'] = array('id' => $adid);
    $conditions['return_type'] = 'single';
    $ads = $db->getRows('ads', $conditions);

    if ($ads != null) {
        if (file_exists("./" . $ads['image'])) {
            if (unlink("./" . $ads['image'])) {
                if ($db->delete('ads', $con)) {
                    echo "OK";
                }
            } else {
                echo "FAILED";
            }
        }
    } else {
        echo "FAILED";
    }


    $con['id'] = $adid;
}
/* ============================== Advertising Operations Ends here ==============================*/


/* ============================== Enable/Disable Google/Client ads Starts here ==============================*/ else if (isset($_POST['ad_status']) && $_POST['ad_status'] == "update") {
    $gstatus = trim(strip_tags($_POST['gstatus']));
    $cstatus = trim(strip_tags($_POST['cstatus']));

    $file = fopen("ads.json", "r");
    // Check extension
    while (!feof($file)) {
        $line = fgets($file);
    }
    fclose($file);

    $adstatus = json_decode($line, true);
    $adstatus['google_ads'] = $gstatus;
    $adstatus['client_ads'] = $cstatus;

    if (file_put_contents("ads.json", json_encode($adstatus))) {
        echo "OK";
    } else {
        echo "FAILED";
    }
}
/* ============================== Enable/Disable Google/Client ads Ends here ==============================*/ else {
    echo "Invalid request";
}
