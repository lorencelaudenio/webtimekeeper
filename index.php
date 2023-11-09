<?php
include('header.php');
include('conn.php');
include('scripts.php');

$username = $_POST['username'] ?? null;
$password = $_POST['password'] ?? null;
$cpassword = $_POST['password'] ?? null;
$notes = $_POST['notes'] ?? null;
$timein = "";
$timeout = "";

date_default_timezone_set("Asia/Manila");
$curdate = date("Y-m-d");
$curtime = date("H:i:s");
$timezone = date_default_timezone_set("Asia/Manila");

if (isset($_POST['timein'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $curdate = date("Y-m-d");
    $curtime = date("H:i:s");
    $notes = ""; // Make sure to define $notes if it's not coming from the form

    if ($username == '' || $password == '') {
        echo "<script>alert('All fields required.');</script>";
    } else {
        // Sanitize user input to prevent SQL injection
        $username = mysqli_real_escape_string($conn, $username);
        $password = mysqli_real_escape_string($conn, $password);

        $checkUsers = $conn->query("SELECT * FROM tblUsers WHERE username='$username' AND password='$password'");
        
        if (mysqli_num_rows($checkUsers) > 0) {
            $checkDate = $conn->query("SELECT * FROM $username WHERE date = '$curdate'");
            
            if (mysqli_num_rows($checkDate) > 0) {
                $datelogassoc = mysqli_fetch_assoc($checkDate);
                $dateassoc = $datelogassoc['date'];
                $timeinassoc = $datelogassoc['timein'];
                $timeoutassoc = $datelogassoc['timeout'];
                
                if (!empty($timeinassoc)) {
                    if (!empty($timeoutassoc)) {
                        echo "
                            <script>
                            alert('You are already logged in and logged out today!');
                            window.location.href = 'view.php';
                            </script>
                        ";
                    } else {
                        echo "<script>alert('You are already logged in. Please logout.');</script>";
                    }
                } else {
                    // Update the existing record with a timein value
                    $update = $conn->query("UPDATE $username SET timein = '$curtime' WHERE date = '$curdate'");
                    echo "<script>alert('Successfully logged in!');</script>";
                }
            } else {
                $insert = $conn->query("INSERT INTO $username (`date`, `timein`, `timeout`, `notes`) VALUES ('$curdate', '$curtime', '00:00:00', '$notes')");
                echo "<script>alert('Successfully logged in!');</script>";
            }
        } else {
            echo "<script>alert('Invalid username or password. Please try again.');</script>";
        }
    }
}


if (isset($_POST['timeout'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $curdate = date("Y-m-d");
    $curtime = date("H:i:s");
    $notes = ""; // Make sure to define $notes if it's not coming from the form

    if ($username == '' || $password == '') {
        echo "<script>alert('All fields required.');</script>";
    } else {
        // Sanitize user input to prevent SQL injection
        $username = mysqli_real_escape_string($conn, $username);
        $password = mysqli_real_escape_string($conn, $password);

        $checkUsers = $conn->query("SELECT * FROM `tblUsers` WHERE `username`='$username' AND `password`='$password'");

        if (mysqli_num_rows($checkUsers) > 0) {
            $checkDate = $conn->query("SELECT * FROM $username WHERE `date` = '$curdate'");

            if (mysqli_num_rows($checkDate) > 0) {
                $datelogassoc = mysqli_fetch_assoc($checkDate);
                $timeoutassoc = $datelogassoc['timeout'];

                if ($timeoutassoc != '00:00:00') {
                    echo "
                        <script>
                            alert('You are already logged out!');
                            window.location.href = 'view.php';
                        </script>
                    ";
                } else {
                    // Update the existing record with a timeout value and notes
                    $update = $conn->query("UPDATE $username SET `timeout`='$curtime', `notes`='$notes' WHERE `date`='$curdate'");
                    echo "<script>alert('Successfully logged out!');</script>";
                }
            } else {
                echo "<script>alert('You have not logged in today.');</script>";
            }
        } else {
            echo "<script>alert('Invalid username or password. Please try again.');</script>";
        }
    }
}



if (isset($_POST['view'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username == '' || $password == '') {
        echo "<script>alert('All fields required.');</script>";
    } else {
        // Sanitize user input to prevent SQL injection
        $username = mysqli_real_escape_string($conn, $username);
        $password = mysqli_real_escape_string($conn, $password);

        $checkUsers = $conn->query("SELECT * FROM tblUsers WHERE username='$username' AND password='$password'");

        if (mysqli_num_rows($checkUsers) > 0) {
            // Start a session
            session_start();

            // Store the username in a session variable for later use
            $_SESSION['username'] = $username;

            // Redirect to the 'view.php' page
            header("location: view.php");
            exit(); // Terminate the script to ensure a clean redirect
        } else {
            echo "<script>alert('Invalid username or password. Please try again.');</script>";
        }
    }
}

?>
<title>Webtimekeeper - Time kept!</title>



<section class="vh-100 " style="background-color: hsl(0, 0%, 96%)">
    <div class="container py-3 h-100  col-md-4 rounded">
    <div class="row d-flex  justify-content-center align-items-center h-100">
    <div class="card   shadow " style="border-radius: 1rem;">
        <div class="card-body p-4 text-center">
            <form class="col-12" action="" method="post">
                <div class="text-center">
                    <h4 class="mb-3">Webtimekeeper <i class="bi bi-clock"></i>  </h4>
                </div>

                <hr>

                <div>
                    <?php echo date_default_timezone_get() ;?> <i class="bi bi-geo-alt"></i>
                </div>

                <div >
                    <h1 id="txt" class="display-4 font-weight-bold"></h1>
                </div>

                <div>
                    <?php echo $date= date('l, M j') ;?>
                </div>

                <div class="form-group mt-3">
                    <input type="text" name="username" class="form-control" id="username" aria-describedby="usernameHelp" placeholder="Enter username" required>
                    <!-- <small id="usernameHelp" class="form-text text-muted">We'll never share your details with anyone else.</small> -->
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" aria-label="Enter password" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                        <span class="input-group-text" id="basic-addon2"><i class="bi bi-eye" onclick="ShowPass()"></i></span>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <input type="text" name="notes" id="notes" class="form-control" placeholder="Enter notes" aria-label="Enter notes" aria-describedby="basic-addon2">
                </div>
                
                <div class="btn-group btn-group-lg" role="group" aria-label="Basic example">
                    <input type="submit" name="timein" class="btn btn-success" value="Timein" <?php echo $timein;?>/>
                    <input type="submit" name="timeout" class="btn btn-primary" value="Timeout" <?php echo $timeout;?>/>
                    <input type="submit" name="view" class="btn btn-info" value="View"/>
                </div>

                <p class="text-center text-muted mt-3 mb-0">Don't have an account?  <a href="register.php"
                    class=""><u>Register here</u></a>
                </p>
            </form>   
        </div>
    </div>
    </div>
    </div>
</section>
<?php include('footer.php');?>