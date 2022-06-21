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

/*$ads_status = array("google_ads" => 0, "client_ads" => 1, "enabled" => 0, "disabled" => 1);
file_put_contents("ads.json", json_encode($ads_status));*/
$file = fopen("ads.json", "r");

//Output lines until EOF is reached
while (!feof($file)) {
  $line = fgets($file);
}
fclose($file);

$adstatus = json_decode($line, true);
$adstatus['google_ads'];
$adstatus['client_ads'];

$limit = 10;
// Paging limit & offset
$offset = !empty($_GET['page']) ? (($_GET['page'] - 1) * $limit) : 0;
$con = array(
  'start' => $offset,
  'limit' => $limit
);

$rowCount = count($db->getRows('ads'));


$pagConfig = array(
  //'baseURL' => 'index.php'.$searchStr,
  'baseURL' => 'ads.php',
  'totalRows' => $rowCount,
  'perPage' => $limit
);

$pagination = new Pagination($pagConfig);

// Get users from database
$con = array(
  //'like_or' => $searchArr,
  'start' => $offset,
  'limit' => $limit,
  'order_by' => 'type DESC',
);
//$ads = $db->getRows('videos', $con);
$ads = $db->getRows('ads', $con);

$conc = array(
  'order_by' => 'id ASC'
);
$categories = $db->getRows('categories', $conc);

