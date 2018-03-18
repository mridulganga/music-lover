<?php

include_once 'db.php';
include_once "func.php";


function mainMenu(){
  $html = '<div class="row">
    <div class="col s12">
      <ul class="tabs z-depth-1">
        <li class="tab col s3"><a href="#test1">All</a></li>
        <li class="tab col s3"><a class="active" href="#test2">Artist</a></li>
        <li class="tab col s3"><a href="#test3">Album</a></li>
        <li class="tab col s3"><a href="#test4">Genre</a></li>
      </ul>
    </div>
    <div id="test1" class="col s12">
    <table id="allsongs" class="striped">
      <tr>
        <td>Title</td>
        <td>Album</td>
        <td>Artist</td>
        <td>Genre</td>
      </tr>';

            $result = getSongs("select * from songs");
            while ($row = $result->fetch_assoc()){
              $html.="<tr id=\"song\">
                  <td id='songtitle'>".$row["title"]."</td>
                  <td id='songlink' style='display:none;'>".$row["url"]."</td>
                  <td>".$row["album"]."</td>
                  <td>".$row["artist"]."</td>
                  <td>".$row["genre"]."</td>
              </tr>";
            }

    $html.='</table>

      <a href="#" id="ajaxButton" data-option="main">Test</a>
    </div>
    <div id="test2" class="col s12">Test 2</div>
    <div id="test3" class="col s12">Test 3</div>
    <div id="test4" class="col s12">Test 4</div>
  </div>';
  return $html;
}

function back(){
  $html = '<a href="#" id="ajaxButton" data-option="back">back test</a>';
  return $html;
}

if (isset($_GET["action"])){
  $a =  $_GET["action"];
  if ($a=='main')
    echo mainMenu();
  else if ($a=='back')
    echo back();
}

 ?>
<script type="text/javascript">
  $(".dropdown-button").dropdown();
  $('ul.tabs').tabs();
  initAjax();
</script>
