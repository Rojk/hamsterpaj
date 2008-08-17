
function enable_hetluft()
{
	if(document.getElementById('hetluft_scroll_left'))
	{
		document.getElementById('hetluft_scroll_left').onclick = hetluft_scroll_left;
	}
	if(document.getElementById('hetluft_scroll_right'))
	{
		document.getElementById('hetluft_scroll_right').onclick = hetluft_scroll_right;
	}
}


function hetluft_scroll_left()
{
	hetluft_scroll(-55);
	setTimeout("hetluft_scroll(-40)", 50);
	setTimeout("hetluft_scroll(-20)", 100);
	setTimeout("hetluft_scroll(-10)", 150);
	setTimeout("hetluft_scroll(-5)", 200);
	setTimeout("hetluft_scroll(-3)", 250);
	setTimeout("hetluft_scroll(-2)", 300);
	setTimeout("hetluft_scroll(-1)", 350);
}

function hetluft_scroll_right()
{
	hetluft_scroll(55);
	setTimeout("hetluft_scroll(40)", 50);
	setTimeout("hetluft_scroll(20)", 100);
	setTimeout("hetluft_scroll(10)", 150);
	setTimeout("hetluft_scroll(5)", 200);
	setTimeout("hetluft_scroll(3)", 250);
	setTimeout("hetluft_scroll(2)", 300);
	setTimeout("hetluft_scroll(1)", 350);
}


function hetluft_scroll(change)
{
	document.getElementById('hetluft_container').scrollLeft += change;
}









womAdd('enable_hetluft()');