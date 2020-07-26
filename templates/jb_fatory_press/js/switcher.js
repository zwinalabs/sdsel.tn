/*-----------------------------------------------------------------------------------
/* Styles Switcher
-----------------------------------------------------------------------------------*/

window.console = window.console || (function(){
	var c = {}; c.log = c.warn = c.debug = c.info = c.error = c.time = c.dir = c.profile = c.clear = c.exception = c.trace = c.assert = function(){};
	return c;
})();


jQuery(document).ready(function($) {
	
		
		
		
		

		$("#style-switcher .switch_btn").click(function(e){
			e.preventDefault();
			var div = $("#style-switcher");
			console.log(div.css("left"));
			if (div.css("left") === "-280px") {
				$("#style-switcher").animate({
					left: "0px"
				}); 
			} else {
				$("#style-switcher").animate({
					left: "-280px"
				});
			}
		});

	
		//Layout Switcher
	   $("#layout-style").change(function(e){
			if( $(this).val() == 1){
				$("body").removeClass("jb-boxed"), 
				$(window).resize();
				stickyheader = !stickyheader;
			} else{
				$("body").addClass("jb-boxed"),
				$(window).resize();
				stickyheader = !stickyheader;
			}
		});

		
		$('.bg li a').click(function() {
			var current = $('#style-switcher select[id=layout-style]').find('option:selected').val();
			if(current == '2') {
				var bg = $(this).css("backgroundImage");
				$("body").css("backgroundImage",bg);
			} else {
				alert('Please select boxed layout');
			}
		});	

		$("#reset a").click(function(e){
			var bg = $(this).css("backgroundImage");
			$("body").css("backgroundImage","url(../images/bg/default_bg.png)");
			$("#navigation" ).removeClass("style-2")
		});
			

	});
