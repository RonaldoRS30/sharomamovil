let map, infoWindow;
    getLocation();
    setInterval(function(){
        getLocation();
    }, 300000);
function iniciarMap() {
    navigator.geolocation.getCurrentPosition(function(position) {
        latitude = position.coords.latitude;
        longitude = position.coords.longitude;
        var coord = {lat: latitude, lng: longitude};
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 18,
            center: coord,
            mapId: "MAP_MARKER"
        });
    });
}

function ActualizarMapa(){
    var num = document.getElementById('id_vendedor').value;
    var cont = 0;
    $.ajax({
        type: "POST",
        url:'./mapa.php?idVend='+num,
        dataType: 'json',
        beforeSend: function(objeto){
        },
        success:function(data){
            ubicacion = data[cont][0];

            ubi = ubicacion.split(",");
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 18,
                center: {lat: Number(ubi[0]), lng: Number(ubi[1])},
                mapId: "MAP_MARKER"
            });
            data.forEach(element => {
                ubicacion = data[cont][0];
                ubi = ubicacion.split(",");
                console.log(ubi)
                const marker = new google.maps.marker.AdvancedMarkerElement({
                    position: {lat: Number(ubi[0]), lng: Number(ubi[1])},
                    map,
                });
                cont++;
            });
        }
    })
    
}

function getLocation(){
    if(navigator.geolocation){
        navigator.geolocation.getCurrentPosition(function(position) {
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;
                $.ajax({
                    type: "GET",
                    url:'./geolocation.php?lat='+latitude+'&lng='+longitude,
                    beforeSend: function(objeto){
                        
                    },
                    success:function(data){			
                    }
                })
            },
            function (error){
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        alert("Porfavor, active la ubicacion")
                        $.ajax({
                            type: "GET",
                            url:'./geolocation.php?log=error',
                            beforeSend: function(objeto){
                                
                            },
                            success:function(data){			
                            }
                        })
                    case error.POSITION_UNAVAILABLE:
                        alert("Su navegador/celular no posee GPS")
                        break;
                    case error.UNKNOWN_ERROR:
                        alert("Un error inesperado se presentó, comuníquese con soporte")
                        break;
                }
            },
            {maximumAge:10000, timeout:5000, enableHighAccuracy: true});
    }else{
        alert("No se acepto la ubicación");
    }
}





// function calculateAndDisplayRoute(directionsService, directionsRenderer) {
  
//     directionsService
//       .route({
//         origin: {lat: -12.0631617, lng: -76.9508862},
//         destination: {lat: -12.131617, lng: -77.0508862},
//         optimizeWaypoints: true,
//         travelMode: google.maps.TravelMode.DRIVING,
//       })
//       .then((response) => {
//         directionsRenderer.setDirections(response);
  
//         const route = response.routes[0];
  
//         // For each route, display summary information.
//         for (let i = 0; i < route.legs.length; i++) {
//         }
//       })
//       .catch((e) => window.alert("Directions request failed due to " + status));
//   }

