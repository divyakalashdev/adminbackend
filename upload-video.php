<?php
if (!isset($_COOKIE['mid'])) {
    header('Location: ./login.php');
    exit(0);
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'DB.class.php';
$db = new DB;
include "header.php";

$errorMsg = '';
$class = '';

$profile = '';
$profile_id = 0;
$redirect_url = '';
if (isset($_GET['pid'])) {
    $profile_id = $_GET['pid'];

    $sql = 'SELECT p.*, c.category, c.id as catid FROM `profiles` as p JOIN categories as c ON p.profile_type = c.id WHERE p.id = ' . $profile_id;
    $profile = $db->customQuery($sql);
    if (!empty($profile)) {
        $profile_id = $profile[0]['id'];
        $redirect_url = '?pid=' . $profile_id;
    } else {
        $profile_id = 0;
    }
}


$con = array(
    'order_by' => 'id ASC',
    'where' => array('parent_id' => 0)
);
$categories = $db->getRows('categories', $con);

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

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Upload Video<br>
                <?php
                if (!empty($profile)) {
                ?>
                    <br><img src="<?php echo $profile[0]['avatar']; ?>" width="100" /><br /><?php echo $profile[0]['name']; ?>
                <?php
                }
                ?>
            </h1>
        </div>

        <!-- Content Row -->

        <div class="row">

            <div class="col-xl-12 col-lg-12">
                <div class="">
                    <?php if (!empty($errorMsg)) { ?>
                        <p class="col-xs-12">
                        <div class="<?php echo $class; ?>">
                            <ul><?php echo $errorMsg; ?></ul>
                        </div>
                        </p>
                    <?php } ?>
                    <div id="msg"></div><br>
                    <form class="row g-3 needs-validation-new" id="new_video_form" method="post" enctype='multipart/form-data'>
                        <div class="row">

                            <?php

                            if (!empty($profile)) {
                            ?>
                                <div class="mb-3 col-xl-6 col-lg-6">
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
                                <div class="mb-3 col-xl-6 col-lg-6">
                                    <label class="form-label" id="display_type" name="display_type">Display Type : </label>
                                </div>
                                <div class="mb-3 col-xl-6 col-lg-6">
                                    <select class="form-select form-control" id="subcatselect" name="subcatselect">
                                        <option value="">Sub category</option>
                                    </select>
                                </div>
                            <?php
                            } else {
                            ?>
                                <div class="mb-3 col-xl-6 col-lg-6">
                                    <select class="form-select form-control" id="parentselect" name="parentselect" required>
                                        <option value="">Select parent category</option>
                                        <?php
                                        foreach ($categories as $cat) {
                                            //echo '<option value="'.$cat['id'].'">'.$cat['category'].'</option>';
                                            /*if($cat['display_type'] != 'profile')*/ {
                                                echo '<option value="' . $cat['id'] . '" >' . $cat['category'] . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="valid-feedback">
                                        Looks good!
                                    </div>
                                </div>
                                <div class="mb-3 col-xl-6 col-lg-6">
                                    <label class="form-label" id="display_type" name="display_type">Display Type : </label>
                                </div>
                                <div class="mb-3 col-xl-6 col-lg-6">
                                    <select class="form-select form-control" id="subcatselect" name="subcatselect">
                                        <option value="">Sub category</option>
                                    </select>
                                </div>
                            <?php
                            }
                            ?>
                            <div class="mb-3 col-xl-6 col-lg-6">
                                <select class="form-select form-control" id="videotype" name="videotype" required>
                                    <option value="">Media Type</option>
                                    <option value="live">Live</option>
                                    <option value="youtube">Youtube</option>
                                    <option value="recorded">Recorded</option>
                                    <option value="audio">Audio</option>
                                </select>
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                            </div>
                            <div class="mb-3 col-xl-6 col-lg-6">
                                <input type="text" class="form-control" name="title" id="title" placeholder="Title" required>
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                            </div>
                            <div class="mb-3 col-xl-6 col-lg-6">Image
                                <input type='file' class="form-control" name='image' placeholder="Image" accept="image/*" required />
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                            </div>
                            <div class="mb-3 col-xl-6 col-lg-6" id="divvideo">Video
                                <input type='file' class="form-control" name='video' id="video" placeholder="Video" accept="video/*" />
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                            </div>
                            <div class="mb-3 col-xl-6 col-lg-6 d-none" id="liveurl">Video URL
                                <input type="url" class="form-control" name="livelink" id="livelink" placeholder="http://example.com/livevideo/url">
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                            </div>
                            <div class="mb-3 col-xl-6 col-lg-6 d-none" id="divaudio">Audio
                                <input type='file' class="form-control" name='audio' placeholder="Audio" accept="audio/wav, audio/aac, audio/mp3" />
                                <!--<div class="valid-feedback">
                                    Looks good!
                                </div>-->
                            </div>
                            <div class="mb-3" id="descriptions">Description
                                <textarea class="form-control" name='description' placeholder="Description"></textarea>
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                            </div>
                            <div class="mb-3" id="tags">Tags
                                <input type="text" class="form-control" name="tags" id="tags" placeholder="Tags" data-role="tagsinput">
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                            </div>
                            <div class="mb-3 col-xl-6 col-lg-6">
                                <?php
                                if (!empty($profile)) {
                                ?>
                                    <input type="hidden" value="<?php echo $profile[0]['id']; ?>" name="profile_id">
                                <?php
                                } else {
                                ?>
                                    <input type="hidden" value="0" name="profile_id">
                                <?php
                                }
                                ?>
                                <input type="hidden" name="submit_new_video" value="newvideo" />
                                <button type="submit" class="btn btn-primary" id="btn_submit" name="btn_submit" value="submit_video">Submit</button>
                            </div>
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
    <!-- /.container-fluid -->
</div>
<?php include "js_include.php"; ?>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js" integrity="sha512-9UR1ynHntZdqHnwXKTaOm1s6V9fExqejKvg5XMawEMToW4sSw+3jtLrYfZPijvnwnnE8Uol1O9BcAskoxgec+g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $(document).ready(function() {
        // File upload via Ajax
        $("#new_video_form").on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = ((evt.loaded / evt.total) * 100);
                            $('.progress-bar').css("width", percentComplete.toFixed(1) + '%');
                            //$(".progress-bar").width(percentComplete.toFixed(1) + '%');
                            $(".progress-bar").html(percentComplete.toFixed(1) + '%');
                        }
                    }, false);
                    return xhr;
                },
                type: 'POST',
                url: 'ajax-videos.php',
                data: new FormData(this),
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                type: 'post',

                beforeSend: function() {
                    $(".progress-bar").width('0%');
                    $('#uploadStatus').html('<img src="img/loading.gif" width="50px"/>');
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
        });

        // File type validation
        $("#video").change(function() {
            var allowedTypes = ['video/mp4', 'video/mpeg', 'video/avi', 'video/mkv'];
            var file = this.files[0];
            var fileType = file.type;
            if (!allowedTypes.includes(fileType)) {
                alert('Please select a valid file (MPEG/AVI/MP4).');
                $("#video").val('');
                return false;
            }
        });
    });

    $('#parentselect').on('change', function(e) {
        var optionSelected = $("option:selected", this);
        var valueSelected = this.value;

        if ($("#parentselect option:selected").text() == "Audios") {
            $('#divvideo').addClass('d-none');
            $('#divaudio').removeClass('d-none');
        } else {
            $('#divaudio').addClass('d-none');
            $('#divvideo').removeClass('d-none');
        }

        var formdata = new FormData();
        formdata.append('get', 'subcat');
        formdata.append('parentid', valueSelected);
        $.ajax({
            url: "ajax.php",
            //dataType: 'script',
            cache: false,
            contentType: false,
            processData: false,
            data: formdata,
            dataType: "json",
            type: 'post',
            success: function(response) {
                //alert(response.displaytype);
                if (response.displaytype == 'profile') {
                    //$('#subcatselect').hide();
                    $('#display_type').text("Display Type : " + response.displaytype.toUpperCase());
                } else {
                    $('#subcatselect').show();
                    $('#subcatselect').html(response.option);
                    $('#display_type').text("Display Type : " + response.displaytype.toUpperCase());
                }
            }
        });
    });
    $('#videotype').on('change', function(e) {
        var optionSelectedType = $("option:selected", this);
        var valueSelectedType = this.value;
        if (valueSelectedType == "live" && $("#parentselect option:selected").text() == "Audio") {
            $('#divvideo').addClass('d-none');
            $('#divaudio').addClass('d-none');
            $('#liveurl').removeClass('d-none');
        } else if (valueSelectedType != "live" && $("#parentselect option:selected").text() == "Audio") {
            $('#divvideo').addClass('d-none');
            $('#divaudio').removeClass('d-none');
            $('#liveurl').addClass('d-none');
        } else if (valueSelectedType == "live" && $("#parentselect option:selected").text() != "Audio") {
            $('#divvideo').addClass('d-none');
            $('#divaudio').addClass('d-none');
            $('#liveurl').removeClass('d-none');
        } else if (valueSelectedType == "audio") {
            $('#divvideo').addClass('d-none');
            $('#divaudio').removeClass('d-none');
            $('#liveurl').addClass('d-none');
        } else if (valueSelectedType == 'youtube') {
            $('#divvideo').addClass('d-none');
            $('#divaudio').addClass('d-none');
            $('#liveurl').removeClass('d-none');
        } else {
            $('#liveurl').addClass('d-none');
            $('#divaudio').addClass('d-none');
            $('#divvideo').removeClass('d-none');
        }
    });
    $('#parentselect').trigger('change');
</script>

<?php include "footer.php"; ?>