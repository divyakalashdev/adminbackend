<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_COOKIE['mid'])) {
    header('Location: ./login.php');
    exit(0);
}
include 'DB.class.php';
include 'functions.php';
$db = new DB;
include "header.php";
include_once 'Pagination.php';

$profile = '';
$profile_id = 0;
$limit = 10;
// Paging limit & offset
$offset = !empty($_GET['page']) ? (($_GET['page'] - 1) * $limit) : 0;

$concount = array(
    'return_type' => 'count'
);
if (isset($_GET['pid'])) {
    $profile_id = $_GET['pid'];
    $concount['where'] = array('profile_id' => $profile_id);

    $pcon = array(
        'where' => array('id' => $profile_id),
        'return_type' => 'single'
    );
    $sql = 'SELECT p.*, c.category, c.id as catid FROM `profiles` as p JOIN categories as c ON p.profile_type = c.id WHERE p.id = ' . $profile_id;
    $profile = $db->customQuery($sql);
    if (!empty($profile)) {
        $profile_id = $profile[0]['id'];
    } else {
        $profile_id = 0;
    }
    //$profile = $db->getRows('profiles', $pcon);
    $sql = 'SELECT tags.video_id, tags.tags, videos.*, categories.parent_id, categories.category, categories.priority FROM videos INNER JOIN categories ON videos.catid = categories.id LEFT JOIN tags ON tags.video_id = videos.id WHERE videos.profile_id = ' . $profile_id . ' ORDER BY videos.id DESC LIMIT ' . $offset . ', ' . $limit;
    $rowCount = $db->getRows('videos', $concount);
} else if (isset($_GET['c'])) {
    $catid = $_GET['c'];
    $sql = 'SELECT tags.video_id, tags.tags, videos.*, categories.parent_id, categories.category, categories.priority FROM videos INNER JOIN categories ON videos.catid = categories.id LEFT JOIN tags ON tags.video_id = videos.id WHERE videos.catid = ' . $catid . ' ORDER BY videos.id DESC LIMIT ' . $offset . ', ' . $limit;
    $concount['where'] = array('catid' => $catid);
    $rowCount = $db->getRows('videos', $concount);
} else {
    $sql = 'SELECT tags.video_id, tags.tags, videos.*, categories.parent_id, categories.category, categories.priority FROM videos INNER JOIN categories ON videos.catid = categories.id LEFT JOIN tags ON tags.video_id = videos.id ORDER BY videos.id DESC LIMIT ' . $offset . ', ' . $limit;
    $rowCount = $db->getRows('videos', $concount);
}

$pagConfig = array(
    'baseURL' => 'videos.php',
    'totalRows' => $rowCount,
    'perPage' => $limit
);
$pagination = new Pagination($pagConfig);


$videos = $db->customQuery($sql);

$conc = array(
    'order_by' => 'id ASC',
    'where' => array('parent_id' => 0)
);
$categories = $db->getRows('categories', $conc);
$random = rand(10, 100);

