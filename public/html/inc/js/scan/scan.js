document.app.Scan = {
	
	init: function() {
		var self = this;

		this.initElements();

        this.objResult.hide();

        this.objNotfound.hide();

        this.objToHideWhenOff.show();

		Quagga.init(this.defaultValues, function(err) {
			if (err) return self.handleError(err);
			self.initCameraSelection();
			document.app.Scan.attachListeners();
			document.app.Scan.checkCapabilities();				
			Quagga.start();	
		});

        Quagga.onProcessed(function(result) {
            var drawingCtx = Quagga.canvas.ctx.overlay,
                drawingCanvas = Quagga.canvas.dom.overlay;

            if (result) {
                if (result.boxes) {
                    drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")), parseInt(drawingCanvas.getAttribute("height")));
                    result.boxes.filter(function (box) {
                        return box !== result.box;
                    }).forEach(function (box) {
                        Quagga.ImageDebug.drawPath(box, {x: 0, y: 1}, drawingCtx, {color: "green", lineWidth: 2});
                    });
                }

                if (result.box) {
                    Quagga.ImageDebug.drawPath(result.box, {x: 0, y: 1}, drawingCtx, {color: "#00F", lineWidth: 2});
                }

                if (result.codeResult && result.codeResult.code) {
                    Quagga.ImageDebug.drawPath(result.line, {x: 'x', y: 'y'}, drawingCtx, {color: 'red', lineWidth: 3});
                }
            }
        });

        Quagga.onDetected(function(result) {
            //var canvas = Quagga.canvas.dom.image;
            document.app.Scan.closeScan();
            document.app.Scan.doAjax("getProduct.html",{ barcode: result.codeResult.code },document.app.Scan.displayResult);
        });

	},
	
	defaultValues : {
		inputStream: {			
			type : "LiveStream",
			constraints: {
				width		: {min: parseInt(document.querySelector('select[name="input-stream_constraints"]').value.split('x')[0])},
				height		: {min: parseInt(document.querySelector('select[name="input-stream_constraints"]').value.split('x')[1])},
				facingMode	: "environment",
				aspectRatio	: {min: 1, max: 2}
			}
		},
		numOfWorkers: 0,
		frequency: 10,
		decoder: {
			readers : [{
				format: 'ean_reader',
				config: {}
			}]
		},
		locate: true
	},

	initElements:function(){

		console.log('TODO : If cannot torch hide button');

        this.objControlsForm = $(".controls FORM");
        this.objExpire = $('#expire');
        this.objToHideWhenOff = $('.toHideWhenOff');
        this.objBarcode = $('#barcode');
        this.objResult = $('#result');
        this.objNotfound = $('#notfound');
        this.objCameraTorch = $("#cameraTorch");
        this.objControls = $(".controls .reader-config-group");
        this.objAddproduct = $('#addproduct');
        this.objRemoveproduct = $('#removeproduct');
        this.objNbproductfield = $('#nbproductfield');
	},

	attachListeners: function() {
		var self = this;
		
		/*
		 * Click on Torch Button
		 * => Toggle mobile Torch
		 */
        this.objCameraTorch.on("click",function(e) {
			e.preventDefault();
			var obj = $(this);
			self.applySetting('torch',obj.hasClass('off'));
            obj.toggleClass('off');
		});
		
		/*
		 * barre code detection and mobile option form 
		 */	
		this.objControls.on("change", "input, select", function(e) {
			e.preventDefault();
			var $target = $(e.target),
				value = $target.attr("type") === "checkbox" ? $target.prop("checked") : $target.val(),
				name = $target.attr("name"),
				state = self._convertNameToState(name);

			//console.log("Value of "+ state + " changed to " + value);
			self.setState(state, value);
		});

        /**
		 * +1 to product
         */
        this.objAddproduct.off('click').on('click',function(){
            var  objNbproduct = $('#nbproduct');
            self.objNbproductfield.val(parseInt(self.objNbproductfield.val())+1);
            objNbproduct.html(self.objNbproductfield.val());
        });

        /**
         * -1 to product
         */
        this.objRemoveproduct.off('click').on('click',function(){
            var objNbproduct = $('#nbproduct');
            if(self.objNbproductfield.val()>1){
                self.objNbproductfield.val(self.objNbproductfield.val()-1);
                objNbproduct.html(self.objNbproductfield.val());
            }
        });

        /**
		 * date picker
         */
        document.app.Scan.expireDatePicker= M.Datepicker.init(document.querySelector('.datepicker'),{format: 'dd/mm/yyyy'});

        console.log('TODO : CALENDAR TRANSLATION');

	},		
	
	closeScan:function(){
		Quagga.stop();
        this.objToHideWhenOff.hide();
		this.objControlsForm[0].reset();
        this.objNbproductfield.val(1);
        this.objBarcode.val('');
        this.objExpire.val('');
        document.app.Scan.detachListeners();
	},
	
	handleError: function(err) {
		console.log(err);
	},
	
	checkCapabilities: function() {
		var track = Quagga.CameraAccess.getActiveTrack();
		var capabilities = {};
		if (typeof track.getCapabilities === 'function') {
			capabilities = track.getCapabilities();
		}
		this.applySettingsVisibility('zoom', capabilities.zoom);
		this.applySettingsVisibility('torch', capabilities.torch);
	},
	
	updateOptionsForMediaRange: function(node, range) {
		console.log('updateOptionsForMediaRange', node, range);
		var NUM_STEPS = 6;
		var stepSize = (range.max - range.min) / NUM_STEPS;
		var option;
		var value;
		while (node.firstChild) {
			node.removeChild(node.firstChild);
		}
		for (var i = 0; i <= NUM_STEPS; i++) {
			value = range.min + (stepSize * i);
			option = document.createElement('option');
			option.value = value;
			option.innerHTML = value;
			node.appendChild(option);
		}
	},
	
	applySettingsVisibility: function(setting, capability) {

		var node= document.querySelector('input[name="settings_' + setting + '"]'),
			btn	= document.querySelector('button.' + setting );			
		
		// depending on type of capability
		if (typeof capability === 'boolean') {
			if (node) node.parentNode.style.display = capability ? 'block' : 'none';
			if (btn)  btn.style.display = capability ? 'block' : 'none';				
			return;
		}
		if (window.MediaSettingsRange && capability instanceof window.MediaSettingsRange) {
			//var node = document.querySelector('select[name="settings_' + setting + '"]');
			if (node) {
				this.updateOptionsForMediaRange(node, capability);
				node.parentNode.style.display = 'block';
			}
			if (btn) btn.style.display = 'block';
			//return;
		}
	},
	
	initCameraSelection: function(){
		var streamLabel = Quagga.CameraAccess.getActiveStreamLabel();

		return Quagga.CameraAccess.enumerateVideoDevices()
		.then(function(devices) {
			function pruneText(text) {
				return text.length > 30 ? text.substr(0, 30) : text;
			}
			var $deviceSelection = document.getElementById("deviceSelection");
			while ($deviceSelection.firstChild) {
				$deviceSelection.removeChild($deviceSelection.firstChild);
			}

			devices.forEach(function(device) {
				var $option = document.createElement("option");
				$option.value = device.deviceId || device.id;
				$option.appendChild(document.createTextNode(pruneText(device.label || device.deviceId || device.id)));
				$option.selected = streamLabel === device.label ? 'selected' : false;
				$deviceSelection.appendChild($option);
			});

            M.FormSelect.init(document.querySelector('#deviceSelection'));

		});
	},
	
	_accessByPath: function(obj, path, val) {
		var parts = path.split('.'),
			depth = parts.length,
			setter = (typeof val !== "undefined");

		return parts.reduce(function(o, key, i) {
			if (setter && (i + 1) === depth) {
				if (typeof o[key] === "object" && typeof val === "object") {
					Object.assign(o[key], val);
				} else {
					o[key] = val;
				}
			}
			return key in o ? o[key] : {};
		}, obj);
	},
	
	_convertNameToState: function(name) {
		return name.replace("_", ".").split("-").reduce(function(result, value) {
			return result + value.charAt(0).toUpperCase() + value.substring(1);
		});
	},
	
	detachListeners: function() {

        this.objControls.off("change");

        this.objCameraTorch.off("click");

	},
	
	applySetting: function(setting, value) {
		var track = Quagga.CameraAccess.getActiveTrack();
		if (track && typeof track.getCapabilities === 'function') {
			switch (setting) {
			case 'zoom':
				return track.applyConstraints({advanced: [{zoom: parseFloat(value)}]});
			case 'torch':
				return track.applyConstraints({advanced: [{torch: !!value}]});
			}
		}
	},

	setState: function(path, value) {
		var self = this;

		if (typeof self._accessByPath(self.inputMapper, path) === "function") {
			value = self._accessByPath(self.inputMapper, path)(value);
		}

		if (path.startsWith('settings.')) {
			var setting = path.substring(9);
			return self.applySetting(setting, value);
		}
		self._accessByPath(self.state, path, value);

		document.app.Scan.detachListeners();
		Quagga.stop();
		document.app.Scan.init();
	},
	
	inputMapper: {
		inputStream: {
			constraints: function(value){
				if (/^(\d+)x(\d+)$/.test(value)) {
					var values = value.split('x');
					return {
						width: {min: parseInt(values[0])},
						height: {min: parseInt(values[1])}
					};
				}
				return {
					deviceId: value
				};
			}
		},
		numOfWorkers: function(value) {
			return parseInt(value);
		},
		decoder: {
			readers: function(value) {
				if (value === 'ean_extended') {
					return [{
						format: "ean_reader",
						config: {
							supplements: [
								'ean_5_reader', 'ean_2_reader'
							]
						}
					}];
				}
				return [{
					format: value + "_reader",
					config: {}
				}];
			}
		}
	},
	
	state: this.defaultValues,

	doAjax:function(url, data , callback){

        M.Toast.dismissAll();

        document.app.loader.open();

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
            .always(function() {document.app.loader.close();});
	},

	displayResult:function(data){

        $('#barcode').val(data.barcode);

		if(!data.name){

            /**
			 * CASE PRODUCT NOT FOUND
             */

			$('#notfoundbarcode').html(data.barcode);

            $('#notfound').show();

		}
		else {

            /**
             * CASE PRODUCT FOUND
             */

            $('#result .list')
                .html("")
                .append(
                    "<div style=\"float:left;margin:1.52rem 1rem .912rem 0;\"><img style=\"height:100px;\" src=\"upload/products/" + data.barcode + ".jpg\"/></div>"
                    + "<div style=\"overflow: hidden;\"><div><h4><b id=\"nbproduct\">1</b> x " + data.name + "</h4></div>"
                    + "<div>dénomination générique : " + data.generic + "</div>"
                    + "<div>code bar : " + data.barcode + "</div></div>"
                );

            $('#result').show();
        }
	},

	addToStock:function(data){
        document.app.Scan.init();
        document.app.Scan.toastResult = M.toast({
                html: data.qte+' x '+ data.name + '<button class="btn-flat toast-action" onclick="document.app.Scan.doAjax(\'canceladdtostock.html\',{ id: '+data.id+' },document.app.Scan.cancelAddToStock);">CANCEL</button>',
                displayLength : 7000,
                classes:'green'
            });
	},

    cancelAddToStock:function(data) {
        document.app.Scan.toastResult = M.toast({
                html: data.qte + ' x ' + data.name + ' <br> has been canceled',
                displayLength: 7000,
                classes: 'orange accent-2'
            });
    }

};

//init scanner
$(document).ready(function() {
    //init scan
    document.app.Scan.init();
});