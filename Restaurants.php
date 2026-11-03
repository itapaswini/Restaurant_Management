<!DOCTYPE html>
<html>
<head>
    <title>Restaurant List</title>
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
            margin-bottom: 10px;
            animation: fadeDown 0.8s ease;
        }
        .view-label {
            display: inline-block;
            background: #8E44AD;
            color: white;
            font-size: 13px;
            padding: 4px 14px;
            border-radius: 20px;
            margin-bottom: 20px;
            letter-spacing: 1px;
        }
        .section {
            margin-bottom: 60px;
        }
        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-30px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
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
        th, td {
            padding: 25px 35px;
        }
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
        .divider {
            border: none;
            border-top: 2px dashed #9C27B0;
            margin: 40px auto;
            width: 80%;
        }
        .tx-box {
            display: inline-block;
            background: #EDE7F6;
            border: 2px solid #8E44AD;
            border-radius: 12px;
            padding: 14px 30px;
            margin-bottom: 20px;
            font-size: 15px;
            color: #4A148C;
            animation: fadeUp 0.8s ease;
        }
    </style>
</head>
<body>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = mysqli_connect("127.0.0.1", "root", "", "restaurant", 3307);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

/* ─────────────────────────────────────────
   VIEW 1: All restaurants
───────────────────────────────────────── */
mysqli_query($conn, "
    CREATE OR REPLACE VIEW RestaurantView AS
    SELECT restaurant_id, name, location
    FROM restaurants
");

/* ─────────────────────────────────────────
   VIEW 2: Restaurants grouped by location
───────────────────────────────────────── */
mysqli_query($conn, "
    CREATE OR REPLACE VIEW LocationSummaryView AS
    SELECT location, COUNT(restaurant_id) AS total_restaurants
    FROM restaurants
    GROUP BY location
    ORDER BY total_restaurants DESC
");

/* ─────────────────────────────────────────
   TRANSACTION: safely insert a log entry
   (uses restaurant_logs table if it exists,
    silently skips if it doesn't)
───────────────────────────────────────── */
$txStatus = "Not executed";
mysqli_begin_transaction($conn);
try {
    $logTable = mysqli_query($conn, "SHOW TABLES LIKE 'restaurant_logs'");
    if (mysqli_num_rows($logTable) > 0) {
        mysqli_query($conn, "
            INSERT INTO restaurant_logs (log_message, log_time)
            VALUES ('Restaurant list viewed', NOW())
        ");
    }
    mysqli_commit($conn);
    $txStatus = "Committed successfully";
} catch (Exception $e) {
    mysqli_rollback($conn);
    $txStatus = "❌ Rolled back: " . $e->getMessage();
}

/* ══════════════════════════════════════════
   SECTION 1 — RestaurantView (all)
══════════════════════════════════════════ */
echo "<div class='section'>";
echo "<h2>🍴 Restaurant List</h2>";
echo "<div class='view-label'>📋 SOURCE: RestaurantView</div><br>";
echo "<div class='tx-box'>🔄 Transaction Status: $txStatus</div>";

$result = mysqli_query($conn, "SELECT * FROM RestaurantView");
if (!$result) die("Query Failed: " . mysqli_error($conn));

if (mysqli_num_rows($result) > 0) {
    echo "<table>
    <thead><tr>
        <th>Restaurant ID</th>
        <th>Name</th>
        <th>Location</th>
    </tr></thead><tbody>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
            <td>{$row['restaurant_id']}</td>
            <td>{$row['name']}</td>
            <td>{$row['location']}</td>
        </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<div class='no-data'>No restaurants found!</div>";
}
echo "</div>";

echo "<hr class='divider'>";

/* ══════════════════════════════════════════
   SECTION 2 — LocationSummaryView (grouped)
══════════════════════════════════════════ */
echo "<div class='section'>";
echo "<h2>📍 Restaurants by Location</h2>";
echo "<div class='view-label'>📋 SOURCE: LocationSummaryView</div>";

$result2 = mysqli_query($conn, "SELECT * FROM LocationSummaryView");
if (!$result2) die("Query Failed: " . mysqli_error($conn));

if (mysqli_num_rows($result2) > 0) {
    echo "<table>
    <thead><tr>
        <th>Location</th>
        <th>Total Restaurants</th>
    </tr></thead><tbody>";
    while ($row = mysqli_fetch_assoc($result2)) {
        echo "<tr>
            <td>{$row['location']}</td>
            <td>{$row['total_restaurants']}</td>
        </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<div class='no-data'>No location data found!</div>";
}
echo "</div>";

mysqli_close($conn);
?>

</body>
</html>