
<?php
  include_once 'db.php';
  include_once "func.php";
  include_once 'nav.php';

  $head = getHeader("Groove Jelly Fish","materializecss jquery materializejs");
  $nav = getNav();


  if (isset($_POST["add_submit"])){
    $title = $_POST["title"];
    $access = $_POST["access"];
    $songs_id = $_POST["songs_id"];
    $id = createPlaylist($title,$access);
    foreach ($songs_id as $sid) {
      addSongtoPlaylist($id,$sid);
    }
  }

  ?>

  <!DOCTYPE html>
  <html>
    <?php echo $head;?>
  <body>
    <?php echo $nav; ?>

<div class="container row" style="overflow:hidden; margin-bottom:200px;">
  <div class="col s12" style="margin-top:50px;">

    <form action="add-playlist.php" method="post">
      <input type="text" name="title" value="" placeholder="Title"><br>
      <p>
        <input name="access" type="radio" id="test1" value="0" checked/>
        <label for="test1">Public</label>
      </p>
      <p>
        <input name="access" type="radio" id="test2" value="1"/>
        <label for="test2">Private</label>
      </p><br><br>
      <b>Select Songs : </b>
      <?php
        $list = getSongs("select * from songs;");
        while ($row=$list->fetch_assoc()){
       ?>
          <p>
            <input class="with-gap" name="songs_id[]" type="checkbox" id="<?php echo $row["id"]; ?>" value="<?php echo $row["id"]; ?>"/>
            <label for="<?php echo $row["id"]; ?>"> <?php echo $row["title"]; ?></label>
          </p>

      <?php } ?><br><br>
      <input type="submit" name="add_submit" value="Create Playlist" class="btn">

    </form>

  </div>
</div>

<script>
$(document).ready(function() {
        $(".dropdown-button").dropdown();
 });
</script>
</div>
</body>
</html>
