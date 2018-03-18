var audio;
var playlist;
var tracks;
var current;
var len;

var playbtn;
var seek;
var songadd;

function init(){
 current = 0;
 audio = $('audio');
 playlist = $('#playlist');
 playbtn = $('#play_pause');
 seek = $('#seek');


 tracks = playlist.find('a');
 len = tracks.length - 1;
 audio[0].volume = 1;
 audio[0].play();
 playlist.find('a').click(function(e){
     e.preventDefault();
     link = $(this);
     current = link.index();
     console.log(link.data('song'));
     $("#title").html(link.html());
     run(link);
 });
 audio[0].addEventListener('ended',function(e){
     nextSong();
 });
 audio[0].addEventListener('playing',function(e){
    playbtn.html("Pause");
 });
 audio[0].addEventListener('timeupdate',function(e){
    seek.val(audio[0].currentTime);
 });
 audio[0].addEventListener('durationchange',function(e){
    seek.attr({'max':audio[0].duration,'min':'0'});
 });
 audio[0].addEventListener('pause',function(e){
    playbtn.html("Play");
 });

 seek.on("input", function () {
    audio[0].currentTime = seek.val();
});

 playbtn.click(function(e){
   if (playbtn.html()=='Play'){
      if (audio[0].src=='')
        run($(playlist.find('a')[0]));
      audio[0].play();
    }
  else
      audio[0].pause();
 });

}

function prevSong(){
  current--;
  if(current == -1){
    current = 0;
    link = playlist.find('a')[0];
  }else{
    link = playlist.find('a')[current];
  }
  console.log($(link).html());
  $("#title").html($(link).html());
  run($(link));
}

function nextSong(){
  current++;
  if(current == len+1){
    current = 0;
    link = playlist.find('a')[0];
  }else{
    link = playlist.find('a')[current];
  }
  console.log($(link).html());
  $("#title").html($(link).html());
  run($(link));
}

function run(link){
     audio[0].src = link.data("song");
     link.addClass('active').siblings().removeClass('active');
     audio[0].play();
}

init();


    // $('#ajaxcon').html(httpRequest.responseText);
    // $("#allsongs").find("tr").click(function(e){
    //   if ($(this).find("#songtitle")){
    //     var tr;
    //     tr = $(this);
    //     var name = tr.find("#songtitle").html();
    //     var link = tr.find("#songlink").html();
    //     $("#playlist").html($("#playlist").html() + "<a href=\"#\" data-song='" + link + "' class=\"collection-item waves-effect waves-light\">"+name +" </a>");
    //     init();
    //     //alert(tr.find("#songtitle").html());
    //   }
    //
    // });
    // $('#playlist').find("#btnremove").click(function(e){
    //   alert($(this).parent().html());
    //   $(this).parent().remove();});
    // }
