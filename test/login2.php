<?php
session_start();
session_unset();
session_destroy();
$dsn = 'mysql:host=localhost;dbname=contact_db';
$user = 'root';
$pass = '';
$conn = new PDO($dsn, $user, $pass);
$qery = $conn->query("SELECT * FROM `login`");
$result = $qery->fetchAll();
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["names"]) and isset($_POST["passs"])) {
        foreach ($result as $row) {
            if ($row['name'] == $_POST['names'] and $row['password'] == $_POST['passs']) {
                echo "Login Successful";
                setcookie("name_cookie", $_POST["names"]);
                setcookie("pass_cookie", $_POST["passs"]);
                header("Location:process_form.php");
                if (($_POST["names"]) == "pro" and ($_POST["passs"]) == 123) {
                    session_start();
                    $_SESSION["name_session"] = $_POST["names"];
                    $_SESSION["pass_session"] = $_POST["passs"];

                    exit;
                }
                exit;
            } else {
                echo "Invalid username or password";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Login </title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        form {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 300px;
            max-width: 100%;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 15px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <form action="" method="post">
        <label for="names">Username:</label>
        <input type="text" id="names" name="names">
        <br>
        <label for="passs">Password:</label>
        <input type="password" id="passs" name="passs">
        <br>
        <input type="submit" value="Submit">
    </form>
</body>

</html>