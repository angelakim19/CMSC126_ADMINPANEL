// fetch_updates.php
<?php
include 'db_connection.php';

$sql = "SELECT *, TIMESTAMPADD(HOUR, HOUR(max_duration), reservation_time) AS reservation_end_time FROM reading_area";
$result = $conn->query($sql);

$reservations = [];
while ($row = $result->fetch_assoc()) {
    $reservations[] = $row;
}

echo json_encode($reservations);

$conn->close();
?>