$explore_videos = $db->readExploreVideos();
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css" integrity="sha512-xmGTNt20S0t62wHLmQec2DauG9T+owP9e6VU8GigI0anN7OXLip9i7IwEhelasml2osdxX71XcYm6BQunTQeQg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    .bootstrap-tagsinput {
        background-color: #fff;
        border: 1px solid #ccc;
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        display: block;
        padding: 4px 6px;
        color: #555;
        vertical-align: middle;
        border-radius: 4px;
        max-width: 100%;
        line-height: 22px;
        cursor: text;
    }

    .bootstrap-tagsinput input {
        border: none;
        box-shadow: none;
        outline: none;
        background-color: transparent;
        padding: 0 6px;
        margin: 0;
        width: auto;
        max-width: inherit;
    }

    .bootstrap-tagsinput .tag {
        margin-right: 2px;
        background: lightblue;
        color: #414141;
        border-radius: inherit;
        padding-left: 5px;
    }

    .bootstrap-tagsinput .tag [data-role="remove"] {
        margin-left: 8px;
        cursor: pointer;
        background: cadetblue;
        border-radius: inherit;
    }

    /*.container{
	padding: 20px;
}*/
    .upload-div {
        width: 350px;
        margin: auto;
        background-color: #f3f3f3;
        color: #333;
        padding: 12px;
        box-shadow: 0 0px 10px 1px rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
        font-size: 16px;
    }

    .upload-div h4 {
        text-align: center;
        text-transform: uppercase;
        font-size: 18px;
        color: #666;
        margin-top: 0;
    }

    .upload-div input[type="file"] {
        display: block;
        width: 100%;
        height: 25px;
        padding: 8px;
        font-size: 1.5rem;
        font-weight: 400;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        border-radius: .25rem;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    }

    .upload-div input[type="file"]:focus {
        color: #495057;
        background-color: #fff;
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
    }

    .upload-div input[type="submit"] {
        display: inline-block;
        font-weight: 400;
        color: #212529;
        text-align: center;
        vertical-align: middle;
        background-color: transparent;
        border: 1px solid transparent;
        padding: .375rem .75rem;
        font-size: 1.5rem;
        line-height: 1.5;
        border-radius: .25rem;
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
        margin-top: 10px;
        cursor: pointer;
    }

    .upload-div input[type="submit"]:hover {
        color: #fff;
        background-color: #0069d9;
        border-color: #0062cc;
    }

    .progress {
        display: -ms-flexbox;
        display: flex;
        height: 20px;
        overflow: hidden;
        font-size: .75rem;
        background-color: #e9ecef;
        border-radius: .25rem;
        margin-top: 10px;
        margin: auto;
        width: 50%;
    }

    .progress-bar {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-direction: column;
        flex-direction: column;
        -ms-flex-pack: center;
        justify-content: center;
        overflow: hidden;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        background-color: #28a745;
        transition: width .6s ease;
        font-size: 16px;
        text-align: center;
    }

    #uploadStatus {
        padding: 10px 20px;
        margin-top: 10px;
        font-size: 18px;
        text-align: center;
    }
