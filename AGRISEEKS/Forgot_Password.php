<?php include('config.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles2.css">
    <title>Forgot Password - AGRISEEKS</title>
</head>
<body class="bg-light">
    <header>
        <h1>AGRISEEKS</h1>
        <nav>
            <ul class="nav justify-content-center">
                <li><a href="home.php">Home</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
        </nav>
    </header>

    <div class="container mt-5">
        <h2 class="text-center">Forgot Password</h2>
            <div class="card-body">
                <form action="forgot_password.php" method="POST" onsubmit="return validatePasswords();">
                    <div class="form-group">
                        <label for="aadhar_number">Aadhaar Number:</label>
                        <input type="text" class="form-control" id="aadhar_number" name="aadhar_number" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password:</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" class="form-control" id="confirm_password" required>
                    </div>
                    <button type="submit" name="reset_password" class="btn btn-primary btn-block">Reset Password</button>
                </form>
            </div>
        </div>
    </div>

    <footer class="text-center mt-4">
        <p>AGRISEEKS | Designed for a better agricultural future</p>
        <p>&copy; 2024 AGRISEEKS.com</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // JavaScript for validating the password and confirm password fields
        function validatePasswords() {
            var newPassword = document.getElementById('new_password').value;
            var confirmPassword = document.getElementById('confirm_password').value;

            if (newPassword !== confirmPassword) {
                alert('Passwords do not match!');
                return false;  // Prevent form submission if passwords don't match
            }
            return true;  // Proceed with form submission if passwords match
        }
    </script>

</body>
</html>

<?php
if (isset($_POST['reset_password'])) {
    $aadhar_number = $_POST['aadhar_number'];
    $new_password = $_POST['new_password'];
    
    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Check if Aadhaar number exists in the database
    $sql = "SELECT * FROM users WHERE aadhar_number = '$aadhar_number'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Update the password in the database
        $update_sql = "UPDATE users SET password = '$hashed_password' WHERE aadhar_number = '$aadhar_number'";
        if ($conn->query($update_sql) === TRUE) {
            echo "<script>alert('Password reset successfully! Please login with your new password.');</script>";
        } else {
            echo "<script>alert('Error updating password. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('Aadhaar number not found.');</script>";
    }
}
?>
