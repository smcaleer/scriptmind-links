$(document).ready(function() {

$('.pop').each(function() {
	var pop = $(this);
	var popList = $(this).parent().children('.pop-list');
	pop.hover(function(){popList.show();}, function(){})
	popList.hover(function(){}, function(){popList.hide();})
}
);

$('div.tt').siblings('a.htt').each(function()
{
	var htt = $(this);
	var tt = htt.siblings('.tt');
	htt.hover(function(){ tt.show();}, function(){});
	tt.hover(function(){}, function(){tt.hide()});
});

$('form').jqTransform();

});
