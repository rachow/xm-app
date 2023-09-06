/**
 *  @author: $rachow
 *  @copyright: XM App
 *
 *  Apps bundled JS
 */

window.object_lastPoll = [];
window.store = {};
window.delay = ms => new Promise(res => setTimeout(res, ms));
window.xm_loader = '<div class="spinner-border mt-2 mb-2" role="status">';
window.xm_loader += '<span class="sr-only">Loading...</span>';
window.xm_loader += '</div>';
window.btn_loader = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

/**
 * returns plain object
*/
window.serialize = (data) => {
	let obj = {};
	for (let [key, value] of data) {
		if (obj[key] !== undefined) {
			if (!Array.isArray(obj[key])) {
				obj[key] = [obj[key]];
			}
			obj[key].push(value);
		} else {
			obj[key] = value;
		}
	}
	return obj;
};

/**
 * wrapper to control logging
*/
window.log = (d, type) => {	
	if (d.constructor == Array || d.constructor == Object) {
		d = JSON.stringify(d);
	}
	if (type == undefined) {
		type = 'info';
	}
	console.log(type + ':' + d);

    // additionally can fire events asynchronously.
};

/**
 * grab debug data formatted for DOM
*/
window.dd = (obj) => {
	let d = obj;
	if (typeof obj == "object" || typeof obj == "array") {
		d = JSON.stringify(obj, null, '\t');
    }
	return '<small style="word-break: break-all">' + d + '<small>';
};

window.hasStorage = (type) => {
	let storage = null;
	//: mozilla.org using WebAPI
	if (type == undefined || type == "") {
		type = 'localStorage';
	}

	try {
	    storage = window[type];
	    let item = '__VOODOO__';
	    storage.setItem(item, item);
	    storage.removeItem(item, item);
	    return true;
	} catch(ex) {
	    return ex instanceof DOMException && (
	    	ex.code === 22 || // everything except Firefox
		ex.code === 1014 || // Firefox
		ex.name === 'QuotaExceedError' ||
		ex.name === 'NS_ERROR_DOM_QUOTA_REACHED') && 
		(storage && storage.length !== 0);
	}
};

/**
 * get the browsers storage api
 */
window.getStoreObj = () => {
	let store = null;
    if (hasStorage() == true) {	
        store = window.localStorage;
		return store;
	}
	log('localstorage not supported.', 'error');
	return null;
};

/**
 * grab from the localStorage.
*/
window.getStore = (key) => {
	let store = getStoreObj();
	if (store !== null) {
		return store.getItem(key);
	}
	return false;
};

/**
 * clear everything in localStorage.
*/
window.clearStore = () => {
	let store = getStoreObj();
	if (store !== null) {
		store.clear();
	}
};

/**
 * get store key from localStorage.
*/
window.getStoreKey = (indx) => {
	let store = getStoreObj();
	if (store !== null) {
		return store.key(indx);
	}
	return false;
};

/**
 * add to the localStorage.
*/
window.addStore = (key, item) => {
	let store = getStoreObj();
	if (store !== null) {
		store.setItem(key, item);
		return true;
	}
	return false;
};

/**
 * removing from localStorage helper.
*/
window,removeStore = (item) => {
	let store = getStoreObj();
	if (store !== null) {
		store.removeItem(item);
		return true;
	}
	return false;
};


/**
 * register onload routines and actions.
*/
$(function(){

    //

});

