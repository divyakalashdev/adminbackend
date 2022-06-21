<?php

if (!isset($_COOKIE['mid'])) {
  header('Location: ./login.php');
  exit(0);
}
include 'DB.class.php';
include 'functions.php';
$db = new DB;
include "header.php";
?>
<div class="main_content_iner ">
  <div class="container-fluid p-0 ">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
      <!--<a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>-->
    </div>

    <div class="row">

      <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
          <!-- Card Header - Dropdown -->
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Videos</h6>
            <!--<div>
                            <a class="btn btn-primary btn-sm" href="create-user.php" >Create New App Users</a>
                        </div>-->
          </div>
          <!-- Card Body -->
          <div class="card-body">
            <div class="user_list">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Title</th>
                      <th>Video</th>
                      <th>Thumbnail</th>
                      <th>Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $conditions['order_by'] = 'id';
                    $conditions['limit'] = '5';

                    $videos = $db->getRows('videos', $conditions);
                    if (!empty($videos)) {
                      foreach ($videos as $vid) {
                        echo '<tr>
                                                <td>' . $vid['title'] . '</td>
                                                <td><video width="220" controls>
                  <source src="' . $vid['video_url'] . '" type="video/mp4">
                </video></td>
                                                <td><img src="' . $vid['thumbnail'] . '" width="150"</td>
                                                <td>' . $vid['created_at'] . '</td>
                                              </tr>';
                      }
                    }
                    ?>
                    <tr>
                      <td colspan="4"><a href="videos.php">View All...</a></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">

    </div>

  </div>
  <!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
<?php include 'js_include.php'; ?>
<?php include "footer.php"; ?>