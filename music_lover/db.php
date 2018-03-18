<?php
  require_once 'mp3_get_tags.php';
  initDB();
  session_start();

  function connectDB(){
    return new mysqli("localhost","root","1234","music_lover");
  }

  function executeDB($sql){
    $c = connectDB();
    $result = $c->query($sql);
    $c->close();
    return $result;
  }

  function initDB(){
    $sql = "create table if not exists users (id int(5) primary key auto_increment,
            name varchar(150),
            email varchar(300) not null unique,
            password varchar(300) not null
            )";
    executeDB($sql);
    createUser("admin","admin@gmail.com","admin123","admin123");
    executeDB($sql);
    $sql = "create table if not exists songs(id int(10) primary key auto_increment,
        title varchar(100),
        album varchar(50),
        artist varchar(50),
        genre varchar(50),
        url varchar(1000)
      )";
      executeDB($sql);
    $sql = "create table if not exists playlists(id int(10) primary key auto_increment,
            name varchar(100) not null, user_id int(10) not null, access int(1) not null)";
            //access =0 (public)
            //        1 (private)
      executeDB($sql);
    $sql = "create table if not exists playlist_songs(pl_id int(10) not null, song_id int(10) not null)";
      executeDB($sql);

    $sql = "create table if not exists views(song_id int(10) not null, views int(10))";
    executeDB($sql);
  }

    function addSong($song){
      $sql = 'insert into songs values(0,
      "'.$song->title.'",
      "'.$song->album.'",
      "'.$song->artist.'",
      "'.$song->genre.'",
      "'.$song->link.'")';
      executeDB($sql);
    }

    function getSongs($sql){
      $result = executeDB($sql);
      return $result;
    }

    function removeSong($url){
      executeDB("delete from songs where url='".$url."'");
    }

    function removeAllSongs(){
      executeDB("delete from songs");
    }

  function createUser($name,$email,$pass,$pass2){
    if ($pass!=$pass2)  return 0;
    //if ($pass.length<5)   return -1;
    $pass = hash("sha256",$pass);
    $sql = 'insert into users values(0,"'.$name.'","'.$email.'","'.$pass.'")';
    return executeDB($sql);
  }

  function loginUser($email,$pass){
    $result =executeDB("select * from users where email='".$email."' and password='".hash("sha256",$pass)."'");
    if ($result->num_rows>0){
      $_SESSION["email"] = $email;
      return 1;
    }
    return 0;
  }

  function isUserLogged(){
    if (isset($_SESSION["email"]))
      return 1;
    else
      return 0;
  }

  function getUsername($email){
    $sql = "select name from users where email='".$email."'";
    $result = executeDB($sql);
    if ($result->num_rows>0){
      $row = $result->fetch_assoc();
      $name = $row["name"];
      return $name;
    }
  }

  function getUserId($email){
    $sql = "select id from users where email='".$email."'";
    $result = executeDB($sql);
    if ($result->num_rows>0){
      $row = $result->fetch_assoc();
      $name = $row["id"];
      return $name;
    }
  }

  function logoutUser(){
    session_destroy();
    return 1;
  }

  function findSongs(){
    $path = "assets/files/";
    $files = getFiles();
    $songs_list= array();
    $i=0;
    removeAllSongs();
    foreach ($files as $file){
      if (substr($file,-3)=='mp3'){
        //echo $file;
        $idtag = mp3_get_tags($path.$file);
        $song=null;
        if ($idtag["title"]!='')
          $song->title = $idtag["title"];
        else
          $song->title = substr($file,0,-4);
        $song->link = $path.$file;
        $song->album = $idtag["album"];
        $song->artist = $idtag["artist"];
        $song->genre = $idtag["genre"];
        //print_r($song);
        $songs_list[$i++] = $song;
        //delete all

        addSong($song);
    }
  }
    return $songs_list;
  }

  function getFiles(){
    $path = "assets/files/";
    $files = array_diff(scandir($path), array('.', '..'));
    //print_r ($files);
    return $files;
  }

  function getallPlaylists(){
    return executeDB("select * from playlists where access=0");

  }
  function getPrivatePlaylists($uid){
    $result = executeDB("select * from playlists where access=1 and user_id=$uid");
    return $result;
  }

  function createPlaylist($title,$access){
    $userid = getUserId($_SESSION["email"]);
    executeDB("insert into playlists values (0,'$title',$userid,$access)");
    $result = executeDB("select id from playlists where name='$title'");
    if ($result->num_rows>0){
      $row = $result->fetch_assoc();
      return $row["id"];
    }
  }

  function addSongtoPlaylist($plid,$song_id){
    executeDB("insert into playlist_songs values($plid,$song_id)");
  }

  getSongs();
 ?>