</style>
<!-- Begin Page Content -->
<div class="main_content_iner ">
    <div class="container-fluid p-0 ">

        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">Videos</h3>
                            </div>
                        </div>
                    </div>
                    <div class="white_card_body">
                        <div class="QA_section">
                            <div class="white_box_tittle list_header">
                                <?php
                                if (!empty($profile)) {
                                ?>
                                    <h6 class="m-0 font-weight-bold text-primary"><img src="<?php echo $profile[0]['avatar']; ?>" width="100" /><br />Videos from <?php echo $profile[0]['name']; ?></h6>
                                    <div class="box_right d-flex lms_block">
                                        <div class="add_button ms-2">
                                            <a class="btn_1" href="upload-video.php<?php echo '?pid=' . $profile_id; ?>">Upload New Video</a>
                                        </div>
                                    </div>
                                <?php
                                } else {
                                ?>
                                    <h6 class="m-0 font-weight-bold text-primary">Video List</h6>
                                    <div class="box_right d-flex lms_block">
                                        <div class="add_button ms-2">
                                            <a href="arrange-explore.php" data-bs-toggle="modal" data-bs-target="#addcategory" class="btn_1">Arr. Exp.</a>
                                        </div>
                                        <div class="add_button ms-2">
                                            <a href="arrange-videos.php" data-bs-toggle="modal" data-bs-target="#addcategory" class="btn_1">Arr. Vid.</a>
                                        </div>
                                        <div class="add_button ms-2">
                                            <a href="upload-video.php" data-bs-toggle="modal" data-bs-target="#addcategory" class="btn_1">New</a>
                                        </div>
                                        <div class="add_button ms-2">
                                            <select class="form-select form-control" id="filter_category" name="filter_category">
                                                <option value="">Filter by category</option>
                                                <?php
                                                if (!empty($categories)) {
                                                    foreach ($categories as $cat) {
                                                        $selected = '';
                                                        if ($cat['id'] != 2) {
                                                            /* if ($parent_category == $cat['id']) {
                                                                $selected = 'selected';
                                                            } */
                                                            echo '<option value="' . $cat['id'] . '" ' . $selected . '>' . $cat['category'] . '</option>';
                                                        }
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="add_button ms-2">
                                            <select class="form-select form-control" id="filter_subcategory" name="filter_subcategory">
                                                <option value="">Filter by sub category</option>
                                            </select>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                            <div class="QA_table mb_30">

                                <table class="table lms_table_active ">
                                    <thead>
                                        <tr>
                                            <!-- <th scope="col">Id</th> -->
                                            <th scope="col" style="width:20%;">Title</th>
                                            <th scope="col">Add - Explore</th>
                                            <th scope="col">Video</th>
                                            <th scope="col">Thumbnail</th>
                                            <th scope="col">Description</th>
                                            <th scope="col">Category</th>
                                            <th scope="col">Upload Date</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($videos)) {
                                            foreach ($videos as $vid) {
                                                $mediafile = '';
                                                $mediafile = '';
                                                if (stripos($vid['video_url'], 'youtu') > 0) {
                                                    $mediafile = '<iframe width="150" height="90" src="https://www.youtube.com/embed/' . $db->get_youtube_id_from_url($vid['video_url']) . '"></iframe>';
                                                    //$mediafile = '<video controls="true"><source src="'.$vid['video_url'].'" type="video/mp4" /></video>';
                                                } else if ($vid['type'] == "live") {
                                                    $mediafile = '<video width="150" height="90" controls>
                                                        <source src="' . $vid['video_url'] . '" type="application/x-mpegURL">
                                                    </video>';
                                                } else {
                                                    if (!empty($vid['video_url'])) {
                                                        $mediafile = '<video width="150" height="90" controls>
                                                          <source src="' . $vid['video_url'] . '?s=' . $random . '" type="video/' . explode('.', $vid['video_url'])[1] . '">
                                                        </video>';
                                                    } else if (!empty($vid['audio_url'])) {
                                                        $mediafile = '<audio controls>
                                                              <source src="' . $vid['audio_url'] . '" type="audio/' . explode('.', $vid['audio_url'])[1] . '">
                                                            Your browser does not support the audio element.
                                                        </audio>';
                                                    }
                                                }

                                                if (!empty($explore_videos) && in_array($vid['id'], $explore_videos)) {
                                                    $explore = '<td><button class="btn btn-danger btn-xs" onclick="removeExplore(\'' . $vid['id'] . '\')"><span class="fa fa-trash"></span></button></td>';
                                                } else {
                                                    $explore = '<td><button class="btn btn-success btn-xs" onclick="addExplore(\'' . $vid['id'] . '\')"><span class="fa fa-plus"></span></button></td>';
                                                }

                                                echo '<tr>
                                    <!--<td>' . $vid['id'] . '</td>-->
                                    <th scope="row" id="t' . $vid['id'] . '"><a href="#" class="question_content">' . $vid['title'] . '</a></th>
                                    ' . $explore . '
                                    <td id="v' . $vid['id'] . '">' . $mediafile . '</td>
                                    <td id="i' . $vid['id'] . '"><img src="' . $vid['thumbnail'] . '?s=' . $random . '" width="90" /></td>
                                    <td id="desc' . $vid['id'] . '">' . substr($vid['description'], 0, 50) . '</td>
                                    <td>' . $vid['category'] . '</td>
                                    <td>' . $vid['created_at'] . '</td>
                                    <td>
                                        <input type="hidden" id="tag' . $vid['id'] . '" value="' . $vid['tags'] . '" />
                                        <input type="hidden" id="ty' . $vid['id'] . '" value="' . $vid['type'] . '" />
                                        <input type="hidden" id="pi' . $vid['id'] . '" value="' . $vid['parent_id'] . '" /><input type="hidden" id="ci' . $vid['id'] . '" value="' . $vid['catid'] . '" />
                                        <button class="btn btn-danger btn-xs" onclick="changeId(\'e\', \'' . $vid['id'] . '\')"><span class="fa fa-edit"></span></button>
                                        <button class="btn btn-danger btn-xs" data-toggle="modal" data-target="#deleteCategoryModal" onclick="changeId(\'d\', \'' . $vid['id'] . '\')"><span class="fa fa-trash"></span></button>
                                    </td>
                                  </tr>';
                                            }
                                        } else {
                                            echo '<tr><td colspan="8" class="text-center" style="font-weight: 900;font-size: xxx-large;">No data found.</td></tr>';
                                        }
                                        ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <?php //echo $pagination->createLinks(); 
                ?>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="deleteCategoryModal" tabindex="-1" role="dialog" aria-labelledby="deleteCategoryLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteCategoryLabel">Delete video</h5>
                    <button type="button" class="close" id="xnpc_modal" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-dismissible fade show d-none" role="alert">
                        <button type="button" class="close" id="delete_video_alert" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form class="row g-3 needs-validation-delete" id="delete_video_form" novalidate method="post">
                        <input type="hidden" value="" id="delete_video_id" name="delete_video_id" />
                        <div class="col-md-12">
                            Are you sure! You want to delete?
                        </div>

                        <div class="col-12 mt-5">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                            <button class="btn btn-primary delete-submit" type="submit" value="update" id="delete_video" name="delete_video">Yes</button>
                            <div class="spinner-border text-secondary delete-video-spinner d-none" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryLabel">Update video</h5>
                    <button type="button" class="close" id="xnpc_modal" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-dismissible fade show d-none" role="alert">
                        <button type="button" class="close" id="update_parent_cat_alert" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form class="row g-3 needs-validation-update" id="update_video_from" novalidate method="post" enctype="multipart/form-data">
                        <?php

                        if (!empty($profile)) {
                        ?>
                            <div class="form-group col-xl-6 col-lg-6">
                                <select class="form-select form-control" id="parentselect" name="parentselect" required>
                                    <option value="">Select parent category</option>
                                    <?php
                                    foreach ($categories as $cat) {
                                        $select = '';
                                        if ($cat['display_type'] == 'profile') {
                                            if ($cat['id'] == $profile[0]['catid']) {
                                                $select = 'selected';
                                            }
                                            echo '<option value="' . $cat['id'] . '" ' . $select . '>' . $cat['category'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                            </div>
                            <div class="form-group col-xl-6 col-lg-6">
                                <label class="form-select form-control" id="display_type" name="display_type">Display Type : </label>
                            </div>
                            <div class="form-group col-xl-6 col-lg-6">
                                <select class="form-select form-control" id="subcatselect" name="subcatselect">
                                    <option value="">Sub category</option>
                                </select>
                            </div>
                        <?php
                        } else {
                        ?>
                            <div class="form-group col-xl-12 col-lg-12">
                                <select class="form-select form-control" id="parentselect" name="parentselect" required>
                                    <option value="">Select parent category</option>
                                    <?php
                                    foreach ($categories as $cat) {
                                        if ($cat['display_type'] != 'profile') {
                                            echo '<option value="' . $cat['id'] . '" ' . $select . '>' . $cat['category'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-xl-12 col-lg-12">
                                <select class="form-select form-control" id="subcatselect" name="subcatselect">
                                    <option value="">Sub category</option>
                                </select>
                            </div>
                        <?php
                        }
                        ?>

                        <div class="form-group col-xl-12 col-lg-12">
                            <select class="form-select form-control" id="videotype" name="videotype" required>
                                <option value="">Video Type</option>
                                <option value="live">Live</option>
                                <option value="youtube">Youtube</option>
                                <option value="recorded">Recorded</option>
                                <option value="audio">Audio</option>
                            </select>
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                        </div>
                        <div class="form-group col-xl-12 col-lg-12">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" id="title" placeholder="Video title" required>
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                        </div>
                        <div class="form-group col-xl-12 col-lg-12">
                            <label for="image" class="form-label">Image</label>
                            <input type='file' class="form-control" name='image' id="image" placeholder="Image" accept="image/*" />
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                        </div>
                        <div class="form-group col-xl-12 col-lg-12" id="divvideo">
                            <label for="video" class="form-label">Video</label>
                            <input type='file' class="form-control" name='video' id="video" placeholder="Video" accept="video/*" />
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                        </div>
                        <div class="form-group col-xl-12 col-lg-12" id="divaudio">
                            <label for="video" class="form-label">Audio</label>
                            <input type='file' class="form-control" name='audio' id="audio" placeholder="Audio" accept="audio/wav, audio/aac" />
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                        </div>
                        <div class="form-group col-xl-12 col-lg-12 d-none" id="liveurl">Live URL
                            <input type="url" class="form-control" name="livelink" id="livelink" placeholder="http://example.com/livevideo/url">
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                        </div>
                        <div class="form-group col-xl-12 col-lg-12" id="descriptions">Description
                            <textarea class="form-control" name="description" id="description" placeholder="Video description"></textarea>
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                        </div>
                        <div class="form-group col-xl-12 col-lg-12">Tags
                            <input type="text" class="form-control" name="tags" id="tags" placeholder="Tags" data-role="tagsinput">
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                        </div>

                        <div class="col-12 mt-5">
                            <input type="hidden" value="" id="edit_video_id" name="edit_video_id" />
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button class="btn btn-primary update-submit" type="submit" value="update" id="update_video" name="update_video">Update</button>
                            <!-- Progress bar -->
                            <div class="progress">
                                <div class="progress-bar"></div>
                            </div>
                            <!-- Display upload status -->
                            <div id="uploadStatus"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- New Video /Audio Upload Modal -->
    <div class="modal fade" id="newCategoryModal" tabindex="-1" role="dialog" aria-labelledby="newCategoryLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newCategoryLabel">New video</h5>
                    <button type="button" class="close" id="xnpc_modal" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-dismissible fade show d-none" role="alert">
                        <button type="button" class="close" id="new_parent_cat_alert" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form class="row g-3 needs-validation-new" id="new_video_form" novalidate method="post" enctype="multipart/form-data">
                        <div class="form-group col-xl-12 col-lg-12">
                            <select class="form-select form-control" id="newparentselect" name="newparentselect" required>
                                <option value="">Select parent category</option>
                                <?php
                                foreach ($categories as $cat) {
                                    echo '<option value="' . $cat['id'] . '">' . $cat['category'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-xl-12 col-lg-12">
                            <select class="form-select form-control" id="newsubcatselect" name="newsubcatselect">
                                <option value="">Sub category</option>
                            </select>
                        </div>
                        <div class="form-group col-xl-12 col-lg-12">
                            <label for="newtitle" class="form-label">Title</label>
                            <input type="text" class="form-control" name="newtitle" id="newtitle" placeholder="Video title" required>
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                        </div>
                        <div class="form-group col-xl-12 col-lg-12">
                            <label for="newimage" class="form-label">Image</label>
                            <input type='file' class="form-control" name='image' id="newimage" placeholder="newImage" accept="image/*" />
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                        </div>
                        <div class="form-group col-xl-12 col-lg-12" id="newdivvideo">
                            <label for="newvideo" class="form-label">Video</label>
                            <input type='file' class="form-control" name='newvideo' id="newvideo" placeholder="Video" accept="video/*" />
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                        </div>
                        <div class="form-group col-xl-12 col-lg-12" id="newdivaudio">
                            <label for="newvideo" class="form-label">Audio</label>
                            <input type='file' class="form-control" name='newaudio' id="newaudio" placeholder="Audio" accept="audio/wav, audio/aac" />
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                        </div>
                        <!-- Progress bar -->
                        <div class="progress">
                            <div class="progress-bar"></div>
                        </div>
                        <!-- Display upload status -->
                        <div id="uploadStatus"></div>

                        <div class="col-12 mt-5">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button class="btn btn-primary new-submit" type="submit" value="new" id="new_video" name="new_video">Update</button>
                            <div class="spinner-border text-secondary new-video-spinner d-none" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "js_include.php"; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js" integrity="sha512-9UR1ynHntZdqHnwXKTaOm1s6V9fExqejKvg5XMawEMToW4sSw+3jtLrYfZPijvnwnnE8Uol1O9BcAskoxgec+g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    (function() {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation-update')
        Array.prototype.slice.call(forms)
            .forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    } else {
                        event.preventDefault();
                        var image = $("#image").prop("files")[0];
                        var video = $("#video").prop("files")[0];
                        var audio = $("#audio").prop("files")[0];
                        var title = $("#title").val();
                        var livelink = $("#livelink").val();
                        var videotype = $("#videotype").val();
                        var pcid = $("#parentselect").val();
                        var scid = $("#subcatselect").val();
                        var id = $("#edit_video_id").val();
                        var form_data = new FormData();
                        form_data.append("vid", id);
                        form_data.append("title", title);
                        form_data.append("videotype", videotype);
                        form_data.append("description", $('#description').val());
                        form_data.append("parent_id", pcid);
                        form_data.append("sub_id", scid);
                        form_data.append("image", image);
                        form_data.append("livelink", livelink);
                        form_data.append("video", video);
                        form_data.append("audio", audio);
                        form_data.append('tags', $('#tags').val());
                        form_data.append("update_video", 'update');
                        /*
                        $('.update-submit').addClass('d-none');
                        $('.update-video-spinner').removeClass('d-none');
                        $.ajax({
                            url: "ajax.php",
                            dataType: 'script',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,                         
                            type: 'post',
                            success: function(response){
                                console.log(response);
                                if(response == "OK"){
                                    $('#update_parent_cat_alert').removeClass('d-none').addClass('alert-success').text("New video added.");
                                    location.reload();
                                }else{
                                    $('#update_parent_cat_alert').removeClass('d-none').addClass('alert-danger').text("Failed to add new video! Please try later.");
                                }
                                $('.update-submit').removeClass('d-none');
                                $('.update-video-spinner').addClass('d-none');
                            }
                        });*/
                        $.ajax({
                            xhr: function() {
                                var xhr = new window.XMLHttpRequest();
                                xhr.upload.addEventListener("progress", function(evt) {
                                    if (evt.lengthComputable) {
                                        var percentComplete = ((evt.loaded / evt.total) * 100);
                                        $('.progress-bar').css("width", percentComplete.toFixed(1) + '%');
                                        //$(".progress-bar").width(percentComplete + '%');
                                        $(".progress-bar").html(percentComplete.toFixed(2) + '%');
                                    }
                                }, false);
                                return xhr;
                            },
                            type: 'POST',
                            url: 'ajax.php',
                            //data: new FormData(this),
                            data: form_data,
                            cache: false,
                            contentType: false,
                            processData: false,
                            dataType: "json",
                            type: 'post',

                            beforeSend: function() {
                                $(".progress-bar").width('0%');
                                $('#uploadStatus').html('<img src="img/loading.gif"/>');
                            },
                            error: function(jqXHR, exception) {
                                $('#uploadStatus').html('<p style="color:#EA4335;">' + jqXHR.responseText + '</p>');
                                //$('#uploadStatus').html('<p style="color:#EA4335;">File upload failed, please try again.</p>');
                            },
                            success: function(resp) {
                                if (resp.type == 'ok') {
                                    $('#new_video_form')[0].reset();
                                    $('#uploadStatus').html('<p class="' + resp.class + '">' + resp.msg + '</p>');
                                } else {
                                    $('#uploadStatus').html('<p class="' + resp.class + '">' + resp.msg + '</p>');
                                }
                            }
                        });
                    }

                    form.classList.add('was-validated')
                }, false)
            })
    })();

    $("#video").change(function() {
        var allowedTypes = ['video/mp4', 'video/mpeg', 'video/avi'];
        var file = this.files[0];
        var fileType = file.type;
        if (!allowedTypes.includes(fileType)) {
            alert('Please select a valid file (MPEG/AVI/MP4).');
            $("#video").val('');
            return false;
        }
    });

    (function() {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation-new')
        Array.prototype.slice.call(forms)
            .forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    } else {
                        event.preventDefault();
                        var image = $("#newimage").prop("files")[0];
                        var video = $("#newvideo").prop("files")[0];
                        var audio = $("#newaudio").prop("files")[0];
                        var title = $("#newtitle").val();
                        var pcid = $("#newparentselect").val();
                        var scid = $("#newsubcatselect").val();
                        var form_data = new FormData();
                        form_data.append("profileid", '<?php echo $profile_id; ?>');
                        form_data.append("title", title);
                        form_data.append("parent_id", pcid);
                        form_data.append("sub_id", scid);
                        form_data.append("image", image);
                        form_data.append("video", video);
                        form_data.append("audio", audio);
                        form_data.append("new_video", 'new');
                        //alert(form_data);
                        $('.new-submit').addClass('d-none');
                        $('.new-video-spinner').removeClass('d-none');

                        $.ajax({
                            xhr: function() {
                                var xhr = new window.XMLHttpRequest();
                                xhr.upload.addEventListener("progress", function(evt) {
                                    if (evt.lengthComputable) {
                                        var percentComplete = ((evt.loaded / evt.total) * 100);
                                        $(".progress-bar").width(percentComplete.toFixed(2) + '%');
                                        $(".progress-bar").html(percentComplete.toFixed(2) + '%');
                                    }
                                }, false);
                                return xhr;
                            },
                            url: "ajax.php",
                            dataType: 'script',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            type: 'post',
                            beforeSend: function() {
                                $(".progress-bar").width('0%');
                                $('#uploadStatus').html('<img src="img/loading.gif"/>');
                            },
                            error: function() {
                                $('#uploadStatus').html('<p style="color:#EA4335;">File upload failed, please try again.</p>');
                            },
                            success: function(resp) {
                                /*if(resp == 'ok'){
                                    $('#uploadForm')[0].reset();
                                    $('#uploadStatus').html('<p style="color:#28A74B;">File has uploaded successfully!</p>');
                                }else if(resp == 'err'){
                                    $('#uploadStatus').html('<p style="color:#EA4335;">Please select a valid file to upload.</p>');
                                }*/
                                if (resp == "OK") {
                                    $('#new_parent_cat_alert').removeClass('d-none').addClass('alert-success').text("New category added.");
                                    location.reload();
                                } else {
                                    $('#new_parent_cat_alert').removeClass('d-none').addClass('alert-danger').text("Failed to add new category! Please try later.");
                                }
                                $('.new-submit').removeClass('d-none');
                                $('.new-video-spinner').addClass('d-none');
                            }
                        });
                    }

                    form.classList.add('was-validated')
                }, false)
            })
    })();

    (function() {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation-delete')

        Array.prototype.slice.call(forms)
            .forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    } else {
                        event.preventDefault();
                        var values = $('#delete_video_form').serializeArray();
                        values.push({
                            name: "delete_video",
                            value: 'delete'
                        });
                        $('.delete-submit').addClass('d-none');
                        $('.delete-video-spinner').removeClass('d-none');
                        $.ajax({
                            url: "ajax.php",
                            type: "post",
                            data: values,
                            success: function(response) {
                                console.log(response);
                                if (response == "OK") {
                                    $('#delete_video_alert').removeClass('d-none').addClass('alert-success').text("Category deleted.");
                                    location.reload();
                                } else {
                                    $('#delete_video_alert').removeClass('d-none').addClass('alert-danger').text("Failed to delete category! Please try later.");
                                }
                                $('.delete-submit').removeClass('d-none');
                                $('.delete-video-spinner').addClass('d-none');
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.log(textStatus, errorThrown);
                            }
                        });
                    }

                    form.classList.add('was-validated')
                }, false)
            })
    })();

    function changeId(t, id) {
        if (t == 'd') {
            $('#delete_video_id').val(id);
        } else if (t == 'e') {
            $('#title').val($("#t" + id).text());
            if ($("#pi" + id).val() == '0') {
                $('#parentselect').val($("#ci" + id).val()).change();
            } else {
                $('#parentselect').val($("#pi" + id).val()).change();
                getSubCategory($('#parentselect').val());
                setTimeout(function() {
                    $("select#subcatselect").val($("#ci" + id).val()).change();
                }, 2000);
            }
            $('#tags').tagsinput('add', $("#tag" + id).val());
            $('#tags').val($("#tag" + id).val());
            $('#videotype').val($("#ty" + id).val());
            $('#description').val($("#desc" + id).text());
            $('#edit_video_id').val(id);
            $('#parentselect').change();
            $('#editCategoryModal').modal('show');
        }
    }

    $('#parentselect').on('change', function(e) {
        var optionSelected = $("option:selected", this);
        var valueSelected = this.value;
        //alert(valueSelected);
        if ($("#parentselect option:selected").text() == "Audios") {
            $('#editCategoryLabel').text("Update audio");
            $('#divvideo').addClass('d-none');
            $('#divaudio').removeClass('d-none');
        } else {
            $('#editCategoryLabel').text("Update video");
            $('#divaudio').addClass('d-none');
            $('#divvideo').removeClass('d-none');
        }
        getSubCategory(valueSelected);
    });

    $('#filter_category').on('change', function() {
        var formdata = new FormData();
        formdata.append('get', 'subcat');
        formdata.append('parentid', $('#filter_category').val());
        $.ajax({
            url: "ajax.php",
            type: "post",
            data: formdata,
            cache: false,
            contentType: false,
            dataType: "json",
            processData: false,
            success: function(response) {
                if (response.length == '0') {
                    window.location = './videos.php?c=' + $('#filter_category').val();
                } else if (response.length != '0') {
                    $('#filter_subcategory').html(response.option);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    });

    $('#filter_subcategory').on('change', function() {
        window.location = './videos.php?c=' + $('#filter_subcategory').val();
    });

    async function getSubCategory(valueSelected, replaceidhtml) {
        var formdata = new FormData();
        formdata.append('get', 'subcat');
        formdata.append('parentid', valueSelected);
        $.ajax({
            url: "ajax.php",
            type: "post",
            data: formdata,
            cache: false,
            contentType: false,
            dataType: "json",
            processData: false,
            success: function(response) {
                $('#subcatselect').html(response.option);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }

    $('#newparentselect').on('change', function(e) {
        var optionSelected = $("option:selected", this);
        var valueSelected = this.value;
        //alert(valueSelected);
        if ($("#newparentselect option:selected").text() == "Audio") {
            $('#newCategoryLabel').text("Update audio");
            $('#newdivvideo').addClass('d-none');
            $('#newdivaudio').removeClass('d-none');
        } else {
            $('#newCategoryLabel').text("Update video");
            $('#newdivaudio').addClass('d-none');
            $('#newdivvideo').removeClass('d-none');
        }
        var formdata = new FormData();
        formdata.append('get', 'subcat');
        formdata.append('parentid', valueSelected);
        $.ajax({
            url: "ajax.php",
            type: "post",
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#newsubcatselect').html(response);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    });

    $('#videotype').on('change', function(e) {
        var optionSelectedType = $("option:selected", this);
        var valueSelectedType = this.value;
        if (valueSelectedType == "live") {
            $('#divvideo').addClass('d-none');
            $('#divaudio').addClass('d-none');
            $('#liveurl').removeClass('d-none');
        } else if (valueSelectedType == "audio") {
            $('#divvideo').addClass('d-none');
            $('#divaudio').removeClass('d-none');
            $('#liveurl').addClass('d-none');
        } else {
            $('#liveurl').addClass('d-none');
            $('#divaudio').addClass('d-none');
            $('#divvideo').removeClass('d-none');
        }
    });

    function addExplore(vid) {
        var formdata = new FormData();
        formdata.append('videoid', vid);
        formdata.append('add_explore', 'add');
        $.ajax({
            url: "ajax-videos.php",
            type: "post",
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response == 'OK') {
                    location.reload();
                } else if (response != 'FAILED') {
                    alert(response);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }

    function removeExplore(vid) {
        var formdata = new FormData();
        formdata.append('videoid', vid);
        formdata.append('remove_explore', 'remove');
        $.ajax({
            url: "ajax-videos.php",
            type: "post",
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                location.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }


    $('#parent_cat_alert').on('click', function() {
        location.reload();
    });
</script>
<?php include "footer.php"; ?>