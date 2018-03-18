<?php

include_once 'db.php';
include_once "func.php";
include_once 'nav.php';

$head = getHeader("Music Lover","materializecss jquery materializejs");
$nav = getNav();


if (isset($_GET["action"])){
  if ($_GET["action"]=='logout')
    logoutUser();
    redirect("user.php");
}

if (isset($_GET["email"])){
  //user login attempt
  $email = $_GET["email"];
  $pass = $_GET["pass"];
  if (loginUser($email,$pass)){
    redirect("user.php");
  }
}


function printUserDetails(){
  $username = getUsername($_SESSION["email"]);
  $html='<ul class="collection">
          <li class="collection-item">'.$username.'</li>
          <li class="collection-item">'.$_SESSION["email"].'</li>
          </ul>';
  echo $html;
}


function showLoginForm(){
  $html='<div class="row">
      <form class="col s12 m6 l6" style="float:none; margin:30px auto;" action="user.php" method="get">
        <div class="row">
          <div class="input-field row">
            <input name="email" id="email" type="email" class="validate">
            <label for="email">Email</label>
          </div>
          <div class="input-field row">
            <input id="pass" name="pass" type="password" class="validate">
            <label for="pass">Password</label>
          </div>
          <button type="submit" name="button" class="btn btn-large">Login</button>
        </div>
      </form>
    </div>
';
  echo $html;
}

 ?>

 <!DOCTYPE html>
 <html>
   <?php echo $head; ?>
   <body>
     <?php echo $nav; ?>

     <div class="container">
       <?php
          if (isUserLogged()){
            echo '<center><h3>Profile</h3></center>';
            printUserDetails();
          }
          else{
            echo '<center><h3>Login</h3></center>';
            showLoginForm();
          }

        ?>
     </div>

   </body>
 </html>
