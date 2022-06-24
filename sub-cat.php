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
if (isset($_GET['pid'])) {
  $parent_id = $_GET['pid'];
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
</style>
<!-- Begin Page Content -->
<div class="main_content_iner ">
  <div class="container-fluid p-0 ">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0 text-gray-800">Categories</h1>
    </div>
    <div class="row">
      <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
          <!-- Card Header - Dropdown -->
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Sub Category</h6>
            <!-- <div>
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#arrangeCategoryModal">
                Arrange
              </button>
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#newCategoryModal">
                New
              </button>
            </div> -->
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
                  <textarea type="text" class="form-control" id="description" value="" name="description"></textarea>
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



    <!-- Update Sub Category Modal -->
    <div class="modal fade" id="updatesubCategoryModal" tabindex="-1" role="dialog" aria-labelledby="subCategoryLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="subCategoryLabel">Update sub category</h5>
            <button type="button" class="close" id="xnpc_modal" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="alert alert-dismissible fade show d-none" role="alert">
              <button type="button" class="close" id="update_sub_cat_alert" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form class="row g-3 needs-validation-updatesubcat" id="update_sub_category_form" novalidate method="post">
              <input type="hidden" value="" id="updatesub_cat_id" name="updatesub_cat_id" />
              <div class="col-md-12">
                <label for="parentCatName" class="form-label">Sub category name</label>
                <input type="text" class="form-control" id="updatesubcategory_name" value="" name="updatesubcategory_name" required>
                <div class="valid-feedback">
                  Looks good!
                </div>
              </div>

              <div class="col-md-12">
                <label for="update_description" class="form-label">Description</label>
                <textarea type="text" class="form-control" id="update_description" value="" name="update_description"></textarea>
              </div>

              <div class="col-md-12">
                <label for="newimage" class="form-label">Thumbnail *</label>
                <input type="file" class="form-control" id="updateimage" name="updateimage" />
                <img src="" id="update_sub_cat_img" class="mt-2" width="20%" />
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

    <!-- Delete Sub Category Modal -->
    <div class="modal fade" id="deleteSubCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editSubCategoryLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editSubCategoryLabel">Delete sub category</h5>
            <button type="button" class="close" id="xnpc_modal" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="alert alert-dismissible fade show d-none" role="alert">
              <button type="button" class="close" id="delete_sub_cat_alert" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form class="row g-3 needs-validation-subcatdelete" id="delete_sub_category_form" novalidate method="post">
              <input type="hidden" value="" id="delete_subcat" name="delete_subcat" />
              <div class="col-md-12">
                Are you sure! You want to delete?
              </div>

              <div class="col-12 mt-5">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                <button class="btn btn-primary delete-subcat-submi" type="submit" value="update" id="delete_sub_category" name="delete_sub_category">Yes</button>
                <div class="spinner-border text-secondary delete-scat-spinner d-none" role="status">
                  <span class="sr-only">Loading...</span>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include "js_include.php"; ?>


<script src="./js/fileinput.js" type="text/javascript"></script>
<script src="./js/theme.js" type="text/javascript"></script>
<script>
  $("#posters").fileinput({
    theme: 'fa',
    uploadUrl: '#',
    showUpload: false,
    allowedFileExtensions: ['jpg', 'jpeg', 'png'],
    overwriteInitial: false,
    //maxFileSize: 2000,
    maxFilesNum: 10,
    slugCallback: function(filename) {
      return filename.replace('(', '_').replace(']', '_');
    }
  });

  $('#newimage').on('change', function(event) {
    $('#mthumbnail').attr('src', URL.createObjectURL(event.target.files[0]));
  });


  (function() {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation-addsubcat')

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
            var image = $("#updateimage").prop("files")[0];
            form_data.append("updatesub_cat_id", $('#updatesub_cat_id').val());
            form_data.append("updatesubcategory_name", $("#updatesubcategory_name").val());
            form_data.append("description", $("#update_description").val());
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
                  $('#update_sub_cat_alert').removeClass('d-none').addClass('alert-success').text("New sub category added.");
                  location.reload();
                } else {
                  $('#update_sub_cat_alert').removeClass('d-none').addClass('alert-danger').text("Failed to add new sub category! Please try later.");
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

  (function() {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation-subcatdelete')

    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
      .forEach(function(form) {
        form.addEventListener('submit', function(event) {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          } else {
            event.preventDefault();
            var values = $('#delete_sub_category_form').serializeArray();
            values.push({
              name: "delete_sub_category",
              value: 'delete'
            });
            $('.delete-subcat-submit').addClass('d-none');
            $('.delete-scat-spinner').removeClass('d-none');
            $.ajax({
              url: "ajax.php",
              type: "post",
              data: values,
              success: function(response) {
                console.log(response);
                if (response == "OK") {
                  $('#delete_sub_cat_alert').removeClass('d-none').addClass('alert-success').text("New sub category added.");
                  location.reload();
                } else {
                  $('#delete_sub_cat_alert').removeClass('d-none').addClass('alert-danger').text("Failed to add new sub category! Please try later.");
                }
                $('.delete-subcat-submit').removeClass('d-none');
                $('.delete-scat-spinner').addClass('d-none');
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