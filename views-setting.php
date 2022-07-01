<?php
if (!isset($_COOKIE['mid'])) {
        header('Location: ./login.php');
        exit(0);
}
include 'DB.class.php';
include 'functions.php';
$db = new DB;
include "header.php";
$msg = '';
if (isset($_POST['updatesetting'])) {
        $viewtime = trim(strip_tags($_POST['viewtime']));
        $viewcountlimit = trim(strip_tags($_POST['viewcountlimit']));

        $setting['view_timing_limit'] = $viewtime;
        $setting['view_counts_limit'] = $viewcountlimit;

        if (file_put_contents("video-settings.json", json_encode($setting))) {
                $msg = 'ok';
        } else {
                $msg = 'fail';
        }
}

$file = fopen("video-settings.json", "r");
$line = '';
//Output lines until EOF is reached
while (!feof($file)) {
        $line .= fgets($file);
}
fclose($file);

$view_setting = json_decode($line, true);

?>
<!-- Begin Page Content -->
<div class="main_content_iner ">
        <div class="container-fluid p-0 ">

                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Views Setting</h1>
                </div>
                <div class="row">
                        <div class="col-xl-12 col-lg-12">
                                <div class="card shadow mb-4">
                                        <!-- Card Header - Dropdown -->
                                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                                <h6 class="m-0 font-weight-bold text-primary">Settings</h6>

                                        </div>
                                        <!-- Card Body -->
                                        <div class="card-body">
                                                <div class="col-lg-12">
                                                        <div class="white_card card_height_100 mb_30">
                                                                <div class="white_card_header">
                                                                        <div class="box_header m-0">
                                                                                <div class="main-title">
                                                                                        <h3 class="m-0">Video Views & Timing Counter</h3>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                                <?php if (!empty($msg) && $msg = 'ok') {
                                                                        $m = 'Settings updated.';
                                                                        $c = 'alert-success';
                                                                } else if (!empty($msg) && $msg == 'fail') {
                                                                        $m = 'Failed to update settings! Please try later.';
                                                                        $c = 'alert-danger';
                                                                }
                                                                if (!empty($msg)) {
                                                                ?>
                                                                        <div class="col-lg-12 d-flex justify-content-center">
                                                                                <div class="text-center alert <?php echo $c; ?> col-lg-6" role="alert">
                                                                                        <?php echo $m; ?>
                                                                                </div>
                                                                        </div>
                                                                <?php
                                                                }
                                                                ?>
                                                                <div class="white_card_body">
                                                                        <h6 class="card-subtitle mb-2">Here we can set time limit to count the views and counts for per person views.</h6>
                                                                        <form method="post">
                                                                                <div class="mb-3">
                                                                                        <label class="form-label" for="viewtime">Time Limit (in seconds)</label>
                                                                                        <input type="number" value="<?php echo $view_setting['view_timing_limit']; ?>" class="form-control" name="viewtime" id="viewtime" aria-describedby="timeHelp" placeholder="Enter time limit in seconds">
                                                                                        <small id="timeHelp" class="form-text text-muted">This time will be used to count the views if the view duration exeeds.</small>
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                        <label class="form-label" for="viewcountlimit">View counter per Person</label>
                                                                                        <input type="number" value="<?php echo $view_setting['view_counts_limit']; ?>" class="form-control" name="viewcountlimit" id="viewcountlimit" aria-describedby="viewCountHelp" placeholder="Enter view counting limit">
                                                                                        <small id="viewCountHelp" class="form-text text-muted">If user views on single video riches then views will not be counted.</small>
                                                                                </div>
                                                                                <button type="submit" class="btn btn-primary" value="update" name="updatesetting">Update</button>
                                                                        </form>
                                                                </div>
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
                                        <h5 class="modal-title" id="editCategoryLabel">Delete User</h5>
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


</div>


<?php include "js_include.php"; ?>
<script>

</script>
<?php include "footer.php"; ?>