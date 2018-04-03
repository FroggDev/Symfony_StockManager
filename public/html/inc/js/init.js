/**
 * Init main JS container
 */
document.app={};
document.app.product={};

/**
 * Init Materialize elements
 */
document.app.tooltips       = M.Tooltip.init(document.querySelectorAll('.tooltipped'));
document.app.parallax       = M.Parallax.init(document.querySelectorAll('.parallax'));
document.app.sidenav        = M.Sidenav.init(document.querySelector('.sidenav'),{ edge:'right'});
document.app.loader         = M.Modal.init(document.querySelector('#modal-loader'));
document.app.dropdowns      = M.Dropdown.init(document.querySelectorAll('.dropdown-trigger'));
document.app.collapsibles   = M.Collapsible.init(document.querySelectorAll('.collapsible'));
document.app.toastResult    = null;
document.app.imagebox       = M.Materialbox.init(document.querySelectorAll('.materialboxed'));

/**
 * Extra functions
 */

document.app.addListener = function(element, eventName, handler) {
    if (element.addEventListener) {
        element.addEventListener(eventName, handler, false);
    }
    else if (element.attachEvent) {
        element.attachEvent('on' + eventName, handler);
    }
    else {
        element['on' + eventName] = handler;
    }
};

document.app.removeListener = function(element, eventName, handler) {
    if (element.addEventListener) {
        element.removeEventListener(eventName, handler, false);
    }
    else if (element.detachEvent) {
        element.detachEvent('on' + eventName, handler);
    }
    else {
        element['on' + eventName] = null;
    }
};

document.app.addListenerAll = function(elements, eventName, handler) {

    for (btn in elements){
        this.addListener(elements[btn], eventName, handler)
    }

};

document.app.removeListenerAll = function(elements, eventName, handler) {
    for (btn in elements){
        this.removeListener(elements[btn], eventName, handler)
    }
};

document.app.preventPropagation = function(evt){
    evt.stopPropagation();
};

document.app.doAjax=function(url, data , callback,isloading){
    M.Toast.dismissAll();
    new Ajax(url,callback, { param : data } , isloading);
};
