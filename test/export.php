<?php
$dsn = 'mysql:host=localhost;dbname=contact_db';
$user = 'root';
$pass = '';
$conn = new PDO($dsn, $user, $pass);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=messages.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Name', 'Email', 'Subject', 'Message', 'Created At']); 

$search = isset($_POST['search']) ? trim($_POST['search']) : '';

if (!empty($search)) {
    $sql = "SELECT * FROM parking WHERE lane LIKE :search OR exit_time LIKE :search OR entry_time LIKE :search OR status LIKE :search OR cost LIKE :search" ;
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':search', '%' . $search . '%');
    $stmt->execute();
    $result = $stmt->fetchAll();
} else {
    $query = $conn->query("SELECT * FROM parking");
    $result = $query->fetchAll();
}

foreach ($result as $row) {
    fputcsv($output, [
        $row['lane'],
        $row['cost'],
        $row['entry_time'],
        $row['exit_time'],
        $row['status']
    ]);
}

fclose($output);

exit;
header('Location: index.php');
?>