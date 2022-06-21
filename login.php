<?php
session_start();
$redirectURL = "";
// Include and initialize DB class
include 'DB.class.php';
$db = new DB();
//echo md5("AdminDK@22");
// Database table name
$usersTbl = 'users';

if (isset($_POST['login-submit'])) {
  // Get form fields value
  $username = trim(strip_tags($_POST['username']));
  $password = trim(strip_tags($_POST['input_pwd']));

  // Fields validation
  $errorMsg = '';

  if (empty($username)) {
    $errorMsg .= '<li>Please enter a valid email id.</li>';
  }
  if (empty($password)) {
    $errorMsg .= '<li>Please enter your password.</li>';
  }
  // Submit the form data
  if (empty($errorMsg)) {
    // Submitted form data
    $conditions['where'] = array(
      'email' => $username,
      'password' => md5($password),
      'status' => 1
    );

    $conditions['return_type'] = 'single';
    $userData = $db->getRows($usersTbl, $conditions);
    $userData = !empty($sessData['userData']) ? $sessData['userData'] : $userData;

    if (!empty($userData) && count($userData) > 1) {
      setcookie('mid', $userData['id'], time() + (86400 * 30), "/"); // 86400 = 1 day
      setcookie('type', $userData['role'], time() + (86400 * 30), "/"); // 86400 = 1 day
      $sessData['status']['type'] = 'success';
      $sessData['status']['msg'] = 'You are successfully logged in.';

      if ($userData['role'] == 'Admin') {
        $redirectURL = 'index.php';
      }

      header("Location:" . $redirectURL);
      // Remote submitted fields value from session
      unset($sessData['userData']);
      exit;
    } else {
      $sessData['status']['type'] = 'error';
      $sessData['status']['msg'] = '<li>Invalid username/Password</li>';
    }
  } else {
    $sessData['status']['type'] = 'error';
    $sessData['status']['msg'] = '<li>Please fill all the mandatory fields.</li>' . $errorMsg;
  }

  // Get status message from session
  if (!empty($sessData['status']['msg'])) {
    $statusMsg = $sessData['status']['msg'];
    $statusMsgType = $sessData['status']['type'];
  }

  // Store status into the session
  $_SESSION['sessData'] = $sessData;
}
$path = DB::getBasePath();
?>
<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Admin - Login</title>

  <!-- Custom fonts for this template-->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

  <div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

      <div class="col-xl-10 col-lg-12 col-md-9">

        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
              <div class="col-lg-6 offset-lg-3">
                <div class="p-5">
                  <div class="text-center">
                    <!--<h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>-->
                    <h1 class="h4 text-gray-900 mb-4">Admin</h1>
                  </div>
                  <?php if (!empty($statusMsg) && ($statusMsgType == 'error')) { ?>
                    <p class="col-xs-12">
                    <div class="alert alert-danger">
                      <ul><?php echo $statusMsg; ?></ul>
                    </div>
                    </p>
                  <?php } ?>
                  <div id="msg"></div><br>
                  <form class="user" method="post">
                    <div class="form-group">
                      <input type="email" class="form-control form-control-user" id="username" name="username" aria-describedby="emailHelp" placeholder="Enter Email Address...">
                    </div>
                    <div class="form-group">
                      <input type="password" class="form-control form-control-user" id="input_pwd" name="input_pwd" placeholder="Password">
                    </div>
                    <!--<div class="form-group">
                      <div class="custom-control custom-checkbox small">
                        <input type="checkbox" class="custom-control-input" id="customCheck">
                        <label class="custom-control-label" for="customCheck">Remember Me</label>
                      </div>
                    </div>-->
                    <button type="submit" id="btn_login" name="login-submit" class="btn btn-primary btn-user btn-block">Login</button>
                  </form>
                  <hr>
                  <!--<div class="text-center">
                    <a class="small" href="forgot-password.php">Forgot Password?</a>
                  </div>-->
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>

  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin-2.min.js"></script>

  <script>
    $('#btn_login').on('click', function() {
      var user = $.trim($('#username').val());
      var pwd = $.trim($('#input_pwd').val());
      var msg = '';
      if (user == '' || !isEmail(user)) {
        msg += '<li>Please enter a valid email id.</li>';
      }
      if (pwd == '') {
        msg += '<li>Please enter your password.</li>';
      }
      if (msg != '') {
        $('#msg').addClass('text-danger alert-danger alert').html('<ul><li>Please fill all the mandatory fields.</li>' + msg + '</ul>');
        return false;
      } else {
        return true;
      }
    });
  </script>

</body>

</html>