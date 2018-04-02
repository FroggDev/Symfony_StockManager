/**
 * Init main JS container
 */
document.app={};
document.app.product={}

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
}

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
}

document.app.addListenerAll = function(elements, eventName, handler) {

    for (btn in elements){
        this.addListener(elements[btn], eventName, handler)
    }

}

document.app.removeListenerAll = function(elements, eventName, handler) {
    for (btn in elements){
        this.removeListener(elements[btn], eventName, handler)
    }
}

document.app.preventPropagation = function(evt){
    evt.stopPropagation();
}



document.app.doAjax=function(url, data , callback,isloading){

    M.Toast.dismissAll();

    if(isloading) document.app.loader.open();

    $.ajax({
        method: "POST",
        url: url,
        data: data
    })
        .done(function(data) {

            console.log("TODO : MANAGE ERROR HERE");

            console.log(data);

            try {
                data = JSON.parse(data);

                if (data.result === "ok") {
                    callback(data);
                }
                else {alert( "error : else" );}
            }
            catch(e) {
                alert("error : catch");
                console.log(e);
            }
        })
        .fail(function() {alert( "error : fail" );})
        .always(function() {if(isloading) document.app.loader.close();});
}

document.app.product.addToStock=function(data){
    document.app.Scan.init();
    document.app.toastResult = M.toast({
        html: data.qte+' x '+ data.name + '<button class="btn-flat toast-action" onclick="document.app.doAjax(\'canceladdtostock.html\',{ id: '+data.id+' },document.app.product.cancelAddToStock);">CANCEL</button>',
        displayLength : 7000,
        classes:'green'
    });
}

document.app.product.cancelAddToStock=function(data) {
    document.app.toastResult = M.toast({
        html: data.qte + ' x ' + data.name + ' <br> has been canceled',
        displayLength: 7000,
        classes: 'orange accent-2'
    });
}

document.app.product.removeFromStock=function(data){
    document.app.toastResult = M.toast({
        html: 'removed '+ data.name + '<button class="btn-flat toast-action" onclick="document.app.doAjax(\'canceladdtostock.html\',{ id: '+data.id+' },document.app.product.cancelRemoveFormStock);">CANCEL</button>',
        displayLength : 7000,
        classes:'green',
        completeCallback: function(){
            document.app.product.cancelCurrent.remove(data.qte)
        }
    });
}

document.app.product.cancelRemoveFormStock=function(data) {

    document.app.product.cancelCurrent.reset();

    document.app.toastResult = M.toast({
        html: 'Removed ' +  data.name + ' <br> has been canceled',
        displayLength: 7000,
        classes: 'orange accent-2'
    });
}

document.app.product.cancelCurrent= {

    obj:null,

    nbProduct:null,

    set:function(obj,nbProduct)
    {
        this.obj = obj;
        this.nbProduct = nbProduct;
    },

    reset:function()
    {
        //restore datas
        this.obj.innerHTML = this.nbProduct;

        //reset current obj
        this.obj = null;
        this.nbProduct = null;
    },

    remove:function(qte)
    {
        //if no more product left, remove the li product after cancel toast time
        if(qte===0 && this.obj){
            this.obj.parentNode.parentNode.parentNode.parentNode.removeChild(this.obj.parentNode.parentNode.parentNode);
        }
    }
}