<?php
include('config.php');
session_start();

if (!isset($_SESSION['aadhar_number'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['job_id'])) {
    die("Error: Job ID not provided.");
}

$job_id = $_GET['job_id'];

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $labourers_needed = intval($_POST['labourers_needed']);
    $valid_labourers = [];
    $errors = [];

    for ($i = 1; $i <= $labourers_needed; $i++) {
        $aadhar = $_POST['aadhar_' . $i];
        $name = $_POST['name_' . $i];
        $phone = $_POST['phone_' . $i];
        $skillset = $_POST['skillset_' . $i];

        $sql_user = "SELECT * FROM users WHERE aadhar_number = ?";
        $stmt_user = $conn->prepare($sql_user);
        if (!$stmt_user) {
            die("Error preparing labourer verification query: (" . $conn->errno . ") " . $conn->error);
        }
        $stmt_user->bind_param("s", $aadhar);
        $stmt_user->execute();
        $user_result = $stmt_user->get_result();
        if ($user_result->num_rows > 0) {
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

    if (empty($errors)) {
        $labourer_data = json_encode($valid_labourers);
        $insert_sql = "INSERT INTO job_applications (job_id, labourer_data, created_at) VALUES (?, ?, NOW())";
        $insert_stmt = $conn->prepare($insert_sql);
        if (!$insert_stmt) {
            die("Error preparing application insertion query: (" . $conn->errno . ") " . $conn->error);
        }
        $insert_stmt->bind_param("is", $job_id, $labourer_data);
        if ($insert_stmt->execute()) {
            echo "<script>alert('Application and Bid submitted successfully!');</script>";
        } else {
            echo "<script>alert('Error submitting application and bid. Please try again.');</script>";
        }

        $labourer_aadhar = $_SESSION['aadhar_number'];
        $bid_amount = $_POST['bid_amount'];
        $insert_bid_sql = "INSERT INTO bids (job_id, labourer_aadhar, bid_amount) VALUES (?, ?, ?)";
        $insert_bid_stmt = $conn->prepare($insert_bid_sql);
        if (!$insert_bid_stmt) {
            die("Error preparing bid insertion query: (" . $conn->errno . ") " . $conn->error);
        }
        $insert_bid_stmt->bind_param("isd", $job_id, $labourer_aadhar, $bid_amount);
        $insert_bid_stmt->execute();
    } else {
        foreach ($errors as $error) {
            echo "<script>alert('$error');</script>";
        }
    }
}

$sql_bids = "SELECT * FROM bids WHERE job_id = ?";
$stmt_bids = $conn->prepare($sql_bids);
if (!$stmt_bids) {
    die("Error preparing bids query: (" . $conn->errno . ") " . $conn->error);
}
$stmt_bids->bind_param("i", $job_id);
$stmt_bids->execute();
$bids_result = $stmt_bids->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Labour Application and Bidding</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles2.css">
</head>
<body>
<header class="text-center">
    <h1>AGRISEEKS</h1>
    <nav>
        <ul class="nav justify-content-center">
            <li ><a  href="home.php">Home</a></li>
            <li ><a  href="logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<div class="container mt-5">
    <div class="job-details">
        <h2>Job Details</h2>
        <p><strong>Job Title:</strong> <?php echo htmlspecialchars($job_data['job_title']); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($job_data['job_description']); ?></p>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($job_data['location']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($job_data['phone']); ?></p>
    </div>

    <div class="form-section">
        <h3>Apply for the Job and Place a Bid</h3>
        <form method="POST">
            <div class="form-group">
                <label for="labourers_needed">Number of Labourers Applying:</label>
                <input type="number" class="form-control" id="labourers_needed" name="labourers_needed" min="1" required>
            </div>
            <div id="labourers_section"></div>
            <div class="form-group">
                <label for="bid_amount">Bid Amount:</label>
                <input type="number" id="labourers_needed" name="labourers_needed" min="0.01" required><br><br>
        <button type="submit" name="apply">Submit Application and Bid</button>
    </form>
    </div>
<section>
    <h3>Current Bids</h3>
    <table border="5">
        <tr>
            <th>Bidder</th>
            <th>Bid Amount</th>
            <th>Bid Time</th>
        </tr>
        <?php
        if ($bids_result->num_rows > 0) {
            while ($bid = $bids_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($bid['username']) . "</td>";
                echo "<td>" . htmlspecialchars($bid['bid_amount']) . "</td>";
                echo "<td>" . htmlspecialchars($bid['bid_time']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No bids placed yet.</td></tr>";
        }
        ?>
    </table>
</section>
<script>
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
<footer>
    <p>AGRISEEKS | Designed for a better agricultural future</p>
    <p>&copy; 2024 AGRISEEKS.com</p>
</footer>
</body>
</html>

<?php
$stmt_job->close();
$conn->close();
?>
