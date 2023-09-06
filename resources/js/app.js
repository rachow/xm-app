require('./bootstrap');

window.baseURL = process.env.MIX_APP_URL;

/**
 * icon set for fancy console.
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

/**
 * restrict embedding to iframe
 * @2023
*/
if(window.top != window.self){
	top.location.href = document.location.href;
}

