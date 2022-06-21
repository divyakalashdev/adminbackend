<?php
if (!isset($_COOKIE['mid'])) {
  header('Location: ./login.php');
  exit(0);
}
include 'DB.class.php';
include 'functions.php';
$db = new DB;
include "header.php";
include_once 'Pagination.php';

$limit = 10;
// Paging limit & offset
$offset = !empty($_GET['page']) ? (($_GET['page'] - 1) * $limit) : 0;
$con = array(
  'start' => $offset,
  'limit' => $limit
);
$rowCount = 0;
$rows = $db->getRows('profiles');
if (!empty($rows)) {
  $rowCount = count($rows);
}


$pagConfig = array(
  //'baseURL' => 'index.php'.$searchStr,
  'baseURL' => 'profiles.php',
  'totalRows' => $rowCount,
  'perPage' => $limit
);

$pagination = new Pagination($pagConfig);

$sql = "SELECT tags.profile_id, tags.tags, p.*, c.category, c.display_type as type FROM `profiles` as p LEFT JOIN categories as c ON p.profile_type = c.id LEFT JOIN tags ON tags.profile_id = p.id ORDER BY id DESC";
$profilelist = $db->customQuery($sql);


$conc = array(
  'order_by' => 'id ASC',
  'where' => array('display_type' => 'profile')
);
$categories = $db->getRows('categories', $conc);
$random = rand(10, 100);
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
    background-color: #628eba;
    border-radius: .25rem;
    margin-top: 10px;
    width: 100%;
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

  .img-wraps {
    width: 50%;
    position: relative;
    display: inline-block;
    font-size: 0;
    float: left;
    padding: 2px;
  }

  .img-wraps .closes {
    position: absolute;
    top: 5px;
    right: 8px;
    z-index: 100;
    background-color: #FFF;
    padding: 4px 3px;

    color: #000;
    font-weight: bold;
    cursor: pointer;

    text-align: center;
    font-size: 22px;
    line-height: 10px;
    border-radius: 50%;
    border: 1px solid red;
  }

  .img-wraps:hover .closes {
    opacity: 1;
  }

  .img-responsive {
    width: 100%;
  }
</style>

