function hpblog_scroll(direction, repeats)
{
	if(direction == 'forwards')
	{
		for(var i = 0; i < repeats; i++)
		{
			document.getElementById('hpblog_scroll').scrollLeft += 3;
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft +=   2", 25);
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft +=   8", 50);
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft +=  10", 75);
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft +=  18", 100);
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft +=  36", 125);
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft +=  79", 150);
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft += 161", 175);
			
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft += 161", 200);
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft +=  79", 225);
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft +=  36", 250);
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft +=  18", 275);
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft +=  10", 300);
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft +=   8", 325);
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft +=   3", 350);
		}
	}
	else
	{
		for(var i = 0; i < repeats; i++)
		{
			document.getElementById('hpblog_scroll').scrollLeft -= 3;
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft -=   2", 25);
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft -=   8", 50);
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft -=  10", 75);
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft -=  18", 100);
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft -=  36", 125);
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft -=  79", 150);
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft -= 161", 175);
			
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft -= 161", 200);
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft -=  79", 225);
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft -=  36", 250);
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft -=  18", 275);
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft -=  10", 300);
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft -=   8", 325);
			setTimeout("document.getElementById('hpblog_scroll').scrollLeft -=   3", 350);
		}
	}
}