
function enable_dl_clear_buttons()
{var clear_buttons=getElementsByClassName(document,'a','category_clear');for(var i=0;i<clear_buttons.length;i++)
{clear_buttons[i].onclick=dl_clear_button_click;}}
function dl_clear_button_click()
{return confirm('Vill du verkligen '+this.title.toLowerCase()+'?');}
womAdd('enable_dl_clear_buttons()');