$random = rand(10, 100);
?>
<!-- Begin Page Content -->
<div class="main_content_iner ">
  <div class="container-fluid p-0 ">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0 text-gray-800">Ads</h1>
    </div>
    <div class="row">
      <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
          <!-- Card Header - Dropdown -->
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Ads</h6>
            <div>
              <button type="button" class="btn btn-primary btn-sm" href="#" data-toggle="modal" data-target="#edAdsModal">Enable/Disable</button>
              <button type="button" class="btn btn-primary btn-sm" href="#" data-toggle="modal" data-target="#newAdsModal">New ad</button>
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
                      <th style="width:20%;">Advertising Text</th>
                      <th>Banner</th>
                      <th>Landing URL</th>
                      <th>Type</th>
                      <th>Sequence</th>
                      <th>Screen Name</th>
                      <th>Click</th>
                      <th>Upload Date</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    foreach ($ads as $ad) {
                      echo '<tr>
                                <td>' . $ad['id'] . '</td>
                                <td id="t' . $ad['id'] . '">' . $ad['text'] . '</td>
                                <td id="i' . $ad['id'] . '"><img src="' . $ad['image'] . '?s=' . $random . '" width="150"</td>
                                <td id="url' . $ad['id'] . '">' . $ad['url'] . '</td>
                                <td id="type' . $ad['id'] . '">' . $ad['type'] . '</td>
                                <td id="sequence' . $ad['id'] . '">' . $ad['sequence'] . '</td>
                                <td id="screen' . $ad['id'] . '">' . $ad['screen_name'] . '</td>
                                <td>' . $ad['click'] . '</td>
                                <td>' . $ad['created_at'] . '</td>
                                <td>
                                    <button class="btn btn-danger btn-xs" onclick="changeId(\'e\', \'' . $ad['id'] . '\')"><span class="fa fa-edit"></span></button>
                                    <button class="btn btn-danger btn-xs" data-toggle="modal" data-target="#deleteAdModal" onclick="changeId(\'d\', \'' . $ad['id'] . '\')"><span class="fa fa-trash"></span></button>
                                </td>
                              </tr>';
                    }
                    ?>

                  </tbody>
                </table>
                <?php echo $pagination->createLinks(); ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="deleteAdModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editCategoryLabel">Delete Ad</h5>
          <button type="button" class="close" id="xnpc_modal" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="alert alert-dismissible fade show d-none" role="alert">
            <button type="button" class="close" id="delete_ad_alert" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form class="row g-3 needs-validation-delete" id="delete_ad_form" novalidate method="post">
            <input type="hidden" value="" id="delete_ad_id" name="delete_ad_id" />
            <div class="col-md-12">
              Are you sure! You want to delete?
            </div>

            <div class="col-12 mt-5">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
              <button class="btn btn-primary delete-submit" type="submit" value="update" id="delete_ad" name="delete_ad">Yes</button>
              <div class="spinner-border text-secondary delete-ad-spinner d-none" role="status">
                <span class="sr-only">Loading...</span>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Update Banner Ad Modal -->
  <div class="modal fade" id="updateAdsModal" tabindex="-1" role="dialog" aria-labelledby="updateAdsLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="updateAdsLabel">Update Ad</h5>
          <button type="button" class="close" id="xnpc_modal" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="alert alert-dismissible fade show d-none" role="alert">
            <button type="button" class="close" id="update_ads_alert" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form class="row g-3 needs-validation-update" id="update_ad_from" novalidate method="post" enctype="multipart/form-data">
            <div class="form-group col-xl-12 col-lg-12">
              <label for="update_banner_type" class="form-label">Banner Type</label>
              <select class="form-select form-control" id="update_banner_type" name="update_banner_type" required>
                <option value="">Type</option>
                <option value="Big Banner">Big Banner</option>
                <option value="Small Banner">Small Banner</option>
              </select>
            </div>
            <div class="form-group col-xl-12 col-lg-12">
              <label for="update_banner_sequence" class="form-label">Ads Sequence</label>
              <select class="form-select form-control" id="update_banner_sequence" name="update_banner_sequence" required>
                <option value="">Ads Sequence</option>
                <option value="1">Banner Ad 1</option>
                <option value="2">Banner Ad 2</option>
                <option value="3">Banner Ad 3</option>
                <option value="4">Banner Ad 4</option>
                <option value="5">Banner Ad 5</option>
                <option value="6">Banner Ad 6</option>
                <option value="7">Banner Ad 7</option>
                <option value="8">Banner Ad 8</option>
                <option value="9">Banner Ad 9</option>
                <option value="10">Banner Ad 10</option>
              </select>
            </div>
            <div class="form-group col-xl-12 col-lg-12">
              <label for="update_category" class="form-label">Category</label>
              <select class="form-select form-control" id="update_category" name="update_category">
                <option value="">Select category</option>
                <?php
                foreach ($categories as $cat) {
                  echo '<option value="' . $cat['id'] . '">' . $cat['category'] . '</option>';
                }
                ?>
              </select>
            </div>
            <div class="form-group col-xl-12 col-lg-12">
              <label for="update_advertising_text" class="form-label">Advertising text</label>
              <input type="text" class="form-control" name="update_advertising_text" id="update_advertising_text" placeholder="Advertising text" required>
              <div class="valid-feedback">
                Looks good!
              </div>
            </div>
            <div class="form-group col-xl-12 col-lg-12">
              <label for="update_image" class="form-label">Image</label>
              <input type='file' class="form-control" name='update_image' id="update_image" placeholder="Banner image" />
              <div class="valid-feedback">
                Looks good!
              </div>
            </div>
            <div class="form-group col-xl-12 col-lg-12">
              <label for="update_landing_url" class="form-label">Landing URL</label>
              <input type='url' class="form-control" name='update_landing_url' id="update_landing_url" placeholder="https://example.com" pattern="https://.*" required />
              <div class="valid-feedback">
                Looks good!
              </div>
            </div>
            <div class="form-group col-xl-12 col-lg-12">
              <label for="update_screen_name" class="form-label">Screen Name</label>
              <select class="form-select form-control" id="update_screen_name" name="update_screen_name" required>
                <option value="">Type</option>
                <!--<option value="All">All</option>-->
                <option value="home">Home Screen</option>
                <option value="detail">Detail Page</option>
              </select>
            </div>
            <div class="col-12 mt-5">
              <input type="hidden" value="" id="edit_ad_id" name="edit_ad_id" />
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button class="btn btn-primary update-ads-submit" type="submit" value="save" id="save_ad" name="save_ad">Save</button>
              <div class="spinner-border text-secondary update-ads-spinner d-none" role="status">
                <span class="sr-only">Loading...</span>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Add New Banner Ad Modal -->
  <div class="modal fade" id="newAdsModal" tabindex="-1" role="dialog" aria-labelledby="newAdsLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="newAdsLabel">New Ad</h5>
          <button type="button" class="close" id="xnpc_modal" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="alert alert-dismissible fade show d-none" role="alert">
            <button type="button" class="close" id="save_ads_alert" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form class="row g-3 needs-validation-new" id="new_ad_from" novalidate method="post" enctype="multipart/form-data">
            <div class="form-group col-xl-12 col-lg-12">
              <label for="banner_type" class="form-label">Banner Type</label>
              <select class="form-select form-control" id="banner_type" name="banner_type" required>
                <option value="">Type</option>
                <option value="Big Banner">Big Banner</option>
                <option value="Small Banner">Small Banner</option>
              </select>
            </div>
            <div class="form-group col-xl-12 col-lg-12">
              <label for="banner_sequence" class="form-label">Ads Sequence</label>
              <select class="form-select form-control" id="banner_sequence" name="banner_sequence" required>
                <option value="">Ads Sequence</option>
                <option value="1">Banner Ad 1</option>
                <option value="2">Banner Ad 2</option>
                <option value="3">Banner Ad 3</option>
                <option value="4">Banner Ad 4</option>
                <option value="5">Banner Ad 5</option>
                <option value="6">Banner Ad 6</option>
                <option value="7">Banner Ad 7</option>
                <option value="8">Banner Ad 8</option>
                <option value="9">Banner Ad 9</option>
                <option value="10">Banner Ad 10</option>
              </select>
            </div>
            <div class="form-group col-xl-12 col-lg-12">
              <label for="category" class="form-label">Category</label>
              <select class="form-select form-control" id="category" name="category">
                <option value="">Select category</option>
                <?php
                foreach ($categories as $cat) {
                  echo '<option value="' . $cat['id'] . '">' . $cat['category'] . '</option>';
                }
                ?>
              </select>
            </div>
            <div class="form-group col-xl-12 col-lg-12">
              <label for="advertising_text" class="form-label">Advertising text</label>
              <input type="text" class="form-control" name="advertising_text" id="advertising_text" placeholder="Advertising text" required>
              <div class="valid-feedback">
                Looks good!
              </div>
            </div>
            <div class="form-group col-xl-12 col-lg-12">
              <label for="image" class="form-label">Image</label>
              <input type='file' class="form-control" name='image' id="image" placeholder="Banner image" required />
              <div class="valid-feedback">
                Looks good!
              </div>
            </div>
            <div class="form-group col-xl-12 col-lg-12">
              <label for="landing_url" class="form-label">Landing URL</label>
              <input type='url' class="form-control" name='landing_url' id="landing_url" placeholder="https://example.com" pattern="https://.*" required />

              <div class="valid-feedback">
                Looks good!
              </div>
            </div>
            <div class="form-group col-xl-12 col-lg-12">
              <label for="screen_name" class="form-label">Screen Name</label>
              <select class="form-select form-control" id="screen_name" name="screen_name" required>
                <option value="">Type</option>
                <!--<option value="All">All</option>-->
                <option value="home">Home Screen</option>
                <option value="detail">Detail Page</option>
              </select>
            </div>
            <div class="col-12 mt-5">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button class="btn btn-primary save-ads-submit" type="submit" value="save" id="save_ad" name="save_ad">Save</button>
              <div class="spinner-border text-secondary save-ads-spinner d-none" role="status">
                <span class="sr-only">Loading...</span>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>


  <!-- Add New Banner Ad Modal -->
  <div class="modal fade" id="edAdsModal" tabindex="-1" role="dialog" aria-labelledby="edAdsLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="edAdsLabel">Enable/Disable Ads</h5>
          <button type="button" class="close" id="xnpc_modal" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!--<div class="alert alert-dismissible fade show " role="alert" id="adstatus_alert">Status updated</div>-->
          <form class="row g-3 needs-validation-adstatus" id="ad_status_from" novalidate method="post">
            <div class="form-group col-xl-12 col-lg-12">
              <?php
              $enabled = $disabled = $statustxt = '';
              if ($adstatus['google_ads'] == 0) {
                $statustxt = "<span class='badge bg-success'>Active</span>";
                $enabled = "checked";
              } else {
                $statustxt = "<span class='badge bg-danger'>Inactive</span>";
                $disabled = "checked";
              }

              $cenabled = $cdisabled = $cstatustxt = '';
              if ($adstatus['client_ads'] == 0) {
                $cstatustxt = "<span class='badge bg-success'>Active</span>";
                $cenabled = "checked";
              } else {
                $cstatustxt = "<span class='badge bg-danger'>Inactive</span>";
                $cdisabled = "checked";
              }
              ?>
              <label for="screen_name" class="form-label">Turn On/Off?</label>
              <!---->

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="gaenable" id="gaenable" value="0" <?php echo $enabled; ?>>
                <label class="form-check-label" for="gaenable">
                  Google Ads&nbsp;&nbsp;<?php echo $statustxt; ?>
                </label>
              </div>
              <br>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="caenable" id="caenable" value="0" <?php echo $cenabled; ?>>
                <label class="form-check-label" for="caenable">
                  Client Ads&nbsp;&nbsp;<?php echo $cstatustxt; ?>
                </label>
              </div>

            </div>
            <div class="col-12 mt-5">
              <div class="spinner-border text-secondary update-adstatus-spinner d-none" role="status">
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
<script>
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
            var banner_type = $("#banner_type").val();
            var image = $("#image").prop("files")[0];
            var title = $("#advertising_text").val();
            var url = $("#landing_url").val();
            var screen_name = $("#screen_name").val();
            var banner_sequence = $("#banner_sequence").val();
            var category = $("#category").val();
            var form_data = new FormData();
            form_data.append("type", banner_type);
            form_data.append("sequence", banner_sequence);
            form_data.append("category", category);
            form_data.append("text", title);
            form_data.append("image", image);
            form_data.append("url", url);
            form_data.append("screen_name", screen_name);
            form_data.append("save_ad", 'save');
            //alert(form_data);
            $('.save-ads-submit').addClass('d-none');
            $('.save-ads-spinner').removeClass('d-none');
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
                  $('#save_ads_alert').removeClass('d-none').addClass('alert-success').text("New advertising saved.");
                  location.reload();
                } else {
                  $('#save_ads_alert').removeClass('d-none').addClass('alert-danger').text("Failed to save new advertising! Please try later.");
                }
                $('.save-ads-submit').removeClass('d-none');
                $('.save-ads-spinner').addClass('d-none');
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
            var adid = $('#edit_ad_id').val();
            var banner_type = $("#update_banner_type").val();
            var update_banner_sequence = $("#update_banner_sequence").val();
            var update_category = $("#update_category").val();
            var image = $("#update_image").prop("files")[0];
            var title = $("#update_advertising_text").val();
            var url = $("#update_landing_url").val();
            var screen_name = $("#update_screen_name").val();
            var form_data = new FormData();
            form_data.append("adid", adid);
            form_data.append("type", banner_type);
            form_data.append("sequence", update_banner_sequence);
            form_data.append("category", update_category);
            form_data.append("text", title);
            form_data.append("image", image);
            form_data.append("url", url);
            form_data.append("screen_name", screen_name);
            form_data.append("update_ad", 'update');
            //alert(form_data);
            $('.update-ads-submit').addClass('d-none');
            $('.update-ads-spinner').removeClass('d-none');
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
                  $('#update_ads_alert').removeClass('d-none').addClass('alert-success').text("Advertising updated.");
                  location.reload();
                } else {
                  $('#update_ads_alert').removeClass('d-none').addClass('alert-danger').text("Failed to update advertising! Please try later.");
                }
                $('.update-ads-submit').removeClass('d-none');
                $('.update-ads-spinner').addClass('d-none');
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
            var values = $('#delete_ad_form').serializeArray();
            values.push({
              name: "delete_ad",
              value: 'delete'
            });
            $('.delete-submit').addClass('d-none');
            $('.delete-ad-spinner').removeClass('d-none');
            $.ajax({
              url: "ajax.php",
              type: "post",
              data: values,
              success: function(response) {
                console.log(response);
                if (response == "OK") {
                  $('#delete_ad_alert').removeClass('d-none').addClass('alert-success').text("Category deleted.");
                  location.reload();
                } else {
                  $('#delete_ad_alert').removeClass('d-none').addClass('alert-danger').text("Failed to delete category! Please try later.");
                }
                $('.delete-submit').removeClass('d-none');
                $('.delete-ad-spinner').addClass('d-none');
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

  <?php
  if ($adstatus['google_ads'] == 0) {
    echo "var gstatus = 0;";
  } else {
    echo "var gstatus = 1;";
  }

  $cenabled = $cdisabled = $cstatustxt = '';
  if ($adstatus['client_ads'] == 0) {
    echo 'var cstatus = 0;';
  } else {
    echo 'var cstatus = 1;';
  }
  ?>
  $('#gaenable').change(function() {
    if ($(this).is(":checked")) {
      gstatus = 0;
    } else {
      gstatus = 1;
    }
    changeAdsStatus(gstatus, cstatus);
  });

  $('#caenable').change(function() {
    if ($(this).is(":checked")) {
      cstatus = 0;
    } else {
      cstatus = 1;
    }
    changeAdsStatus(gstatus, cstatus);
  });

  function changeAdsStatus(gstatus, cstatus) {
    var form_data = new FormData();
    form_data.append("gstatus", gstatus);
    form_data.append("cstatus", cstatus);
    form_data.append("ad_status", 'update');

    $('.update-adstatus-submit').addClass('d-none');
    $('.update-adstatus-spinner').removeClass('d-none');
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
          $('#adstatus_alert').removeClass('d-none').addClass('alert-success').text("Status updated.");
          location.reload();
        } else {
          $('#adstatus_alert').removeClass('d-none').addClass('alert-danger').text("Failed to update status! Please try later.");
        }
        $('.update-adstatus-submit').removeClass('d-none');
        $('.update-adstatus-spinner').addClass('d-none');
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log(textStatus, errorThrown);
      }
    });
  }

  /*$('input[type=radio][name=caenable]').change(function() {
      var gstatus = 0;
      var cstatus = 0;
      if (this.value == '0') {
          gstatus = 0;
          cstatus = 1;
      }
      else if (this.value == '1') {
          gstatus = 1;
          cstatus = 0;
      }
      
      var form_data = new FormData();
      form_data.append("gstatus", gstatus);
      form_data.append("cstatus", cstatus);
      form_data.append("ad_status", 'update');
      
      $('.update-adstatus-submit').addClass('d-none');
      $('.update-adstatus-spinner').removeClass('d-none');
      $.ajax({
          url: "ajax.php",
          type: "post",
          data: form_data,
          dataType: 'script',
          cache: false,
          contentType: false,
          processData: false,
          success: function (response) {
              console.log(response);
              if(response == "OK"){
                  $('#adstatus_alert').removeClass('d-none').addClass('alert-success').text("Status updated.");
                  location.reload();
              }else{
                  $('#adstatus_alert').removeClass('d-none').addClass('alert-danger').text("Failed to update status! Please try later.");
              }
              $('.update-adstatus-submit').removeClass('d-none');
              $('.update-adstatus-spinner').addClass('d-none');
          },
          error: function(jqXHR, textStatus, errorThrown) {
              console.log(textStatus, errorThrown);
          }
      });
  });*/

  /*$('input[type=radio][name=clientad]').change(function() {
      if (this.value == '0') {
          //alert("Allot Thai Gayo Bhai");
          $("input[type=radio][name=googlead]").prop('checked', true);
      }
      else if (this.value == '1') {
          //alert("Transfer Thai Gayo");
          $("input[type=radio][name=googlead]").prop('checked', true);
      }
  });*/

  function changeId(t, id) {
    if (t == 'd') {
      $('#delete_ad_id').val(id);
    } else if (t == 'e') {
      $("#update_banner_type").val($("#type" + id).text());
      $('#update_banner_sequence').val($('#sequence' + id).text());
      $('#update_advertising_text').val($("#t" + id).text());
      $('#update_landing_url').val($("#url" + id).text());
      //$('#update_screen_name').val($("#screen"+id).text());
      $("#update_screen_name").val($("#screen" + id).text()).change();
      $('#edit_ad_id').val(id);
      $('#updateAdsModal').modal('show');
    }
  }

  $('#parent_cat_alert').on('click', function() {
    location.reload();
  });
</script>
<?php include "footer.php"; ?>