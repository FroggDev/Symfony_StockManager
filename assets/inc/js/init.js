/**
 * Init main JS container
 */
document.app={};
document.app.Product={};

/**
 * Init Materialize elements
 */
document.app.tooltips       = M.Tooltip.init(document.querySelectorAll('.tooltipped'));
document.app.parallax       = M.Parallax.init(document.querySelectorAll('.parallax'));
document.app.sidenav        = M.Sidenav.init(document.querySelector('.sidenav'),{ edge:'right'});
document.app.loader         = M.Modal.init(document.querySelector('#modal-loader'));
document.app.dropdowns      = M.Dropdown.init(document.querySelectorAll('.dropdown-trigger'),{constrainWidth: false});
document.app.collapsibles   = M.Collapsible.init(document.querySelectorAll('.collapsible'));
document.app.imagebox       = M.Materialbox.init(document.querySelectorAll('.materialboxed'));
document.app.datepicker     = M.Datepicker.init(document.querySelector('.datepicker'));
document.app.formselect     = M.FormSelect.init(document.querySelectorAll('select'));

document.app.toastResult    = null;

/**
 * Extra functions
 */

document.app.Util = {

    addListener : function(element, eventName, handler) {
        if (element.addEventListener) {
            element.addEventListener(eventName, handler, false);
        }
        else if (element.attachEvent) {
            element.attachEvent('on' + eventName, handler);
        }
        else {
            element['on' + eventName] = handler;
        }
    },


    removeListener : function(element, eventName, handler) {
        if (element.addEventListener) {
            element.removeEventListener(eventName, handler, false);
        }
        else if (element.detachEvent) {
            element.detachEvent('on' + eventName, handler);
        }
        else {
            element['on' + eventName] = null;
        }
    },


    addListenerAll : function(elements, eventName, handler) {

        for (btn in elements){
            this.addListener(elements[btn], eventName, handler)
        }

    },

    removeListenerAll : function(elements, eventName, handler) {
        for (btn in elements){
            this.removeListener(elements[btn], eventName, handler)
        }
    },

    preventPropagation : function(evt){
        evt.stopPropagation();
    },

    doAjax : function(url, data , callback,method='GET'){
        M.Toast.dismissAll();
        new Ajax(url,callback, { method: method ,param : data } , true);
    }
};