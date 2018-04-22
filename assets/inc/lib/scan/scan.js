document.app.Scan = {
	
	init: function() {
		var self = this;

		this.initElements();

        this.attachListeners();

        /**
		 * If camera anvas not found
         */
		if(this.objControlsForm.length===0){return;}

        this.objNotfound.hide();

        this.objToHideWhenOff.show();

		Quagga.init(this.defaultValues, function(err) {
			if (err) return self.handleError(err);
			self.initCameraSelection();
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
            document.app.Util.doAjax(document.app.url.barcode,{ barcode: result.codeResult.code },document.app.Scan.displayResult);
        });

	},
	
	defaultValues : {
		inputStream: {			
			type : "LiveStream",
			constraints: {
				width		: {min: parseInt(document.querySelector('select[name="input-stream_constraints"]') ? document.querySelector('select[name="input-stream_constraints"]').value.split('x')[0] : 0)},
				height		: {min: parseInt(document.querySelector('select[name="input-stream_constraints"]') ? document.querySelector('select[name="input-stream_constraints"]').value.split('x')[1] : 0)},
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
        this.objToHideWhenOff = $('.toHideWhenOff');
        this.objNotfound = $('#notfound');
        this.objCameraTorch = $("#cameraTorch");
        this.objControls = $(".controls .reader-config-group");
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
	},
	
	closeScan:function(){
		Quagga.stop();
        this.objToHideWhenOff.hide();
		this.objControlsForm[0].reset();
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

	displayResult:function(data){

        $('#barcode').val(data.barcode);

		if(!data.name){

            document.app.Scan.closeScan();

            /**
			 * CASE PRODUCT NOT FOUND
             */

			$('#notfoundbarcode').html(data.barcode);

            $('#notfound').show();

		}
		else {

            /**
			 * Case found add & remove
             */

			if(document.app.Scan.remove){
                document.location=document.app.url.displayDelResult+'/'+data.barcode;
            }
			else{
                document.location=document.app.url.displayProduct+'/'+data.barcode;
            }
        }
	}

};

//init scanner
$(document).ready(function() {
    //init scan
    document.app.Scan.init();
});