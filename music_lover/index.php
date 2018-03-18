
<?php
  include_once 'db.php';
  include_once "func.php";
  include_once 'nav.php';

  $head = getHeader("Music Lover","materializecss jquery materializejs");
  $nav = getNav();
  //findSongs();

  // $songs_list_arr = getSongs("select * from songs");
  // $songs_list = array();
  // $i=0;
  // $song=null;
  // while($row=$songs_list_arr->fetch_assoc()){
  //   $song=null;
  //   $song->title = $row["title"];
  //   $song->link = $row["url"];
  //   $song->album = $row["album"];
  //   $song->artist = $row["artist"];
  //   $song->genre = $row["genre"];
  //   $songs_list[$i++] = $song;
  // }
  // $songs_list = null;

?>

<!DOCTYPE html>
<html>
  <?php echo $head;?>
<body>
  <?php echo $nav; ?>
  <div class="container" style="overflow:hidden; margin-bottom:200px;">
<div class="row" style="margin-top:50px;">
<div class="col m8 l8 s12">

  <form class="" action="index.php" method="get">
    <input type="hidden" name="action" value="search">
    <input type="text" name="query" placeholder="Search Songs" value="">
  </form>

      <table id="allsongs" class="striped">
        <thead>
            <th><a href="index.php?action=sort&crit=title">Title</a></th>
            <th><a href="index.php?action=sort&crit=album">Album</a></th>
            <th><a href="index.php?action=sort&crit=artist">Artist</a></th>
            <th><a href="index.php?action=sort&crit=genre">Genre</a></th>
        </thead>
        <tbody>
<?php

  if (isset($_GET["action"])){
    if ($_GET["action"]=='sort'){
      $crit = $_GET["crit"];
      switch ($crit){
        case 'title':
          $sql = "select * from songs order by title";
          break;
        case 'album':
         $sql="select * from songs order by album";
         break;
         case "artist":
         $sql= "select * from songs order by artist";
         break;
         case 'genre':
         $sql="select * from songs order by genre";
         break;
      }
    }
    elseif ($_GET["action"]=='search') {
      $q = $_GET["query"];
      $sql = "select * from songs where title like('%".$q."%') or album like('%".$q."%') or artist like('%".$q."%') or genre like('%".$q."%')";
    }
  }
  else {
    $sql = "select * from songs";
  }


  $result = getSongs($sql);
                        while ($row = $result->fetch_assoc()){
                          $html.="<tr id=\"song\">
                              <td id='songtitle'>".$row["title"]."</td>
                              <td id='songlink' style='display:none;'>".$row["url"]."</td>
                              <td>".$row["album"]."</td>
                              <td>".$row["artist"]."</td>
                              <td>".$row["genre"]."</td>
                          </tr>";
  }
  echo $html;
?>
        </tbody>
</table>
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
