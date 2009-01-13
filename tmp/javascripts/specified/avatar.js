
window.onload=function()
{if(document.getElementById('user_avatar'))
{document.getElementById('user_avatar').onclick=function()
{window.close();}}
if(document.getElementById('remove_avatar')&&document.getElementById('user_id')&&document.getElementById('user_id').value>0)
{document.getElementById('remove_avatar').onclick=function()
{window.location.href='?refuse='+document.getElementById('user_id').value;}}
if(document.getElementById('presentation'))
{document.getElementById('presentation').onclick=function()
{window.open('/traffa/profile.php?id='+document.getElementById('user_id').value);window.close();}}
if(document.getElementById('guestbook'))
{document.getElementById('guestbook').onclick=function()
{window.open('/traffa/guestbook.php?view='+document.getElementById('user_id').value);window.close();}}}