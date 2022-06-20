<?php
if(!isset($_COOKIE['mid'])){
    header('Location: ./login.php');exit(0);
}
include 'DB.class.php';
include 'functions.php';
$db = new DB;
include "header.php";
include_once 'Pagination.php';

$limit = 10;
// Paging limit & offset
$offset = !empty($_GET['page'])?(($_GET['page']-1)*$limit):0;
$con = array(
    'start' => $offset,
    'limit' => $limit
);

$rowCount = count($db->getRows('appusers'));


$pagConfig = array(
    //'baseURL' => 'index.php'.$searchStr,
    'baseURL' => 'quotes.php',
    'totalRows' => $rowCount,
    'perPage' => $limit
);

$pagination = new Pagination($pagConfig);

// Get users from database
$con = array(
    'start' => $offset,
    'limit' => $limit,
    'order_by' => 'id DESC',
);

$users = $db->getRows('appusers', $con);

$random = rand(10,100);
?>
<!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Quotes</h1>
          </div>
          <div class="row">
            <div class="col-xl-12 col-lg-12">
              <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Quotes</h6>
                  <div>
                    <button type="button" class="btn btn-primary btn-sm" href="#" data-toggle="modal" data-target="#newQuoteModal">New Quote</button>
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
                            <th style="width:20%;">First Name</th>
                            <th>Last Name</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>Registered From</th>
                            <th>Date</th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                        $i = 0;
                        foreach($users as $user){
                            $i++;
                            $firstname = empty($user['first_name']) ? 'NA' : $user['first_name'];
                            $lastname = empty($user['last_name']) ? 'NA' : $user['last_name'];
                            $mobile = empty($user['mobile']) ? 'NA' : $user['mobile'];
                            $email = empty($user['email']) ? 'NA' : $user['email'];
                            echo '<tr>
                                <td>'.$i.'</td>
                                <td >'.$firstname.'</td>
                                <td >'.$lastname.'</td>
                                <td >'.$mobile.'</td>
                                <td >'.$email.'</td>
                                <td >'.$user['register_source'].'</td>
                                <td>'.$user['created_at'].'</td>
                                <td>
                                    <!--<button class="btn btn-danger btn-xs" onclick="changeId(\'e\', \''.$user['id'].'\')"><span class="fa fa-edit"></span></button>-->
                                    <button class="btn btn-danger btn-xs" data-toggle="modal" data-target="#deleteAdModal" onclick="changeId(\'d\', \''.$user['id'].'\')"><span class="fa fa-trash"></span></button>
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
                    <input type="hidden" value="" id="delete_user_id" name="delete_user_id" />
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
        <div class="modal fade" id="updateQuoteModal" tabindex="-1" role="dialog" aria-labelledby="updateQuoteLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="updateQuoteLabel">Update Quote</h5>
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
                <div class="form-group col-xl-12 col-lg-12">
                    <form class="row g-3 needs-validation-update" id="update_ad_from" novalidate method="post" enctype="multipart/form-data">
                        <div class="form-group col-xl-12 col-lg-12">
                            <label for="update_quote_text" class="form-label">Quote</label>
                            <textarea type="text" class="form-control" name="update_quote_text" id="update_quote_text" placeholder="Quote" required></textarea>
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                        </div>
                        <div class="form-group col-xl-12 col-lg-12">
                            <label for="update_quote_writer" class="form-label">Quote Written By</label>
                            <input type='text' class="form-control" name='update_quote_writer' id="update_quote_writer" placeholder="Quote Written By" />
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                        </div>
                        <div class="col-12 mt-5">
                            <input type="hidden" value="" id="edit_quote_id" name="edit_quote_id"/>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button class="btn btn-primary update-quote-submit" type="submit" value="save" id="save_quote" name="save_quote">Save</button>
                            <div class="spinner-border text-secondary update-quote-spinner d-none" role="status">
                              <span class="sr-only">Loading...</span>
                            </div>
                        </div><br>
                    </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Add New Banner Ad Modal -->
        <div class="modal fade" id="newQuoteModal" tabindex="-1" role="dialog" aria-labelledby="newQuoteLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="newQuoteLabel">New Quote</h5>
                <button type="button" class="close" id="xnpc_modal" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="form-group col-xl-12 col-lg-12">
                  <form method="post" class="row g-3 needs-validation-new" id="new_quote_from" novalidate >
                      <div class="col-12 mt-5">
                        <label for="new_quote_text" class="form-label">Quote</label>
                        <textarea class="form-control" name="new_quote_text" id="new_quote_text" placeholder="Quote" required></textarea>
                        <div class="valid-feedback">
                            Looks good!
                        </div>
                    </div>
                    <div class="col-12 mt-5">
                        <label for="new_quote_writer" class="form-label">Quote Written By</label>
                        <input type='text' class="form-control" name='new_quote_writer' id="new_quote_writer" placeholder="Quote Written By" />
                        <div class="valid-feedback">
                            Looks good!
                        </div>
                    </div>
                    <div class="col-12 mt-5">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button class="btn btn-primary save-ads-submit" type="submit" value="save" id="save_quote" name="save_quote">Save</button>
                        <div class="spinner-border text-secondary save-ads-spinner d-none" role="status">
                          <span class="sr-only">Loading...</span>
                        </div>
                    </div><br>
                </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        

<?php include "js_include.php";?>
<script>

(function () {
  'use strict'
  var forms = document.querySelectorAll('.needs-validation-new')
  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }else{
            event.preventDefault();
            var quote = $("#new_quote_text").val();
            var writtenby = $("#new_quote_writer").val();
            var form_data = new FormData();
            form_data.append("quote", quote);
            form_data.append("quotewrittenby", writtenby);
            form_data.append("save_quote", 'save');
            //alert(form_data);
            $('.save-ads-submit').addClass('d-none');
            $('.save-ads-spinner').removeClass('d-none');
            $.ajax({
                url: "ajax-user.php",
                dataType: 'script',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,                         
                type: 'post',
                success: function(response){
                    console.log(response);
                    if(response == "OK"){
                        $('#save_quotes_alert').removeClass('d-none').addClass('alert-success').text("New advertising saved.");
                        location.reload();
                    }else{
                        $('#save_quotes_alert').removeClass('d-none').addClass('alert-danger').text("Failed to save new advertising! Please try later.");
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

(function () {
  'use strict'
  var forms = document.querySelectorAll('.needs-validation-update')
  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }else{
            event.preventDefault();
            var quoteid = $('#edit_quote_id').val();
            var quotetext = $("#update_quote_text").val();
            var quotewriter = $("#update_quote_writer").val();
            var form_data = new FormData();
            form_data.append("qid", quoteid);
            form_data.append("quote", quotetext);
            form_data.append("writtenby", quotewriter);
            form_data.append("update_quote", 'update');
            //alert(form_data);
            $('.update-quote-submit').addClass('d-none');
            $('.update-quote-spinner').removeClass('d-none');
            $.ajax({
                url: "ajax-user.php",
                dataType: 'script',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function(response){
                    console.log(response);
                    if(response == "OK"){
                        $('#update_ads_alert').removeClass('d-none').addClass('alert-success').text("Advertising updated.");
                        location.reload();
                    }else{
                        $('#update_ads_alert').removeClass('d-none').addClass('alert-danger').text("Failed to update advertising! Please try later.");
                    }
                    $('.update-quote-submit').removeClass('d-none');
                    $('.update-quote-spinner').addClass('d-none');
                }
            });
        }

        form.classList.add('was-validated')
      }, false)
    })
})();

(function () {
  'use strict'
  var forms = document.querySelectorAll('.needs-validation-delete')

  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }else{
            event.preventDefault();
            var values = $('#delete_ad_form').serializeArray();
            values.push({name: "delete_quote", value: 'delete'});
            $('.delete-submit').addClass('d-none');
            $('.delete-ad-spinner').removeClass('d-none');
            $.ajax({
                url: "ajax-user.php",
                type: "post",
                data: values ,
                success: function (response) {
                    console.log(response);
                    if(response == "OK"){
                        $('#delete_ad_alert').removeClass('d-none').addClass('alert-success').text("Category deleted.");
                        location.reload();
                    }else{
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

function changeId(t, id){
    if(t == 'd'){
        $('#delete_user_id').val(id);
    }else if(t == 'e'){
        $('#edit_quote_id').val(id);
        $("#update_quote_text").val($("#q"+id).text());
        $('#update_quote_writer').val($('#w'+id).text());
        $('#updateQuoteModal').modal('show');
    }
}
    
$('#parent_cat_alert').on('click', function () {
    location.reload();
});
    
</script>
<?php include "footer.php"; ?>