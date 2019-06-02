<div id="map"></div>
<script>
var customLabel = {
  sign_board: {
    label: 'S'
  }
};

function initMap() {
  var map = new google.maps.Map(document.getElementById('map'), {
    center: new google.maps.LatLng(37.246944, -121.815552),
    zoom: 18
  });

  var infoWindow = new google.maps.InfoWindow;
  var filepath = "<?php echo  $filepath ?>";
  
  // Change this depending on the name of your PHP or XML file
  downloadUrl(filepath, function(data) {
    var xml = data.responseXML;
    var markers = xml.documentElement.getElementsByTagName('marker');
    Array.prototype.forEach.call(markers, function(markerElem) {
      var id = markerElem.getAttribute('id');
      var name = markerElem.getAttribute('class_label');
      var address = markerElem.getAttribute('address');
      var type = markerElem.getAttribute('type');
      var point = new google.maps.LatLng(
        parseFloat(markerElem.getAttribute('lng')),
        parseFloat(markerElem.getAttribute('lat')));

        var infowincontent = document.createElement('div');
        var strong = document.createElement('strong');
        strong.textContent = name
        infowincontent.appendChild(strong);
        infowincontent.appendChild(document.createElement('br'));

        var text = document.createElement('text');
        text.textContent = address
        infowincontent.appendChild(text);
        var icon = customLabel[type] || {};
        var marker = new google.maps.Marker({
          map: map,
          position: point,
          label: icon.label
        });

        var contentString = '<div id="content">'+
            '<div id="siteNotice">'+
            '</div>'+
            '<h3 id="firstHeading" class="firstHeading">'+name+'</h3>'+
            '<div id="bodyContent">'+
            '<img src="'+address+'" height="50" width="50"</img> '+
            ''+
            '</div>'+
            '</div>';


        marker.addListener('click', function() {
          infoWindow.setContent(contentString);
          infoWindow.open(map, marker);
        });
      });
    });
  }

  function downloadUrl(url, callback) {
    var request = window.ActiveXObject ?
    new ActiveXObject('Microsoft.XMLHTTP') :
    new XMLHttpRequest;

    request.onreadystatechange = function() {
      if (request.readyState == 4) {
        request.onreadystatechange = doNothing;
        callback(request, request.status);
      }
    };

    request.open('GET', url, true);
    request.send(null);
  }

  function doNothing() {}
</script>

<!-- Replace the value of the key parameter with your own API key. -->
<script async defer
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAvQz4Sv8iRT_0CmUZq1MfHW9cJT-floEM&callback=initMap">
</script>
