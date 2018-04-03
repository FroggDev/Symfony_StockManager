/**
 * PRODUCT MANAGER
 */

document.app.product = {

    addToStock: function (data) {
        document.app.Scan.init();
        document.app.toastResult = M.toast({
            html: data.qte + ' x ' + data.name + '<button class="btn-flat toast-action" onclick="document.app.doAjax(\'canceladdtostock.html\',{ id: ' + data.id + ' },document.app.product.cancelAddToStock);">CANCEL</button>',
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
            html: 'removed ' + data.name + '<button class="btn-flat toast-action" onclick="document.app.doAjax(\'canceladdtostock.html\',{ id: ' + data.id + ' },document.app.product.cancelRemoveFormStock);">CANCEL</button>',
            displayLength: 7000,
            classes: 'green',
            completeCallback: function () {
                document.app.product.cancelCurrent.remove(data.qte)
            }
        });
    },

    cancelRemoveFormStock: function (data) {

        document.app.product.cancelCurrent.cancel();

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
            for (expire in this.expires) {
                this.selectExpire.insertAdjacentHTML('afterbegin', '<option value="' + this.expires[expire] + '">' + this.expires[expire] + '</option>');
            }

            //refresh display
            document.app.product.list.init();

            //reset current obj
            this.obj = null;
            this.nbProduct = null;
            this.expires = null;
        },

        remove: function (qte) {
            //if no more product left, remove the li product after cancel toast time
            if (qte === 0 && this.obj) {
                this.obj.parentNode.parentNode.parentNode.parentNode.removeChild(this.obj.parentNode.parentNode.parentNode);
            }
        }
    },


    removeProduct: function (evt) {

        // Prevent collapse to trigger
        document.app.preventPropagation(evt);

        var nbProductObj = this.parentNode.parentNode.querySelector('.nbProduct');
        var nbProduct = parseInt(nbProductObj.innerHTML);
        var inputExpire = this.parentNode.parentNode.querySelector('.select-dropdown');
        var selectExpire = this.parentNode.parentNode.querySelector('select');
        var expireDates = inputExpire.value.split(", ").filter(function (val) {
            return val !== ""
        });

        if (nbProduct > 0 && expireDates.length > 0) {

            for (date in expireDates) {
                selectExpire.remove(selectExpire.querySelector('option[value="' + expireDates[date] + '"]').index);
            }

            //set selected
            selectExpire.querySelector('option').selected = "selected";

            document.app.product.cancelCurrent.set(nbProductObj, nbProduct, expireDates, selectExpire);

            nbProductObj.innerHTML = parseInt(nbProduct) + (expireDates.length * -1);

            //refresh display
            document.app.product.list.init();

            document.app.doAjax("removefromstock.html", {
                barcode: this.parentNode.parentNode.querySelector('.barcode').value,
                expire: expireDates
            }, document.app.product.removeFromStock);
        }
    },


    list: {

        init: function () {
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
            document.app.addListenerAll(document.querySelectorAll('.select-dropdown'), 'click', document.app.preventPropagation);
            /**
             * close option by click
             */
            var closeOptions = document.querySelectorAll('.dropdown-content LI.disabled');
            document.app.addListenerAll(closeOptions, 'click', this.closeSelect);
        },


        closeSelect: function (evt) {
            console.log('TODO : FIND BETTER AND CLEANER WAY HERE');
            document.app.product.list.init();
        }
    }
}
