window._ = require('lodash');

try {
    require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
window.axios.defaults.baseURL = '/ajax';

/**
 * solving many things here.
*/
window.axios.interceptors.response.use((response) => {
	return response;
}, (error) => {
	if (error.response) {
		let http_code = error.response.status;
		if( http_code === 401) {
			let err = 'Your session has expired.';
			let paths = window.location.pathname.split("/");
	
			if (paths[1] != "login") {
				console.log('error:'+err);
				if($('#modal-info-alert').length){
					$('.modal-info-text').html(err + '<br> You must login.');
					$('#modal-info-alert').modal('show');
					$('#modal-info-alert').find('button').bind("click", function(e){
						
						//window.location.href = '/login';	
						window.location.reload(); /* reload will maintain the redirect back */
					});
				} else {
					alert(err);
					window.location.reload();
				}
			}

		} else if (http_code == 500) {
			let err = 'We apologies for the error.';
			console.log('error: '+err);
		
			// TODO: add to Bugsnag so engineers are aware.
			let err_body = error.response.data;
			//Bugsnag.notify(error);

			if ($('#modal-error-alert').length) {
				$('.modal-error-text').html(err + '<br>Our engineers are taking a look.');
				$('#modal-error-alert').modal('show');
			} else {
				alert(err + "\r\nOur engineers are taking a look.");
			}
		}
	}
	return Promise.reject(error);
});


