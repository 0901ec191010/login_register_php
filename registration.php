<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: login.php");
    exit(); 
}

$errors = array();
$successMessage = '';

if (isset($_POST["submit"])) {
    $fullName = $_POST["fullname"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $passwordRepeat = $_POST["repeat_password"];

    // Validate inputs
    if (empty($fullName) || empty($email) || empty($password) || empty($passwordRepeat)) {
        array_push($errors, "All fields are required");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($errors, "Email is not valid");
    }
    if (strlen($password) < 8) {
        array_push($errors, "Password must be at least 8 characters long");
    }
    if ($password !== $passwordRepeat) {
        array_push($errors, "Passwords do not match");
    }

    require_once "database.php";
    
    if (count($errors) === 0) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) > 0) {
                array_push($errors, "Email already exists!");
            }
        } else {
            die("Something went wrong with the query");
        }

        if (count($errors) === 0) {
            $sql = "INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)";
            if (mysqli_stmt_prepare($stmt, $sql)) {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                mysqli_stmt_bind_param($stmt, "sss", $fullName, $email, $passwordHash);
                mysqli_stmt_execute($stmt);
                $successMessage = "You are registered successfully.";
                // header("Location: login.php");
                // exit();
            } else {
                die("Something went wrong with the query");
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <style>
       
    </style>
</head>
<body>
    <div class="container">
        <?php
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
        if ($successMessage) {
            echo "<div class='alert alert-success'>$successMessage</div>";
        }
        ?>
        <div class="text-center py-3"><h2>Register Form</h2></div>
        <form action="registration.php" method="post">
            <div class="form-group">
                <input type="text" class="form-control" name="fullname" placeholder="Full Name:" value="<?php echo htmlspecialchars($fullName ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email:" value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password:">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password:">
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Register" name="submit">
            </div>
        </form>
        <div class="text-center mt-3">
            <p>Already Registered? <a href="login.php">Login Here</a></p>
        </div>
    </div>

    <script>
       setTimeout(function() {
           var alerts = document.querySelectorAll('.alert');
           alerts.forEach(function(alert) {
               alert.remove();
           });
       }, 1000);
    </script>
</body>
</html>
