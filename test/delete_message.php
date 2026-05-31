<?php
session_start();
$dsn = 'mysql:host=localhost;dbname=contact_db';
$user = 'root';
$pass = '';
$conn = new PDO($dsn, $user, $pass);

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if (isset($_SESSION["pass_session"]) && isset($_SESSION["name_session"])) {
        $sql = "DELETE FROM parking WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        header("Location: Show_search-data.php");
        exit();
    }
}
