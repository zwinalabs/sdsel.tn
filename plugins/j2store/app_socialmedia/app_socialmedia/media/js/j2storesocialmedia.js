/**

*/
function j2store_socialsharing_twitter_click(message)
{
	if (typeof message === 'undefined')
	message = encodeURIComponent(location.href);
	window.open('https://twitter.com/intent/tweet?text=' + message, 'sharertwt', 'toolbar=0,status=0,width=640,height=445');
}

function j2store_socialsharing_facebook_click(message)
{
	window.open('http://www.facebook.com/sharer.php?u=' + encodeURIComponent(location.href), 'sharer', 'toolbar=0,status=0,width=660,height=445');
}

/**
* If app id is available then we could use the feed api to share url and customise message
* ref: https://developers.facebook.com/docs/sharing/reference/feed-dialog/v2.4
*/
function j2store_socialsharing_facebook_click_feed_api(title, caption,desc)
{	
	var options = '';
	options += "&name=" + title ;
	options += "&caption=" + caption ;
	options += "&description=" + desc ;
	window.open('http://www.facebook.com/sharer.php?u=' + encodeURIComponent(location.href) + options, 'sharer', 'toolbar=0,status=0,width=660,height=445');
}


function j2store_socialsharing_google_click(message)
{	
	window.open('https://plus.google.com/share?url='+encodeURIComponent(location.href), 'sharergplus', 'toolbar=0,status=0,width=660,height=445');
}

function j2store_socialsharing_pinterest_click(message,media)
{
	
	window.open('http://www.pinterest.com/pin/create/button/?url=' + encodeURIComponent(location.href)+'&media='+encodeURIComponent(media)+'&description='+message, 'sharerpinterest', 'toolbar=0,status=0,width=660,height=445');
}

function j2store_socialsharing_linkedin_click(message)
{
	window.open('http://www.linkedin.com/shareArticle?mini=true&url=' + encodeURIComponent(location.href)+'&title='+message, 'sharerpinterest', 'toolbar=0,status=0,width=660,height=445');
}