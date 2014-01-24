jQuery(document).ready(function() {

	if( jQuery('#ggl-map').length > 0 ){
	//google map api
		var map;

		function initialize() {
			var mapOptions = {
				zoom: 12,
				center: new google.maps.LatLng(37.956, 23.710)
			};
			map = new google.maps.Map(document.getElementById('ggl-map'),
				mapOptions);
		//	console.log(map);
		}

		google.maps.event.addDomListener(window, 'load', initialize);
		

	}


	



});