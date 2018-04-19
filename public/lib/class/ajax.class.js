/* **** */
/* AJAX */

/* **** */
function Ajax(url, callback, opt, isloading, onprogress) {
    this.init(url, callback, opt, isloading, onprogress);
}

Ajax.prototype =
    {
        url: null,
        xhr: null,
        method: 'GET',
        async: true,
        param: '',
        isloading: false,

        init: function (url, callback, opt, isloading, onprogress) {
            this.isloading = isloading;

            //Set vars
            this.url = url;
            if (opt) {
                if (opt.method) this.method = opt.method;
                if (opt.async) this.async = opt.async;
                if (opt.param && this.method === 'GET') {
                    this.param = opt.param;
                    this.url += '?' + Object.entries(opt.param).map(e => e.join('=')).join('&');
                }
            }

            if (this.isloading) document.app.loader.open();

            //Get Xhr obj
            this.getXhr();
            this.xhr.callback = callback;
            //Get request status
            this.xhr.onreadystatechange = this.readyState;

            //send the request
            this.xhr.open(this.method, this.url, this.async);

            this.xhr.upload.onprogress = onprogress;

            this.xhr.send(this.method === 'GET' ? '' : opt.param);
        },

        getXhr: function () {
            var xhr, versions, i = len = 0;

            if (typeof XMLHttpRequest !== 'undefined')
                xhr = new XMLHttpRequest();
            else {
                versions = ["MSXML2.XmlHttp.5.0",
                    "MSXML2.XmlHttp.4.0",
                    "MSXML2.XmlHttp.3.0",
                    "MSXML2.XmlHttp.2.0",
                    "Microsoft.XmlHttp"];

                for (len = versions.length; i < len; i++) {
                    try {
                        xhr = new ActiveXObject(versions[i]);
                        break;
                    }
                    catch (e) {
                    }
                } // end for
            }
            //set found xhr
            this.xhr = xhr;
        },

        readyState: function () {
            switch (true) {
                //loading
                case this.readyState < 4 :
                    return;
                    break;
                //all is ok
                case this.readyState === 4 :

                    data = JSON.parse(this.responseText);

                    if (data.result === "ok") {
                        this.callback(data);
                    }
                    else {
                        alert("Data result not ok !");
                        console.log('TODO : MANAGE ERROR HERE ??');
                    }

                    break;
                default:
                    alert("An error occured while proccessing an XHR connexion, script has stopped, you can contact site owner or check your web console for more informations.");
                    console.log(this);
                    break;
            }
            if (document.app.loader) document.app.loader.close();
        }
    }