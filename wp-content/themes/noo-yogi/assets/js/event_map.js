google.maps.event.addDomListener(window, 'load', initialize);
var geocoder;
var map;
var infowindow;
var marker;
function initialize() {
    geocoder = new google.maps.Geocoder();

    var mapOptions = {
        center: new google.maps.LatLng(nooEventMap.latitude,nooEventMap.longitude),
        zoom: parseInt(nooEventMap.def_zoom),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    map = new google.maps.Map(document.getElementById('noo_event_google_map'), mapOptions);

    infowindow = new google.maps.InfoWindow({
        position: map.getCenter()
    });

    infowindow = new google.maps.InfoWindow({
        position: map.getCenter()
    });


    marker = new google.maps.Marker({
        map: map,
        position: new google.maps.LatLng(nooEventMap.latitude,nooEventMap.longitude),
        draggable: false
    });

    google.maps.event.addListener(map, "idle", function() {
        google.maps.event.trigger(map, 'resize');
    });

}