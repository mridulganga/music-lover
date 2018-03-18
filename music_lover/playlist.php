
<?php
  include_once 'db.php';
  include_once "func.php";
  include_once 'nav.php';

  $head = getHeader("Groove Jelly Fish","materializecss jquery materializejs");
  $nav = getNav();

  if (isset($_GET["load"])){
    $id = $_GET["load"];
    //echo $id;
    $sql = "select * from playlist_songs where pl_id=$id";
    $i=0;
    $ids =  array();
    $result = executeDB($sql);
    while ($row=$result->fetch_assoc()) {
      $ids[$i++] = $row["song_id"];
    }
    $ids_str = "(" . implode($ids,",") . ")";
    //echo $ids_str;
    $sql = "select * from songs where id in $ids_str";

    $songs_list_arr = getSongs($sql);
    //print_r ($songs_list_arr);
    $songs_list = array();
    $i=0;
    $song=null;
    while($row=$songs_list_arr->fetch_assoc()){
      $song=null;
      $song->title = $row["title"];
      $song->link = $row["url"];
      $song->album = $row["album"];
      $song->artist = $row["artist"];
      $song->genre = $row["genre"];
      $songs_list[$i++] = $song;
    }

  }

?>


<!DOCTYPE html>
<html>
  <?php echo $head;?>
<body>
  <?php echo $nav; ?>
<div class='container'>
  <div class="row" style="margin-top:50px;">
    <div class="col s12 m8 l8">

      <?php
      if (isset($_SESSION["email"]))
        echo '<a href="add-playlist.php" class="btn">Add Playlist</a><br><br>';

       ?>

    Playlists
    <div class="collection">
      <?php

      $result = getallPlaylists();

      if ($result->num_rows>0)
      {
        while ($row = $result->fetch_assoc()) {
          echo '<a href="playlist.php?load='.$row["id"].'" class="collection-item">'.$row["name"].'</a>';
        }
      }

      if (isset($_SESSION["email"])){
        $userid = getUserId($_SESSION["email"]);
        $result = getPrivatePlaylists($userid);

              if ($result->num_rows>0)
              {
                echo "</div>User Lists<div class=\"collection\">";
                while ($row = $result->fetch_assoc()) {
                  echo '<a href="playlist.php?load='.$row["id"].'" class="collection-item">'.$row["name"].'</a>';
                }
              }
      }

      ?>
    </div>
  </div>

      <div class="col s12 m4 l4" style="padding:20px!important;">
        <h5>Main Playlist</h5>
        <div class="row">
          <ul id="playlist" class="collection">
    <?php
    foreach ($songs_list as $song){echo '<a href="#" data-song="'.$song->link.'" class="collection-item waves-effect waves-light">'.$song->title.'</a>';}
    ?>
          </ul>
        </div>
      </div>
  </div>

  <style media="screen">
    .collection-item.active{background: #34495e!important; color: white!important;}
    .collection-item{color:#34495e!important;}
    .tab a{color: #34495e!important; font-weight: bold;}
    .tab a.active{}
    audio::-webkit-media-controls-panel{background:#2c3e50; }
  </style>

  <div  style="overflow:hidden; position:fixed; width:100%; bottom:0px; padding:20px; background:#2c3e50; color:white; left:0;">
    <h5 id="title"></h5>
    <audio controls style="display:none; width:100%;">Audio Streaming Not Supported</audio>
    <div class="row">
      <div class="col s4 m2 l2">
          <button id="play_pause" class="btn">Play</button>
      </div>
      <div class="col s8 m10 l10">
        <p class="range-field" style="padding:0; margin:0; background:#2c3e50;">
          <input type="range" id="seek" min="0" max="100"  style="background:#2c3e50;"/>
        </p>
      </div>
    </div>

  </div>

  <script src="app.js"></script>
  <script src="ajax.js"></script>

  <script>
  $(document).ready(function() {
          makeRequest('ajax.php?action=main');
          $(".dropdown-button").dropdown();
          $('ul.tabs').tabs();


   });
  </script>
</div>
</body>
</html>
