<?php
include('config.php'); // Database connection
session_start();

// Check if the user is logged in
if (!isset($_SESSION['aadhar_number'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Get the logged-in user's Aadhaar number and username
$aadhar_number = $_SESSION['aadhar_number'];
$username = $_SESSION['username'];

// Fetch jobs posted by the logged-in farmer using aadhar_number
$sql = "SELECT * FROM jobs WHERE aadhar_number = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error preparing query: " . $conn->error);
}

$stmt->bind_param("s", $aadhar_number);
$stmt->execute();
$result = $stmt->get_result();

// Handle job posting
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_job'])) {
    $job_title = $_POST['job_title'];
    $job_description = $_POST['job_description'];
    $location = $_POST['location'];
    $workers_needed = $_POST['workers_needed'];

    // Prepare an SQL statement to insert the job
    $insert_sql = "INSERT INTO jobs (aadhar_number, username, job_title, job_description, location, workers_needed, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $insert_stmt = $conn->prepare($insert_sql);
    
    if (!$insert_stmt) {
        die("Error preparing insert query: " . $conn->error);
    }

    $insert_stmt->bind_param("sssssi", $aadhar_number, $username, $job_title, $job_description, $location, $workers_needed);

    // Execute the insertion
    if ($insert_stmt->execute()) {
        echo "<script>alert('Job posted successfully!');</script>";
    } else {
        echo "<script>alert('Error posting job. Please try again.');</script>";
    }

    // Close the insert statement
    $insert_stmt->close();

    // Refresh the job list
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard</title>
    <link rel="stylesheet" href="styles2.css">
</head>
<body>

    <header>
        <h1>AGRISEEKS</h1>
        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    
    <h2>Welcome, <?php echo htmlspecialchars($username); ?></h2>
    
    
    <section>
        <h2>Your Posted Jobs</h2>
        <table border="1">
            <tr>
                <th>Job Title</th>
                <th>Job Description</th>
                <th>Location</th>
                <th>Workers Needed</th>
                <th>Posted On</th>
                <th>Labour Applications</th>
            </tr>

            <?php
            // Display jobs if any
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['job_title']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['job_description']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['workers_needed']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                    
                    // Fetch job applications for each job
                    echo "<td><ul>";
                    $job_id = $row['job_id'];
                    $sql_applications = "SELECT * FROM job_applications WHERE job_id = ?";
                    $stmt_applications = $conn->prepare($sql_applications);
                    
                    if (!$stmt_applications) {
                        die("Error preparing application query: " . $conn->error);
                    }
                    
                    $stmt_applications->bind_param("i", $job_id);
                    $stmt_applications->execute();
                    $applications_result = $stmt_applications->get_result();
                    
                    while ($application = $applications_result->fetch_assoc()) {
                        $labourers = json_decode($application['labourer_data'], true);
                        foreach ($labourers as $labourer) {
                            echo "<li>Aadhar: " . htmlspecialchars($labourer['aadhar']) . 
                                 " | Name: " . htmlspecialchars($labourer['name']) . 
                                 " | Phone: " . htmlspecialchars($labourer['phone']) . 
                                 " | Skillset: " . htmlspecialchars($labourer['skillset']) . "</li>";
                        }
                    }
                    echo "</ul></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No jobs posted yet.</td></tr>";
            }
            ?>
        </table>
    </section>
    <br>
    <br>
    <section>
        <h2>Post a New Job</h2>
        <form id="postJobForm" method="POST">
            <label for="job_title">Job Title:</label>
            <input type="text" id="job_title" name="job_title" placeholder="Enter Job Title" required><br><br>

            <label for="job_description">Job Description:</label>
            <textarea id="job_description" name="job_description" placeholder="Enter Job Description" required></textarea><br><br>

            <label for="location">Location:</label>
            <input type="text" id="location" name="location" placeholder="Enter Location" required><br><br>

            <label for="workers_needed">Number of Workers Needed:</label>
            <input type="number" id="workers_needed" name="workers_needed" placeholder="Enter Number" required><br><br>

            <button type="submit" name="post_job">Post Job</button>
        </form>
    </section>
    
    <footer id="contact">
        <p>AGRISEEKS | Designed for a better agricultural future</p>
        <p>&copy; 2024 AGRISEEKS.com</p>
    </footer>

</body>
</html>

<?php
// Close the database connection
$stmt->close();
$conn->close();
?>
