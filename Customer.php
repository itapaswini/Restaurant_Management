<!DOCTYPE html>
<html>
<head>
    <title>Restaurant - Customers</title>
     <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap" rel="stylesheet">
</head>
<body>

<h2>Add Customer</h2>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = mysqli_connect("127.0.0.1", "root", "", "restaurant", 3307);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $email = $_POST['email'];

    $sql = "INSERT INTO customers (name, email) VALUES ('$name', '$email')";
    
    if(mysqli_query($conn, $sql)){
        echo "Customer added successfully.<br><br>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<form method="post">
    Name: <input type="text" name="name" required><br><br>
    Email: <input type="email" name="email" required><br><br>
    <input type="submit" name="submit" value="Add Customer">
</form>

<hr>

<h2>Customers List</h2>

<?php
$result = mysqli_query($conn, "SELECT * FROM customers");

echo "<table border='1'>
<tr>
<th>Customer ID</th>
<th>Name</th>
<th>Email</th>
</tr>";

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
    <td>{$row['customer_id']}</td>
    <td>{$row['name']}</td>
    <td>{$row['email']}</td>
    </tr>";
}

echo "</table>";

mysqli_close($conn);
?>

<style>
body {
    background: linear-gradient(135deg, #F3E5F5, #E1BEE7);
    font-family: 'Playfair Display', serif;
    text-align: center;
    padding: 40px;
}

/* Heading */
h2 {
    font-size: 40px;
    color: #4A148C;
    margin-bottom: 30px;
    animation: fadeDown 0.8s ease;
}

/* Animations */
@keyframes fadeDown {
    from { opacity: 0; transform: translateY(-30px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Form */
form {
    margin-bottom: 40px;
    animation: fadeUp 0.6s ease;
}

/* Inputs */
input[type="text"],
input[type="email"] {
    padding: 14px 18px;
    font-size: 17px;
    border: 2px solid #7B9ACC;
    border-radius: 6px;
    width: 260px;
    transition: 0.3s;
}

input[type="text"]:focus,
input[type="email"]:focus {
    border-color: #4A148C;
    box-shadow: 0 0 10px rgba(74,20,140,0.2);
}

/* Button */
input[type="submit"] {
    padding: 14px 25px;
    background: linear-gradient(45deg, #8E44AD, #6A1B9A);
    color: white;
    font-size: 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.3s;
}

input[type="submit"]:hover {
    transform: translateY(-2px);
    background: linear-gradient(45deg, #6A1B9A, #4A148C);
}

/* Table */
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

/* Hover zoom */
table:hover {
    transform: scale(1.03);
    box-shadow: 0 15px 35px rgba(0,0,0,0.25);
}

/* Cells */
th, td {
    padding: 25px 30px;
}

/* Header */
th {
    background: linear-gradient(45deg, #8E44AD, #6A1B9A);
    color: white;
    font-size: 20px;
    letter-spacing: 1px;
}

/* Row animation */
tbody tr {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeUp 0.6s ease forwards;
}

/* Stagger effect */
tbody tr:nth-child(1) { animation-delay: 0.1s; }
tbody tr:nth-child(2) { animation-delay: 0.2s; }
tbody tr:nth-child(3) { animation-delay: 0.3s; }
tbody tr:nth-child(4) { animation-delay: 0.4s; }
tbody tr:nth-child(5) { animation-delay: 0.5s; }

/* Row colors */
tbody tr:nth-child(odd) {
    background-color: #F3E5F5;
}

tbody tr:nth-child(even) {
    background-color: #FFFFFF;
}

/* Hover */
tbody tr:hover {
    background-color: #D1C4E9;
    transform: scale(1.01);
}

/* Messages */
.success-message {
    color: #4A148C;
    font-weight: bold;
    margin-bottom: 20px;
    animation: fadeUp 0.5s ease;
}

.error-message {
    color: #c62828;
    font-weight: bold;
    margin-bottom: 20px;
    animation: fadeUp 0.5s ease;
}

/* No data */
.no-data {
    margin-top: 30px;
    font-size: 20px;
    color: #4A148C;
    font-weight: bold;
    animation: fadeUp 0.8s ease;
}
</style>
</body>
</html>