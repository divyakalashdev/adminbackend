<?php
if (!isset($_COOKIE['mid'])) {
  header('Location: ./login.php');
  exit(0);
}
include 'DB.class.php';
include 'functions.php';
$db = new DB;
include "header.php";

$parent_id = 0;
$type = '';
if (isset($_GET['t'])) {
  $type = $_GET['t'];
}
if (isset($_GET['pid'])) {
  $parent_id = $_GET['pid'];
}
if ($type == 'e') {
  $sub_id = $_GET['pid'];
  $subcats = $db->getRows('categories', array('where' => array('id' => $sub_id), 'return_type' => 'single'));
  $posters = $db->getRows('category_posters', array('where' => array('cat_id' => $sub_id)));
}


?>
<link href="./css/bootstrap.min.css" rel="stylesheet">
<link href="./css/fileinput.css" media="all" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" media="all" rel="stylesheet" type="text/css" />
<style>
  ul {
    padding: 20px;
    list-style-type: decimal;

  }

  ul li {
    margin: 0 10px;
    padding: 0 10px;
  }

  .tab {
    width: 50px;
  }

  .thumbimage-area {
    position: relative;
    width: 19%;
    /* background: #333; */
  }

  .thumbimage-area img {
    max-width: 100%;
    height: auto;
  }

  .image-area {
    position: relative;
    width: 30%;
    /* background: #333; */
  }

  .image-area img {
    max-width: 100%;
    height: auto;
  }

  .main-section {
    margin: 0 auto;
    padding: 20px;
    margin-top: 100px;
    background-color: #fff;
    box-shadow: 0px 0px 20px #c1c1c1;
  }

  .fileinput-remove,
  .fileinput-upload {
    display: none;
  }

  .kv-file-upload {
    display: none;
  }

  .file-upload-indicator {
    display: none;
  }

  .file-drag-handle {
    display: none;
  }
</style>
<!-- Begin Page Content -->
<div class="main_content_iner ">
  <div class="container-fluid p-0 ">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0 text-gray-800">Categories</h1>
    </div>

    <?php

    if ($type == 'n') {
    ?>
      <div class="row">
        <div class="col-xl-12 col-lg-12">
          <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
              <h6 class="m-0 font-weight-bold text-primary">Sub Category</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
              <div class="user_list">
                <form class="row g-3 needs-validation-addsubcat" id="new_sub_category_form" novalidate method="post" enctype="multipart/form-data">
                  <input type="hidden" id="parent_cat_id" name="parent_cat_id" value="<?php echo $parent_id; ?>" />
                  <div class="col-md-12">
                    <label for="parentCatName" class="form-label">Sub category name *</label>
                    <input type="text" class="form-control" id="subCatName" value="" name="subcategory_name" required>
                    <div class="valid-feedback">
                      Looks good!
                    </div>
                  </div>

                  <div class="col-md-12">
                    <label for="description" class="form-label">Description</label>
                    <textarea type="text" class="form-control" id="description" name="description"></textarea>
                  </div>

                  <div class="col-md-12">
                    <label for="newimage" class="form-label">Thumbnail *</label>
                    <input type="file" class="form-control" id="newimage" name="newimage" required />
                    <div class="valid-feedback">
                      Looks good!
                    </div>
                  </div>

                  <div class="thumbimage-area m-2">
                    <img src="https://via.placeholder.com/240x320.png" alt="Preview" id="mthumbnail">
                  </div>
                  <label for="newimage" class="form-label">Posters *</label>


                  <div class="file-loading">
                    <input id="posters" type="file" multiple class="file" data-overwrite-initial="false" data-min-file-count="1" required>
                    <div class="valid-feedback">
                      Looks good!
                    </div>
                  </div>


                  <div class="col-12 mt-5">
                    <button type="button" id="cnpc_modal" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary new-subcat-submit" type="submit" value="add" name="submit_sub_category">Save</button>
                    <div class="spinner-border text-secondary new-scat-spinner d-none" role="status">
                      <span class="sr-only">Loading...</span>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php
    } else if ($type == 'e' && !empty($subcats)) { ?>
      <div class="row">
        <div class="col-xl-12 col-lg-12">
          <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
              <h6 class="m-0 font-weight-bold text-primary">Sub Category</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
              <div class="user_list">
                <form class="row g-3 needs-validation-updatesubcat" id="update_sub_category_form" novalidate method="post" enctype="multipart/form-data">
                  <input type="hidden" id="sub_cat_id" name="sub_cat_id" value="<?php echo  $subcats['id']; ?>" />
                  <div class="col-md-12">
                    <label for="parentCatName" class="form-label">Sub category name *</label>
                    <input type="text" class="form-control" id="subCatName" value="<?php echo $subcats['category']; ?>" name="subcategory_name" required>
                    <div class="valid-feedback">
                      Looks good!
                    </div>
                  </div>

                  <div class="col-md-12">
                    <label for="description" class="form-label">Description</label>
                    <textarea type="text" class="form-control" id="description" name="description"><?php echo $subcats['description']; ?></textarea>
                  </div>

                  <div class="col-md-12">
                    <label for="newimage" class="form-label">Thumbnail *</label>
                    <input type="file" class="form-control" id="newimage" name="newimage" />
                    <div class="valid-feedback">
                      Looks good!
                    </div>
                  </div>

                  <div class="thumbimage-area m-2">
                    <img src="<?php echo $subcats['thumbnail']; ?>" alt="Preview" id="mthumbnail">
                  </div>
                  <label for="newimage" class="form-label">Posters (If you wish to remove some posters just remove from below)</label>


                  <div class="file-loading">
                    <input id="posters" type="file" multiple class="file" data-overwrite-initial="false" data-min-file-count="1">
                    <div class="valid-feedback">
                      Looks good!
                    </div>
                  </div>


                  <div class="col-12 mt-5">
                    <button type="button" id="cnpc_modal" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary update-subcat-submit" type="submit" value="add" name="update_sub_category">Save</button>
                    <div class="spinner-border text-secondary update-scat-spinner d-none" role="status">
                      <span class="sr-only">Loading...</span>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php
    }
    ?>


  </div>
