<?php
if (!isset($_COOKIE['mid'])) {
  header('Location: ./login.php');
  exit(0);
}
include 'DB.class.php';
include 'functions.php';
$db = new DB;
include "header.php";

// Get users from database
$con = array(
  'order_by' => 'priority ASC',
  'where' => array('parent_id' => 0)
);
$categories = $db->getRows('categories', $con);

$sql = "SELECT s.*, p.category as parent_cat, p.id as catid FROM categories p INNER JOIN categories s ON p.id = s.parent_id ORDER BY s.priority";
$subcategories = $db->customQuery($sql);
?>
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
            <h6 class="m-0 font-weight-bold text-primary">Categories</h6>
            <div>
              <!-- Button trigger modal -->
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#arrangeCategoryModal">
                Arrange
              </button>
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#newCategoryModal">
                New
              </button>
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
                      <th class="text-center"><span style="font-size:12px">Enable/Disable</span><br>Ads</th>
                      <th style="width:20%;">Category</th>
                      <th>Display Type</th>
                      <th>Display Order</th>
                      <th>Date</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $i = 1;
                    foreach ($categories as $cat) {

                      if ($cat['status'] == 0) {
                        $status_color = 'btn-danger';
                        $status_icon = 'fa-eye-slash';
                      } else {
                        $status_color = 'btn-success';
                        $status_icon = 'fa-eye';
                      }
                      if ($cat['ad_status'] == 0) {
                        $adstatus_color = 'btn-danger';
                        $adstatus_icon = 'fa-eye-slash';
                      } else {
                        $adstatus_color = 'btn-success';
                        $adstatus_icon = 'fa-eye';
                      }
                      echo '<tr>
                                <td id="item-' . $cat['id'] . '">' . $i++ . '</td>
                                <td class="text-center"><button class="btn ' . $adstatus_color . ' btn-xs" onclick="disableAds(\'' . $cat['id'] . '\', \'' . $cat['ad_status'] . '\')"><span class="fa ' . $adstatus_icon . '"></span> Ads</button></td>
                                <td>
                                    ' . $cat['category'];
                      if (!empty($subcategories)) {
                        $c = 0;
                        foreach ($subcategories as $sub) {
                          if ($sub['parent_id'] == $cat['id']) {
                            if ($c == 0) {
                              echo '<a href="#" data-toggle="modal" data-target="#arrangeSubCategoryModal" onclick="loadSubCategory(' . $sub['parent_id'] . ')">&nbsp;&nbsp;&nbsp;&nbsp;<i class="ti-exchange-vertical">Arrange</i></a><br>->';
                              $c++;
                            }
                            echo $sub['category'] . " <input type='hidden' id='desc" . $sub['id'] . "' value='" . $sub['description'] . "' /><input type='hidden' id='" . $sub['id'] . "img' value='" . $sub['thumbnail'] . "' /><a onclick=\"changeId('usc', '" . $sub['id'] . "', '" . $sub['category'] . "')\" class=\"badge text-primary\">&nbsp;<span class=\"fa fa-edit\"></span></a>
                                            <a onclick=\"changeId('dsc', '" . $sub['id'] . "')\" class=\"badge text-primary\">&nbsp;<span class=\"fa fa-trash\"></span></a><br>";
                          }
                        }
                      }
                      echo '</td>
                                <td id="dt-' . $cat['id'] . '">' . ucwords($cat['display_type']) . '</td>
                                <td>' . $cat['priority'] . '</td>
                                <td>' . $cat['created_at'] . '</td>
                                <td>
                                    <input type="hidden" id="hyt' . $cat['id'] . '" value="' . $cat['height'] . '" />
                                    <button class="btn btn-primary btn-xs" onclick="changeId(\'e\', \'' . $cat['id'] . '\', \'' . $cat['category'] . '\')"><span class="fa fa-edit"></span></button>
                                    <button class="btn btn-danger btn-xs" data-toggle="modal" data-target="#deleteCategoryModal" onclick="changeId(\'d\', \'' . $cat['id'] . '\', \'\')"><span class="fa fa-trash"></span></button>
                                    <button class="btn ' . $status_color . ' btn-xs" onclick="disableCategory(\'' . $cat['id'] . '\', \'' . $cat['status'] . '\')"><span class="fa ' . $status_icon . '"></span></button>
                                    <a class="btn btn-success btn-xs" href="sub-cat.php?t=n&pid=' . $cat['id'] . '" ><span class="fa fa-plus">&nbsp;Sub Cat</span></a> <!--onclick="changeId(\'sc\', \'' . $cat['id'] . '\')" data-toggle="modal" data-target="#subCategoryModal" -->
                                </td>
                              </tr>';
                    }
                    ?>

                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- New Parent Category Modal -->
    <div class="modal fade" id="newCategoryModal" tabindex="-1" role="dialog" aria-labelledby="newCategoryLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="newCategoryLabel">Add new parent category</h5>
            <button type="button" class="close" id="xnpc_modal" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="alert alert-dismissible fade show d-none" role="alert">
              <button type="button" class="close" id="parent_cat_alert" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form class="row g-3 needs-validation" id="new_parent_category" novalidate method="post">
              <div class="col-md-12">
                <label for="parentCatName" class="form-label">Category name</label>
                <input type="text" class="form-control" id="parentCatName" value="" name="category_name" required>
                <div class="valid-feedback">
                  Looks good!
                </div>
              </div>

              <div class="col-md-12">
                <label for="display_type" class="form-label">Display Type</label>
                <select class="form-control" id="display_type" value="" name="display_type" required>
                  <option value="">Select display type</option>
                  <option value="thumbnail">Thumbnail</option>
                  <option value="profile">Profile</option>
                  <option value="trending">Trending</option>
                  <option value="tvchannel">TVChannel</option>
                </select>
                <div class="valid-feedback">
                  Looks good!
                </div>
              </div>

              <div class="col-md-12">
                <label for="display_height" class="form-label">Display Height of List Items(IN App)</label>
                <select class="form-control" id="display_height" value="" name="display_height" required>
                  <option value="">Select height</option>
                  <option value="100" selected>100</option>
                  <option value="150">150</option>
                  <option value="200">200</option>
                  <option value="250">250</option>
                </select>
                <div class="valid-feedback">
                  Looks good!
                </div>
              </div>

              <div class="col-12 mt-5">
                <button type="button" id="cnpc_modal" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button class="btn btn-primary new-submit" type="submit" value="add" name="submit_parent_category">Save</button>
                <div class="spinner-border text-secondary new-pcat-spinner d-none" role="status">
                  <span class="sr-only">Loading...</span>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Parent Category Modal -->
    <div class="modal fade" id="deleteCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editCategoryLabel">Delete parent category</h5>
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
            <form class="row g-3 needs-validation-delete" id="delete_parent_category_form" novalidate method="post">
              <input type="hidden" value="" id="delete_cat" name="cat_id" />
              <div class="col-md-12">
                Are you sure! You want to delete?
              </div>

              <div class="col-12 mt-5">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                <button class="btn btn-primary delete-submit" type="submit" value="update" id="delete_cat" name="delete_parent_category">Yes</button>
                <div class="spinner-border text-secondary delete-pcat-spinner d-none" role="status">
                  <span class="sr-only">Loading...</span>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Prent Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editCategoryLabel">Update parent category</h5>
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
            <form class="row g-3 needs-validation-update" id="update_parent_category_from" novalidate method="post">
              <div class="col-md-12">
                <label for="parentCatName" class="form-label">Category name</label>
                <input type="text" class="form-control" id="edit_parentCatName" value="" name="edit_category_name" required>
                <div class="valid-feedback">
                  Looks good!
                </div>
              </div>

              <div class="col-md-12">
                <label for="display_type" class="form-label">Display Type</label>
                <select class="form-control" id="edit_display_type" value="" name="edit_display_type" required>
                  <option value="">Select display type</option>
                  <option value="thumbnail">Thumbnail</option>
                  <option value="profile">Profile</option>
                  <option value="trending">Trending</option>
                  <option value="tvchannel">TVChannel</option>
                </select>
                <div class="valid-feedback">
                  Looks good!
                </div>
              </div>

              <div class="col-md-12">
                <label for="edit_display_height" class="form-label">Display Height of List Items(IN App)</label>
                <select class="form-control" id="edit_display_height" value="" name="edit_display_height" required>
                  <option value="">Select height</option>
                  <option value="100" selected>100</option>
                  <option value="150">150</option>
                  <option value="200">200</option>
                  <option value="250">250</option>
                </select>
                <div class="valid-feedback">
                  Looks good!
                </div>
              </div>

              <div class="col-12 mt-5">
                <input type="hidden" value="" id="edit_cat_id" name="edit_cat_id" />
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button class="btn btn-primary update-submit" type="submit" value="update" id="update_cat" name="update_parent_category">Update</button>
                <div class="spinner-border text-secondary update-pcat-spinner d-none" role="status">
                  <span class="sr-only">Loading...</span>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Arrange Category Modal -->
    <div class="modal fade" id="arrangeCategoryModal" tabindex="-1" role="dialog" aria-labelledby="sortCategoryLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="sortCategoryLabel">Arrange category order</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="alert alert-dismissible fade show d-none" role="alert">
              <button type="button" class="close" id="arrange_parent_cat_alert" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <ul id="sortable">
              <?php
              $i = 1;
              foreach ($categories as $cat) {
                echo '<li id="item-' . $cat['id'] . '" dir="rtl">' . $cat['category'] . '<i class="tab fa fa-arrows-alt"></i></li>';
                $i++;
              }
              ?>
            </ul>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <div class="spinner-border text-secondary arrange-pcat-spinner d-none" role="status">
              <span class="sr-only">Loading...</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Arrange Sub Category Modal -->
    <div class="modal fade" id="arrangeSubCategoryModal" tabindex="-1" role="dialog" aria-labelledby="sortCategoryLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="sortCategoryLabel">Arrange sub category order</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="alert alert-dismissible fade show d-none" role="alert">
              <button type="button" class="close" id="arrange_parent_cat_alert" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <ul id="subcat_sortable">

            </ul>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <div class="spinner-border text-secondary arrange-pcat-spinner d-none" role="status">
              <span class="sr-only">Loading...</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- New Sub Category Modal -->
    <div class="modal fade" id="subCategoryModal" tabindex="-1" role="dialog" aria-labelledby="subCategoryLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="subCategoryLabel">Add sub category</h5>
            <button type="button" class="close" id="xnpc_modal" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="alert alert-dismissible fade show d-none" role="alert">
              <button type="button" class="close" id="add_sub_cat_alert" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form class="row g-3 needs-validation-addsubcat" id="new_sub_category_form" novalidate method="post" enctype="multipart/form-data">
              <input type="hidden" value="" id="parent_cat_id" name="parent_cat_id" />
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
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script>
  (function() {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation')

    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
      .forEach(function(form) {
        form.addEventListener('submit', function(event) {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          } else {
            event.preventDefault();
            var values = $('#new_parent_category').serializeArray();
            values.push({
              name: "submit_parent_category",
              value: 'add'
            });
            $('.new-submit').addClass('d-none');
            $('.new-pcat-spinner').removeClass('d-none');
            $.ajax({
              url: "ajax.php",
              type: "post",
              data: values,
              success: function(response) {
                console.log(response);
                if (response == "OK") {
                  $('#parent_cat_alert').removeClass('d-none').addClass('alert-success').text("New category added.");
                  location.reload();
                } else {
                  $('#parent_cat_alert').removeClass('d-none').addClass('alert-danger').text("Failed to add new category! Please try later.");
                }
                $('.new-submit').removeClass('d-none');
                $('.new-pcat-spinner').addClass('d-none');
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
    var forms = document.querySelectorAll('.needs-validation-update')

    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
      .forEach(function(form) {
        form.addEventListener('submit', function(event) {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          } else {
            event.preventDefault();
            var values = $('#update_parent_category_from').serializeArray();
            values.push({
              name: "update_parent_category",
              value: 'update'
            });
            $('.update-submit').addClass('d-none');
            $('.update-pcat-spinner').removeClass('d-none');
            $.ajax({
              url: "ajax.php",
              type: "post",
              data: values,
              success: function(response) {
                console.log(response);
                if (response == "OK") {
                  $('#update_parent_cat_alert').removeClass('d-none').addClass('alert-success').text("New category added.");
                  location.reload();
                } else {
                  $('#update_parent_cat_alert').removeClass('d-none').addClass('alert-danger').text("Failed to add new category! Please try later.");
                }
                $('.update-submit').removeClass('d-none');
                $('.update-pcat-spinner').addClass('d-none');
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
    var forms = document.querySelectorAll('.needs-validation-delete')

    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
      .forEach(function(form) {
        form.addEventListener('submit', function(event) {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          } else {
            event.preventDefault();
            var values = $('#delete_parent_category_form').serializeArray();
            values.push({
              name: "delete_parent_category",
              value: 'delete'
            });
            $('.delete-submit').addClass('d-none');
            $('.delete-pcat-spinner').removeClass('d-none');
            $.ajax({
              url: "ajax.php",
              type: "post",
              data: values,
              success: function(response) {
                console.log(response);
                if (response == "OK") {
                  $('#update_parent_cat_alert').removeClass('d-none').addClass('alert-success').text("Category deleted.");
                  location.reload();
                } else {
                  $('#update_parent_cat_alert').removeClass('d-none').addClass('alert-danger').text("Failed to delete category! Please try later.");
                }
                $('.delete-submit').removeClass('d-none');
                $('.delete-pcat-spinner').addClass('d-none');
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

  function changeId(t, id, name) {
    if (t == 'd') {
      $('#delete_cat').val(id);
    } else if (t == 'e') {
      $('#edit_display_height').val($('#hyt' + id).val());
      $('#edit_parentCatName').val(name);
      $('#edit_display_type').val($('#dt-' + id).text().toLowerCase());
      $('#edit_cat_id').val(id);
      $('#editCategoryModal').modal('show');
    } else if (t == 'sc') {
      $('#parent_cat_id').val(id);
    } else if (t == 'usc') {
      $('#update_sub_cat_img').attr('src', $('#' + id + "img").val());
      $('#updatesub_cat_id').val(id);
      $('#updatesubcategory_name').val(name);
      $('#update_description').val($('#desc' + id).val());
      $('#updatesubCategoryModal').modal('show');
    } else if (t == 'dsc') {
      $('#delete_subcat').val(id);
      $('#deleteSubCategoryModal').modal('show');
    }
  }

  function disableAds(cid, status) {
    var form_data = new FormData();
    form_data.append("disable_ads", 'disable');
    form_data.append("catid", cid);
    form_data.append("status", status);
    $.ajax({
      url: "ajax.php",
      dataType: 'script',
      cache: false,
      contentType: false,
      processData: false,
      data: form_data,
      type: 'post',
      success: function(response) {
        console.log(response);
        if (response == "OK") {
          location.reload();
        } else {
          alert("Failed to disable category Ad! Please try later.");
        }
        $('.arrange-pcat-spinner').addClass('d-none');
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log(textStatus, errorThrown);
      }
    });
  }

  function disableCategory(cid, status) {
    var form_data = new FormData();
    form_data.append("disable_category", 'disable');
    form_data.append("catid", cid);
    form_data.append("status", status);
    $.ajax({
      url: "ajax.php",
      dataType: 'script',
      cache: false,
      contentType: false,
      processData: false,
      data: form_data,
      type: 'post',
      success: function(response) {
        console.log(response);
        if (response == "OK") {
          location.reload();
        } else {
          alert("Failed to disable category! Please try later.");
        }
        $('.arrange-pcat-spinner').addClass('d-none');
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log(textStatus, errorThrown);
      }
    });
  }

  function loadSubCategory(parent_id) {
    var form_data = new FormData();
    form_data.append("load_sub_cat", 'load');
    form_data.append("parentid", parent_id);
    $.ajax({
      url: "ajax.php",
      dataType: 'script',
      cache: false,
      contentType: false,
      processData: false,
      data: form_data,
      type: 'post',
      success: function(response) {
        //console.log(response);
        if (response != "FAILED") {
          $('#subcat_sortable').html(response);
        } else {
          alert("Failed to load sub categories! Please try later.");
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log(textStatus, errorThrown);
      }
    });
  }

  $(document).ready(function() {
    $('#subcat_sortable').sortable({
      axis: 'y',
      stop: function(event, ui) {
        var data = $(this).sortable('serialize') + "&arrange_category=arrange";
        //alert(data);
        $('.arrange-pcat-spinner').removeClass('d-none');
        $.ajax({
          url: "ajax.php",
          type: "post",
          data: data,
          success: function(response) {
            //console.log(response);
            if (response == "OK") {
              $('#arrange_parent_cat_alert').removeClass('d-none').addClass('alert-success').text("Sub Category order saved.");
              location.reload();
            } else {
              $('#arrange_parent_cat_alert').removeClass('d-none').addClass('alert-danger').text("Failed to save category order! Please try later.");
            }
            $('.arrange-pcat-spinner').addClass('d-none');
          },
          error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
          }
        });
      }
    });
    $('#sortable').sortable({
      axis: 'y',
      stop: function(event, ui) {
        var data = $(this).sortable('serialize') + "&arrange_category=arrange";
        //alert(data);
        $('.arrange-pcat-spinner').removeClass('d-none');
        $.ajax({
          url: "ajax.php",
          type: "post",
          data: data,
          success: function(response) {
            //alert(response);
            if (response == "OK") {
              $('#arrange_parent_cat_alert').removeClass('d-none').addClass('alert-success').text("Category order saved.");
              location.reload();
            } else {
              $('#arrange_parent_cat_alert').removeClass('d-none').addClass('alert-danger').text("Failed to save category order! Please try later.");
            }
            $('.arrange-pcat-spinner').addClass('d-none');
          },
          error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
          }
        });
      }
    });

  });

  $('#parent_cat_alert').on('click', function() {
    location.reload();
  });
</script>
<?php include "footer.php"; ?>