$(document).ready(function() {

	/* script to fill the trending pages menu */
	if($('#trending-pages-menu').length) {
		$.getJSON(mw.util.wikiScript('api')+'?action=trendingmenu&format=json', function (result) {
			var html = '';
			for(var i = 0; i < 5; i++) {
				if (result.trendingmenu[i]) {
					html += '<li><a href="'+result.trendingmenu[i].href+'">'+result.trendingmenu[i].text+'</a></li>';
				}
			}
			$('#trending-pages-menu').html(html);
		});
	}
});
