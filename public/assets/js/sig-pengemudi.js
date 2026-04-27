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

    const canvas = document.getElementById("sig-canvas");
    const ctx = canvas.getContext("2d");
    const driver_name = document.getElementById("nama_pengemudi_");

    ctx.strokeStyle = "#222222";
    ctx.lineWidth = 3;

    var drawing = false;
    var mousePos = {
        x: 0,
        y: 0,
    };
    var lastPos = mousePos;

    canvas.addEventListener("mousedown", function (e) {
            drawing = true;
            lastPos = getMousePos(canvas, e);
            
        },
        false
    );

    canvas.addEventListener("mouseup", function (e) {
            drawing = false;
        },
        false
    );

    canvas.addEventListener("mousemove", function (e) {
            mousePos = getMousePos(canvas, e);
        },
        false
    );

    // Add touch event support for mobile
    canvas.addEventListener("touchstart", function (e) { }, false);

    canvas.addEventListener("touchmove", function (e) {
            var touch = e.touches[0];
            var me = new MouseEvent("mousemove", {
                clientX: touch.clientX,
                clientY: touch.clientY,
            });
            canvas.dispatchEvent(me);
        },
        false
    );

    canvas.addEventListener("touchstart", function (e) {
            mousePos = getTouchPos(canvas, e);
            var touch = e.touches[0];
            var me = new MouseEvent("mousedown", {
                clientX: touch.clientX,
                clientY: touch.clientY,
            });
            canvas.dispatchEvent(me);
        },
        false
    );

    canvas.addEventListener("touchend", function (e) {
            var me = new MouseEvent("mouseup", {});
            canvas.dispatchEvent(me);
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
            ctx.moveTo(lastPos.x, lastPos.y);
            ctx.lineTo(mousePos.x, mousePos.y);
            ctx.stroke();
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

	function clearCanvas() {
		ctx.clearRect(0, 0, canvas.width, canvas.height);
		ctx.beginPath();
		ctx.width = canvas.width;
		ctx.strokeStyle = "#222222";
		ctx.lineWidth = 3;

        Swal.fire({
            title: "Tanda Tangan pengemudi telah dibersihkan",
            text: "Silahkan melakukan tanda tangan ulang",
            timer: 2000,
            timerProgressBar: true,
            icon: "warning",
        });
        // sigText.value = "";
        document.getElementById("sig-check").style.display = "none";
	}

    // Set up the UI
    // var clearBtn = document.getElementById("sig-clearBtn");
    var submitBtn = document.getElementById("btn-submit");
    var sigImage = document.getElementById("sig-image");
    var sigText = document.getElementById("sig-dataUrl");

    $(document).on("click", "#sig-clearBtn", function () {
        clearCanvas();
    });

    function isCanvasBlank(canvas) {
        const ctx = canvas.getContext("2d");
        const pixelData = ctx.getImageData(0, 0, canvas.width, canvas.height).data;
        for (let i = 0; i < pixelData.length; i += 4) {
            if (!(pixelData[i] === 255 && pixelData[i + 1] === 255 && pixelData[i + 2] === 255 && pixelData[i + 3] === 255) &&
				pixelData[i + 3] !== 0) {
				return true; // Canvas is not blank
			}
        }
        return false; // Canvas is blank (white)
    }

    submitBtn.addEventListener("click", function (e) {
        if (driver_name.value == "") {
            $("html, body").animate(
                { scrollTop: $("#sig-canvas").offset().top - 100 },
                500
            );
            Swal.fire({
                title: "Nama Pengemudi belum diisi",
                text: "Silahkan isi nama pengemudi terlebih dahulu",
                icon: "warning",
            });
            e.preventDefault();
        } else {
            var dataUrl = canvas.toDataURL("image/png").replace('data:image/png;base64,', '');
            sigText.innerHTML = dataUrl;
            sigText.value = dataUrl;
        }
    }, false);
})();
