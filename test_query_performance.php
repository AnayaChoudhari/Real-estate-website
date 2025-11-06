<?php
include 'components/connect.php';

echo "<h2>Query Performance Test</h2>";
echo "<p>Testing search query performance with and without optimization...</p>";

// Test query WITHOUT optimization (using SELECT *)
$start_time = microtime(true);

$query_unoptimized = "SELECT * FROM `property`
                      WHERE address LIKE '%mumbai%'
                      OR property_name LIKE '%mumbai%'
                      ORDER BY date DESC";
$stmt = $conn->prepare($query_unoptimized);
$stmt->execute();
$results = $stmt->fetchAll();

$end_time = microtime(true);
$unoptimized_time = ($end_time - $start_time) * 1000; // Convert to milliseconds

echo "<p><strong>Unoptimized Query Time:</strong> " . round($unoptimized_time, 2) . " ms</p>";

// Test query WITH optimization (selecting only needed columns + using indexes)
$start_time = microtime(true);

$query_optimized = "SELECT id, property_name, address, price, type, offer, bhk,
                    status, furnished, carpet, image_01, user_id, date
                    FROM `property`
                    WHERE address LIKE '%mumbai%'
                    OR property_name LIKE '%mumbai%'
                    ORDER BY date DESC";
$stmt = $conn->prepare($query_optimized);
$stmt->execute();
$results = $stmt->fetchAll();

$end_time = microtime(true);
$optimized_time = ($end_time - $start_time) * 1000;

echo "<p><strong>Optimized Query Time:</strong> " . round($optimized_time, 2) . " ms</p>";

$improvement = $unoptimized_time - $optimized_time;
$percentage = ($improvement / $unoptimized_time) * 100;

echo "<p><strong>Improvement:</strong> " . round($improvement, 2) . " ms (" . round($percentage, 1) . "% faster)</p>";
echo "<p><strong>Results Found:</strong> " . count($results) . " properties</p>";

// Show index usage
echo "<h3>Active Indexes on 'property' table:</h3>";
$show_indexes = $conn->query("SHOW INDEX FROM property");
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Column</th><th>Index Name</th><th>Type</th></tr>";
while($index = $show_indexes->fetch(PDO::FETCH_ASSOC)){
    echo "<tr>";
    echo "<td>" . $index['Column_name'] . "</td>";
    echo "<td>" . $index['Key_name'] . "</td>";
    echo "<td>" . $index['Index_type'] . "</td>";
    echo "</tr>";
}
echo "</table>";
?>
