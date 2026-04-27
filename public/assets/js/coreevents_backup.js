class CoreEvents {
	constructor() {
		const self = this;
		this.requests = [];
		this.url = "";
		this.ajax = "";
		this.csrf = {};
		this.filter = {};
		this.tableColumn = [];
		this.insertHandler = {
			placeholder: "",
			afterAction: function () { }
		};
		this.editHandler = {
			placeholder: "",
			afterAction: function () { }
		};
		this.deleteHandler = {
			placeholder: "",
			afterAction: function () { }
		};
		this.resetHandler = {
			action: function () { }
		};
		this.requestHandler = {
			beforeAction: () => { },
			afterAction: (response) => { },
			errorAction: () => { },
		};
	}
	load(filter, placeholder = "", columnDefs) {
		var thisClass = this;
		//ANCHOR - Datatable
		$("#datatable").DataTable().clear().destroy();
		this.table = $("#datatable").DataTable({
			serverSide: true,
			deferRender: false,
			orderClasses: true,
			processing: true,
			ordering: true,
			// scrollY: 400,
			// scrollCollapse: true,
			// scroller: true,
			// paging: false,
			// responsive: true,
			paging: true,
			searching: {
				regex: true
			},
			lengthMenu: [
				[10, 25, 50, 100, -1],
				[10, 25, 50, 100, "Semua"],
			],
			// language: {
			// 	processing: "<div class='loading-indicator'><img src='https://devel74.nginovasi.id/simpelpol/assets/img/simpelpol.svg' width='100px' height='50px' /></div>",
			// },
			language: {
				decimal: "",
				emptyTable: "Tidak ada data yang tersedia",
				info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
				infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
				infoFiltered: "(disaring dari _MAX_ total data)",
				infoPostFix: "",
				thousands: ",",
				lengthMenu: "Lihat _MENU_ data",
				loadingRecords: "Memuat...",
				processing: "<div class='loading-indicator'><img src='https://devel74.nginovasi.id/simpelpol/assets/img/simpelpol.svg' width='100px' height='50px' /></div>",
				search: "Cari:",
				zeroRecords: "Tidak ada data yang cocok",
				paginate: {
					first: "<i class='fas fa-angle-double-left'></i>",
					last: "<i class='fas fa-angle-double-right'></i>",
					next: "<i class='fas fa-angle-right'></i>",
					previous: "<i class='fas fa-angle-left'></i>",
				},
				aria: {
					orderable: "Urutkan kolom",
					orderableReverse: "Urutkan kolom secara terbalik"
				}
			},
			pageLength: 10,
			searchDelay: 1000,
			ajax: {
				type: "POST",
				url: thisClass.url + "_load",
				dataType: "json",
				beforeSend: function () {
					$(".loading-indicator").addClass("visible");
				},
				data: function (data) {
					data.filter = filter;
					dataStart = data.start;
					let form = {};
					Object.keys(data).forEach(function (key) {
						form[key] = data[key] || "";
					});
					let info = {
						start: data.start || 0,
						length: data.length,
						draw: 1,
					};
					$.extend(form, info);
					$.extend(form, thisClass.csrf);
					return form;
				},
				complete: function (response) {
					$(".loading-indicator").removeClass("visible");
					$(".otorisasi").tooltip({
						html: true,
						delay: {
							show: 100,
							hide: 0
						},
					});
					$(".tooltipp").tooltip({
						html: true,
						delay: {
							show: 100,
							hide: 0
						},
					});
					ScrollReveal().reveal(".dt-scrollreveal", {
						duration: 700, // Animation duration in milliseconds
						origin: 'bottom', // Where the animation starts (can be 'top', 'right', 'bottom', 'left')
						distance: '50px', // Distance the element moves in the animation
						easing: 'ease-in-out', // The easing function for the animation
						reset: true // If true, the animation repeats every time the element is scrolled into view
					});
				},
			},
			fixedColumns: thisClass.fixedColumns,
			columns: thisClass.tableColumn,
			rowCallback: function (row, data) {
				$(row).addClass("dt-scrollreveal");

			},
			layout: {
				topStart: 'pageLength',
				topEnd: {
					search: {
						placeholder: placeholder,
					}
				},
			},
			columnDefs: columnDefs,
			initComplete: thisClass.initComplete,
		}).on("init.dt", function () {
			$(this).css("width", "100%");
		});

		//ANCHOR - Event submit form
		$(document).on("submit", "#form", function (e) {
			e.preventDefault();
			let $this = $(this);
			Swal.fire({
				title: "Simpan data ?",
				icon: "question",
				showCancelButton: true,
				confirmButtonText: "Simpan",
				cancelButtonText: "Batal",
				reverseButtons: true,
			}).then(function (result) {
				if (result.value) {
					Swal.fire({
						title: "",
						icon: "info",
						text: "Proses menyimpan data, mohon ditunggu...",
						willOpen: function () {
							Swal.showLoading();
						},
					});
					// $.ajax({
					// 	url	: `${$this.url}_save`,
					// 	type: "post",
					// 	data: $this.serialize(),
					// 	dataType: "json",
					// 	success: function(result) {
					// 		Swal.close();
					// 		if (result.success) {
					// 			Swal.fire({
					// 				position: "center",
					// 				icon: "success",
					// 				title: thisClass.insertHandler.placeholder,
					// 				showConfirmButton: false,
					// 				timer: 1500,
					// 			});
					// 			$("#form").trigger("reset");
					// 			$('.nav-tabs li:contains(Data) a').tab('show');
					// 			thisClass.table.ajax.reload();
					// 			thisClass.insertHandler.afterAction(result);
					// 		} else {
					// 			Swal.fire("Perhatian!", result.message, "warning");
					// 		}
					// 	},
					// 	error: function() {
					// 		Swal.close();
					// 		Swal.fire("Perhatian!", "Maaf, terjadi kesalahan. Silakan coba lagi nanti.", "warning");
					// 	},
					// });
				}
			});
		})
			//ANCHOR - Event reset form
			.on("reset", "#form", function () {
				$("#id").val("");
				$(".sel2").val(null).trigger("change");
				thisClass.resetHandler.action();
			});
		$(document).off("click", ".edit");
		//ANCHOR - Event edit data
		$(document).on("click", ".edit", function () {
			let $this = $(this);
			let data = {
				id: $this.data("id")
			};
			$.extend(data, thisClass.csrf);
			Swal.fire({
				title: "",
				icon: "info",
				text: "Proses mengambil data, mohon ditunggu...",
				didOpen: function () {
					Swal.showLoading();
				},
			});
			$.ajax({
				url: `${$this.url}_edit`,
				type: "post",
				data: data,
				dataType: "json",
				success: function (result) {
					Swal.close();
					if (result.success) {
						$("#form").trigger("reset");
						for (var keyy in result.data) {
							$("#" + keyy).val(result.data[keyy]);
							if (keyy.startsWith("sel2_")) {
								let sel_id = keyy.replace("sel2_", "");
								let sel_name = sel_id.replace("_id", "_name");
								$("#" + sel_id).select2("trigger", "select", {
									data: {
										id: result.data["sel2_" + sel_id],
										text: result.data["sel2_" + sel_name],
									},
								});
							}
						}
						$(".nav-tabs li:contains(Update) a").tab("show");
						thisClass.editHandler.afterAction(result);
					} else {
						Swal.fire("Perhatian!", result.message, "warning");
					}
				},
				error: function () {
					Swal.close();
					Swal.fire("Perhatian!", "Maaf, terjadi kesalahan. Silakan coba lagi nanti.", "warning");
				},
			});
		})
			//ANCHOR - Event delete data
			.on("click", ".delete", function () {
				let $this = $(this);
				let data = {
					id: $this.data("id")
				};
				$.extend(data, thisClass.csrf);
				Swal.fire({
					title: "Hapus data ?",
					icon: "warning",
					showCancelButton: true,
					confirmButtonText: "Hapus",
					confirmButtonColor: "#d33",
					cancelButtonText: "Batal",
					reverseButtons: true,
				}).then(function (result) {
					if (result.value) {
						$.ajax({
							url: `${$this.url}_delete`,
							type: "post",
							data: data,
							dataType: "json",
							success: function (result) {
								Swal.close();
								if (result.success) {
									Swal.fire("Sukses", thisClass.deleteHandler.placeholder, "success");
									thisClass.table.ajax.reload();
									thisClass.deleteHandler.afterAction();
								} else {
									Swal.fire("Perhatian!", result.message, "warning");
								}
							},
							error: function () {
								Swal.close();
								Swal.fire("Perhatian!", "Maaf, terjadi kesalahan. Silakan coba lagi nanti.", "warning");
							},
						});
					}
				});
			})
			//ANCHOR - Event otorisasi data
			.on("click", ".otorisasi", function (e) {
				e.preventDefault();
				let $this = $(this);
				let rawdata = $this.data();
				const sendData = JSON.stringify(rawdata);
				const data = $.extend(thisClass.csrf, rawdata);
				// console.log(dataWithCsrf);
				// const sendDataWithCsrf = JSON.stringify(dataWithCsrf);
				Swal.fire({
					title: "Apakah anda yakin ingin otorisasi data ini ?",
					icon: "question",
					showCancelButton: true,
					confirmButtonText: "Ya",
					cancelButtonText: "Tidak",
					reverseButtons: true,
				}).then(function (result) {
					if (result.value) {
						Swal.fire({
							title: "",
							icon: "info",
							text: "Proses menyimpan data, mohon ditunggu...",
							didOpen: function () {
								Swal.showLoading();
							},
						});
						$.ajax({
							url: `${$this.url}_otorisasi`,
							type: "post",
							data: data,
							dataType: "json",
							success: function (result) {
								Swal.close();
								if (result.success) {
									Swal.fire("Sukses", "telah diotorisasi", "success");
									thisClass.table.ajax.reload();
								} else {
									Swal.fire("Perhatian!", result.message, "warning");
								}
							},
							error: function () {
								Swal.close();
								Swal.fire("Perhatian!", "Maaf, terjadi kesalahan. Silakan coba lagi nanti.", "warning");
							},
						});
					}
				});
			});
		//ANCHOR - Event detail data
		$(document).on("click", ".detail", function () {
			let $this = $(this);
			let data = {
				id: $this.data("id")
			};
			$.extend(data, thisClass.csrf);
			Swal.fire({
				title: "",
				icon: "info",
				text: "Proses mengambil data, mohon ditunggu...",
				didOpen: function () {
					Swal.showLoading();
				},
			});
			$.ajax({
				url: `${$this.url}_detail`,
				type: "POST",
				data: data,
				dataType: "json",
				success: function (result) {
					Swal.close();
					if (result.success) {
						$("#" + result.atr.modal_body).html(result.data);
						$("#" + result.atr.modal).modal("toggle");
					}
				},
				error: function () {
					Swal.close();
					Swal.fire("Perhatian!", "Maaf, terjadi kesalahan. Silakan coba lagi nanti.", "warning");
				},
			});
		})
			//ANCHOR - Event upload pic
			.on("change", ".upload-pic", function () {
				var folder = $(this).data("folder");
				var file_data = $(this).prop("files")[0];
				var maxSize = 5 * 1024 * 1024;
				let $this = $(this);
				if (file_data.size <= maxSize) {
					var form_data = new FormData();
					form_data.append("file", file_data);
					form_data.append("folder", folder);
					$.each(thisClass.csrf, function (key) {
						form_data.append(key, thisClass.csrf[key]);
					});
					Swal.fire({
						title: "",
						icon: "info",
						text: "Proses mengambil file, mohon ditunggu...",
						didOpen: function () {
							Swal.showLoading();
						},
					});
					$.ajax({
						url: `${$this.url}_upload`,
						dataType: "json",
						cache: false,
						contentType: false,
						processData: false,
						data: form_data,
						type: "post",
						success: function (response) {
							Swal.close();
							if (response.success) {
								thisClass.uploadHandler.afterAction(response);
							} else {
								Swal.fire("Perhatian!", response.error, "warning");
							}
						},
						error: function () {
							Swal.close();
							Swal.fire("Perhatian!", "Maaf, terjadi kesalahan. Silakan coba lagi nanti.", "warning");
						},
					});
				} else {
					Swal.fire("Perhatian!", "Ukuran file tidak boleh melebihi 2MB", "warning");
					file_data = "";
				}
			})
			//ANCHOR - Event upload2 pic
			.on("change", ".upload-pic2", function () {
				var folder = $(this).data("folder");
				var file_data = $(this).prop("files")[0];
				var maxSize = 5 * 1024 * 1024;
				let $this = $(this);
				if (file_data.size <= maxSize) {
					var form_data = new FormData();
					form_data.append("file", file_data);
					form_data.append("folder", folder);
					$.each(thisClass.csrf, function (key) {
						form_data.append(key, thisClass.csrf[key]);
					});
					Swal.fire({
						title: "",
						icon: "info",
						text: "Proses mengambil file, mohon ditunggu...",
						didOpen: function () {
							Swal.showLoading();
						},
					});
					$.ajax({
						url: `${$this.url}_upload2`,
						dataType: "json",
						cache: false,
						contentType: false,
						processData: false,
						data: form_data,
						type: "post",
						success: function (response) {
							Swal.close();
							if (response.success) {
								thisClass.uploadHandler2.afterAction(response);
							} else {
								Swal.fire("Perhatian!", response.error, "warning");
							}
						},
						error: function () {
							Swal.close();
							Swal.fire("Perhatian!", "Maaf, terjadi kesalahan. Silakan coba lagi nanti.", "warning");
						},
					});
				} else {
					Swal.fire("Perhatian!", "Ukuran file tidak boleh melebihi 2MB", "warning");
					file_data = "";
				}
			})
			//ANCHOR - Event upload file
			.on("change", ".upload-file", function () {
				var folder = $(this).data("folder");
				var file_data = $(this).prop("files")[0];
				// Validate file type and size
				if (file_data) {
					const maxSizeMB = 5;
					const allowedType = "application/pdf";
					if (file_data.type !== allowedType) {
						Swal.fire("Perhatian!", "Hanya file PDF yang diperbolehkan.", "warning");
						$(this).val(''); // Clear the file input
						return;
					}
					if (file_data.size / 1024 / 1024 > maxSizeMB) {
						Swal.fire("Perhatian!", "Ukuran file maksimal 5 MB.", "warning");
						$(this).val(''); // Clear the file input
						return;
					}
				} else {
					Swal.fire("Perhatian!", "File tidak ditemukan.", "warning");
					return;
				}
				var form_data = new FormData();
				form_data.append("file", file_data);
				form_data.append("folder", folder);
				$.each(thisClass.csrf, function (key) {
					form_data.append(key, thisClass.csrf[key]);
				});
				Swal.fire({
					title: "",
					icon: "info",
					text: "Proses mengambil file, mohon ditunggu...",
					didOpen: function () {
						Swal.showLoading();
					},
				});
				$.ajax({
					url: thisClass.upload,
					dataType: "json",
					cache: false,
					contentType: false,
					processData: false,
					data: form_data,
					type: "post",
					success: function (response) {
						Swal.close();
						if (response.success) {
							thisClass.uploadHandler.afterAction(response);
						} else {
							Swal.fire("Perhatian!", response.error, "warning");
						}
					},
					error: function () {
						Swal.close();
						Swal.fire("Perhatian!", "Maaf, terjadi kesalahan. Silakan coba lagi nanti.", "warning");
					},
				});
			})
		//ANCHOR - Event upload2 file on dev
		$(document).on("change", ".upload-file-dev", function () {
			const folder = $(this).data("folder");
			const fileInput = $(this);
			const file = fileInput.prop("files")[0];
			const previewId = fileInput.closest('div').next().find('object').attr('id');
			const fileURL_id = fileInput.next('input[type="hidden"]').attr('id');
			const errorId = fileInput.siblings("small[id$='-error']").attr('id');
			const fileURL = URL.createObjectURL(file);
			const size_file = (file.size / 1024 / 1024).toFixed(2);

			// Validate file type and size
			if (file) {
				const maxSizeMB = 5;
				const allowedType = "application/pdf";

				if (file.type !== allowedType) {
					Swal.fire("Perhatian!", "Hanya file PDF yang diperbolehkan.", "warning");
					fileInput.val(''); // Clear the file input
					return;
				}

				if (file.size / 1024 / 1024 > maxSizeMB) {
					Swal.fire("Perhatian!", "Ukuran file maksimal 5 MB.", "warning");
					fileInput.val(''); // Clear the file input
					return;
				}
			} else {
				Swal.fire("Perhatian!", "File tidak ditemukan.", "warning");
				return;
			}

			// Prepare form data for AJAX
			const formData = new FormData();
			formData.append("file", file);
			formData.append("folder", folder);

			// Add CSRF tokens if available
			if (thisClass.csrf) {
				$.each(thisClass.csrf, function (key, value) {
					formData.append(key, value);
				});
			}

			// Show loading message
			Swal.fire({
				title: "",
				icon: "info",
				text: "Proses mengambil file, mohon ditunggu...",
				didOpen: function () {
					Swal.showLoading();
				},
			});

			// Send AJAX request
			$.ajax({
				url: thisClass.upload,
				dataType: "json",
				cache: false,
				contentType: false,
				processData: false,
				data: formData,
				type: "post",
				success: function (rs) {
					Swal.close();
					if (rs.success) {
						$('#' + fileURL_id).val(rs.data.path);
						$('#' + previewId).attr('data', fileURL).show();
						$('#' + errorId).html(` (File: ${rs.data.originalName} - ${size_file} MB)`);
						thisClass.uploadHandler.afterAction(rs);
					} else {
						Swal.fire("Perhatian!", rs.error || "Gagal mengunggah file.", "warning");
					}
				},
				error: function (xhr, status, error) {
					Swal.close();
					Swal.fire("Perhatian!", "Maaf, terjadi kesalahan. Silakan coba lagi nanti.", "warning");
					console.error("Error: ", status, error);
				}
			});
		})
			//ANCHOR - Event upload2 file
			.on("change", ".upload2-file", function () {
				let value = $(this).data('value');
				let preview = $(this).data('preview');
				var folder = $(this).data("folder");
				var file_data = $(this).prop("files")[0];

				if (file_data) {
					const maxSizeMB = 5;
					const allowedType = "application/pdf";
					if (file_data.type !== allowedType) {
						Swal.fire("Perhatian!", "Hanya file PDF yang diperbolehkan.", "warning");
						$(this).val('');
						return;
					}
					if (file_data.size / 1024 / 1024 > maxSizeMB) {
						Swal.fire("Perhatian!", "Ukuran file maksimal 5 MB.", "warning");
						$(this).val('');
						return;
					}
				} else {
					Swal.fire("Perhatian!", "File tidak ditemukan.", "warning");
					return;
				}

				var form_data = new FormData();
				form_data.append("file", file_data);
				form_data.append("folder", folder);
				$.each(thisClass.csrf, function (key) {
					form_data.append(key, thisClass.csrf[key]);
				});
				Swal.fire({
					title: "",
					icon: "info",
					text: "Proses mengambil file, mohon ditunggu...",
					didOpen: function () {
						Swal.showLoading();
					},
				});
				$.ajax({
					url: thisClass.upload2,
					dataType: "json",
					cache: false,
					contentType: false,
					processData: false,
					data: form_data,
					type: "post",
					success: function (rs) {
						Swal.close();
						rs.value = value;
						rs.preview = preview;
						if (rs.success) {
							thisClass.uploadHandler2.afterAction(rs);
						} else {
							Swal.fire("Perhatian!", rs.error, "warning");
						}
					},
					error: function () {
						Swal.close();
						Swal.fire("Perhatian!", "Maaf, terjadi kesalahan. Silakan coba lagi nanti.", "warning");
					},
				});
			})
			//ANCHOR - Event select2:open
			.on("select2:open", () => {
				document.querySelector(".select2-search__field").focus();
			});
	}
	//ANCHOR - abort all Requests
	abortAllRequests() {
		this.requests.forEach(request => request.abort());
		this.requests = [];
	}
	//ANCHOR - sendRequest
	sendRequest(url, method = 'GET', dataType = 'json', data = null) {
		const thisClass = this;
		this.abortAllRequests();

		const request = $.ajax({
			url: url,
			method: method,
			dataType: dataType,
			data: data,
			beforeSend: function () {
				console.log("Request dimulai...");
				thisClass.requestHandler.beforeAction();
			},
			success: function (response) {
				console.log("Response diterima:", response);
				thisClass.requestHandler.afterAction(response);
			},
			error: function (xhr, status, error) {
				if (status !== 'abort') {
					console.log("AJAX error:", error);
					thisClass.requestHandler.errorAction();
				}
			},
			complete: () => {
				this.requests = this.requests.filter(req => req !== request);
			}
		});
		this.requests.push(request);
	}
	//ANCHOR - loadDatatable
	loadDatatable(element, url, tableColumn, order = null) {
		var thisClass = this;
		var table = $("#" + element).DataTable({
			autoWidth: false,
			serverSide: true,
			processing: true,
			ordering: true,
			order: order == null ? [
				[0, "asc"]
			] : order,
			paging: true,
			searching: {
				regex: true
			},
			lengthMenu: [
				[10, 25, 50, 100, -1],
				[10, 25, 50, 100, "All"],
			],
			pageLength: 10,
			searchDelay: 500,
			ajax: {
				type: "POST",
				url: url,
				dataType: "json",
				data: function (data) {
					// console.log(thisClass);
					// Grab form values containing user options
					dataStart = data.start;
					let form = {};
					Object.keys(data).forEach(function (key) {
						form[key] = data[key] || "";
					});
					// Add options used by Datatables
					let info = {
						start: data.start || 0,
						length: data.length,
						draw: 1,
					};
					$.extend(form, info);
					$.extend(form, thisClass.csrf);
					return form;
				},
				complete: function (response) {
					// console.log(response);
					feather.replace();
				},
			},
			columns: thisClass.tableColumn,
		}).on("init.dt", function () {
			// $(this).css('width','100%');
		});
		return table;
	}
	//ANCHOR - select2Init
	select2Init(id, url, placeholder, parameter, parent = null, selection = null, formatHTML = null) {
		var thisClass = this;
		if ($(id).data("select2")) {
			$(id).select2("destroy");
			thisClass.select2Init(id, url, placeholder, parameter, parent, selection, formatHTML);
		} else {
			if (url != null) {
				var dt = $(id).select2({
					id: function (e) {
						return e.id;
					},
					allowClear: true,
					placeholder: placeholder,
					width: "100%",
					dropdownParent: parent == null ? $(document.body) : $(parent),
					ajax: {
						url: thisClass.ajax + url,
						dataType: "json",
						quietMillis: 500,
						delay: 500,
						data: function (param) {
							var def_param = {
								keyword: param.term, //search term
								perpage: 5, // page size
								page: param.page || 0, // page number
							};
							return Object.assign({}, def_param, parameter);
						},
						processResults: function (data, params) {
							params.page = params.page || 0;
							if (data.success) {
								var results = data.rows;
								if (results.length === 1) {
									var selectedOption = {
										id: results[0].id,
										text: results[0].text,
										selection: results[0],
									};
									$(id).select2("trigger", "select", {
										data: selectedOption,
									});
								}
								return {
									results: data.rows,
									pagination: {
										more: false,
									},
								};
							} else {
								return {
									results: [],
									pagination: {
										more: false,
									},
								};
							}
						},
						transport: function (params, success, failure) {
							var $request = $.ajax(params);
							$request.then(success);
							$request.fail(failure);
							return $request;
						},
					},
					templateResult: function (data) {
						if (formatHTML !== null) {
							return formatHTML(data);
						} else {
							return data.text;
						}
					},
					templateSelection: function (data) {
						if (data.id == "") {
							return placeholder;
						}
						if (selection !== null) {
							selection(data);
						}
						return data.text;
					},
					escapeMarkup: function (m) {
						return m;
					},
				}).on("init", function (data) {
					console.log("select2init");
				});
				$(id).each(function (i, elm) {
					var id = $(elm).data("id");
					var text = $(elm).data("text");
					var $newOption = $('<option selected="selected"></optiol>').val(id).text(text);
					$(elm).append($newOption).trigger("change");
				});
			} else {
				var dt = $(id).select2({
					placeholder: placeholder,
				});
			}
		}
	}
	//ANCHOR - select2Init_multiple
	select2Init_multiple(id, url, placeholder, parameter, parent = null, selection = null, formatHTML = null) {
		var thisClass = this;
		if ($(id).data("select2")) {
			$(id).select2("destroy");
			thisClass.select2Init_multiple(id, url, placeholder, parameter, parent, selection, formatHTML);
		} else {
			if (url != null) {
				var dt = $(id).select2({
					id: function (e) {
						return e.id;
					},
					allowClear: true,
					tags: true,
					tokenSeparators: [',', ' '],
					multiple: true,
					placeholder: placeholder,
					width: "100%",
					dropdownParent: parent == null ? $(document.body) : $(parent),
					ajax: {
						url: thisClass.ajax + url,
						dataType: "json",
						quietMillis: 500,
						delay: 500,
						data: function (param) {
							var def_param = {
								keyword: param.term, //search term
								perpage: 5, // page size
								page: param.page || 0, // page number
							};
							return Object.assign({}, def_param, parameter);
						},
						processResults: function (data, params) {
							params.page = params.page || 0;
							return {
								results: data.rows,
								pagination: {
									more: false,
								},
							};
						},
					},
					templateResult: function (data) {
						if (formatHTML !== null) {
							return formatHTML(data);
						} else {
							return data.text;
						}
					},
					templateSelection: function (data) {
						if (data.id == "") {
							return placeholder;
						}
						if (selection !== null) {
							selection(data);
						}
						return data.text;
					},
					escapeMarkup: function (m) {
						return m;
					},
				}).on("init", function (data) {
					console.log("select2init");
				});
				// $(id).each(function(i, elm) {
				// 	var id = $(elm).data("id");
				// 	var text = $(elm).data("text");
				// 	var $newOption = $('<option selected="selected"></optiol>').val(id).text(text);
				// 	$(elm).append($newOption).trigger("change");
				// });
			} else {
				var dt = $(id).select2({
					placeholder: placeholder,
				});
			}
		}
	}
	//ANCHOR - datepicker
	datepicker(element, fdata = "dd/mm/yyyy", forientation = "bottom") {
		$(element).datepicker({
			format: fdata,
			orientation: forientation,
		}).on("changeDate", function () {
			$(this).datepicker("hide");
		});
	}
	//ANCHOR - datepickerInit
	datepickerInit(id, fdata, forientation, minDate = null, maxDate = null) {
		$(id).datepicker({
			dateFormat: fdata,
			orientation: forientation,
			autoclose: true,
			todayHighlight: true,
			language: "id",
			changeMonth: true,
			changeYear: true,
			maxDate: maxDate,
			minDate: minDate,
		}).on("changeDate", function (e) {
			$(this).datepicker("hide");
		});
	}
	//ANCHOR - daterangepickerInit
	daterangepickerInit(options) {
		var thisClass = this;
		const {
			id,
			format = 'DD-MM-YYYY',
			singleDatePicker = false,
			showDropdowns = true,
			alwaysShowCalendars = true,
			startDate = moment().startOf('month'),
			endDate = moment().endOf('month'),
			minYear = 2015,
			maxYear = parseInt(moment().format('YYYY'), 10),
			minDate = null,
			maxDate = moment().endOf("day"),
			parentEl = 'body',
			callback = null,
			ranges = {
				"Hari ini": [moment(), moment()],
				"Kemarin": [moment().subtract(1, "days"), moment().subtract(1, "days")],
				"7 Hari Terakhir": [moment().subtract(6, "days"), moment()],
				"30 Hari Terakhir": [moment().subtract(29, "days"), moment()],
				"Bulan Ini": [moment().startOf("month"), moment().endOf("month")],
				"Bulan Lalu": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")],
				"Tahun Ini": [moment().startOf("year"), moment().endOf("year")],
			},
			...otherOptions
		} = options;

		$(id).daterangepicker({
			opens: "right",
			drops: "auto",
			singleDatePicker,
			showDropdowns,
			alwaysShowCalendars,
			parentEl,
			autoUpdateInput: false,
			locale: {
				format,
				cancelLabel: "Batal",
				clearLabel: "Bersihkan",
				separator: " to ",
				applyLabel: "Pilih",
				fromLabel: "Dari",
				toLabel: "Ke",
				daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
				monthNames: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
				firstDay: 1,
			},
			startDate,
			endDate,
			minDate,
			minYear,
			maxDate,
			maxYear,
			maxSpan: {
				days: moment().daysInMonth(),
			},
			ranges,
			...otherOptions
		}, function (start, end, label) {
			if (callback) callback(start, end, label);
		});

		$(id).on("apply.daterangepicker", function (ev, picker) {
			const dateRange = singleDatePicker
				? picker.startDate.format(format)
				: picker.startDate.format(format) + " to " + picker.endDate.format(format);
			$(this).val(dateRange);
			if (thisClass.drp?.applyHandler) {
				thisClass.drp.applyHandler(ev, picker);
			}
		});

		$(id).on("cancel.daterangepicker", function (ev, picker) {
			$(this).val("");
			if (thisClass.drp?.cancelHandler) {
				thisClass.drp.cancelHandler(ev, picker);
			}
		});
	}
	//ANCHOR - maskInit
	maskInit(id, maskOpt) {
		const element = document.getElementById(id);
		const maskOptions = maskOpt;
		const masking = IMask(element, maskOptions);
	}
	//ANCHOR - showLoader
	showLoaderInfo(title = '', text = '', timer = 2000) {
		Swal.fire({
			title: title,
			icon: "info",
			text: text,
			timer: timer,
		});
	}
	//ANCHOR - initLocalDT
	initLocalDT(id, data, column) {
		let thisClass = $(this);
		if ($.fn.DataTable.isDataTable(`#${id}`)) {
			$(`#${id}`).DataTable().clear().destroy();
		}
		let dt = $(`#${id}`).DataTable({
			searching: {
				regex: true
			},
			data: data,
			columns: column,
		}).on("init.dt", function () {
			$(this).css("width", "100%");
		});
		return dt;
	}
}

//ANCHOR - function randomColor
function randomColor() {
	var color = Math.floor(Math.random() * 16777215).toString(16);
	return "#" + color;
}
var F = (function () {
	let f = {};
	String.prototype.reverse = function () {
		return this.split("").reverse().join("");
	};
	f.masking = (input) => {
		var x = input.value;
		x = x.replace(/\./g, ""); // Strip out all commas
		x = x.reverse();
		x = x.replace(/.../g, function (e) {
			return e + ".";
		}); // Insert new commas
		x = x.reverse();
		x = x.replace(/^\./, ""); // Remove leading comma
		input.value = x;
	};
	f.debounce = function (func, delay) {
		let debounceTimer;
		return function () {
			const context = this;
			const args = arguments;
			clearTimeout(debounceTimer);
			debounceTimer = setTimeout(() => func.apply(context, args), delay);
		};
	};
	return f;
})();
