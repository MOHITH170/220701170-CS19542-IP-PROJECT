<?php include('config.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles2.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="script.js"></script>
</head>
<body>

<header>
    <h1>AGRISEEKS</h1>
    <nav>
        <ul class="nav justify-content-center">
            <li><a href="home.php">Home</a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
    </nav>
</header>

<div class="container mt-4">
    <h1>Register</h1>

    <form action="register.php" method="POST" onsubmit="return validateRegistrationForm();" class="needs-validation" novalidate>
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" required>
            <div class="invalid-feedback">Please enter a username.</div>
        </div>

        <div class="form-group">
            <label for="aadhar_number">Aadhaar Number:</label>
            <input type="text" class="form-control" id="aadhar_number" name="aadhar_number" maxlength="12" required>
            <div class="invalid-feedback">Please enter a valid Aadhaar number.</div>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number:</label>
            <input type="tel" class="form-control" id="phone" name="phone" pattern="[0-9]{10}" required>
            <div class="invalid-feedback">Please enter a valid 10-digit phone number.</div>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
            <div class="invalid-feedback">Please enter a valid email.</div>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
            <div class="invalid-feedback">Please enter a password.</div>
        </div>

        <div class="form-group">
            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" class="form-control" id="confirmPassword" required>
            <div class="invalid-feedback">Please confirm your password.</div>
        </div>

        <label>Occupation:</label>
        <div id="RADIO" class="form-group">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="occupation" value="farmer" onclick="toggleOccupationFields();" required>
                <label class="form-check-label">Farmer</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="occupation" value="labour" onclick="toggleOccupationFields();" required>
                <label class="form-check-label">Labour</label>
            </div>
        </div>

        <!-- Farmer specific fields -->
        <div id="farmerFields" style="display: none;">
            <div class="form-group">
                <label for="farm_location">Farm Location:</label>
                <input type="text" class="form-control" id="farm_location" name="farm_location">
            </div>
            <div class="form-group">
                <label for="farm_size">Farm Size (in acres):</label>
                <input type="number" class="form-control" id="farm_size" name="farm_size">
            </div>
        </div>

        <!-- Labour specific fields -->
        <div id="labourFields" style="display: none;">
            <div class="form-group">
                <label for="skillset">Skillset:</label>
                <input type="text" class="form-control" id="skillset" name="skillset">
            </div>
            <div class="form-group">
                <label for="experience_years">Experience (in years):</label>
                <input type="number" class="form-control" id="experience_years" name="experience_years">
            </div>
        </div>

        <button type="submit" name="register" class="btn btn-primary">Register</button>
    </form>
</div>

<footer>
    <p>AGRISEEKS | Designed for a better agricultural future</p>
    <p>&copy; 2024 AGRISEEKS.com</p>
</footer>

<script>
    function toggleOccupationFields() {
        if ($('input[name="occupation"]:checked').val() === 'farmer') {
            $('#farmerFields').show();
            $('#labourFields').hide();
        } else {
            $('#labourFields').show();
            $('#farmerFields').hide();
        }
    }

    // Form validation
    (function () {
        'use strict';
        window.addEventListener('load', function () {
            var forms = document.getElementsByClassName('needs-validation');
            Array.prototype.filter.call(forms, function (form) {
                form.addEventListener('submit', function (event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>

</body>
</html>

<?php
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $aadhar = $_POST['aadhar_number'];
    $email = $_POST['email'];
    $phone = $_POST['phone']; // Phone number added here
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $occupation = $_POST['occupation'];

    $sql = "INSERT INTO users (aadhar_number, username, email, phone, password, occupation) VALUES ('$aadhar', '$username', '$email', '$phone', '$password', '$occupation')"; // Updated query with phone number
    
    if ($conn->query($sql) === TRUE) {
        if ($occupation == 'farmer') {
            $farm_location = $_POST['farm_location'];
            $farm_size = $_POST['farm_size'];
            $conn->query("INSERT INTO farmers_details (farmer_aadhar, farm_location, farm_size) VALUES ('$aadhar', '$farm_location', '$farm_size')");
        } else {
            $skillset = $_POST['skillset'];
            $experience = $_POST['experience_years'];
            $conn->query("INSERT INTO labours_details (labour_aadhar, skillset, experience_years) VALUES ('$aadhar', '$skillset', '$experience')");
        }
        echo "<script>alert('Registration successful!');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>
