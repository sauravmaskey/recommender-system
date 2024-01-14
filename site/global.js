function sr(b, c){
	fetch('rate.php', {
	  method: 'post',
	  headers: {
	    'Accept': 'application/json',
	    'Content-Type': 'application/json'
	  },
	  credentials: 'include',
	  body: JSON.stringify({type:"rate", fid: b.substr(4), r: c})
	});
}

window.onload = function (){            
	var el = document.querySelector("#hsubmit");
	var field = document.querySelector("#q");
	el.onclick = function(){
		if(field.value.replace(/\s/g,'') == ""||field.value == null) return false;
	}; 
};