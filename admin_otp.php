<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['otp'])) {
        $otp = $data['otp'];
        $check = mysqli_query($conn, "SELECT count(id) FROM `adminotp`") or die('query failed');
        $row = mysqli_fetch_assoc($check);
        if ($row['count(id)'] == 0) {
            $insert_query = "INSERT INTO `adminotp`(otp) VALUES('$otp')";
            if (mysqli_query($conn, $insert_query)) {
                echo json_encode(['message' => 'OTP inserted successfully']);
            } else {
                echo json_encode(['message' => 'Failed to insert OTP']);
            }
        }else{
            $update_query = "UPDATE `adminotp` SET otp = '$otp' WHERE name = 'otp'";
            if (mysqli_query($conn, $update_query)) {
                echo json_encode(['message' => 'OTP updated successfully']);
            } else {
                echo json_encode(['message' => 'Failed to update OTP']);
            }
        }
    } else {
        echo json_encode(['message' => 'Invalid request']);
    }
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Users</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="users">

   <h1 class="title"> User accounts </h1>

   <div class="box-container">
      <?php
        $result = mysqli_query($conn, "SELECT otp FROM `adminotp` where name = 'otp'") or die('query failed');
        $otp = mysqli_fetch_assoc($result)
      ?>
      <div class="box" style="position:relative">
        <p> Current Admin Registration OTP is <br><b><span id="otp"><?php echo $otp['otp']??'NOT SET'; ?></span></b> </p>
        <button class="btn" onclick="changeOTP()">Change OTP</button>
        <div id="changeSuccess" style="position: absolute; bottom:-35%; left:50%; transform:translateX(-50%); display:none;"></div>
        

      </div>
   </div>

</section>









<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>
<script>
    async function changeOTP(){
        const change = document.getElementById('changeSuccess');
        document.getElementById('otp').innerHTML = Math.floor(10000000000 + Math.random() * 90000000000);
        const response = await fetch('admin_otp.php', {
            method: 'POST',
            headers: {
            'Content-Type': 'application/json'
            },
            body: JSON.stringify({ otp: document.getElementById('otp').innerHTML })
        });
        if (response.ok) {
            change.style.display = 'block';
            change.style.color = 'green';
            change.innerHTML = 'OTP updated successfully';
            change.style.backgroundColor = '#d4edda';
            change.style.border = '1px solid #c3e6cb';
            change.style.padding = '10px';
            change.style.borderRadius = '5px';
            change.style.fontSize = '12px';
            setTimeout(() => {
                change.style.display = 'none';
            }, 1000);
        } else {
            change.style.display = 'block';
            change.style.color = 'red';
            change.innerHTML = 'Failed to update OTP';
            change.style.backgroundColor = '#fff3cd';
            change.style.border = '1px solid #ffeeba';
            change.style.padding = '10px';
            change.style.borderRadius = '5px';
            change.style.fontSize = '12px';
            setTimeout(() => {
                change.style.display = 'none';
            }, 1000);

        }
    }
</script>

</body>
</html>