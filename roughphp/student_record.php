<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

// Create connection
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$registration_id = $_GET['registration_id'] ?? '';

$student = [
    'name' => '',
    'registration_id' => $registration_id,
    'CGPA' => '',
    'Major' => '',
    'Year' => '',
    'Courses_enrolled' => '',
    'email' => '',
    'Institute' => '',
    'Program' => '',
    'Batch' => '',
    'Degree' => '',
    'semester_section' => '',
    'Fees' => '75000',
    'clubs' => '',
    'attendance' => ''
];

$tuition_payments = [];

if (!empty($registration_id)) {
    $stmt = $mysqli->prepare("SELECT * FROM student_record WHERE registration_id = ?");
    $stmt->bind_param("s", $registration_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
    }
    $stmt->close();

    $stmt = $mysqli->prepare("SELECT payment_date, '75000' AS amount FROM student_record WHERE registration_id = ?");
    $stmt->bind_param("s", $registration_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $tuition_payments[] = $row;
    }
    $stmt->close();
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e0f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .navbar {
            background-color: #01579b;
            overflow: hidden;
            width: 100%;
            position: fixed;
            top: 0;
        }
        .navbar a {
            float: left;
            display: block;
            color: #e0f7fa;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        .navbar a:hover {
            background-color: #00c853;
            color: white;
        }
        .container {
            display: flex;
            margin-top: 60px;
            width: 80%;
            gap: 20px;
        }
        .content, .tuition-fee, .club-activities, .attendance {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .content {
            width: 50%;
        }
        .content h2, .tuition-fee h2, .club-activities h2, .attendance h2 {
            color: #01579b;
        }
        .content p, .tuition-fee p, .club-activities p, .attendance p {
            color: #004d40;
        }
        .right-section {
            width: calc(50% - 20px);
            display: flex;
            flex-direction: column;
            gap: 20px;
            position: relative;
        }
        .tuition-fee table {
            width: 100%;
            border-collapse: collapse;
        }
        .tuition-fee th, .tuition-fee td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .tuition-fee th {
            background-color: #01579b;
            color: white;
        }
        .attendance canvas {
            width: 60% !important;
            height: auto !important;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="navbar">
        <a href="#course-details">Course Details</a>
        <a href="#events">Events</a>
        <a href="#contact">Contact</a>
    </div>
    
    <div class="container">
        <div class="content">
            <h2>Student Details</h2>
            <p><strong>Name:</strong> <?= htmlspecialchars($student['name']) ?></p>
            <p><strong>Student ID:</strong> <?= htmlspecialchars($student['registration_id']) ?></p>
            <p><strong>CGPA:</strong> <?= htmlspecialchars($student['CGPA']) ?></p>
            <p><strong>Major:</strong> <?= htmlspecialchars($student['Major']) ?></p>
            <p><strong>Year:</strong> <?= htmlspecialchars($student['Year']) ?></p>
            <p><strong>Courses Enrolled:</strong> <?= htmlspecialchars($student['Courses_enrolled']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($student['email']) ?> (Visible to other course participants)</p>
            <p><strong>Timezone:</strong> Asia/Kolkata</p>
            <p><strong>Institute:</strong> <?= htmlspecialchars($student['Institute']) ?></p>
            <p><strong>Program (Branch):</strong> <?= htmlspecialchars($student['Program']) ?></p>
            <p><strong>Batch:</strong> <?= htmlspecialchars($student['Batch']) ?></p>
            <p><strong>Degree:</strong> <?= htmlspecialchars($student['Degree']) ?></p>
            <p><strong>Semester/Section:</strong> <?= htmlspecialchars($student['semester_section']) ?></p>
            <p><strong>Fees:</strong> <?= htmlspecialchars($student['Fees']) ?></p>
            <p><strong>Clubs:</strong> <?= htmlspecialchars($student['clubs']) ?></p>
        </div>
        <div class="right-section">
            <div class="tuition-fee">
                <h2>Tuition Fee Details</h2>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Payment Date</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody id="tuition-payments">
                        <?php foreach ($tuition_payments as $index => $payment): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($payment['payment_date']) ?></td>
                            <td>75000</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="club-activities">
                <h2>Club Activities</h2>
                <p><strong>Clubs:</strong> <?= htmlspecialchars($student['clubs']) ?></p>
            </div>
            <div class="attendance">
                <h2>Attendance</h2>
                <canvas id="attendanceChart"></canvas>
                <script>
                    const attendance = <?= $student['attendance'] ?>;
                    const attendanceData = {
                        labels: ['Present', 'Absent'],
                        datasets: [{
                            data: [attendance, 100 - attendance],
                            backgroundColor: ['#1E3A8A', '#93C5FD'],
                        }]
                    };

                    const config = {
                        type: 'pie',
                        data: attendanceData,
                    };

                    const attendanceChart = new Chart(
                        document.getElementById('attendanceChart'),
                        config
                    );
                </script>
            </div>
        </div>
    </div>
</body>
</html>
