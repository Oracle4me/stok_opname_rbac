<script>
    const BASE_URL = "<?= base_url('admin/barang') ?>";
    
    let table;
    
    $(document).ready(function() {
        setToday();
        let columns = [{
                data: null,
                orderable: false,
                searchable: false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'tanggal'
            },
            {
                data: 'code'
            },
            {
                data: 'nama'
            },
            {
                data: 'kategori',
                defaultContent: '-'
            },
            {
                data: 'satuan',
                defaultContent: '-'
            }
        ];

        <?php if (can('barang_edit') || can('barang_delete')): ?>
            columns.push({
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(data, type, row) {

                    let btn = '';

                    <?php if (can('barang_edit')): ?>
                        btn += `<button class="btn btn-sm btn-success me-1"
                onclick="editData(${row.id})">
                <i class="bx bx-pencil"></i>
            </button>`;
                    <?php endif; ?>

                    <?php if (can('barang_delete')): ?>
                        btn += `<button class="btn btn-sm btn-danger"
                onclick="deleteData(${row.id})">
                <i class="bx bx-trash"></i>
            </button>`;
                    <?php endif; ?>

                    return btn;
                }
            });
        <?php endif; ?>

        table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            autoWidth: false,
            order: [
                [1, 'asc']
            ],

            ajax: {
                url: BASE_URL,
                type: "GET"
            },

            columns: columns
        });

        // Select2
        const $kategori = $('#kategori_id').select2({
            placeholder: 'Pilih Kategori',
            width: '100%',
            allowClear: true
        });

        const $satuan = $('#satuan_id').select2({
            placeholder: 'Pilih Satuan',
            width: '100%',
            allowClear: true
        });

        $.get(BASE_URL + '/kategori', function(res) {
            let html = '<option></option>';

            res.data.forEach(k => {
                html += `<option value="${k.id}">${k.nama}</option>`;
            });

            $kategori.html(html).trigger('change');
        });

        $.get(BASE_URL + '/satuan', function(res) {
            let html = '<option></option>';

            res.data.forEach(s => {
                html += `<option value="${s.id}">${s.nama}</option>`;
            });

            $satuan.html(html).trigger('change');
        });
    });

    function setToday() {
        const today = new Date().toLocaleDateString('en-CA', {
            timeZone: 'Asia/Jakarta'
        });

        $('#tanggal').attr('max', today);
        $('#tanggal').val(today);
    }


    $('#form').on('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Loading...',
            text: 'Sedang menyimpan data',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });


        $.ajax({
            url: BASE_URL + '/save',
            method: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                Swal.close();

                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });

                    $('#form')[0].reset();
                    $('#kategori_id').val('').trigger('change');
                    $('#satuan_id').val('').trigger('change');
                    $('#id').val('');

                    table.ajax.reload(null, false);

                    $('a[href="#tab-data"]').tab('show');
                } else {
                    Swal.fire({
                        icon: res.status,
                        title: 'Gagal',
                        text: res.message || 'Terjadi kesalahan'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan server'
                });
                console.log(xhr.responseText);
            }
        });
    });

    function editData(id) {

        $.get(BASE_URL + '/' + id, function(res) {
            setToday();
            $('#formTitle').text($('#formTitle').data('edit'));

            $('#id').val(res.id);
            $('#code').val(res.code).prop('readonly', true);
            $('#nama').val(res.nama);

            $('#kategori_id').val(res.kategori_id).trigger('change');
            $('#satuan_id').val(res.satuan_id).trigger('change');

            $('a[href="#tab-form"]').tab('show');
        });

    }

    function deleteData(id) {

        Swal.fire({
            title: 'Yakin?',
            text: "Data akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({
                    url: BASE_URL + '/delete/' + id,
                    method: 'POST',
                    success: function(res) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });

                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal menghapus data'
                        });
                        console.log(xhr.responseText);
                    }
                });

            }

        });
    }

    $('#btn_reset').on('click', function() {
        
        $('#form')[0].reset();
        $('#formTitle').text($('#formTitle').data('add'));

        $('#id').val('');
        $('#code').prop('readonly', false).val('');

        $('#kategori_id').val(null).trigger('change');
        $('#satuan_id').val(null).trigger('change');
    })
</script>