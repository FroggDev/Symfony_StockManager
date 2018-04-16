/**
 * PRODUCT MANAGER
 */

document.app.Product = {

    addToStockEvent:function(){

        var addProductObj = document.querySelector('#addproduct');
        var removeProductObj = document.querySelector('#removeproduct');
        var objNbproductfield = document.querySelector('#nbproductfield');

        if(addProductObj){
            /**
             * +1 to product
             */
            document.app.Util.addListener(addProductObj,'click',function(){
            var  objNbproduct = document.querySelector('#nbproduct');
            objNbproductfield.value = parseInt(objNbproductfield.value)+1;
            objNbproduct.innerHTML = objNbproductfield.value;
            });
        }

        if(removeProductObj){
            /**
             * -1 to product
             */
            document.app.Util.addListener(removeProductObj,'click',function(){
                var objNbproduct = document.querySelector('#nbproduct');
                if(objNbproductfield.value>1){
                    objNbproductfield.value=objNbproductfield.value-1;
                    objNbproduct.innerHTML = objNbproductfield.value;
                }
            });
        }
    },

    addToStock: function (data) {
        //Reset form
        document.querySelector('#expire').value="";
        document.querySelector('#nbproduct').innerHTML=1;
        document.querySelector('#nbproductfield').value=1;

        document.app.toastResult = M.toast({
            html: data.qte + ' x ' + data.name + '<button class="btn-flat toast-action" onclick="document.app.Util.doAjax(document.app.url.cancelAddToStock,{ id: ' + data.id + ' },document.app.Product.cancelAddToStock);">CANCEL</button>',
            displayLength: 7000,
            classes: 'green'
        });
    },

    cancelAddToStock: function (data) {
        document.app.toastResult = M.toast({
            html: data.qte + ' x ' + data.name + ' <br> has been canceled',
            displayLength: 7000,
            classes: 'orange accent-2'
        });
    },

    removeFromStock: function (data) {
        document.app.toastResult = M.toast({
            html: 'removed ' + data.name + '<button class="btn-flat toast-action" onclick="document.app.Util.doAjax(document.app.url.cancelRemoveFromStock,{ id: ' + data.id + ' },document.app.Product.cancelRemoveFormStock);">CANCEL</button>',
            displayLength: 7000,
            classes: 'green',
            completeCallback: function () {
                document.app.Product.cancelCurrent.remove()
            }
        });
    },

    cancelRemoveFormStock: function (data) {

        document.app.Product.cancelCurrent.cancel();

        document.app.toastResult = M.toast({
            html: 'Removed ' + data.name + ' <br> has been canceled',
            displayLength: 7000,
            classes: 'orange accent-2'
        });
    },

    cancelCurrent: {

        obj: null,

        nbProduct: null,

        expires: null,

        selectExpire: null,

        set: function (obj, nbProduct, expires, selectExpire) {
            this.obj = obj;
            this.nbProduct = nbProduct;
            this.expires = expires.reverse();
            this.selectExpire = selectExpire;
        },

        cancel: function () {
            //restore datas
            this.obj.innerHTML = this.nbProduct;

            //restore date
            if(this.selectExpire) {
            for (expire in this.expires) {
                    this.selectExpire.insertAdjacentHTML('afterbegin', '<option value="' + this.expires[expire] + '">' + this.expires[expire] + '</option>');
                }
            }

            //refresh display
            document.app.Product.list.init();

            //reset current obj
            this.obj = null;
            this.nbProduct = null;
            this.expires = null;
        },

        remove: function () {
            //if no more product left, remove the li product after cancel toast time
            if (this.obj && parseInt(this.obj.innerHTML)<=0) {
                this.obj.parentNode.parentNode.parentNode.parentNode.removeChild(this.obj.parentNode.parentNode.parentNode);
            }
        }
    },


    removeProduct: function (evt) {

        // Prevent collapse to trigger
        document.app.Util.preventPropagation(evt);

        var nbProductObj = this.parentNode.parentNode.querySelector('.nbProduct');
        var nbProduct = parseInt(nbProductObj.innerHTML);
        var inputExpire = this.parentNode.parentNode.querySelector('.select-dropdown');
        var selectExpire = this.parentNode.parentNode.querySelector('select');

        var expireDates = inputExpire.value.split(", ").filter(function (val) {
            return val !== ""
        });

        if (nbProduct > 0 && expireDates.length > 0) {

            if(selectExpire){
                for (date in expireDates) {
                    selectExpire.remove(selectExpire.querySelector('option[value="' + expireDates[date] + '"]').index);
                }

                //set selected
                selectExpire.querySelector('option').selected = true;
            }

            document.app.Product.cancelCurrent.set(nbProductObj, nbProduct, expireDates, selectExpire);

            nbProductObj.innerHTML = (parseInt(nbProduct) + (expireDates.length * -1));

            //refresh display
            document.app.Product.list.init();

            document.app.Util.doAjax(document.app.url.removeFromStock, {
                barcode: this.parentNode.parentNode.querySelector('.barcode').value,
                expire: expireDates
            }, document.app.Product.removeFromStock);
        }
    },


    list: {

        init: function () {
            this.initEvents();
        },

        initEvents: function(){
            document.app.Util.addListenerAll(document.querySelectorAll('.collapsible .actionbtn .btn-floating'), 'click', document.app.Util.preventPropagation);
            document.app.Util.addListenerAll(document.querySelectorAll('.collapsible .actionbtn .btn-floating.btnRemove'), 'click', document.app.Product.removeProduct);
            this.initSelects();
        },

        initSelects: function () {
            /**
             * Init selects
             */
            M.FormSelect.init(document.querySelectorAll('select'));
            /**
             * prevent slidown
             */
            document.app.Util.addListenerAll(document.querySelectorAll('.select-dropdown'), 'click', document.app.Util.preventPropagation);
            /**
             * close option by click
             */
            var closeOptions = document.querySelectorAll('.dropdown-content LI.disabled');
            document.app.Util.addListenerAll(closeOptions, 'click', this.closeSelect);
        },


        closeSelect: function (evt) {
            document.app.Product.list.init();
        }
    }
};

document.app.Product.list.init();
document.app.Product.addToStockEvent();
