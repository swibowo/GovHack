$('#location_map').hide();
$(document).ready(
	function()
	{
		$.ajax(
		{
			type: 'get',
			url: 'http://maps.google.com/maps/api/js?sensor=false&callback=load_map',
			success: function()
			{},
			dataType: 'script',
			cache: true
		});
	}
);

function load_map()
{
	$('#location_map').css('width','100%');
	$('#location_map').css('height','300px');
	$('#location_map').css('float','left');
	$('#location_map').css('padding-bottom','20px');
	$('#location_map').append($('<div id="location_map_inner"></div>'));
	$('#location_map_inner').css('width','100%');
	$('#location_map_inner').css('height','100%');

	// make the world centered
	var latlng = new google.maps.LatLng(30, 10);
	var map = new google.maps.Map(
		document.getElementById('location_map_inner'), 
		{
			disableDefaultUI: true,
			navigationControl: true,
			navigationControlOptions:
			{
				style: google.maps.NavigationControlStyle.SMALL
			},
			scaleControl: false,
			scrollwheel: false,
			zoom: 1,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}
	);
	map.setZoom(1);
	$('#location_map').slideDown(3000, function()
	{
		google.maps.event.trigger(map, 'resize');
		map.panTo(latlng);
		zoom_user_location(map);
	});
	retrieve_markers(map, 0, 10);
}

function zoom_user_location(map)
{
	var user_lat = '';
	var user_lon = '';
	if(user_lat != '' && user_lon != '')
	{
		map.setZoom(3);
		var latlng = new google.maps.LatLng(user_lat, user_lon);
		google.maps.event.trigger(map, 'resize');
		map.panTo(latlng);
	}
}

function retrieve_markers(map, start, quantity)
{
	$.ajax(
	{
		type: "POST",
		url: base_url + "items/ajax_get_markers",
		data: "start="+ start + "&quantity=" + quantity + "&ajax_trigger=N4h0vcbm1BKkH298",
		success: function(json_result)
		{
			var result = $.parseJSON(json_result);
			if(result != undefined && result != '' && result['markers'] != undefined && result['markers'] != '')
			{
				$.each(result['markers'],
				function(i, marker)
				{
					place_marker(map, marker['lat'], marker['lon'], unescape(marker['title']), unescape(marker['url']));
				}
				);
				if(result['more'])
				{
					retrieve_markers(map, start + quantity, quantity);
				}
			}
		}
	});
}
function place_marker(map, lat, lon, title, url)
{
	var latlng = new google.maps.LatLng(lat, lon);
	var marker = new google.maps.Marker(
	{ position: latlng, map: map, title: title }
	 );
	var html_content = "<table style='width:100%;'><tr><td><b>" + title + "</b></td></tr><tr><td align='right'><a href='" + url + "' title='" + title + "'>View more details...</a></td></tr></table>";
	var infowindow = new google.maps.InfoWindow(
	{content: html_content}
	);
	google.maps.event.addListener(marker, "click",
	function()
	{
		infowindow.open(map, marker);
	});
	google.maps.event.addListener(map, 'click',
	function()
	{
		infowindow.close();
	});
}
