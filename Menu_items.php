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
            width: 90%;
            font-size: 18px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            border-radius: 12px;
            overflow: hidden;
            animation: fadeUp 0.8s ease;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        table:hover {
            transform: scale(1.02);
            box-shadow: 0 15px 35px rgba(0,0,0,0.25);
        }
        th, td {
            padding: 20px 25px;
        }
        th {
            background: linear-gradient(45deg, #8E44AD, #6A1B9A);
            color: white;
            font-size: 18px;
        }
        tr {
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
        }
        .divider {
            border: none;
            border-top: 2px dashed #9C27B0;
            margin: 40px auto;
            width: 80%;
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

/* ─── VIEW 1: Full menu ─── */
mysqli_query($conn, "
    CREATE OR REPLACE VIEW MenuView AS
    SELECT item_id, restaurant_id, category_id, name, price, available_quantity
    FROM menu_items
");

/* ─── VIEW 2: Only available items, sorted cheapest first ─── */
mysqli_query($conn, "
    CREATE OR REPLACE VIEW AvailableMenuView AS
    SELECT item_id, restaurant_id, category_id, name, price, available_quantity
    FROM menu_items
    WHERE available_quantity > 0
    ORDER BY price ASC
");

/* ─── VIEW 3: Category summary — shows aggregation via view ─── */
mysqli_query($conn, "
    CREATE OR REPLACE VIEW CategorySummaryView AS
    SELECT
        category_id,
        COUNT(item_id)          AS total_items,
        SUM(available_quantity) AS total_stock,
        MIN(price)              AS min_price,
        MAX(price)              AS max_price
    FROM menu_items
    GROUP BY category_id
");

/* ─── TRANSACTION (kept from original) ─── */
mysqli_begin_transaction($conn);
try {
    mysqli_commit($conn);
} catch (Exception $e) {
    mysqli_rollback($conn);
}

/* ════════════════════════════════════════
   SECTION 1 — MenuView (all items)
════════════════════════════════════════ */
echo "<div class='section'>";
echo "<h2>🍽️ All Menu Items</h2>";
echo "<div class='view-label'>📋 SOURCE: MenuView</div>";

$result = mysqli_query($conn, "SELECT * FROM MenuView");
echo "<table>
<thead><tr>
  <th>Item ID</th><th>Restaurant ID</th><th>Category ID</th>
  <th>Name</th><th>Price (₹)</th><th>Available Qty</th>
</tr></thead><tbody>";

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
        <td>{$row['item_id']}</td>
        <td>{$row['restaurant_id']}</td>
        <td>{$row['category_id']}</td>
        <td>{$row['name']}</td>
        <td>₹{$row['price']}</td>
        <td>{$row['available_quantity']}</td>
    </tr>";
}
echo "</tbody></table></div>";

echo "<hr class='divider'>";

/* ════════════════════════════════════════
   SECTION 2 — AvailableMenuView (filtered)
════════════════════════════════════════ */
echo "<div class='section'>";
echo "<h2>✅ Available Items (Cheapest First)</h2>";
echo "<div class='view-label'>📋 SOURCE: AvailableMenuView</div>";

$result2 = mysqli_query($conn, "SELECT * FROM AvailableMenuView");
echo "<table>
<thead><tr>
  <th>Item ID</th><th>Restaurant ID</th><th>Category ID</th>
  <th>Name</th><th>Price (₹)</th><th>Available Qty</th>
</tr></thead><tbody>";

while ($row = mysqli_fetch_assoc($result2)) {
    echo "<tr>
        <td>{$row['item_id']}</td>
        <td>{$row['restaurant_id']}</td>
        <td>{$row['category_id']}</td>
        <td>{$row['name']}</td>
        <td>₹{$row['price']}</td>
        <td>{$row['available_quantity']}</td>
    </tr>";
}
echo "</tbody></table></div>";

echo "<hr class='divider'>";

/* ════════════════════════════════════════
   SECTION 3 — CategorySummaryView (aggregated)
════════════════════════════════════════ */
echo "<div class='section'>";
echo "<h2>📊 Category Summary</h2>";
echo "<div class='view-label'>📋 SOURCE: CategorySummaryView</div>";

$result3 = mysqli_query($conn, "SELECT * FROM CategorySummaryView");
echo "<table>
<thead><tr>
  <th>Category ID</th><th>Total Items</th><th>Total Stock</th>
  <th>Min Price (₹)</th><th>Max Price (₹)</th>
</tr></thead><tbody>";

while ($row = mysqli_fetch_assoc($result3)) {
    echo "<tr>
        <td>{$row['category_id']}</td>
        <td>{$row['total_items']}</td>
        <td>{$row['total_stock']}</td>
        <td>₹{$row['min_price']}</td>
        <td>₹{$row['max_price']}</td>
    </tr>";
}
echo "</tbody></table></div>";

mysqli_close($conn);
?>

</body>
</html>