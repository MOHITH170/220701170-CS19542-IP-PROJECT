<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | AGRISEEKS</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles2.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            margin: 50px auto;
            max-width: 1200px;
        }
        .card img {
            height: 100%;
            object-fit: cover;
        }
        .form-container {
            padding: 2rem;
        }
        .form-container h2 {
            margin-bottom: 1rem;
        }
        #loginForm {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<header>
        <h1>AGRISEEKS</h1>
        <!-- Navigation -->
        <nav>
            <ul class="nav justify-content-center">
                <li><a href="home.php">Home</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </nav>
    </header>

    <!-- Login Card -->
    <div class="card shadow-lg">
        <div class="row g-0">
            <!-- Left Image -->
            <div class="col-md-6 d-none d-md-block">
                <img src="farm4.jpg" alt="Farm Image" class="img-fluid">
            </div>
            
            <!-- Right Side Login Form -->
            <div class="col-md-6">
                <div class="form-container">
                    <h2 class="text-center">Welcome Back!</h2>
                    <form id="loginForm" action="login.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username:</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>

                        <div class="mb-3">
                            <label for="aadhar_number" class="form-label">Aadhaar Number:</label>
                            <input type="text" class="form-control" id="aadhar_number" name="aadhar_number" maxlength="12" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" name="login">Login</button>
                    </form>
                    
                    <div class="mt-3 text-center">
                        <a href="forgot_password.php">Forgot Password?</a><br>
                        <a href="register.php">Don't have an account? Register</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center mt-5">
        <p>AGRISEEKS | Designed for a better agricultural future</p>
        <p>&copy; 2024 AGRISEEKS.com</p>
    </footer>
    <?php 
    include('config.php'); // Database connection
session_start();
if (isset($_POST['login'])) {
    $aadhar = $_POST['aadhar_number'];
    $password = $_POST['password'];
    $username = $_POST['username'];

    // Fetch user data by Aadhaar and Username
    $sql = "SELECT * FROM users WHERE aadhar_number='$aadhar' AND username='$username'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $row['password'])) {
            $_SESSION['aadhar_number'] = $row['aadhar_number'];
            $_SESSION['occupation'] = $row['occupation'];
            $_SESSION['username'] = $row['username'];

            // Redirect to home.php after successful login
            header('Location: home.php');
            exit();
        } else {
            echo "<script>alert('Incorrect password');</script>";
        }
    } else {
        echo "<script>alert('No user found with this Aadhaar number and username');</script>";
    }
}
?>

    <!-- jQuery Form Validation -->
    <script>
        $(document).ready(function() {
            $("#loginForm").submit(function(event) {
                let isValid = true;
                const username = $("#username").val().trim();
                const aadhar = $("#aadhar_number").val().trim();
                const password = $("#password").val().trim();

                if (username === "" || aadhar === "" || password === "") {
                    alert("All fields are required.");
                    isValid = false;
                }

                if (aadhar.length !== 12 || isNaN(aadhar)) {
                    alert("Please enter a valid 12-digit Aadhaar number.");
                    isValid = false;
                }

                return isValid;
            });
        });
    </script>
</body>
</html>