<!-- Begin Page Content -->
<div class="main_content_iner ">
  <div class="container-fluid p-0 ">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0 text-gray-800">Profiles</h1>
    </div>
    <div class="row">
      <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
          <!-- Card Header - Dropdown -->
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Profiles</h6>
            <!--<select class="col-xl-3 col-lg-3 form-select form-control" id="category" name="category">
                        <option value="">Select Profile Type</option>
                        <?php
                        foreach ($categories as $cat) {
                          echo '<option value="' . $cat['id'] . '">' . $cat['category'] . '</option>';
                        }
                        ?>
                    </select>-->
            <div>
              <button class="btn btn-danger btn-xs" data-toggle="modal" data-target="#newProfileModal">New Profile</button>
            </div>
          </div>
          <!-- Card Body -->
          <div class="card-body">
            <div class="user_list">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Id</th>
                      <th>Name</th>
                      <th>Description</th>
                      <th>Tags</th>
                      <th>Avatar</th>
                      <th>Poster</th>
                      <th>Category</th>
                      <th>Upload Date</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>

                    <?php
                    if (!empty($profilelist)) {
                      foreach ($profilelist as $profile) {
                        echo '<tr>
                                    <td>' . $profile['id'] . '</td>
                                    <td id="t' . $profile['id'] . '">' . $profile['name'] . '</td>
                                    <td id="d' . $profile['id'] . '">' . $profile['description'] . '</td>
                                    <td >' . $profile['tags'] . '</td>
                                    <td id="a' . $profile['id'] . '"><img src="' . $profile['avatar'] . '?s=' . $random . '" width="150" /></td>
                                    <td id="p' . $profile['id'] . '"><img src="' . $profile['poster'] . '?s=' . $random . '" width="150" /></td>
                                    <td>' . $profile['category'] . '</td>
                                    <td>' . $profile['created_at'] . '</td>
                                    <td>
                                        <input type="hidden" id="tag' . $profile['id'] . '" value="' . $profile['tags'] . '" />
                                        <input type="hidden" id="catid' . $profile['id'] . '" value="' . $profile['profile_type'] . '" />
                                        <a class="btn btn-danger btn-xs" href="./videos.php?pid=' . $profile['id'] . '"><span class="fa fa-eye"></span></a>
                                        <button class="btn btn-danger btn-xs" onclick="loadPosters(\'' . $profile['id'] . '\')"><span class="fa fa-blog"></span></button>
                                        <button class="btn btn-danger btn-xs" onclick="changeId(\'e\', \'' . $profile['id'] . '\')"><span class="fa fa-edit"></span></button>
                                        <button class="btn btn-danger btn-xs" data-toggle="modal" data-target="#deleteProfileModal" onclick="changeId(\'d\', \'' . $profile['id'] . '\')"><span class="fa fa-trash"></span></button>
                                    </td>
                                  </tr>';
                      }
                    }
                    ?>

                  </tbody>
                </table>
                <?php echo $pagination->createLinks(); ?>

                <?php
                if (empty($profilelist)) {
                  echo "No data found";
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <!-- Modal -->
  <div class="modal fade" id="viewPosters" tabindex="-1" role="dialog" aria-labelledby="viewPostersLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewPostersLabel">Profile posters</h5>
          <button type="button" class="close" id="xnpc_modal" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="posters-list text-center">

          </div>
          <div class="text-center">
            <div class="spinner-border text-secondary posters-loading d-none" role="status">
              <span class="sr-only">Loading...</span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <input type="file" id="newimage" name="newimage" />
          <input type="hidden" id="myprofileid" />
          <button type="button" class="btn btn-primary" id="upload_poster">Upload</button>
        </div>
      </div>
    </div>
  </div>


  <!-- Modal -->
  <div class="modal fade" id="deleteProfileModal" tabindex="-1" role="dialog" aria-labelledby="deleteProfileLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteProfileLabel">Delete Profile</h5>
          <button type="button" class="close" id="xnpc_modal" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="alert alert-dismissible fade show d-none" role="alert">
            <button type="button" class="close" id="delete_profile_alert" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form class="row g-3 needs-validation-delete" id="delete_profile_form" novalidate method="post">
            <input type="hidden" value="" id="delete_profile_id" name="delete_profile_id" />
            <div class="col-md-12">
              Are you sure! You want to delete?
            </div>

            <div class="col-12 mt-5">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
              <button class="btn btn-primary delete-submit" type="submit" value="update" id="delete_video" name="delete_video">Yes</button>
              <div class="spinner-border text-secondary delete-profile-spinner d-none" role="status">
                <span class="sr-only">Loading...</span>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="newProfileModal" tabindex="-1" role="dialog" aria-labelledby="updateProfileLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="updateProfileLabel">New Profile</h5>
          <button type="button" class="close" id="xnpc_modal" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="alert alert-dismissible fade show d-none" role="alert">
            <button type="button" class="close" id="save_profile_alert" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form class="row g-3 needs-validation-new" id="new_profile_from" novalidate method="post" enctype="multipart/form-data">
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
              <label for="newname" class="form-label">Profile Name</label>
              <input type="text" class="form-control" name="newname" id="newname" placeholder="Profile name" required>
              <div class="valid-feedback">
                Looks good!
              </div>
            </div>
            <div class="form-group col-xl-12 col-lg-12">
              <label for="description" class="form-label">Description</label>
              <textarea class="form-control" name='newdescription' id="newdescription" placeholder="Description"></textarea>
              <div class="valid-feedback">
                Looks good!
              </div>
            </div>
            <div class="form-group col-xl-12 col-lg-12">
              <label for="newprofile_pic" class="form-label">Profile Picture</label>
              <input type='file' class="form-control" name='newprofile_pic' id="newprofile_pic" placeholder="Profile Picture" accept="image/*" />
              <div class="valid-feedback">
                Looks good!
              </div>
            </div>
            <div class="form-group col-xl-12 col-lg-12">
              <label for="newposter" class="form-label">Image</label>
              <input type='file' class="form-control" name='newposter' id="newposter" placeholder="Poster" accept="image/*" />
              <div class="valid-feedback">
                Looks good!
              </div>
            </div>
            <div class="form-group col-xl-12 col-lg-12">Tags
              <input type="text" class="form-control" name="newtags" id="newtags" placeholder="Tags" data-role="tagsinput">
              <div class="valid-feedback">
                Looks good!
              </div>
            </div>

            <div class="col-12 mt-5">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button class="btn btn-primary new-submit" type="submit" value="save" id="new_profile" name="new_profile">Save</button>
              <div class="spinner-border text-secondary new-profile-spinner d-none" role="status">
                <span class="sr-only">Loading...</span>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- New Video /Audio Upload Modal -->
  <div class="modal fade" id="updateProfileModal" tabindex="-1" role="dialog" aria-labelledby="newCategoryLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="newCategoryLabel">Update Profile</h5>
          <button type="button" class="close" id="xnpc_modal" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="alert alert-dismissible fade show d-none" role="alert">
            <button type="button" class="close" id="update_profile_alert" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form class="row g-3 needs-validation-update" id="update_profile_form" novalidate method="post" enctype="multipart/form-data">
            <div class="form-group col-xl-12 col-lg-12">
              <select class="form-select form-control" id="updateparentselect" name="updateparentselect" required>
                <option value="">Select parent category</option>
                <?php
                foreach ($categories as $cat) {
                  echo '<option value="' . $cat['id'] . '">' . $cat['category'] . '</option>';
                }
                ?>
              </select>
            </div>

            <div class="form-group col-xl-12 col-lg-12">
              <label for="updatename" class="form-label">Profile Name</label>
              <input type="text" class="form-control" name="updatename" id="updatename" placeholder="Profile name" required>
              <div class="valid-feedback">
                Looks good!
              </div>
            </div>
            <div class="form-group col-xl-12 col-lg-12">
              <label for="description" class="form-label">Description</label>
              <textarea class="form-control" name='updatedescription' id="updatedescription" placeholder="Description"></textarea>
              <div class="valid-feedback">
                Looks good!
              </div>
            </div>
            <div class="form-group col-xl-12 col-lg-12">
              <label for="updateprofile_pic" class="form-label">Profile Picture</label>
              <input type='file' class="form-control" name='updateprofile_pic' id="updateprofile_pic" placeholder="Profile Picture" accept="image/*" />
              <div class="valid-feedback">
                Looks good!
              </div>
            </div>
            <div class="form-group col-xl-12 col-lg-12">
              <label for="poster" class="form-label">Image</label>
              <input type='file' class="form-control" name='updateposter' id="updateposter" placeholder="Poster" accept="image/*" />
              <div class="valid-feedback">
                Looks good!
              </div>
            </div>
            <div class="form-group col-xl-12 col-lg-12">Tags
              <input type="text" class="form-control" name="updatetags" id="updatetags" placeholder="Tags" data-role="tagsinput">
              <div class="valid-feedback">
                Looks good!
              </div>
            </div>

            <div class="col-12 mt-5">
              <input type="hidden" id="update_profile_id" name="update_profile_id" />
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button class="btn btn-primary update-submit" type="submit" value="update" id="update_profile" name="update_profile">Update</button>
              <div class="spinner-border text-secondary update-profile-spinner d-none" role="status">
                <span class="sr-only">Loading...</span>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="confirm" tabindex="-1" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">

        <div class="modal-body">
          Are you sure?
        </div>
        <div class="modal-footer">
          <input type="hidden" id="delposterid" name="delposterid" />
          <button type="button" data-dismiss="modal" class="btn btn-primary" id="delete_poster">Delete</button>
          <button type="button" data-dismiss="modal" class="btn">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include "js_include.php"; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js" integrity="sha512-9UR1ynHntZdqHnwXKTaOm1s6V9fExqejKvg5XMawEMToW4sSw+3jtLrYfZPijvnwnnE8Uol1O9BcAskoxgec+g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
  function loadPosters(id) {
    getProfilePosters(id);
    $('#myprofileid').val(id);
    $('#viewPosters').modal('show');
  }

  function deletePoster(id) {
    //alert(id);
    $('#delposterid').val(id);
    $('#confirm').modal('show');
  }

  $('#delete_poster').on('click', function() {
    $('#posters-loading').removeClass("d-none");
    var posterid = $('#delposterid').val();
    $.ajax({
      url: "ajax-profile.php",
      data: 'delete_poster=delete&posterid=' + posterid,
      type: 'post',
      success: function(response) {
        $('#posters-loading').addClass("d-none");
        if (response != 'FAILED') {
          //$('#imgdiv'+posterid).remove();
          $('.posters-list').html(response);
          //getProfilePosters($('#myprofileid').val());
        }
      }
    });
  });

  $('#upload_poster').on('click', function() {
    $('#posters-loading').removeClass("d-none");
    var image = $("#newimage").prop("files")[0];
    var form_data = new FormData();
    form_data.append("profileid", $('#myprofileid').val());
    form_data.append("posterimage", image);
    form_data.append("upload_poster", 'save');
    $.ajax({
      url: "ajax-profile.php",
      data: form_data,
      dataType: 'script',
      cache: false,
      contentType: false,
      processData: false,
      data: form_data,
      type: 'post',
      success: function(response) {
        $('#posters-loading').addClass("d-none");
        if (response != "FAILED") {
          $('#posterimage').val('');
          $('.posters-list').html(response);
          //getProfilePosters($('#myprofileid').val());
        }
      }
    });
  });

  function getProfilePosters(pid) {
    $('#posters-loading').removeClass("d-none");
    $.ajax({
      url: "ajax-profile.php",
      data: 'profile_posters=posters&pid=' + pid,
      type: 'post',
      success: function(response) {
        $('.posters-list').html(response);
        $('#posters-loading').addClass("d-none");
      }
    });
  }

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
            var image = $("#newprofile_pic").prop("files")[0];
            var newposter = $("#newposter").prop("files")[0];
            var name = $("#newname").val();
            var description = $("#newdescription").val();
            var pcid = $("#newparentselect").val();

            var form_data = new FormData();
            /*var filedata = document.getElementsByName("newposter");
            var i = 0, len = $("#newposter").prop("files").length, img, reader, file;

            for (; i < len; i++) {
                file = $("#newposter").prop("files")[i];
                form_data.append("poster[]", file);
            }*/

            //var newposter = $("#newposter").prop("files");
            form_data.append("catid", pcid);
            form_data.append("name", name);
            form_data.append("description", description);
            form_data.append("profilepic", image);
            form_data.append("poster", newposter);
            form_data.append("tags", $("#newtags").val());
            form_data.append("save_profile", 'save');
            //alert(form_data);
            $('.new-submit').addClass('d-none');
            $('.new-profile-spinner').removeClass('d-none');
            $.ajax({
              url: "ajax-profile.php",
              dataType: 'script',
              cache: false,
              contentType: false,
              processData: false,
              data: form_data,
              type: 'post',
              success: function(response) {
                if (response == "OK") {
                  $('#save_profile_alert').removeClass('d-none').addClass('alert-success').text("New category added.");
                  location.reload();
                } else {
                  $('#save_profile_alert').removeClass('d-none').addClass('alert-danger').text("Failed to add new category! Please try later.");
                }
                $('.new-submit').removeClass('d-none');
                $('.new-profile-spinner').addClass('d-none');
              }
            });
          }

          form.classList.add('was-validated')
        }, false)
      })
  })();

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

            var form_data = new FormData();
            var profileid = $("#update_profile_id").val();
            var image = $("#updateprofile_pic").prop("files")[0];
            var poster = $("#updateposter").prop("files")[0];

            /*var filedata = document.getElementsByName("updateposter");
            var i = 0, len = $("#updateposter").prop("files").length, img, reader, file;

            for (; i < len; i++) {
                file = $("#updateposter").prop("files")[i];
                form_data.append("poster[]", file);
            }*/

            //var newposter = $("#updateposter").prop("files");
            var name = $("#updatename").val();
            var description = $("#updatedescription").val();
            var pcid = $("#updateparentselect").val();


            form_data.append("pid", profileid);
            form_data.append("catid", pcid);
            form_data.append("name", name);
            form_data.append("description", description);
            form_data.append("profilepic", image);
            form_data.append("poster", poster);
            form_data.append("tags", $("#updatetags").val());
            form_data.append("update_profile", 'update');
            //alert(form_data);
            $('.update-submit').addClass('d-none');
            $('.update-profile-spinner').removeClass('d-none');
            $.ajax({
              url: "ajax-profile.php",
              dataType: 'script',
              cache: false,
              contentType: false,
              processData: false,
              data: form_data,
              type: 'post',
              success: function(response) {
                if (response == "OK") {
                  $('#update_profile_alert').removeClass('d-none').addClass('alert-success').text("New profile saved.");
                  location.reload();
                } else {
                  $('#update_profile_alert').removeClass('d-none').addClass('alert-danger').text("Failed to save new profile! Please try later.");
                }
                $('.update-submit').removeClass('d-none');
                $('.update-profile-spinner').addClass('d-none');
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
            var values = $('#delete_profile_form').serializeArray();
            values.push({
              name: "delete_profile",
              value: 'delete'
            });
            $('.delete-submit').addClass('d-none');
            $('.delete-profile-spinner').removeClass('d-none');
            $.ajax({
              url: "ajax-profile.php",
              type: "post",
              data: values,
              success: function(response) {
                if (response == "OK") {
                  $('#delete_profile_alert').removeClass('d-none').addClass('alert-success').text("Category deleted.");
                  location.reload();
                } else {
                  $('#delete_profile_alert').removeClass('d-none').addClass('alert-danger').text("Failed to delete category! Please try later.");
                }
                $('.delete-submit').removeClass('d-none');
                $('.delete-profile-spinner').addClass('d-none');
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
      $('#delete_profile_id').val(id);
    } else if (t == 'e') {
      $('#updatetags').tagsinput('add', $("#tag" + id).val());
      $('#updatetags').val($("#tag" + id).val());
      $('#updateparentselect').val($('#catid' + id).val());
      $('#updatename').val($("#t" + id).text());
      $('#updatedescription').val($("#d" + id).text());
      $('#update_profile_id').val(id);
      $('#updateProfileModal').modal('show');
    }
  }

  $('#parentselect').on('change', function(e) {
    var optionSelected = $("option:selected", this);
    var valueSelected = this.value;
    //alert(valueSelected);
    if ($("#parentselect option:selected").text() == "Audio") {
      $('#updateProfileLabel').text("Update audio");
      $('#divvideo').addClass('d-none');
      $('#divaudio').removeClass('d-none');
    } else {
      $('#updateProfileLabel').text("Update video");
      $('#divaudio').addClass('d-none');
      $('#divvideo').removeClass('d-none');
    }
    getSubCategory(valueSelected);
  });

  async function getSubCategory(valueSelected) {
    var formdata = new FormData();
    formdata.append('get', 'subcat');
    formdata.append('parentid', valueSelected);
    $.ajax({
      url: "ajax-profile.php",
      type: "post",
      data: formdata,
      cache: false,
      contentType: false,
      processData: false,
      success: function(response) {
        $('#subcatselect').html(response);
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
      url: "ajax-profile.php",
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
      $('#liveurl').removeClass('d-none');
    } else {
      $('#liveurl').addClass('d-none');
      $('#divvideo').removeClass('d-none');
    }
  });

  $('#parent_cat_alert').on('click', function() {
    location.reload();
  });
</script>
<?php include "footer.php"; ?>