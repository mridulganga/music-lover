<?php

function getHeader($title,$wincludes){
  $html = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="msapplication-tap-highlight" content="no">';
  $html.='<title>'.$title.'</title>';

  $includes_list = explode(" ",$wincludes);
  for ($i=0 ; $i < sizeof($includes_list) ; $i++){
    if ($includes_list[$i]=='materializecss')
      $html.='<link href="assets/css/materialize.css" rel="stylesheet">';
    if ($includes_list[$i]=='materializejs')
      $html.='<script src="assets/js/materialize.js"></script>';
    if ($includes_list[$i]=='jquery')
      $html.='<script src="assets/js/jquery.js"></script>';
  }
  return '<head>'.$html.'</head>';
}

function redirect($url){
  echo '<script>window.location ="'.$url.'" ;</script>';
}


 ?>
