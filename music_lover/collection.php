
<?php
  include_once 'db.php';
  include_once "func.php";
  include_once 'nav.php';

  $head = getHeader("Groove Jelly Fish","materializecss jquery materializejs");
  $nav = getNav();
?>


<!DOCTYPE html>
<html>
  <?php echo $head;?>
<body>
  <?php echo $nav; ?>
<div class='container'>
  <div class="row" style="margin-top:50px;">
    Collections
  </div>
</div>
</body>
</html>
