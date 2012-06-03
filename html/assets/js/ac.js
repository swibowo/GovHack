var bottom = false;

$(document).ready(
        function() {

		// center bar on load and also on every resize
		fixup_hbar();
		fixup_loading();

		load_images(selected_year);
	
		$(
			function()
			{

				$(window).scroll(
					function()
					{
						if(bottom)
						{
				   			if($(window).scrollTop() + $(window).height() == $(document).height())
							{
								window.setTimeout(
          								function()
									{
                								load_images(selected_year, 'previous');
										if(has_previous)
										{
											$(window).scrollTop(100);
										}
               								},
                							1000
        							);
				  			}
						}
						else
						{
							bottom = true;
						}
			
						if($(window).scrollTop() == 0)
                                                {
							window.setTimeout(
                                                        	function()
                                                                {
                                                			load_images(selected_year, 'next');
									if(has_next)
									{
										$(window).scrollTop($(document).height() - 100);
									}
								},
								1000
							);
                                                }
					}
				);
		
            			$(window).resize(
					function()
					{
						fixup_hbar();
						fixup_loading();
						load_images_delayed(selected_year);
            				}
				);

				$(window).scroll(
					function()
					{
						//fixup_hbar();
            				}
				);

				check_rotation();
        		}
		);
        }
);

function load_images_delayed(year)
{
	// loading image
	$("#loading_image").css('display', 'block');

	window.setTimeout(
		function()
		{
			load_images(year);
		},
		1000
	);
}

var current_offset = '';
var next_offset = '';
var previous_offset = '';
var has_next = '';
var has_previous = '';

function load_images(year, action)
{
	if(action == undefined)
	{
		action = 'same';
	}

	// loading image
	$("#loading_image").css('display', 'block');

	var class_left = 'left-entries';
	var class_right = 'right-entries';
	
	var do_ajax = false;

	var other_url = '';
	if(action == 'next')
	{
		// next
		if(has_next)
		{
			do_ajax = true;
			if(next_offset != '')
                        {
                                other_url += '/' + next_offset;
				current_offset = next_offset;
                        }	
		}	
	}
	else if(action == 'same')
	{
		// don't advance the offsets
		do_ajax = true;
		other_url += '/' + current_offset;
	}
	else
	{
		// previous

		if(has_previous)
		{
			do_ajax = true;
			if(previous_offset != '')
			{
				other_url += '/' + previous_offset;
				current_offset = previous_offset;
			}
		}
	}

	if(do_ajax)
	{
		$.ajax(
			{
				type: "POST",
				url: base_url + "ajax/getlist/" + $('#ac_body').width() + year + other_url,
				data: "",
				success: function(results)
				{
					results = $.parseJSON(results);

					// {"count":0,"next":-125,"has_next":true,"previous":-135,"has_prev":true,"records":[],"width":160}
					var max_size = results.width;
					next_offset = results.next;
					previous_offset = results.previous;
					has_previous = results.has_prev;
					has_next = results.has_next;

					// add the images
					$("#chronicle-container").html("<div class='timeline_container'><div class='timeline'></div></div>");

					//$("#chronicle-container").html("");

					$.each(results.records,
							function(i, record)
							{
								var current_class = class_left;
								if(i % 2 == 0)
								{
									current_class = class_right;
								}

								//$("#chronicle-container").append('<div class="box ' + current_class  + '" style="width:' + max_size + 'px;max-width:' + max_size + 'px;"><img src="' + base_url + record.img + '" class="thumbnails" /><p class="chronicle-description" style="max-width:' + max_size + 'px;">' + record.description + '</p></div>');
								$("#chronicle-container").append('<div class="box ' + current_class  + '"><img src="' + base_url + record.img + '" class="thumbnails" /><p class="chronicle-description">[Year ' +record.year + '] ' + record.description + '</p></div>');
							}
					);

					$("#loading_image").css('display', 'none');
					fixup_hbar();
				}
			}
		);
	}
	else
	{
		$("#loading_image").css('display', 'none');
	}
}

function fixup_hbar()
{
	// position in the middle the timeline
	/*
	var screen_size = $('#ac_body').width();
	var hbar_size = $('#hbar').width();
	var space = (screen_size - hbar_size) / 2;
	if($('#hbar').css('right') != space)
	{
		$('#hbar').css('right', space);
	}
	*/
	var screen_size = $('#ac_body').width();
	var hbar_size = $('#timeline_container').width();
	var space = (screen_size - hbar_size) / 2;
	if($('#timeline_container').css('right') != space)
	{
		$('#timeline_container').css('right', space);
	}

	// fix height

	//alert($('#chronicle-container').height());
	//$('#timeline').height($('#chronicle-container').height());
//	alert($('#chronicle-container').scrollTop());
//	$('#timeline').css('top', $('#chronicle-container').scrollTop());
}

function fixup_loading()
{
	// position in the middle the timeline
	var screen_size = $('#ac_body').width();
	var loading_image = $('#loading_image').width();
	var space = (screen_size - loading_image) / 2;
	if($('#loading_image').css('right') != space)
	{
		$('#loading_image').css('right', space);
	}

	// position vertically
        var screen_size = $('#ac_body').height();
        var loading_image = $('#loading_image').height();
        var space = (screen_size - loading_image) / 2;

        if($('#loading_image').css('top') != space)
        {
                $('#loading_image').css('top', space);
        }
}

function Arrow_Points()
{
var s = $('#chronicle').find('.item');
$.each(s,function(i,obj){
var posLeft = $(obj).css("left");
$(obj).addClass('borderclass');
if(posLeft == "0px")
{
html = "<span class='rightCorner'></span>";
$(obj).prepend(html);
}
else
{
html = "<span class='leftCorner'></span>";
$(obj).prepend(html);
}
});
}	
