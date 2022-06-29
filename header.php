<?php
require_once 'DB.class.php';
$path = DB::getBasePath();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title> <?php if (isset($_COOKIE['type'])) {
                echo $_COOKIE['type'];
            } ?> - Dashboard</title>
    <link rel="icon" href="img/logo.png" type="image/png">

    <link rel="stylesheet" href="css/bootstrap1.min.css" />

    <link rel="stylesheet" href="vendors/themefy_icon/themify-icons.css" />

    <link rel="stylesheet" href="vendors/niceselect/css/nice-select.css" />

    <link rel="stylesheet" href="vendors/owl_carousel/css/owl.carousel.css" />

    <link rel="stylesheet" href="vendors/gijgo/gijgo.min.css" />

    <link rel="stylesheet" href="vendors/font_awesome/css/all.min.css" />
    <link rel="stylesheet" href="vendors/tagsinput/tagsinput.css" />

    <link rel="stylesheet" href="vendors/datepicker/date-picker.css" />

    <link rel="stylesheet" href="vendors/scroll/scrollable.css" />

    <link rel="stylesheet" href="vendors/datatable/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="vendors/datatable/css/responsive.dataTables.min.css" />
    <link rel="stylesheet" href="vendors/datatable/css/buttons.dataTables.min.css" />

    <link rel="stylesheet" href="vendors/text_editor/summernote-bs4.css" />

    <link rel="stylesheet" href="vendors/morris/morris.css">

    <link rel="stylesheet" href="vendors/material_icon/material-icons.css" />

    <link rel="stylesheet" href="css/metisMenu.css">

    <link rel="stylesheet" href="css/style1.css" />
    <link rel="stylesheet" href="css/colors/default.css" id="colorSkinCSS">

</head>

<body class="crm_body_bg">
    <?php
    if (isset($_COOKIE['type']) && $_COOKIE['type'] == 'Admin') {
        include "sidebar.php";
    }
    ?>

    <section class="main_content dashboard_part large_header_bg">

        <div class="container-fluid g-0">
            <div class="row">
                <div class="col-lg-12 p-0 ">
                    <div class="header_iner d-flex justify-content-between align-items-center">
                        <div class="sidebar_icon d-lg-none">
                            <i class="ti-menu"></i>
                        </div>
                        <div class="serach_field-area d-flex align-items-center">
                            <div class="search_inner">
                                <!-- <form action="index_3.html#">
                                    <div class="search_field">
                                        <input type="text" placeholder="Search here...">
                                    </div>
                                    <button type="submit"> <img src="img/icon/icon_search.svg" alt=""> </button>
                                </form> -->
                            </div>
                            <span class="f_s_14 f_w_400 ml_25 white_text text_white">Apps</span>
                        </div>
                        <div class="header_right d-flex justify-content-between align-items-center">
                            <!-- <div class="header_notification_warp d-flex align-items-center">
                                <li>
                                    <a class="bell_notification_clicker nav-link-notify" href="index_3.html#"> <img src="img/icon/bell.svg" alt="">
                                    </a>

                                    <div class="Menu_NOtification_Wrap">
                                        <div class="notification_Header">
                                            <h4>Notifications</h4>
                                        </div>
                                        <div class="Notification_body">

                                            <div class="single_notify d-flex align-items-center">
                                                <div class="notify_thumb">
                                                    <a href="index_3.html#"><img src="img/staf/2.png" alt=""></a>
                                                </div>
                                                <div class="notify_content">
                                                    <a href="index_3.html#">
                                                        <h5>Cool Marketing
                                                        </h5>
                                                    </a>
                                                    <p>Lorem ipsum
                                                        dolor
                                                        sit amet
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="single_notify d-flex align-items-center">
                                                <div class="notify_thumb">
                                                    <a href="index_3.html#"><img src="img/staf/4.png" alt=""></a>
                                                </div>
                                                <div class="notify_content">
                                                    <a href="index_3.html#">
                                                        <h5>Awesome
                                                            packages
                                                        </h5>
                                                    </a>
                                                    <p>Lorem ipsum
                                                        dolor
                                                        sit amet
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="single_notify d-flex align-items-center">
                                                <div class="notify_thumb">
                                                    <a href="index_3.html#"><img src="img/staf/3.png" alt=""></a>
                                                </div>
                                                <div class="notify_content">
                                                    <a href="index_3.html#">
                                                        <h5>what a
                                                            packages
                                                        </h5>
                                                    </a>
                                                    <p>Lorem ipsum
                                                        dolor
                                                        sit amet
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="single_notify d-flex align-items-center">
                                                <div class="notify_thumb">
                                                    <a href="index_3.html#"><img src="img/staf/2.png" alt=""></a>
                                                </div>
                                                <div class="notify_content">
                                                    <a href="index_3.html#">
                                                        <h5>Cool Marketing
                                                        </h5>
                                                    </a>
                                                    <p>Lorem ipsum
                                                        dolor
                                                        sit amet
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="single_notify d-flex align-items-center">
                                                <div class="notify_thumb">
                                                    <a href="index_3.html#"><img src="img/staf/4.png" alt=""></a>
                                                </div>
                                                <div class="notify_content">
                                                    <a href="index_3.html#">
                                                        <h5>Awesome
                                                            packages
                                                        </h5>
                                                    </a>
                                                    <p>Lorem ipsum
                                                        dolor
                                                        sit amet
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="single_notify d-flex align-items-center">
                                                <div class="notify_thumb">
                                                    <a href="index_3.html#"><img src="img/staf/3.png" alt=""></a>
                                                </div>
                                                <div class="notify_content">
                                                    <a href="index_3.html#">
                                                        <h5>what a
                                                            packages
                                                        </h5>
                                                    </a>
                                                    <p>Lorem ipsum
                                                        dolor
                                                        sit amet
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="nofity_footer">
                                            <div class="submit_button text-center pt_20">
                                                <a href="index_3.html#" class="btn_1">See
                                                    More</a>
                                            </div>
                                        </div>
                                    </div>

                                </li>
                                <li>
                                    <a class="CHATBOX_open nav-link-notify" href="index_3.html#"> <img src="img/icon/msg.svg" alt="">
                                    </a>
                                </li>
                            </div> -->
                            <div class="profile_info">
                                <img src="img/avatar.jpg" alt="#">
                                <div class="profile_info_iner">
                                    <div class="profile_author_name">
                                        <p>
                                            <?php
                                            if (isset($_COOKIE['type'])) {
                                                echo "Admin";
                                            }
                                            ?>
                                        </p>
                                        <!-- <h5>Dr. Robar Smith</h5> -->
                                    </div>
                                    <div class="profile_info_details">
                                        <a href="#">My Profile </a>
                                        <a href="#">Settings</a>
                                        <a href="#" data-toggle="modal" data-target="#logoutModal">Log Out </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>