  var httpRequest;

function initAjax(){
  if ($('#ajaxButton').attr("data-option")=="main")
    document.getElementById("ajaxButton").onclick = function() { makeRequest('ajax.php?action=back'); };
  if ($('#ajaxButton').attr("data-option")=="back")
    document.getElementById("ajaxButton").onclick = function() { makeRequest('ajax.php?action=main'); };
}

  function makeRequest(url) {
    httpRequest = new XMLHttpRequest();

    if (!httpRequest) {
      alert('Giving up :( Cannot create an XMLHTTP instance');
      return false;
    }
    httpRequest.onreadystatechange = callbackfunc;
    httpRequest.open('GET', url);
    httpRequest.send();
  }

  function callbackfunc() {
    if (httpRequest.readyState === XMLHttpRequest.DONE) {
      if (httpRequest.status === 200) {
        //alert(httpRequest.responseText);
        //do the div update
        $('#ajaxcon').html(httpRequest.responseText);
        $("#allsongs").find("tr").click(function(e){
          if ($(this).find("#songtitle")){
            var tr;
            tr = $(this);
            var name = tr.find("#songtitle").html();
            var link = tr.find("#songlink").html();
            $("#playlist").html($("#playlist").html() + "<a href=\"#\" data-song='" + link + "' class=\"collection-item waves-effect waves-light\">"+name +" <i id='btnremove'>X</i></a>");
            init();
            //alert(tr.find("#songtitle").html());
          }

        });
        $('#playlist').find("#btnremove").click(function(e){
          alert($(this).parent().html());
          $(this).parent().remove();});
      } else {
        alert('There was a problem with the request.');
      }
    }
  }
