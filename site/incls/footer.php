<?php if(!empty($films)) { if(empty($user)) {?><script>
	var screen = document.querySelector('#tscreen'); var trig = document.querySelectorAll(".c-rating"); var span = document.querySelectorAll(".close")[0]; 
	for(let x=0;x<trig.length;x++) {trig[x].addEventListener('click', function(){document.querySelector(".message").innerHTML = 'Sign up and start rating! <br><a href="register.php"><button class="util-btn">Sign up</button></a>'; screen.style.display = "block"; }); }
    span.onclick = function() {screen.style.display = "none"; }
    window.onclick = function(event) {if (event.target == screen) {screen.style.display = "none"; } }
</script><?php } ?><script src="global.js"></script><script src="rating.min.js"></script><script type="text/javascript">var el = document.querySelectorAll('.c-rating');for(let i=0;i<el.length;i++) {var myRating = rating(el[i], s[i], 5, r => sr(el[i].getAttribute("name"), r)); }</script><?php } ?><script src="jquery.js"></script><script src="jquery-ui.js"></script><script>
	var $elem = $("#q").autocomplete({ 
	source:function( request, response ) {
		$.ajax({type: "POST", url: "autocomp.php",data: {q: request.term.trim()},cache: false,success: function( data ) {response( data );}
		});}}), elemAutocomplete = $elem.data("ui-autocomplete") || $elem.data("autocomplete");
if (elemAutocomplete) { elemAutocomplete._renderItem = function (ul, item) { var newText = String(item.value).replace(new RegExp(this.term.trim(), "gi"),"<strong>$&</strong>");
        return $("<li></li>").data("item.autocomplete", item).append("<div>" + newText + "</div>").appendTo(ul);
    };}
</script></div>
</body>
</html>