<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "contact_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['lane_data'])) {
  $laneData = $_POST['lane_data'];
  $current_time = date("Y-m-d H:i:s");

  if (preg_match("/(L\d):(IN|OUT)/", $laneData, $matches)) {
    $lane = $matches[1];
    $status = $matches[2];

    if ($status == "IN") {
      $stmt = $conn->prepare("INSERT INTO parking (lane, status, entry_time) VALUES (?, 'parking', ?)");
      $stmt->bind_param("ss", $lane, $current_time);
      $stmt->execute();
    } else {
      $stmt = $conn->prepare("SELECT id, entry_time FROM parking WHERE lane=? AND status='parking' ORDER BY id DESC LIMIT 1");
      $stmt->bind_param("s", $lane);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $entry_time = new DateTime($row['entry_time']);
        $exit_time = new DateTime($current_time);
        $duration = $exit_time->getTimestamp() - $entry_time->getTimestamp(); // حساب الفارق بالثواني

        $cost = $duration * 1; // 1 جنيه لكل ثانية

        $update = $conn->prepare("UPDATE parking SET status='out', exit_time=?, cost=? WHERE id=?");
        $update->bind_param("sdi", $current_time, $cost, $id);
        $update->execute();
      }
    }
  }
}

$conn->close();
?>
