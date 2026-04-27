(function () {
	window.requestAnimFrame = (function (callback) {
		return (
			window.requestAnimationFrame ||
			window.webkitRequestAnimationFrame ||
			window.mozRequestAnimationFrame ||
			window.oRequestAnimationFrame ||
			window.msRequestAnimaitonFrame ||
			function (callback) {
				window.setTimeout(callback, 1000 / 60);
			}
		);
	})();

	const canvasmkn = document.getElementById("sig-canvas-mekanik_edit");
	const ctxmkn_edit = canvasmkn.getContext("2d");
	const mekanik_name = document.getElementById("mekanik_name_edit");

	ctxmkn_edit.strokeStyle = "#222222";
	ctxmkn_edit.lineWidth = 3;

	var drawing = false;
	var mousePos = {
		x: 0,
		y: 0,
	};
	var lastPos = mousePos;

	canvasmkn.addEventListener(
		"mousedown",
		function (e) {
			drawing = true;
			lastPos = getMousePos(canvasmkn, e);
		},
		false
	);

	canvasmkn.addEventListener(
		"mouseup",
		function (e) {
			drawing = false;
		},
		false
	);

	canvasmkn.addEventListener(
		"mousemove",
		function (e) {
			mousePos = getMousePos(canvasmkn, e);
		},
		false
	);

	// Add touch event support for mobile
	canvasmkn.addEventListener("touchstart", function (e) { }, false);

	canvasmkn.addEventListener(
		"touchmove",
		function (e) {
			var touch = e.touches[0];
			var me = new MouseEvent("mousemove", {
				clientX: touch.clientX,
				clientY: touch.clientY,
			});
			canvasmkn.dispatchEvent(me);
		},
		false
	);

	canvasmkn.addEventListener(
		"touchstart",
		function (e) {
			mousePos = getTouchPos(canvasmkn, e);
			var touch = e.touches[0];
			var me = new MouseEvent("mousedown", {
				clientX: touch.clientX,
				clientY: touch.clientY,
			});
			canvasmkn.dispatchEvent(me);
		},
		false
	);

	canvasmkn.addEventListener(
		"touchend",
		function (e) {
			var me = new MouseEvent("mouseup", {});
			canvasmkn.dispatchEvent(me);
		},
		false
	);

	function getMousePos(canvasDom, mouseEvent) {
		var rect = canvasDom.getBoundingClientRect();
		return {
			x: mouseEvent.clientX - rect.left,
			y: mouseEvent.clientY - rect.top,
		};
	}

	function getTouchPos(canvasDom, touchEvent) {
		var rect = canvasDom.getBoundingClientRect();
		return {
			x: touchEvent.touches[0].clientX - rect.left,
			y: touchEvent.touches[0].clientY - rect.top,
		};
	}

	function renderCanvas() {
		if (drawing) {
			ctxmkn_edit.moveTo(lastPos.x, lastPos.y);
			ctxmkn_edit.lineTo(mousePos.x, mousePos.y);
			ctxmkn_edit.stroke();
			lastPos = mousePos;
		}
	}

	// Prevent scrolling when touching the canvas
	document.body.addEventListener(
		"touchstart",
		function (e) {
			if (e.target == canvas) {
				e.preventDefault();
			}
		},
		false
	);
	document.body.addEventListener(
		"touchend",
		function (e) {
			if (e.target == canvas) {
				e.preventDefault();
			}
		},
		false
	);
	document.body.addEventListener(
		"touchmove",
		function (e) {
			if (e.target == canvas) {
				e.preventDefault();
			}
		},
		false
	);

	(function drawLoop() {
		requestAnimFrame(drawLoop);
		renderCanvas();
	})();

	// Set up the UI
	// var clearBtnMkn = document.getElementById("sig-clearBtn-mekanik");
	var submitBtnMkn = document.getElementById("form_submit_ttd_edit");
	var sigTextMkn = document.getElementById("sig-dataUrl-mekanik_edit");

	function clearCanvas() {
		ctxmkn_edit.clearRect(0, 0, canvasmkn.width, canvasmkn.height);
		ctxmkn_edit.beginPath();
		ctxmkn_edit.width = canvasmkn.width;
		ctxmkn_edit.strokeStyle = "#222222";
		ctxmkn_edit.lineWidth = 3;

		Swal.fire({
			title: "Tanda Tangan mekanik telah dibersihkan",
			text: "Silahkan melakukan tanda tangan ulang",
			timer: 2000,
			timerProgressBar: true,
			icon: "warning",
		});
		// sigTextMkn.value = "";
		document.getElementById("sig-check-mekanik_edit").style.display = "none";
	}

	$(document).on("click", "#sig-clearBtn-mekanik_edit", function () {
		clearCanvas();
	});


	function isCanvasBlank(canvas) {
		const ctxmkn_edit = canvas.getContext("2d");
		const pixelDataMkn = ctxmkn_edit.getImageData(0, 0, canvas.width, canvas.height).data;
		for (let i = 0; i < pixelDataMkn.length; i += 4) {
			if (!(pixelDataMkn[i] === 255 && pixelDataMkn[i + 1] === 255 && pixelDataMkn[i + 2] === 255 && pixelDataMkn[i + 3] === 255) &&
				pixelDataMkn[i + 3] !== 0) {
				return true; // Canvas is not blank
			}
		}
		return false; // Canvas is blank (all white or transparent)
	}
	
	submitBtnMkn.addEventListener("click", function (e) {
		if (isCanvasBlank(canvasmkn) === false) {
			Swal.fire({
				title: "Tanda Tangan mekanik",
				text: "Wajib mengisi tanda tangan mekanik",
				icon: "warning",
			});
			e.preventDefault();
		} else {
			var dataUrlMkn = canvasmkn.toDataURL("image/png").replace('data:image/png;base64,', '');
			sigTextMkn.innerHTML = dataUrlMkn;
			sigTextMkn.value = dataUrlMkn;

			// document.getElementById("sig-check-mekanik_edit").style.display = "block";
		}
	}, false);
	
})();
