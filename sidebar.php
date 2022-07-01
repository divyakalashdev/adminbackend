<?php
$maincategories = $db->getRows("categories", array('order_by' => 'priority', 'select' => 'id, category', 'where' => array('parent_id' => 0)));

$sql = "SELECT s.*, p.category as parent_cat, p.id as catid FROM categories p INNER JOIN categories s ON p.id = s.parent_id ORDER BY s.priority";
$subcategories = $db->customQuery($sql);
$subcatids = array();
//print_r($subcategories);
foreach ($subcategories as $sub) {
  //echo array_search($sub['id'], $subcatids);
  if (array_search($sub['parent_id'], $subcatids) == '') {
    array_push($subcatids, $sub['parent_id']);
  }
}

?>
<nav class="sidebar dark_sidebar vertical-scroll  ps-container ps-theme-default ps-active-y">
  <div class="logo d-flex justify-content-between">
    <a href="index.html"><img src="img/logo.png" alt="Logo" /></a>
    <div class="sidebar_close_icon d-lg-none">
      <i class="ti-close"></i>
    </div>
  </div>
  <ul id="sidebar_menu">
    <li class="mm-active">
      <a class="has-arrow" href="index.php" aria-expanded="false">

        <div class="icon_menu">
          <img src="img/menu-icon/dashboard.svg" alt="">
        </div>
        <span>Dashboard</span>
      </a>
    </li>
    <li class="">
      <a class="has-arrow" href="#">
        <div class="icon_menu">
          <img src="img/menu-icon/2.svg" alt="">
        </div>
        <span>Categories</span>
      </a>
      <ul>
        <li><a href="categories.php" style="word-break: break-all;">View All</a></li>
      </ul>
      <?php
      foreach ($maincategories as $mc) {
        if (array_search($mc['id'], $subcatids) == '') {
          echo '<ul>
          <li><a href="videos.php?c=' . $mc['id'] . '" style="word-break: break-all;">' . $mc['category'] . '</a></li>
        </ul>';
        } else if (array_search($mc['id'], $subcatids) != '') {
          echo '<ul><li class=""><a class="has-arrow" href="#" aria-expanded="false">
                  <span>' . $mc['category'] . '</span>
                </a>';
          foreach ($subcategories as $sub) {
            if ($sub['parent_id'] == $mc['id']) {

              echo '<li><a href="videos.php?c=' . $sub['id'] . '" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-' . $sub['category'] . '</a></li>';
            }
          }
          echo "</li></ul>";
        }
      }
      ?>
    </li>
    <li class="">
      <a class="has-arrow" href="#" aria-expanded="false">
        <div class="icon_menu">
          <img src="img/menu-icon/3.svg" alt="">
        </div>
        <span>Videos</span>
      </a>
      <ul>
        <li><a href="videos.php">Video</a></li>
        <li><a href="upload-video.php">Upload New</a></li>
        <li><a href="views-setting.php">Views Setting</a></li>
      </ul>
    </li>
    <li class="">
      <a class="has-arrow" href="#" aria-expanded="false">
        <div class="icon_menu">
          <img src="img/menu-icon/4.svg" alt="">
        </div>
        <span>Profiles</span>
      </a>
      <ul>
        <li><a href="profiles.php">Profiles</a></li>
      </ul>
    </li>
    <li class="">
      <a class="has-arrow" href="#" aria-expanded="false">
        <div class="icon_menu">
          <img src="img/menu-icon/5.svg" alt="">
        </div>
        <span>Ads</span>
      </a>
      <ul>
        <li><a href="ads.php">Ads</a></li>
      </ul>
    </li>
    <li class="">
      <a class="has-arrow" href="#" aria-expanded="false">
        <div class="icon_menu">
          <img src="img/menu-icon/6.svg" alt="">
        </div>
        <span>Quotes</span>
      </a>
      <ul>
        <li><a href="quotes.php">Quotes</a></li>
      </ul>
    </li>
    <li class="">
      <a class="has-arrow" href="#" aria-expanded="false">
        <div class="icon_menu">
          <img src="img/menu-icon/7.svg" alt="">
        </div>
        <span>Users</span>
      </a>
      <ul>
        <li><a href="users.php">Users List</a></li>
      </ul>
    </li>
  </ul>
</nav>
<!-- End of Sidebar -->