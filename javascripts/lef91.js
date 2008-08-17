$(document).ready(function(){
	$("#photo_content").show()
	$("#thumbnail li a").click(function()
	{
		$("#large img").hide().attr({"src": $(this).attr("href"), "title": $("> img", this).attr("title")});
		$("#large h2").html($("> img", this).attr("title"));
		$("#large>img").load(function(){$("#large>img:hidden").fadeIn("slow")});
		return false;
	});

	$("#large img").click(function()
	{
		$("#large h2").html($(this).attr("id"));
		return false;
	});
	
});

//$("#large>img").load(function(){$("#large>img:hidden").fadeIn("slow")});
//$("#large img").hide().attr({"src": $(this).attr("href"), "title": $("> img", this).attr("title")});