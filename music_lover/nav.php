<?php


function getNav(){
  $html = '<!-- Dropdown Structure -->
<ul id="dropdown1" class="dropdown-content">
  <li><a href="user.php">Account</a></li>
  <li><a href="playlist.php">Playlists</a></li>
  <li class="divider"></li>
  <li><a href="user.php?action=logout">Logout</a></li>
</ul>
<nav style="background:#2c3e50;">
  <div class="nav-wrapper container">
    <a href="index.php" class="brand-logo">Music Lover</a>
    <ul class="right hide-on-med-and-down">
      <li><a href="index.php">Home</a></li>
      <li><a href="playlist.php">Playlists</a></li>
      <!-- Dropdown Trigger -->';

      if (!isUserLogged())
        $html.='<li><a href="user.php">Login</a></li>';
      else
        $html.='<li><a class="dropdown-button" href="#!" data-activates="dropdown1">'.getUsername($_SESSION["email"]).'</a></li>';

    $html.='</ul>
  </div>
</nav>
';
return $html;
}


 ?>
