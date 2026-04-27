class CoreEvents {
	constructor(config = {}) {
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
		this.formSelector = config.formSelector || '#form';
		this.applyHandler = config.applyHandler || function () { };
		this.cancelHandler = config.cancelHandler || function () { };
		// this.storageKey = window.location.pathname.split('/')[3] || 'coreEvents';
	}
	load(filter, placeholder = "", columnDefs) {
		const self = this;
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
			pageLength: self.pageLength || 10,
			searchDelay: 1000,
			ajax: {
				type: "POST",
				url: `${self.url}_load`,
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
					// $.extend(form, info);
					// $.extend(form, self.csrf);
					// return form;
					return $.extend({}, form, info, self.csrf);
				},
				complete: function () {
					$(".loading-indicator").removeClass("visible");
					$(".otorisasi, .tooltipp").tooltip({
						html: true,
						delay: {
							show: 100,
							hide: 0
						},
					});
					ScrollReveal().reveal(".dt-scrollreveal", {
						duration: 700,
						origin: 'bottom',
						distance: '50px',
						easing: 'ease-in-out',
						reset: false
					});
				},
			},
			fixedColumns: self.fixedColumns,
			columns: self.tableColumn,
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
			initComplete: self.initComplete,
		}).on("init.dt", function () {
			$(this).css("width", "100%");
		});

		//ANCHOR - Event submit form
		$(document).on("submit", this.formSelector, function (e) {
			e.preventDefault();
			let $this = $(this);
			Swal.fire({
				title: "Simpan data?",
				icon: "question",
				showCancelButton: true,
				confirmButtonText: "Simpan",
				cancelButtonText: "Batal",
				reverseButtons: true,
			}).then((result) => {
				if (result.isConfirmed) {
					Swal.fire({
						title: "",
						icon: "info",
						text: "Proses menyimpan data, mohon ditunggu...",
						didOpen: function () {
							Swal.showLoading();
						},
					});
					self.sendRequest(`${self.url}_save`, "POST", "json", new FormData($this[0]))
						.then(response => {
							Swal.close();
							if (response.success) {
								Swal.fire({
									position: "center",
									icon: "success",
									title: self.insertHandler.placeholder,
									showConfirmButton: false,
									timer: 1500,
								});
								$this.trigger("reset"); // Reset form
								$(".nav-tabs li:contains(Data) a").tab("show"); // Tampilkan tab Data
								self.table.ajax.reload(); // Reload tabel
								self.insertHandler.afterAction(response); // Panggil aksi setelah
							} else {
								Swal.fire("Perhatian!", response.message, "warning");
							}
						})
						.catch(error => {
							Swal.fire("Perhatian!", "Maaf, terjadi kesalahan. Silakan coba lagi nanti.", "warning");
						});
				}
			});

		})
			//ANCHOR - Event reset form
			.on("reset", this.formSelector, function () {
				$("#id").val("");
				$(".sel2").val(null).trigger("change");
				$(this.formSelector).trigger("reset");
				self.resetHandler.action();
				// localStorage.removeItem(self.storageKey);
			});
		$(document).off("click", ".edit");
		//ANCHOR - Event edit data
		$(document).on("click", ".edit", function () {
			let $this = $(this);
			let data = {
				id: $this.data("id"),
			};
			$.extend(data, self.csrf);

			$.ajax({
				url: `${self.url}_edit`,
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
						self.editHandler.afterAction(result);
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
					id: $this.data("id"),
				};
				$.extend(data, self.csrf);
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
						// self.requestHandler = {
						// 	beforeAction: function() {},
						// 	afterAction: function(result) {
						// 		Swal.close();
						// 		if (result.success) {
						// 			Swal.fire({
						// 				position: "center",
						// 				icon: "success",
						// 				title: self.deleteHandler.placeholder,
						// 				showConfirmButton: false,
						// 				timer: 1500,
						// 			});
						// 			self.table.ajax.reload();
						// 			self.deleteHandler.afterAction();
						// 		} else {
						// 			Swal.fire("Perhatian!", result.message, "warning");
						// 		}
						// 	},
						// 	errorAction: function() {
						// 		Swal.close();
						// 		Swal.fire("Perhatian!", "Maaf, terjadi kesalahan. Silakan coba lagi nanti.", "warning");
						// 	}
						// };
						self.sendRequest(`${self.url}_delete`, "POST", "json", data)
							.then(response => {
								if (response.success) {
									Swal.fire({
										position: "center",
										icon: "success",
										title: self.deleteHandler.placeholder,
										showConfirmButton: false,
										timer: 1500,
									});
									self.table.ajax.reload();
									self.deleteHandler.afterAction();
								} else {
									Swal.fire("Perhatian!", response.message, "warning");
								}
							})
							.catch(error => {
								Swal.fire("Perhatian!", "Maaf, terjadi kesalahan. Silakan coba lagi nanti.", "warning");
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
				const data = $.extend(self.csrf, rawdata);
				Swal.fire({
					title: "Apakah anda yakin ingin otorisasi data ini ?",
					icon: "question",
					showCancelButton: true,
					confirmButtonText: "Ya",
					cancelButtonText: "Tidak",
					reverseButtons: true,
				}).then(function (result) {
					if (result.value) {
						self.requestHandler = {
							beforeAction: function () {
								Swal.fire({
									title: "",
									icon: "info",
									text: "Proses otorisasi data, mohon ditunggu...",
									didOpen: function () {
										Swal.showLoading();
									},
								});
							},
							afterAction: function (result) {
								Swal.close();
								if (result.success) {
									Swal.fire({
										position: "center",
										icon: "success",
										title: "Data berhasil diotorisasi",
										showConfirmButton: false,
										timer: 1500,
									});
									self.table.ajax.reload();
								} else {
									Swal.fire("Perhatian!", result.message, "warning");
								}
							},
							errorAction: function () {
								Swal.close();
								Swal.fire("Perhatian!", "Maaf, terjadi kesalahan. Silakan coba lagi nanti.", "warning");
							}
						};
						self.sendRequest(`${self.url}_otorisasi`, "POST", "json", data);
					}
				});
			});
		//ANCHOR - Event detail data
		$(document).on("click", ".detail", function () {
			let $this = $(this);
			let data = {
				id: $this.data("id")
			};
			$.extend(data, self.csrf);
			// Swal.fire({
			// 	title: "",
			// 	icon: "info",
			// 	text: "Proses mengambil data, mohon ditunggu...",
			// 	didOpen: function() {
			// 		Swal.showLoading();
			// 	},
			// });
			// $.ajax({
			// 	url: `${$this.url}_detail`,
			// 	type: "POST",
			// 	data: data,
			// 	dataType: "json",
			// 	success: function(result) {
			// 		Swal.close();
			// 		if (result.success) {
			// 			$("#" + result.atr.modal_body).html(result.data);
			// 			$("#" + result.atr.modal).modal("toggle");
			// 		}
			// 	},
			// 	error: function() {
			// 		Swal.close();
			// 		Swal.fire("Perhatian!", "Maaf, terjadi kesalahan. Silakan coba lagi nanti.", "warning");
			// 	},
			// });
			self.requestHandler = {
				beforeAction: function () {
					Swal.fire({
						title: "",
						icon: "info",
						text: "Proses mengambil data, mohon ditunggu...",
						didOpen: function () {
							Swal.showLoading();
						},
					});
				},
				afterAction: function (result) {
					Swal.close();
					if (result.success) {
						$("#" + result.atr.modal_body).html(result.data);
						$("#" + result.atr.modal).modal("toggle");
					}
				},
				errorAction: function () {
					Swal.close();
					Swal.fire("Perhatian!", "Maaf, terjadi kesalahan. Silakan coba lagi nanti.", "warning");
				},
			};
			self.sendRequest(`${self.url}_detail`, "POST", "json", data);
		})
			//ANCHOR - Event upload pic
			.on("change", ".upload-pic", function () {
				var folder = $(this).data("folder");
				var file_data = $(this).prop("files")[0];
				var maxSize = $(this).data("max-size") ? $(this).data("max-size") * 1024 * 1024 : 1 * 1024 * 1024;
				let $this = $(this);
				if (file_data.size <= maxSize) {
					var form_data = new FormData();
					form_data.append("file", file_data);
					form_data.append("folder", folder);
					$.each(self.csrf, function (key) {
						form_data.append(key, self.csrf[key]);
					});
					Swal.fire({
						title: "",
						icon: "info",
						text: "Proses upload file, mohon ditunggu...",
						didOpen: function () {
							Swal.showLoading();
						},
					});
					self.sendRequest(`${self.url}_upload_pic`, 'POST', 'json', form_data)
						.then(response => {
							Swal.close(); // Close the loading alert
							if (response.success) {
								self.uploadHandler.afterAction(response); // Handle success
							} else {
								Swal.fire("Perhatian!", response.error, "warning"); // Show error
							}
						})
						.catch(error => {
							Swal.close(); // Close the loading alert
							Swal.fire("Perhatian!", "Maaf, terjadi kesalahan. Silakan coba lagi nanti.", "warning"); // Show error
						});
				} else {
					Swal.fire("Perhatian!", "Ukuran file(" + (file_data.size / 1024 / 1024).toFixed(2) + "MB), tidak boleh melebihi 1MB", "warning");
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
					$.each(self.csrf, function (key) {
						form_data.append(key, self.csrf[key]);
					});
					// Swal.fire({
					// 	title: "",
					// 	icon: "info",
					// 	text: "Proses mengambil file, mohon ditunggu...",
					// 	didOpen: function() {
					// 		Swal.showLoading();
					// 	},
					// });
					// $.ajax({
					// 	url: `${$this.url}_upload2`,
					// 	dataType: "json",
					// 	cache: false,
					// 	contentType: false,
					// 	processData: false,
					// 	data: form_data,
					// 	type: "post",
					// 	success: function(response) {
					// 		Swal.close();
					// 		if (response.success) {
					// 			self.uploadHandler2.afterAction(response);
					// 		} else {
					// 			Swal.fire("Perhatian!", response.error, "warning");
					// 		}
					// 	},
					// 	error: function() {
					// 		Swal.close();
					// 		Swal.fire("Perhatian!", "Maaf, terjadi kesalahan. Silakan coba lagi nanti.", "warning");
					// 	},
					// });
					self.requestHandler = {
						beforeAction: function () {
							Swal.fire({
								title: "",
								icon: "info",
								text: "Proses mengambil file, mohon ditunggu...",
								didOpen: function () {
									Swal.showLoading();
								},
							});
						},
						afterAction: function (result) {
							Swal.close();
							if (result.success) {
								self.uploadHandler2.afterAction(result);
							} else {
								Swal.fire("Perhatian!", result.error, "warning");
							}
						},
						errorAction: function () {
							Swal.close();
							Swal.fire("Perhatian!", "Maaf, terjadi kesalahan. Silakan coba lagi nanti.", "warning");
						},
					};
					self.sendRequest(`${self.url}_upload2`, "POST", "json", form_data);
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
				$.each(self.csrf, function (key) {
					form_data.append(key, self.csrf[key]);
				});
				// Swal.fire({
				// 	title: "",
				// 	icon: "info",
				// 	text: "Proses mengambil file, mohon ditunggu...",
				// 	didOpen: function() {
				// 		Swal.showLoading();
				// 	},
				// });
				// $.ajax({
				// 	url: self.upload,
				// 	dataType: "json",
				// 	cache: false,
				// 	contentType: false,
				// 	processData: false,
				// 	data: form_data,
				// 	type: "post",
				// 	success: function(response) {
				// 		Swal.close();
				// 		if (response.success) {
				// 			self.uploadHandler.afterAction(response);
				// 		} else {
				// 			Swal.fire("Perhatian!", response.error, "warning");
				// 		}
				// 	},
				// 	error: function() {
				// 		Swal.close();
				// 		Swal.fire("Perhatian!", "Maaf, terjadi kesalahan. Silakan coba lagi nanti.", "warning");
				// 	},
				// });
				self.requestHandler = {
					beforeAction: function () {
						Swal.fire({
							title: "",
							icon: "info",
							text: "Proses mengambil file, mohon ditunggu...",
							didOpen: function () {
								Swal.showLoading();
							},
						});
					},
					afterAction: function (result) {
						Swal.close();
						if (result.success) {
							self.uploadHandler.afterAction(result);
						} else {
							Swal.fire("Perhatian!", result.error, "warning");
						}
					},
					errorAction: function () {
						Swal.close();
						Swal.fire("Perhatian!", "Maaf, terjadi kesalahan. Silakan coba lagi nanti.", "warning");
					},
				};
				self.sendRequest(self.upload, "POST", "json", form_data);
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
			if (self.csrf) {
				$.each(self.csrf, function (key, value) {
					formData.append(key, value);
				});
			}

			// Show loading message
			// Swal.fire({
			// 	title: "",
			// 	icon: "info",
			// 	text: "Proses mengambil file, mohon ditunggu...",
			// 	didOpen: function() {
			// 		Swal.showLoading();
			// 	},
			// });

			// // Send AJAX request
			// $.ajax({
			// 	url: self.upload,
			// 	dataType: "json",
			// 	cache: false,
			// 	contentType: false,
			// 	processData: false,
			// 	data: formData,
			// 	type: "post",
			// 	success: function(rs) {
			// 		Swal.close();
			// 		if (rs.success) {
			// 			$('#' + fileURL_id).val(rs.data.path);
			// 			$('#' + previewId).attr('data', fileURL).show();
			// 			$('#' + errorId).html(` (File: ${rs.data.originalName} - ${size_file} MB)`);
			// 			self.uploadHandler.afterAction(rs);
			// 		} else {
			// 			Swal.fire("Perhatian!", rs.error || "Gagal mengunggah file.", "warning");
			// 		}
			// 	},
			// 	error: function(xhr, status, error) {
			// 		Swal.close();
			// 		Swal.fire("Perhatian!", "Maaf, terjadi kesalahan. Silakan coba lagi nanti.", "warning");
			// 		console.error("Error: ", status, error);
			// 	}
			// });
			self.requestHandler = {
				beforeAction: function () {
					Swal.fire({
						title: "",
						icon: "info",
						text: "Proses mengambil file, mohon ditunggu...",
						didOpen: function () {
							Swal.showLoading();
						},
					});
				},
				afterAction: function (result) {
					Swal.close();
					if (result.success) {
						$('#' + fileURL_id).val(result.data.path);
						$('#' + previewId).attr('data', fileURL).show();
						$('#' + errorId).html(` (File: ${result.data.originalName} - ${size_file} MB)`);
						self.uploadHandler.afterAction(result);
					} else {
						Swal.fire("Perhatian!", result.error || "Gagal mengunggah file.", "warning");
					}
				},
				errorAction: function () {
					Swal.close();
					Swal.fire("Perhatian!", "Maaf, terjadi kesalahan. Silakan coba lagi nanti.", "warning");
				},
			};
			self.sendRequest(self.upload, "POST", "json", formData);
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
				$.each(self.csrf, function (key) {
					form_data.append(key, self.csrf[key]);
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
					url: self.upload2,
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
							self.uploadHandler2.afterAction(rs);
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
			//ANCHOR - Event upload audio
			.on("change", ".upload-aud", function () {
				var folder = $(this).data("folder");
				var file_data = $(this).prop("files")[0];
				var maxSize = $(this).data("max-size") ? $(this).data("max-size") * 1024 * 1024 : 1 * 1024 * 1024;
				let $this = $(this);
				if (file_data.size <= maxSize) {
					var form_data = new FormData();
					form_data.append("file", file_data);
					form_data.append("folder", folder);
					$.each(self.csrf, function (key) {
						form_data.append(key, self.csrf[key]);
					});
					Swal.fire({
						title: "",
						icon: "info",
						text: "Proses upload file, mohon ditunggu...",
						didOpen: function () {
							Swal.showLoading();
						},
					});
					self.sendRequest(`${self.url}_upload_aud`, 'POST', 'json', form_data)
						.then(response => {
							Swal.close(); // Close the loading alert
							if (response.success) {
								self.uploadAudioHandler.afterAction(response); // Handle success
							} else {
								Swal.fire("Perhatian!", response.error, "warning"); // Show error
							}
						})
						.catch(error => {
							Swal.close(); // Close the loading alert
							Swal.fire("Perhatian!", "Maaf, terjadi kesalahan. Silakan coba lagi nanti.", "warning"); // Show error
						});
				} else {
					Swal.fire("Perhatian!", "Ukuran file(" + (file_data.size / 1024 / 1024).toFixed(2) + "MB), tidak boleh melebihi 1MB", "warning");
					file_data = "";
				}
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
	// sendRequest(url, method = 'GET', dataType = 'json', data = {}) {
	// 	console.log("URL:", url, "Method:", method, "Data:", data);

	// 	const self = this;
	//     this.abortAllRequests();

	//     const request = $.ajax({
	//         url: url,
	//         method: method,
	// 		dataType: dataType,
	//         data: data,
	//         beforeSend: function() {
	//             console.log("Request dimulai...");
	// 			self.requestHandler.beforeAction();
	//         },
	//         success: function(response) {
	//             console.log("Response diterima:", response);
	// 			self.requestHandler.afterAction(response);
	//         },
	//         error: function(xhr, status, error) {
	// 			self.requestHandler.errorAction();
	// 			if (status !== 'abort') {
	// 				console.log("AJAX error:", error);
	//             }
	//         },
	//         complete: () => {
	//             this.requests = this.requests.filter(req => req !== request);
	//         }
	//     });
	//     this.requests.push(request);
	// }
	// sendRequest(url, method = 'POST', dataType = 'json', data = {}) {
	// 	const self = this;
	// 	this.abortAllRequests(); // Aborts any ongoing requests
	// 	const csrfToken = document.querySelector('input[name="x_token"]').getAttribute('value');
	// 	data.csrf_token = csrfToken;

	// 	// const request = $.ajax({
	// 	// 	url: url,
	// 	// 	method: method,
	// 	// 	dataType: dataType,
	// 	// 	contentType: 'application/json', // Set content type for JSON
	// 	// 	data: JSON.stringify(data), // Serialize data if necessary
	// 	// 	beforeSend: function() {
	// 	// 		console.log("Request dimulai...");
	// 	// 		self.requestHandler.beforeAction();
	// 	// 	},
	// 	// 	success: function(response) {
	// 	// 		console.log("Response diterima:", response);
	// 	// 		self.requestHandler.afterAction(response);
	// 	// 	},
	// 	// 	error: function(xhr, status, error) {
	// 	// 		console.error("AJAX error:", {
	// 	// 			xhr: xhr,
	// 	// 			status: status,
	// 	// 			error: error
	// 	// 		});
	// 	// 		self.requestHandler.errorAction();
	// 	// 	},
	// 	// 	complete: () => {
	// 	// 		this.requests = this.requests.filter(req => req !== request);
	// 	// 	}
	// 	// });
	// 	const request = $.ajax({
	// 		url: url,
	// 		method: method,
	// 		dataType: dataType,
	// 		contentType: false, // Prevent jQuery from setting content type
	// 		processData: false, // Prevent jQuery from processing the data
	// 		data: data, // Send the FormData object
	// 		beforeSend: function() {
	// 			console.log("Request dimulai...");
	// 			self.requestHandler.beforeAction();
	// 		},
	// 		success: function(response) {
	// 			console.log("Response diterima:", response);
	// 			self.requestHandler.afterAction(response);
	// 		},
	// 		error: function(xhr, status, error) {
	// 			console.error("AJAX error:", {
	// 				xhr: xhr,
	// 				status: status,
	// 				error: error
	// 			});
	// 			self.requestHandler.errorAction();
	// 		},
	// 		complete: () => {
	// 			this.requests = this.requests.filter(req => req !== request);
	// 		}
	// 	});
	// 	this.requests.push(request);
	// }
	sendRequest(url, method = 'POST', dataType = 'json', data = {}) {
		const self = this;
		this.abortAllRequests(); // Aborts any ongoing requests

		return new Promise((resolve, reject) => {
			const request = $.ajax({
				url: url,
				method: method,
				dataType: dataType,
				contentType: data instanceof FormData ? false : 'application/x-www-form-urlencoded; charset=UTF-8', // Set content type based on data type
				processData: !(data instanceof FormData), // Process data if not FormData
				data: data, // Send the data
				beforeSend: function () {
					console.log("Request dimulai...");
					self.requestHandler.beforeAction();
				},
				success: function (response) {
					console.log("Response diterima:", response);
					self.requestHandler.afterAction(response);
					resolve(response); // Resolve the promise with the response
				},
				error: function (xhr, status, error) {
					console.error("AJAX error:", {
						xhr: xhr,
						status: status,
						error: error
					});
					self.requestHandler.errorAction();
					reject(error); // Reject the promise with the error
				},
				complete: () => {
					this.requests = this.requests.filter(req => req !== request);
				}
			});
			this.requests.push(request);
		});
	}


	//ANCHOR - loadDatatable
	loadDatatable(element, url, tableColumn, order = null) {
		var self = this;
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
					// console.log(self);
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
					$.extend(form, self.csrf);
					return form;
				},
				complete: function (response) {
					// console.log(response);
					feather.replace();
				},
			},
			columns: self.tableColumn,
		}).on("init.dt", function () {
			// $(this).css('width','100%');
		});
		return table;
	}
	//ANCHOR - select2Init
	select2Init(id, url, placeholder, parameter, parent = null, selection = null, formatHTML = null) {
		var self = this;
		if ($(id).data("select2")) {
			$(id).select2("destroy");
			self.select2Init(id, url, placeholder, parameter, parent, selection, formatHTML);
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
						url: self.ajax + url,
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
							console.log("HAHAYUKKK", data.text)
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
						{ return m };
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
					allowClear: true,
					width: "100%",
					dropdownParent: parent == null ? $(document.body) : $(parent),
				});
			}
		}
	}
	//ANCHOR - select2Init_multiple
	select2Init_multiple(id, url, placeholder, parameter, parent = null, selection = null, formatHTML = null) {
		var self = this;
		if ($(id).data("select2")) {
			$(id).select2("destroy");
			self.select2Init_multiple(id, url, placeholder, parameter, parent, selection, formatHTML);
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
						url: self.ajax + url,
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
		var self = this;
		const {
			id,
			drops = "auto",
			opens = "right",
			format = 'DD-MM-YYYY',
			singleDatePicker = false,
			showDropdowns = true,
			alwaysShowCalendars = true,
			startDate = moment().startOf('month'),
			endDate = moment().endOf('month'),
			minYear = 2015,
			maxYear = parseInt(moment().format('YYYY'), 11),
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

		// console.log(`daterangepickerInit ${id}`);
		$(id).daterangepicker({
			opens,
			drops,
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
			startDate, endDate,
			minDate, maxDate,
			minYear, maxYear,
			maxSpan: {
				days: moment(startDate).daysInMonth() + 2
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
			if (self.drp?.applyHandler) {
				self.drp.applyHandler(ev, picker);
			}
		});

		$(id).on("cancel.daterangepicker", function (ev, picker) {
			$(this).val("");
			if (self.drp?.cancelHandler) {
				self.drp.cancelHandler(ev, picker);
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
		let self = $(this);
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

	// detectSelect2Fields() {
	//     return $('#form select').map(function() {
	// 		console.log(`Field ${$(this).attr('id')} detected`);
	//         return $(this).attr('id');  // Returns an array of field IDs
	//     }).get();
	// }

	// handleLocalStorage(fieldId = null, fieldValue = null, isSel2 = false, action = 'set') {
	//     let settings = JSON.parse(localStorage.getItem(this.storageKey)) || {};
	//     if (action === 'set') {
	//         if (fieldId && fieldValue !== null) {
	//             // Store select2 data differently
	// 			console.log(`handleLocalStorage ${action} ${fieldId} to ${fieldValue}`);
	//             if (isSel2) {
	//                 const data = $('#' + fieldId).select2('data')[0];
	// 				console.log(`data: ${JSON.stringify(data)}`);
	//                 settings[`sel2_${fieldId}`] = { id: data.id, name: data.text };
	// 				console.log(`handle Field ${fieldId} changed to: ${JSON.stringify(data.text)}`);
	//             } else {
	// 				settings[fieldId] = fieldValue;  // Store other form field values
	// 				console.log(`handle Field ${fieldId} changed to: ${fieldValue}`);
	//             }
	//             localStorage.setItem(this.storageKey, JSON.stringify(settings));  // Save to localStorage
	//         } else if (fieldId === null) {
	//             localStorage.removeItem(this.storageKey);  // Remove data if no fieldId is provided
	// 			console.log('localStorage removed');
	//         }
	//     } else if (action === 'get') {
	//         if (fieldId) {
	//             if (isSel2 && settings[`sel2_${fieldId}`]) {
	// 				console.log(`Field ${fieldId} restored to: ${settings[`sel2_${fieldId}`].name}`);
	//                 return settings[`sel2_${fieldId}`];  // Return the select2 data
	//             }
	//             return settings[fieldId] || null;  // Return other field data
	//         } else {
	// 			return settings;  // Return all stored settings if no fieldId
	//         }
	//     }

	// }

	// initializeFormListeners() {
	//     const self = this;
	// 	console.log('initializeFormListeners');
	//     $('#form input, #form textarea, #form select').each(function() {
	//         const fieldId = $(this).attr('id');
	// 		console.log(`Field ${fieldId} initialized`);
	//         const isSel2 = self.fieldSel2.includes(fieldId);
	//         $(this).on('input change', function() {
	// 			console.log(`Field ${fieldId} changed`);
	// 			let fieldValue;
	//         if (isSel2) {
	//             const select2Data = $(this).select2('data')[0];
	// 			fieldValue = { id: select2Data.id, name: select2Data.text }; // Select2 data
	//         } else {
	//             fieldValue = $(this).val(); // Standard value for input/textarea
	//         }

	//         self.handleLocalStorage(fieldId, fieldValue, isSel2);
	//         });
	//     });
	// }

	// restoreFormValues() {
	//     const self = this;
	//     $('#form input, #form textarea, #form select').each(function() {
	//         const fieldId = $(this).attr('id');
	//         const isSel2 = self.fieldSel2.includes(fieldId);
	//         const data = self.handleLocalStorage(fieldId, null, isSel2, 'get');
	//         if (data) {
	//             if (isSel2) {
	//                 $(this).append(new Option(data.name, data.id, true, true)).trigger('change');
	//             } else {
	//                 $(this).val(data).trigger('change');
	//             }
	//         }
	//     });
	// }
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
