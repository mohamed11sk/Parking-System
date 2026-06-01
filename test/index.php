<?php
session_start();

function highlight($text, $search) {
    return $search ? str_ireplace($search, '<mark>' . $search . '</mark>', htmlspecialchars($text)) : htmlspecialchars($text);
}
$dsn = 'mysql:host=localhost;dbname=contact_db';
$user = 'root';
$pass = '';
$conn = new PDO($dsn, $user, $pass);

$result = [];
$searchMessage = '';
$search = (isset($_POST['search']) ? trim($_POST['search']) : ''  );

    if (!empty($search)) {
        $sql = "SELECT * FROM parking WHERE lane LIKE :search OR exit_time LIKE :search OR entry_time LIKE :search OR status LIKE :search OR cost LIKE :search" ;
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':search', '%' . $search . '%');
        $stmt->execute();
        $result = $stmt->fetchAll();
        if (empty($result)) {
            $searchMessage = 'No results found for "' . htmlspecialchars($search) . '"';
        }
    }
   if(empty($search) or !isset($_POST['search'])){
    $query = $conn->query("SELECT * FROM parking");
    $result = $query->fetchAll();
  }

if(isset($_POST['ajax']) && $_POST['ajax'] === '1'){
    if (isset($_SESSION["pass_session"]) && isset($_SESSION["name_session"])) {
            if (!empty($searchMessage)) {
                echo "<tr><td colspan='8'>$searchMessage</td></tr>";
            } else {
                foreach ($result as $key) {
                    echo "<tr>";
                  
               echo "<td>" . highlight($key['lane'], $search) . "</td>";
               echo "<td>" . highlight($key['cost'], $search) . " LE </td>";
               echo "<td>" . highlight($key['entry_time'], $search) . "</td>";
               echo "<td>" . highlight($key['exit_time'], $search) . "</td>";
               echo "<td>" . highlight($key['status'], $search) . "</td>";
               
               echo "<td> 
               <a href='#' onclick='confirmDelete(" . htmlspecialchars($key['id']) . ")' class='button delete'>Delete</a>
           </td>";
                }
            }
        } else {
            echo '<tr><td colspan="8">You are not admin , Please login with <span class="MesssageLogin">(marwa OR admin)</span> <button><a href="login.php" target="_blank">Login</a></button></td></tr>';
        }
        exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<style>

</style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>View Messages</title>
    
</head>
<body>
    <h1 id="typing" ></h1>
    <?php
if (isset(  $_SESSION["name_session"] )) {
    echo " <a href='login.php'><button  class='btn'>logout</button></a>";
} else {
    echo " <a href='login.php'><button class='btn' >login</button></a>";
}
if (isset($_SESSION["name_session"] )) {
    printf('<h3 >Hello, %s</h3>', $_COOKIE["name_cookie"]);
}
?>
    <form action="" method="post">
        <input type="text" placeholder="Search..." name="search" id='searchinput'>
        <input type="submit" value="Search">
    </form>

       <!-- export -->
    <form action="export.php" method="post" style="text-align: center; margin-top: 10px;">
        <?php
    if (isset(  $_SESSION["name_session"] )) {
    echo '<input type="submit" value=" EXPORT DATA 📤  ">';  
} ?>
    
</form>

    <table>
        <tr>    
            <th>Line</th>
            <th>Cost</th>
            <th>Time in</th>
            <th>Time out</th>
            <th>STATUS</th>
            <th>Control</th>
        </tr>
        <tbody id='tableBody'>
        <?php
        if (isset($_SESSION["pass_session"]) && isset($_SESSION["name_session"])) {
            if (!empty($searchMessage)) {
                echo "<tr><td colspan='8'>$searchMessage</td></tr>";
            } else {
                foreach ($result as $key) {
                    echo "<tr>";  
               echo "<td>" . highlight($key['lane'], $search) . "</td>";
               echo "<td>" . highlight($key['cost'], $search) . " lE</td>";
               echo "<td>" . highlight($key['entry_time'], $search) . "</td>";
               echo "<td>" . highlight($key['exit_time'], $search) . "</td>";
               echo "<td>" . highlight($key['status'], $search) . "</td>";
               echo "<td> 
               <a href='#' onclick='confirmDelete(" . htmlspecialchars($key['id']) . ")' class='button delete'>Delete</a>
           </td>";
                }
            }
        } else {
            echo '<tr><td colspan="8">You are not admin #pro <button><a href="login.php" target="_blank">Login</a></button></td></tr>';
        }
        ?>         
        </tbody>
    </table>
    <script>
 function fetchData() { //---->ajax
    $.ajax({
        url: '', 
        method: 'POST',
        data: {
            ajax: '1',
            search: $('#searchinput').val() 
        },
        success: function (data) {
            $('#tableBody').html(data);
        }
    });
}
fetchData();
setInterval(fetchData, 500);

       
    $(document).ready(function(){  //----> animation
        $('table').hide().fadeIn(1000);
        $('tr').each(function(i) {
            $(this).delay(i*100).fadeIn(300);
        });
 
    });

    const text = "Parking System"; //----->Behavior write
        let index = 0;    
        function typeWriter() {
            if (index < text.length) {
                document.getElementById("typing").textContent += text.charAt(index);
                index++;
                setTimeout(typeWriter, 200); 
            }else{
                setTimeout(() => {
                    document.getElementById("typing").textContent ='';
                    index=0;
                    typeWriter() 
                }, 200);
            }
        }     
        typeWriter()     

        function confirmDelete(id) { ///----> Delete record  
    Swal.fire({
        title: 'Confirm Delete',
        text: "Are you sure you want to delete this message?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ff00aa',
        cancelButtonColor: '#00c3ff',
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        background: '#1a1a2e',
        color: '#fff'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'delete_message.php?id=' + id;
        }
    });
}

</script>
</body>
</html>
