<!DOCTYPE html>
<html>
<head>
    <title>Restaurant</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #F3E5F5, #E1BEE7);
            font-family: 'Playfair Display', serif;
            text-align: center;
            padding: 40px;
        }
        h2 {
            font-size: 40px;
            color: #4A148C;
            margin-bottom: 30px;
            animation: fadeDown 0.8s ease;
        }
        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-30px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .tx-box {
            display: inline-block;
            background: #EDE7F6;
            border: 2px solid #8E44AD;
            border-radius: 12px;
            padding: 14px 30px;
            margin-bottom: 25px;
            font-size: 15px;
            color: #4A148C;
            animation: fadeUp 0.8s ease;
        }
        table {
            border-collapse: collapse;
            margin: auto;
            width: 85%;
            font-size: 19px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            border-radius: 12px;
            overflow: hidden;
            animation: fadeUp 0.8s ease;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        table:hover {
            transform: scale(1.03);
            box-shadow: 0 15px 35px rgba(0,0,0,0.25);
        }
        th, td { padding: 25px 30px; }
        th {
            background: linear-gradient(45deg, #8E44AD, #6A1B9A);
            color: white;
            font-size: 20px;
            letter-spacing: 1px;
        }
        tbody tr {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeUp 0.6s ease forwards;
        }
        tbody tr:nth-child(1) { animation-delay: 0.1s; }
        tbody tr:nth-child(2) { animation-delay: 0.2s; }
        tbody tr:nth-child(3) { animation-delay: 0.3s; }
        tbody tr:nth-child(4) { animation-delay: 0.4s; }
        tbody tr:nth-child(5) { animation-delay: 0.5s; }
        tbody tr:nth-child(odd)  { background-color: #F3E5F5; }
        tbody tr:nth-child(even) { background-color: #FFFFFF; }
        tbody tr:hover {
            background-color: #D1C4E9;
            transform: scale(1.01);
            transition: all 0.2s ease;
        }
        .no-data {
            margin-top: 30px;
            font-size: 20px;
            color: #4A148C;
            font-weight: bold;
            animation: fadeUp 0.8s ease;
        }
    </style>
</head>
<body>

<h2>👨‍🍳 Employees and Staff</h2>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = mysqli_connect("127.0.0.1", "root", "", "restaurant", 3307);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

/* ─────────────────────────────────────────
   TRANSACTION: safely fetch employees
───────────────────────────────────────── */
$txStatus = "Not executed";
$result   = null;

mysqli_begin_transaction($conn);
try {
    $result = mysqli_query($conn, "SELECT * FROM employees");
    if (!$result) {
        throw new Exception(mysqli_error($conn));
    }
    mysqli_commit($conn);
    $txStatus = "Committed successfully";
} catch (Exception $e) {
    mysqli_rollback($conn);
    $txStatus = "❌ Rolled back: " . $e->getMessage();
}
?>

<div class="tx-box">🔄 Transaction Status: <?= $txStatus ?></div>

<?php if ($result && mysqli_num_rows($result) > 0): ?>
<table>
    <thead>
        <tr>
            <th>Employee ID</th>
            <th>Restaurant ID</th>
            <th>Name</th>
            <th>Position</th>
            <th>Salary</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $row['employee_id'] ?></td>
            <td><?= $row['restaurant_id'] ?></td>
            <td><?= $row['name'] ?></td>
            <td><?= $row['position'] ?></td>
            <td>₹<?= $row['salary'] ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php else: ?>
    <div class="no-data">No employees found!</div>
<?php endif; ?>

<?php mysqli_close($conn); ?>

</body>
</html>