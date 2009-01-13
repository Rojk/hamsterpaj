
function entertain_upload_form_show_button_enable()
{if(document.getElementById('entertain_upload_form_show_button'))
{document.getElementById('entertain_upload_form_show_button').onclick=entertain_upload_form_show_button_click;}}
function entertain_upload_form_show_button_click()
{document.getElementById('entertain_upload_form').style.display="block";document.getElementById('entertain_edit_form').style.display="none";document.getElementById('entertain_upload_form_show_button').style.display="none";}
function open_fullscreen_window(target_url)
{alert('För att få riktig fullskärm trycker du på F11 på ditt tangentbord, när du vill tillbaks till fönsterläge håller du inne ALT-knappen och trycker på F4!');document.getElementById('entertain_player').innerHTML='';var sc_width=screen.width;var sc_height=screen.height;window.open(target_url,'fullscreen_window','width='+sc_width+', height='+sc_height+', toolbar=no, location=no');}
function entertain_preview_buttons_enable()
{if(document.getElementById('entertain_preview_ok_button'))
{document.getElementById('entertain_preview_ok_button').onclick=entertain_preview_ok_button_click;}
if(document.getElementById('entertain_preview_retry_button'))
{document.getElementById('entertain_preview_retry_button').onclick=entertain_preview_retry_button_click;}}
function entertain_preview_ok_button_click()
{document.getElementById('entertain_preview_buttons').style.display="none";document.getElementById('entertain_edit_form').style.display="block";}
function entertain_preview_retry_button_click()
{document.getElementById('entertain_preview_buttons').style.display="none";document.getElementById('entertain_preview').style.display="none";document.getElementById('entertain_upload_form').style.display="block";alert('/ajax-gateways/entertain.ajax.php?action=cancel_upload');xmlhttp_ping('/ajax_gateways/entertain.ajax.php?action=cancel_upload');}
womAdd('entertain_upload_form_show_button_enable()');womAdd('entertain_preview_buttons_enable()');