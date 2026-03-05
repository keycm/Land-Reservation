<?php
// register.php
include 'config.php';

$error = "";
if(isset($_POST['register'])){
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']);
    $p = md5($_POST['password']);

    $check = $conn->query("SELECT * FROM users WHERE email='$email'");

    if($check->num_rows > 0){
        $error = "Email is already registered.";
    } else {
        $insert = $conn->query("INSERT INTO users (fullname, phone, email, password, role) VALUES ('$fullname', '$phone', '$email', '$p', 'BUYER')");
        if($insert){
            header("Location: login.php?success=1");
            exit();
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | EcoEstates</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="login-body">

    <div class="login-box">
        <div style="font-size: 40px; margin-bottom: 10px;">🌲</div>

        <h2>EcoEstates</h2>
        <span class="version">System v2.0</span>

        <?php if($error): ?>
            <div style="background:#ffebee; color:#c62828; padding:10px; border-radius:6px; font-size:13px; margin-bottom:15px;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <i class="fa-solid fa-users-gear"></i>
                <input type="text" name="fullname" placeholder="Full Name" required>
            </div>

            <div class="input-group">
                <i class="fa-solid fa-phone"></i>
                <input type="text" name="phone" placeholder="Phone Number" required>
            </div>

            <div class="input-group">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" name="email" placeholder="Email Address" required>
            </div>

            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" name="register" class="btn-login">
                REGISTER NOW
            </button>
        </form>

        <div class="small-text">
            Already have an account? <a href="login.php">Login here</a><br><br>
            Go back to <a href="index.php">Website Homepage</a>
        </div>
    </div>

</body>
</html>
