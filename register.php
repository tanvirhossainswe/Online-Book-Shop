<?php

include 'config.php';

if(isset($_POST['submit'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));
   $cpass = mysqli_real_escape_string($conn, md5($_POST['cpassword']));
   $user_type = $_POST['user_type'];

   $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email' AND password = '$pass'") or die('query failed');
   $otp = mysqli_query($conn, "SELECT * FROM `adminotp` WHERE name = 'otp'") or die('query failed');
   if(mysqli_num_rows($otp) > 0){
      $row = mysqli_fetch_assoc($otp);
   }else{
      $row = ['otp'=>'admin123'];
   }
   if(mysqli_num_rows($select_users) > 0){
      $message[] = 'user already exist!';
   }else{
      if($pass != $cpass){
         $message[] = 'confirm password not matched!';
      }else if($user_type == 'admin' && $_POST['adminOtp'] != $row['otp']){
         $message[] = 'Admin OTP is required to register as Admin!';
      }else{
         mysqli_query($conn, "INSERT INTO `users`(name, email, password, user_type) VALUES('$name', '$email', '$cpass', '$user_type')") or die('query failed');
         $message[] = 'registered successfully!';
         header('location:login.php');
      }
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>



<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>
   
<div class="form-container">

   <form action="" method="post">
      <h3>Register Now</h3>
      <input type="text" name="name" placeholder="Enter your name" required class="box">
      <input type="email" name="email" placeholder="Enter your email" required class="box">
      <input type="password" name="password" placeholder="Enter your password" required class="box">
      <input type="password" name="cpassword" placeholder="Confirm your password" required class="box">
      <select name="user_type" class="box">
         <option value="user" selected >User</option>
         <option value="admin">Admin</option>
      </select>
      <input type="text" name="adminOtp" placeholder="Enter Admin OTP [Default = admin123]" class="box" style="display:none">
      <input type="submit" name="submit" value="register now" class="btn">
      <p>Already have an account? <a href="login.php">Login now</a></p>
   </form>

</div>

</body>
</html>
<script>
   if(document.querySelector('select').value == 'admin'){
      document.querySelector('input[name="adminOtp"]').style.display = 'block';
      document.querySelector('input[name="adminOtp"]').setAttribute('required', 'required');
   }
   document.querySelector('select').addEventListener('change', function(){
      if(this.value == 'admin'){
         document.querySelector('input[name="adminOtp"]').style.display = 'block';
         document.querySelector('input[name="adminOtp"]').setAttribute('required', 'required');
      }else{
         document.querySelector('input[name="adminOtp"]').style.display = 'none';
         document.querySelector('input[name="adminOtp"]').removeAttribute('required');
      }
   });
</script>