<?php
session_start();
session_unset();
session_destroy();
$dsn = 'mysql:host=localhost;dbname=contact_db';
$user = 'root';
$pass = '';
$error_message='';
$conn = new PDO($dsn, $user, $pass);
$qery = $conn->query("SELECT * FROM `login`");
$result = $qery->fetchAll();
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["names"]) and isset($_POST["passs"])) {
        foreach ($result as $row) {
            if ($row['name'] == $_POST['names'] and $row['password'] == $_POST['passs']) {
                setcookie("name_cookie", $_POST["names"]);
                setcookie("pass_cookie", $_POST["passs"]);
                header("Location:Show_search-data.php");
                if ((($_POST["names"]) == "admin"  and ($_POST["passs"]) == 123 ) or ($_POST['names']=="marwa"))  {
                    session_start();
                    $_SESSION["name_session"] = $_POST["names"];
                    $_SESSION["pass_session"] = $_POST["passs"];

                    exit;
                }
                exit;
            } else {
                $error_message= "Invalid username or password";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">

    <title>Document</title>
</head>
<body>
<form action=""  method="post">
<div class="ring">
  <i style="--clr:#00ff0a;"></i>
  <i style="--clr:#ff0057;"></i>
  <i style="--clr:#fffd44;"></i>
  <div class="login">
    <h2>Login</h2>
    <?php if (!empty($error_message)): ?>
            <div ><span class="error"><?php echo htmlspecialchars($error_message); ?></span></div>
        <?php endif; ?>
    <div class="inputBx">
      <input type="text" placeholder="Username" name="names">
    </div>
    <div class="inputBx">
      <input type="password" placeholder="Password" name="passs">
    </div>
    <div class="inputBx">
      <input type="submit" value="Sign in">
    </div>
    <div class="links">
      <!-- <a href="#">Forget Password</a> -->
      <a href="#">Signup</a>
    </div>
  </div>
</div>
</form>
    
</body>
</html>