<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
		<meta name="description" content="PHP Developer Mini Project">
		<meta name="keywords" content="HTML, CSS, JS, JavaScript, web development, PHP Developer Mini Project">
		<meta name="author" content="Somkiat Laowajeesart">
		<title>PHP Developer Mini Project</title>
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/custom.css" rel="stylesheet">
		<script src="js/jquery-2.1.3.min.js"></script>
		<script src="https://maps.googleapis.com/maps/api/js" type="text/javascript"></script>
		<script type="text/javascript">
		var map;
		var markers = [];
		var infoWindow;
		
		function initialize() {
			$('#historySelect').toggle();
		
			$.ajax({
				method: "GET",
				url: "http://freegeoip.net/json/"
			})
			.done(function( res ) {
				var mapOptions = {
					zoom: 15,
					center: new google.maps.LatLng(res.latitude, res.longitude)
				};
				map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
				infoWindow = new google.maps.InfoWindow();
			});
			
			$('.btn-history').each(function( index ) {
				$(this).click(function(e) { 
					$('#historySelect').toggle();
				});
			});
			
			$('body').delegate('.btn-history-view', 'click', function() {
				var history = $(this).attr('id').split('-');
				$("#search").val(history[1]);
				$('#historySelect').toggle();
				searchLocation();
			});

			$('#btn-search').click(function(e) { 
				searchLocation();
			});
			
			$('#search-form').submit(function(e) {
				searchLocation();
				e.preventDefault() 
			});
		}
		
		function clearLocations() {
			infoWindow.close();
			for (var i = 0; i < markers.length; i++) {
				markers[i].setMap(null);
			}
			markers.length = 0;
		}

		function createMarker(latlng, name, address) {
			var html = '<div class="marker-box"><div class="marker-title">' + name + '</div><div class="marker-detail">' + address + '</div></div>';
			var marker = new google.maps.Marker({
				map: map,
				position: latlng
			});
			google.maps.event.addListener(marker, 'click', function() {
				infoWindow.setContent(html);
				infoWindow.open(map, marker);
			});
			markers.push(marker);
		}
		
		function searchLocation() {
			var search = $("#search").val();
			if (search == '') {
				alert("Fill the city.");
				$("#search").focus();
				return;
			}
			$('#btn-search').attr('disabled', true).toggleClass('disabled').val('SEARCHING');			
			var geocoder = new google.maps.Geocoder();
			var error = 0;
			geocoder.geocode({address: search}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					clearLocations();

					var location = results[0].geometry.location;
					map.setCenter(location);
					searchLocationsNear(search, location);
					var id = 'history-' + search;
					if ($("#" + id).length == 0) {
						$('<li><a href="javascript:;" class="btn-history-view" id="'+id+'">'+search+'</a></li>').insertAfter("#historySelect li:eq(1)");
					}
					$("#caption").html("TWEETS ABOUT " + search);
				} else {
					error = 1;
				}
				
				if (error == 1) {
					alert(search + ' not found');
					$('#btn-search').attr('disabled', false).toggleClass('disabled').val('SEARCH');
					$("#caption").html('');
				}				
			});
		}
		
		function searchLocationsNear(search, center) {
			$.ajax({
				method: "GET",
				url: "tweets",
				data: { lat: center.lat(), long: center.lng(), search: search }
			})
			.done(function( res ) {
				if (res == '' || res == '0') {
					$('#btn-search').attr('disabled', false).toggleClass('disabled').val('SEARCH');
					return;
				}
				var res = jQuery.parseJSON(res);
				var bounds = new google.maps.LatLngBounds();
				
				for (var i in res.statuses) {
					if (res.statuses[i].geo != null) {
						var latlng = new google.maps.LatLng( 
							parseFloat( res.statuses[i].geo.coordinates[0] ),
							parseFloat( res.statuses[i].geo.coordinates[1] ) );
						var url = res.statuses[i].user.url;
						var image = '<img src="' + res.statuses[i].user.profile_image_url + '" title="' + res.statuses[i].user.screen_name + '" />';
						if (url == null) {
							var title = image;
						} else {
							var title = '<a href="' + url + '" target="_blank">' + image + '</a>';
						}				
					
						createMarker( latlng, title, res.statuses[i].text );
						bounds.extend( latlng );
					}
				}
				
				map.fitBounds( bounds );
				$('#btn-search').attr('disabled', false).toggleClass('disabled').val('SEARCH');
			});
		}

		google.maps.event.addDomListener(window, 'load', initialize);
		</script>
	</head>
	<body>
	<div id="caption"></div>
	<div id="map-canvas"></div>
	<div class="row nav-bar">
		<form id="search-form">
		<div class="col-md-8"><input type="text" id="search" value="" autocomplete="off" placeholder="City Name" /></div>
		</form>
		<div class="col-md-2"><input type="button" id="btn-search" class="button" value="SEARCH" /></div>
		<div class="col-md-2"><input type="button" class="button btn-history" value="HISTORY" /></div>
	</div>
	<div class="col-md-12" id="historySelect">
		<ul>
			<li><a href="javascript:;" class="btn-history">< BACK TO THE TWEETS!</a></li>
			<?php foreach ($history as $v) { 
			$v = json_decode( $v );
			?>
			<li><a href="javascript:;" class="btn-history-view" id="history-<?= $v->search; ?>"><?= $v->search; ?></a></li>
			<?php } ?>
		</ul>
	</div>
	</body>
</html>