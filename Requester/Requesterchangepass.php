<?php
define('TITLE', 'Change Password');
define('PAGE', 'Requesterchangepass');
include('includes/header.php');
include('../dbConnection.php');

session_start();

if (!$_SESSION['is_login']) {
  echo "<script> location.href='RequesterLogin.php'; </script>";
  exit; // Exit to prevent further execution if not logged in
}

$rEmail = $_SESSION['rEmail'];

if (isset($_REQUEST['passupdate'])) {
  if (empty($_REQUEST['rPassword'])) {
    // msg displayed if required field missing
    $passmsg = '<div class="alert alert-warning col-sm-6 ml-5 mt-2" role="alert"> Fill All Fields </div>';
  } else {
    // Password Validation
    $password = $_REQUEST['rPassword'];
    if(strlen($password) < 8 || !preg_match("#[0-9]+#", $password) || !preg_match("#[a-zA-Z]+#", $password) || !preg_match("#[^\w]+#", $password)) {
        $passmsg = '<div class="alert alert-warning col-sm-6 ml-5 mt-2" role="alert">Password must be at least 8 characters long and contain at least one number, one letter (uppercase or lowercase), and one special character.</div>';
    } else {
        $sql = "SELECT * FROM requesterlogin_tb WHERE r_email='$rEmail'";
        $result = $conn->query($sql);

        if ($result->num_rows == 1) {
          $rPassword = $_REQUEST['rPassword'];
          $hashPassword = password_hash($rPassword, PASSWORD_DEFAULT); // Hash password

          $sql = "UPDATE requesterlogin_tb SET r_password = ? WHERE r_email = ?";
          $stmt = $conn->prepare($sql);

          // Bind parameters securely
          $stmt->bind_param("ss", $hashPassword, $rEmail);

          if ($stmt->execute()) {
            // below msg display on form submit success
            $passmsg = '<div class="alert alert-success col-sm-6 ml-5 mt-2" role="alert"> Updated Successfully </div>';
          } else {
            // below msg display on form submit failed
            $passmsg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2" role="alert"> Unable to Update </div>';
          }

          // Close the statement (optional, but good practice)
          $stmt->close();
        }
    }
  }
}
?>

<div class="col-sm-9 col-md-10">
  <div class="row">
    <div class="col-sm-6">
      <form class="mt-5 mx-5" method="POST">
        <div class="form-group">
          <label for="inputEmail">Email</label>
          <input type="email" class="form-control" id="inputEmail" value=" <?php echo $rEmail ?>" readonly>
        </div>
        <div class="form-group">
          <label for="inputnewpassword">New Password</label>
          <div class="input-group">
            <input type="password" class="form-control" id="inputnewpassword" placeholder="New Password" name="rPassword">
            <div class="input-group-append">
              <button class="btn btn-outline-secondary" type="button" id="showPasswordButton">Show</button>
            </div>
          </div>
        </div>
        <button type="submit" class="btn btn-danger mr-4 mt-4" name="passupdate">Update</button>
        <button type="reset" class="btn btn-secondary mt-4">Reset</button>
        <?php if(isset($passmsg)) {echo $passmsg; } ?>
      </form>
    </div>
  </div>
</div>
</div>
</div>

<?php
include('includes/footer.php'); 
?>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const newPasswordInput = document.getElementById('inputnewpassword');
    const showPasswordButton = document.getElementById('showPasswordButton');

    showPasswordButton.addEventListener('click', function () {
      if (newPasswordInput.type === 'password') {
        newPasswordInput.type = 'text';
        showPasswordButton.textContent = 'Hide';
      } else {
        newPasswordInput.type = 'password';
        showPasswordButton.textContent = 'Show';
      }
    });
  });
</script>
