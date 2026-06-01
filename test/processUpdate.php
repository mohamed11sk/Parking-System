<?php
$dsn = 'mysql:host=localhost;dbname=contact_db';
$user = 'root';
$pass = '';
$conn = new PDO($dsn, $user, $pass);

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $subject = $_POST['Subject'];
        $message = $_POST['Massage'];

        // photo Update(ADD)
        $pass_database_insert = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $photo_name = $_FILES['image']['name'];
            $photo_tmp = $_FILES['image']['tmp_name'];
            $photo = rand(0, 100) . "_" . $photo_name;
            $imagePath = "uploads/";
            if (!dir($imagePath)) {
                mkdir($imagePath, 0777, true);
            }
            $pass_database_insert = $imagePath . $photo;
            move_uploaded_file($photo_tmp, $pass_database_insert);
        }
        $query = $conn->prepare("
            UPDATE `messages` 
            SET `name` = :name, `email` = :email, `subject` = :subject, `message` = :message, `image_path` = :image_path
            WHERE `id` = :id
        ");

        $query->bindParam(':name', $name);
        $query->bindParam(':email', $email);
        $query->bindParam(':subject', $subject);
        $query->bindParam(':message', $message);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->bindParam(':image_path', $pass_database_insert);

        $query->execute();

        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
