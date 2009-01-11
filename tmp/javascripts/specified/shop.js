
$(document).ready(function(){var female_sizes={'small':'Small/36','medium':'Medium/38','large':'Large/40',}
var male_sizes={'small':'Small','medium':'Medium','large':'Large',}
$('#shop_gender').change(function(){if($('#shop_gender').val()=='male')
{chosen_array=male_sizes;}
else if($('#shop_gender').val()=='female')
{chosen_array=female_sizes;}
$('#shop_size').addOption(chosen_array,false);});});womAdd();