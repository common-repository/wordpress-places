var map;
function wp_places_small_map(l) {
	if(l) {
		if(GMap2) {
			lo = l.split(',');
			var point = new GLatLng(lo[0], lo[1]);
			var map = new GMap2(document.getElementById('map-canvas'));
			map.setCenter(point, 9);
			map.addControl(new GLargeMapControl());
			var marker = new GMarker(point);
			map.addOverlay(marker);
		}
	}
}
function wp_places_large_map(j) {
	if(j) {
		map = new GMap2(document.getElementById('map-canvas'));
		map.setCenter(new GLatLng(0,0), 2);
		map.addControl(new GLargeMapControl());
		for(var i in j) { wp_places_plot_point(j[i]); }
	}
}
function wp_places_plot_point(place) {
	if(place.meta['google-map']) {
		s = place.meta['google-map'].split(',');
		point = new GLatLng(s[0], s[1]);
		var marker = new GMarker(point);
		map.addOverlay(marker);
		GEvent.addListener(marker, 'click', function() {
			map.panTo(point);
			html = '<strong>'+place['name']+'</strong><br />';
			if(place.meta['image-url']) {
				html += '<img src="'+place.meta['image-url']+'" style="width:'+thumbw+'px;height:'+thumbh+'px;" /><br clear="left" />'
			}
			html += 'Post Count: '+place.count+'<br clear="left" />';
			html += '<a href="/places/'+place.slug+'">View Post</a><br clear="left" />'
			marker.openInfoWindowHtml(html);
		});
	}
}