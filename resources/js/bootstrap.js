window._ = require('lodash');

try {
    require('bootstrap');
} catch (e) {}

window.baseURL = process.env.MIX_APP_URL;

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';


/**
 * Icon set for fancy console logging.
*/

window.mad = String.fromCodePoint(0x1F621);
window.scream = String.fromCodePoint(0x1F631);
window.wave = String.fromCodePoint(0x1F44B);
window.bug = String.fromCodePoint(0x1F41E);
window.flame = String.fromCodePoint(0x1F525);
window.wink = String.fromCodePoint(0x1F609);
window.robot = String.fromCodePoint(0x1F916);
window.points = String.fromCodePoint(0x1F4AF);
window.spark = String.fromCodePoint(0x1F4A5);
window.dizzy = String.fromCodePoint(0x1F4AB);
window.bomb = String.fromCodePoint(0x1F4A3);
window.rocket = String.fromCodePoint(0x1F680);


// load bug notifier
Bugsnag.start({
	apiKey: process.env.MIX_BUGSNAG_API_KEY,
	appVersion: process.env.MIX_APP_VERSION //  app release version
});

/**
 * restrict iframe - other options?
 * @2023
*/
if(window.top != window.self){
	top.location.href = document.location.href;
}

