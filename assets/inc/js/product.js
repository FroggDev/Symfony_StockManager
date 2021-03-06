/**
 * PRODUCT MANAGER
 */

document.app.Product = {

    lastAdded:null,

    lastRemoved:null,

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

        //set last added element
        document.app.Product.lastAdded = data;

        document.app.toastResult = M.toast({
            html: data.nbproductfield + ' x ' + data.name + '<button class="btn-flat toast-action" onclick="document.app.Util.doAjax(document.app.url.cancelAddToStock,document.app.Product.lastAdded,document.app.Product.cancelAddToStock);">CANCEL</button>',
            displayLength: 7000,
            classes: 'green'
        });
    },

    cancelAddToStock: function (data) {
        document.app.toastResult = M.toast({
            html: data.nbproductfield + ' x ' + data.name + ' <br> has been canceled',
            displayLength: 7000,
            classes: 'orange accent-2'
        });
    },

    removeFromStock: function (data) {

        //Convert to form data
        var form_data = new FormData();
        form_data.append('data', JSON.stringify(data));

        //set last added element
        document.app.Product.lastRemoved = form_data;

        document.app.toastResult = M.toast({
            html: 'removed ' + data.name + '<button class="btn-flat toast-action" onclick="document.app.Util.doAjax(document.app.url.cancelRemoveFromStock,document.app.Product.lastRemoved ,document.app.Product.cancelRemoveFormStock,\'POST\');">CANCEL</button>',
            displayLength: 7000,
            classes: 'green',
            completeCallback: function () {
                document.app.Product.cancelCurrent.remove()
            }
        });
    },

    cancelRemoveFormStock: function (data) {

        document.app.Product.cancelCurrent.cancel(data);

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

        cancel: function (data) {

            //data = JSON.parse(data);

            i=0;

            //restore datas
            this.obj.innerHTML = this.nbProduct;

            //restore date
            if(this.selectExpire) {
            for (expire in this.expires) {
                    this.selectExpire.insertAdjacentHTML('afterbegin', '<option value="' + data['ids'][i] + '">' + this.expires[expire] + '</option>');
                i++;
                }
            }

            //reset selection
            this.selectExpire.selectedIndex = 0;

            // RESTORE DISPLAY
            this.obj.parentNode.parentNode.parentNode.className='';
            document.querySelector('UL LI.empty').setAttribute('class','empty hide');

            //refresh display
            document.app.Product.list.init();

            //reset current obj
            this.obj = null;
            this.nbProduct = null;
            this.expires = null;
        },

        remove: function () {
            //if no more product left, remove the li product after cancel toast time

            // REMOVE DISPLAY
            if (this.obj && parseInt(this.obj.innerHTML)<=0) {
                //this.obj.parentNode.parentNode.parentNode.parentNode.removeChild(this.obj.parentNode.parentNode.parentNode);
                this.obj.parentNode.parentNode.parentNode.className='hide';
            }

            // If no more li in the page, reload if has pagination, else display message it is empty
            if(document.querySelectorAll('UL.collapsible.bgItems > LI:not(.hide)').length===0){
                if(document.querySelector('.pagination')) {
                    /**
                     * TODO Redirect to first page of pagination
                     */
                }
                else{
                    document.querySelector('UL LI.empty').setAttribute('class','empty');
                }
            }
        }
    },


    removeProduct: function (evt) {

        /**
         * TODO : THIS ONE CAN BE CLEANED
         */

        // Prevent collapse to trigger
        document.app.Util.preventPropagation(evt);

        var nbProductObj = this.parentNode.parentNode.querySelector('.nbProduct');
        var nbProduct = parseInt(nbProductObj.innerHTML);
        var inputExpire = this.parentNode.parentNode.querySelector('.select-dropdown');
        var selectExpire = this.parentNode.parentNode.querySelector('select');

        var expireDates = inputExpire.value.split(", ").filter(function (val) {
            return val !== ""
        });

        var ids = [];

        console.log(this.parentNode.parentNode.querySelector('h4 .nbProduct').innerHTML + this.parentNode.parentNode.querySelector('h4 .name').innerHTML);

        if (nbProduct > 0 && expireDates.length > 0) {

            if(selectExpire){
                for (date in expireDates) {

                    select = selectExpire.querySelectorAll('option');

                    for(option in select){
                        if(select[option].text===expireDates[date]){
                            ids.push(select[option].value);
                            selectExpire.remove(select[option].index);
                            break;
                        }
                    }

                    //selectExpire.remove(selectExpire.querySelector('option[value="' + val + '"]').index);
                }

                //set selected
                selectExpire.querySelector('option').selected = true;
            }

            document.app.Product.cancelCurrent.set(nbProductObj, nbProduct, expireDates, selectExpire);

            // Update display of number of product left (VERY IMPORTANT)
            nbProductObj.innerHTML = (parseInt(nbProduct) - expireDates.length );

            //refresh display
            document.app.Product.list.init();

            document.app.Util.doAjax(document.app.url.removeFromStock, {
                ids: ids,
                name : expireDates.length + this.parentNode.parentNode.querySelector('h4 .name').innerHTML
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
