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

    const canvasrmp = document.getElementById("sig-canvas-ptaramp");
    const ctxrmp = canvasrmp.getContext("2d");
    const ptaramp_name = document.getElementById("ptaramp_name");

    ctxrmp.strokeStyle = "#222222";
    ctxrmp.lineWidth = 3;

    var drawing = false;
    var mousePos = {
        x: 0,
        y: 0,
    };
    var lastPos = mousePos;

    canvasrmp.addEventListener("mousedown", function (e) {
        drawing = true;
        lastPos = getMousePos(canvasrmp, e);
    }, false
    );

    canvasrmp.addEventListener("mouseup", function (e) {
        drawing = false;
    },
        false
    );

    canvasrmp.addEventListener("mousemove", function (e) {
        mousePos = getMousePos(canvasrmp, e);
    },false);

    // Add touch event support for mobile
    canvasrmp.addEventListener("touchstart", function (e) { }, false);

    canvasrmp.addEventListener("touchmove", function (e) {
        var touch = e.touches[0];
        var me = new MouseEvent("mousemove", {
            clientX: touch.clientX,
            clientY: touch.clientY,
        });
        canvasrmp.dispatchEvent(me);
    },
        false
    );

    canvasrmp.addEventListener("touchstart", function (e) {
        mousePos = getTouchPos(canvasrmp, e);
        var touch = e.touches[0];
        var me = new MouseEvent("mousedown", {
            clientX: touch.clientX,
            clientY: touch.clientY,
        });
        canvasrmp.dispatchEvent(me);
    },
        false
    );

    canvasrmp.addEventListener("touchend", function (e) {
        var me = new MouseEvent("mouseup", {});
        canvasrmp.dispatchEvent(me);
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
            ctxrmp.moveTo(lastPos.x, lastPos.y);
            ctxrmp.lineTo(mousePos.x, mousePos.y);
            ctxrmp.stroke();
            lastPos = mousePos;
        }
    }

    // Prevent scrolling when touching the canvas
    document.body.addEventListener("touchstart", function (e) {
        if (e.target == canvas) {
            e.preventDefault();
        }
    },
        false
    );
    document.body.addEventListener("touchend", function (e) {
        if (e.target == canvas) {
            e.preventDefault();
        }
    },
        false
    );
    document.body.addEventListener("touchmove", function (e) {
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
    // var clearBtnRmp = document.getElementById("sig-clearBtn-ptaramp");
    var submitBtnRmp = document.getElementById("btn-submit");
    var sigImage = document.getElementById("sig-image");
    var sigTextRmp = document.getElementById("sig-dataUrl-ptaramp");
    
	function clearCanvas() {
		ctxrmp.clearRect(0, 0, canvasrmp.width, canvasrmp.height);
		ctxrmp.beginPath();
		ctxrmp.width = canvasrmp.width;
		ctxrmp.strokeStyle = "#222222";
		ctxrmp.lineWidth = 3;

        Swal.fire({
            title: "Tanda Tangan PPA telah dibersihkan",
            text: "Silahkan melakukan tanda tangan ulang",
            timer: 2000,
            timerProgressBar: true,
            icon: "warning",
        });
        // sigTextRmp.value = "";
        document.getElementById("sig-check-ptaramp").style.display = "none";
	}

    $(document).on("click", "#sig-clearBtn-ptaramp", function () {
        clearCanvas();
    });

    function isCanvasBlank(canvasrmp) {
        const ctxrmp = canvasrmp.getContext("2d");
        const pixelDataRmp = ctxrmp.getImageData(0, 0, canvasrmp.width, canvasrmp.height).data;
        for (let i = 0; i < pixelDataRmp.length; i += 4) {
            if (!(pixelDataRmp[i] === 255 && pixelDataRmp[i + 1] === 255 && pixelDataRmp[i + 2] === 255 && pixelDataRmp[i + 3] === 255) &&
				pixelDataRmp[i + 3] !== 0) {
				return true; // Canvas is not blank
			}
        }
        return false; // Canvas is blank (white)
    }

    submitBtnRmp.addEventListener("click", function (e) {
        if (isCanvasBlank(canvasrmp) && ptaramp_name.value != "") {
            var dataUrlRmp = canvasrmp.toDataURL("image/png").replace('data:image/png;base64,', '');
            sigTextRmp.innerHTML = dataUrlRmp;
            sigTextRmp.value = dataUrlRmp;
        } else {
            Swal.fire({
                title: "Tanda Tangan PPA belum dibuat",
                text: "Silahkan melakukan tanda tangan terlebih dahulu",
                icon: "warning",
            });
            e.preventDefault(); // Prevent form submission
        }
    }, false);
})();
