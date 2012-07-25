/* 
 * Fills all password fields named "password" with the password cookie
 */
window.onload = function() {
	var cookies = document.cookie.split(';');
	for(var i = 0; i <= cookies.length; i++) {
		var a = cookies[i].split('=');
		if(a[0] == 'password') {
			inputs = document.getElementsByName('password');
			for(var n = 0; n <= inputs.length; n++) {
				inputs[n].value = a[1];
			}
		}
	}
};