</div>


<!-- Delete Image Poster Confirmation Dialg -->
<div class="modal fade" id="deletePoster" tabindex="-1" role="dialog" aria-labelledby="subCategoryLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="subCategoryLabel">Delete</h5>
        <button type="button" class="close" id="xnpc_modal" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Are your sure you want to delete poster? Once deleted data wouldn't be rolled back.</p>
        <div class="alert alert-danger d-none" role="alert" id="deletepostermsg"></div>
      </div>
      <div class="modal-footer">
        <input type="hidden" id="delposterid" />
        <input type="hidden" id="delposterdiv" />
        <button type="button" class="btn btn-primary" onclick="deletePoster()">Yes</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
      </div>
    </div>
  </div>
</div>
<?php include "js_include.php"; ?>


<script src="./js/fileinput.js" type="text/javascript"></script>
<script src="./js/theme.js" type="text/javascript"></script>
<script>
  var urllist = [];
  var keylist = [];
  <?php
  if ($type == 'n') {
  ?>
    $("#posters").fileinput({
      theme: 'fa',
      uploadUrl: '#',
      showUpload: false,
      allowedFileExtensions: ['jpg', 'jpeg', 'png'],
      overwriteInitial: false,
      maxFilesNum: 10,
      slugCallback: function(filename) {
        return filename.replace('(', '_').replace(']', '_');
      }
    });
  <?php
  } else if ($type == 'e') {
    $urllist = '';
    $key = '';
    if (!empty($posters)) {
      $key .= 'keylist = [';
      foreach ($posters as $p) {
        $urllist .= 'urllist.push(\'' . $p['poster'] . '\');';
        $key .= '{caption: \'' . str_replace("category/subcats/posters/", "",  $p['poster']) . '\', size: ' . filesize($p['poster']) . ', key: ' . $p['id'] . '},';
      }
      $key .= '],';
      echo $key;
      echo $urllist;
    }


  ?>
    $("#posters").fileinput({
      theme: 'fa',
      uploadUrl: '#',
      showUpload: false,
      allowedFileExtensions: ['jpg', 'jpeg', 'png'],
      overwriteInitial: false,
      //maxFileSize: 2000,
      initialPreview: urllist,
      initialPreviewAsData: true,
      initialPreviewConfig: keylist,
      maxFilesNum: 10,
      slugCallback: function(filename) {
        return filename.replace('(', '_').replace(']', '_');
      }
    });
  <?php
  }
  ?>

  $(".kv-file-remove").on("click", function() {
    var dataId = $(this).attr("data-key");
    var divId = $(this).closest('.file-preview-frame').attr('data-fileindex');
    $('#delposterid').val(dataId);
    $('#delposterdiv').val(divId);
    $('#deletePoster').modal('show');
  });

  function deletePoster() {
    var form_data = new FormData();
    form_data.append("posterid", $('#delposterid').val());
    form_data.append("del_subcat_posters", 'delete');

    $.ajax({
      url: "ajax.php",
      type: "post",
      data: form_data,
      dataType: 'script',
      cache: false,
      contentType: false,
      processData: false,
      success: function(response) {
        if (response == "OK") {
          $('.file-preview-frame[data-fileindex="' + $('#delposterdiv').val() + '"]').remove();
          $('#deletePoster').modal('hide');
        } else {
          $('#deletepostermsg').addClass('d-block').remove('d-none').text("Failed to delete poster! Please try later.");
        }

      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log(textStatus, errorThrown);
      }
    });
  }
  $('#newimage').on('change', function(event) {
    $('#mthumbnail').attr('src', URL.createObjectURL(event.target.files[0]));
  });


  (function() {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation-addsubcat');

    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
      .forEach(function(form) {
        form.addEventListener('submit', function(event) {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          } else {
            event.preventDefault();

            var form_data = new FormData();
            var image = $("#newimage").prop("files")[0];
            var files = $("#posters").get(0).files;


            for (var i = 0; i < files.length; i++) {
              form_data.append("posters[]", files[i]);
            }

            form_data.append("parent_cat_id", $('#parent_cat_id').val());
            form_data.append("subcategory_name", $("#subCatName").val());
            form_data.append("description", $("#description").val());
            form_data.append("newimage", image);
            form_data.append("submit_sub_category", 'add');

            $('.new-subcat-submit').addClass('d-none');
            $('.new-scat-spinner').removeClass('d-none');
            $.ajax({
              url: "ajax.php",
              type: "post",
              data: form_data,
              dataType: 'script',
              cache: false,
              contentType: false,
              processData: false,
              success: function(response) {
                console.log(response);
                if (response == "OK") {
                  $('#add_sub_cat_alert').removeClass('d-none').addClass('alert-success').text("New sub category added.");
                  location.reload();
                } else {
                  $('#add_sub_cat_alert').removeClass('d-none').addClass('alert-danger').text("Failed to add new sub category! Please try later.");
                }
                $('.new-subcat-submit').removeClass('d-none');
                $('.new-scat-spinner').addClass('d-none');
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

  (function() {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation-updatesubcat')

    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
      .forEach(function(form) {
        form.addEventListener('submit', function(event) {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          } else {
            event.preventDefault();

            var form_data = new FormData();
            var image = $("#newimage").prop("files")[0];
            var files = $("#posters").get(0).files;
            for (var i = 0; i < files.length; i++) {
              form_data.append("posters[]", files[i]);
            }
            form_data.append("subcat", $('#sub_cat_id').val());
            form_data.append("subcategory_name", $("#subCatName").val());
            form_data.append("description", $("#description").val());
            form_data.append("newimage", image);
            form_data.append("update_sub_category", 'update');

            $('.update-subcat-submit').addClass('d-none');
            $('.update-scat-spinner').removeClass('d-none');
            $.ajax({
              url: "ajax.php",
              type: "post",
              data: form_data,
              dataType: 'script',
              cache: false,
              contentType: false,
              processData: false,
              success: function(response) {
                console.log(response);
                if (response == "OK") {
                  $('#update_sub_cat_alert').removeClass('d-none').addClass('alert-success').text("Sub Category update successfully.");
                  location.reload();
                } else {
                  $('#update_sub_cat_alert').removeClass('d-none').addClass('alert-danger').text("Failed to update sub category! Please try later.");
                }
                $('.update-subcat-submit').removeClass('d-none');
                $('.update-scat-spinner').addClass('d-none');
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
</script>
<?php include "footer.php"; ?>