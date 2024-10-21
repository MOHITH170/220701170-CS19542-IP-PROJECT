<?php 
include('config.php'); // Database connection
session_start();

// Check if the user is logged in
if (!isset($_SESSION['aadhar_number'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Fetch available jobs from the jobs table
$sql_jobs = "SELECT * FROM jobs";
$result_jobs = $conn->query($sql_jobs);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Labour Dashboard</title>
    <link rel="stylesheet" href="styles2.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<style>
        /* Flashy and modern styles */
        P12 {
            font-size:xx-large;
            color:green;
            text-align: center;
            font-family: 'Times New Roman', Times, serif;
        }
</style>
<header>
    <h1>AGRISEEKS</h1>
    <nav>
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</header>
<P12>Job Listings</P12> 
<section>
   
    <table>
        <tr>
            <th>Job Title</th>
            <th>Job Description</th>
            <th>Location</th>
            <th>Workers Needed</th>
            <th>Phone</th>
            <th>Maximum Salary</th>
            <th>Posted On</th>
            <th>Apply</th> <!-- Add "Apply" column -->
        </tr>
        <?php
        // Display jobs if any
        if ($result_jobs->num_rows > 0) {
            while ($row = $result_jobs->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['job_title']) . "</td>";
                echo "<td>" . htmlspecialchars($row['job_description']) . "</td>";
                echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                echo "<td>" . htmlspecialchars($row['workers_needed']) . "</td>";
                echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                echo "<td>" . htmlspecialchars($row['max_salary']) . "</td>";
                echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";                
                //the Apply Now 
                echo "<td><a href='A.php?job_id=" . $row['job_id'] . "'>Apply Now</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No jobs available at the moment.</td></tr>";
        }
        ?>
    </table>
</section>

<footer>
    <p>AGRISEEKS | Designed for a better agricultural future</p>
    <p>&copy; 2024 AGRISEEKS.com</p>
</footer>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
