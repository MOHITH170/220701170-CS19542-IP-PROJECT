<?php 
include('config.php'); // Database connection
session_start();

// Check if the user is logged in
if (!isset($_SESSION['aadhar_number'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

if (!isset($_GET['job_id'])) {
    die("Error: Job ID not provided.");
}

$job_id = $_GET['job_id']; // Assuming job_id is passed to this page

// Fetch job details
$sql_job = "SELECT * FROM jobs WHERE job_id = ?";
$stmt_job = $conn->prepare($sql_job);

if (!$stmt_job) {
    die("Error preparing job details query: (" . $conn->errno . ") " . $conn->error);
}

$stmt_job->bind_param("i", $job_id);
$stmt_job->execute();
$job_result = $stmt_job->get_result();
$job_data = $job_result->fetch_assoc();

if (!$job_data) {
    die("Error: Job not found.");
}

// Handle labour application submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply'])) {
    $labourers_needed = intval($_POST['labourers_needed']);
    $valid_labourers = [];
    $errors = [];

    // Loop to process each labourer's details
    for ($i = 1; $i <= $labourers_needed; $i++) {
        $aadhar = $_POST['aadhar_' . $i];
        $name = $_POST['name_' . $i];
        $phone = $_POST['phone_' . $i];
        $skillset = $_POST['skillset_' . $i];

        // Verify the labourer exists in the users table
        $sql_user = "SELECT * FROM users WHERE aadhar_number = ?";
        $stmt_user = $conn->prepare($sql_user);

        if (!$stmt_user) {
            die("Error preparing labourer verification query: (" . $conn->errno . ") " . $conn->error);
        }

        $stmt_user->bind_param("s", $aadhar);
        $stmt_user->execute();
        $user_result = $stmt_user->get_result();

        if ($user_result->num_rows > 0) {
            // If labourer is valid, add them to the list
            $valid_labourers[] = [
                'aadhar' => $aadhar,
                'name' => $name,
                'phone' => $phone,
                'skillset' => $skillset
            ];
        } else {
            $errors[] = "Labourer $i (Aadhar: $aadhar) is not registered.";
        }
    }

    // If there are no errors, process the application
    if (empty($errors)) {
        // Store application in the database
        $insert_sql = "INSERT INTO job_applications (job_id, labourer_data, created_at) VALUES (?, ?, NOW())";
        $labourer_data = json_encode($valid_labourers); // Save labourers' data as JSON
        $insert_stmt = $conn->prepare($insert_sql);

        if (!$insert_stmt) {
            die("Error preparing application insertion query: (" . $conn->errno . ") " . $conn->error);
        }

        $insert_stmt->bind_param("is", $job_id, $labourer_data);
        if ($insert_stmt->execute()) {
            echo "<script>alert('Application submitted successfully!');</script>";
        } else {
            echo "<script>alert('Error submitting application. Please try again.');</script>";
        }
    } else {
        // Show errors if any labourers were invalid
        foreach ($errors as $error) {
            echo "<script>alert('$error');</script>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Labour Application</title>
    <link rel="stylesheet" href="styles2.css">
</head>
<body>

<header>
    <h1>Apply for Job</h1>
    <nav>
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<h2>Job Details</h2>
<p><strong>Job Title:</strong> <?php echo htmlspecialchars($job_data['job_title']); ?></p>
<p><strong>Description:</strong> <?php echo htmlspecialchars($job_data['job_description']); ?></p>
<p><strong>Location:</strong> <?php echo htmlspecialchars($job_data['location']); ?></p>
<p><strong>Phone:</strong> <?php echo htmlspecialchars($job_data['phone']); ?></p>

<section>
    <h3>Apply for the Job</h3>
    <form method="POST">
        <label for="labourers_needed">Number of Labourers Applying:</label>
        <input type="number" id="labourers_needed" name="labourers_needed" min="1" required><br><br>

        <div id="labourers_section"></div>

        <button type="submit" name="apply">Submit Application</button>
    </form>
</section>

<footer>
    <p>AGRISEEKS | Designed for a better agricultural future</p>
    <p>&copy; 2024 AGRISEEKS.com</p>
</footer>

<script>
// Handle dynamic fields for labourer details
document.getElementById('labourers_needed').addEventListener('input', function() {
    const numberOfLabourers = this.value;
    let labourersSection = document.getElementById('labourers_section');
    labourersSection.innerHTML = '';

    for (let i = 1; i <= numberOfLabourers; i++) {
        labourersSection.innerHTML += `
            <h4>Labourer ${i}</h4>
            <label for="aadhar_${i}">Aadhar Number:</label>
            <input type="text" id="aadhar_${i}" name="aadhar_${i}" required><br><br>

            <label for="name_${i}">Name:</label>
            <input type="text" id="name_${i}" name="name_${i}" required><br><br>

            <label for="phone_${i}">Phone:</label>
            <input type="text" id="phone_${i}" name="phone_${i}" required><br><br>

            <label for="skillset_${i}">Skillset:</label>
            <input type="text" id="skillset_${i}" name="skillset_${i}" placeholder="Enter skillset" required><br><br>
        `;
    }
});
</script>

</body>
</html>

<?php
// Close the database connections
$stmt_job->close();
$conn->close();
?>
