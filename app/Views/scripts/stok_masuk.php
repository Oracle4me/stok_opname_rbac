<script>
    const BASE_URL = "<?= base_url('admin/stok-masuk') ?>";

    $(document).ready(function() {

        setToday();

        $('#barang_id').select2({
            placeholder: 'Pilih Barang',
            width: '100%',
            allowClear: true
        });

        loadBarang();

        table = $('#datatables').DataTable({
            processing: true,
            responsive: true,
            autoWidth: false,
            order: [
                [0, 'desc']
            ],

            ajax: {
                url: BASE_URL + '/data',
                type: 'GET',
                dataSrc: 'data'
            },

            columns: [{
                    data: 'tanggal'
                },
                {
                    data: 'nama'
                },
                {
                    data: 'tipe',
                    defaultContent: '-',
                    className: "text-center",
                    render: function(data) {
                        if (!data) return "-"
                        console.log("dadadad", data)
                        return `<span class="badge bg-success">Masuk</span>`;
                    }
                },
                {
                    data: 'qty',
                    className: 'text-center'
                },
                {
                    data: 'keterangan',
                    defaultContent: '-'
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function(row) {
                        let buttons = '';

                        <?php if (can('sttok_masuk_detail')): ?>
                            buttons += `
                                <button class="btn btn-sm btn-info btn-detail" data-id="${row.id}" title="Detail">
                                    <i class="bx bx-show"></i>
                                </button>
                            `;
                        <?php endif; ?>

                        <?php if (can('stok_masuk_edit')): ?>
                            buttons += `
                            <button class="btn btn-sm btn-warning btn-edit" data-id="${row.id}" title="Edit">
                                <i class="bx bx-pencil"></i>
                            </button>
                        `;
                        <?php endif; ?>

                        <?php if (can('stok_masuk_delete')): ?>
                            buttons += `
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${row.id}" title="Hapus">
                                <i class="bx bx-trash"></i>
                            </button>
                        `;
                        <?php endif; ?>

                        return buttons || '-';
                    }
                }
            ]
        });

    });

    function setToday() {
        const today = new Date().toLocaleDateString('en-CA', {
            timeZone: 'Asia/Jakarta'
        });

        $('#tanggal').val(today);
        $('#tanggal').attr('max', today);
    }

    function loadBarang() {
        $.get(BASE_URL + '/data-barang', function(res) {

            let data = res.map(item => ({
                id: item.id,
                text: `${item.code} - ${item.nama}` +
                    (item.is_locked == 1 ? ' (Sedang Opname)' : ''),
                disabled: item.is_locked == 1
            }));

            $('#barang_id').select2({
                placeholder: 'Pilih Barang',
                width: '100%',
                data: data
            });

        });
    }

    $('#form').submit(function(e) {
        e.preventDefault();
        let id = $('#id').val();

        Swal.fire({
            title: 'Loading...',
            text: 'Sedang menyimpan data',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        let data = {
            tanggal: $('#tanggal').val(),
            barang_id: $('#barang_id').val(),
            qty: $('#qty').val(),
            keterangan: $('#keterangan').val()
        };

        if (!data.barang_id) {
            Swal.fire('Error', 'Pilih barang dulu', 'error');
            return;
        }
        let url = id ?
            BASE_URL + '/update/' + id :
            BASE_URL + '/save';

        $.ajax({
            url: url,
            method: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json',
            dataType: 'json',

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
                    $('#id').val('');

                    $('a[href="#tab-data"]').tab('show');
                    table.ajax.reload(null, false);
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },

            error: function() {
                Swal.close();
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            }
        });
    });

    // Fungsi Detail
    $(document).on('click', '.btn-detail', function() {
        let id = $(this).data('id');

        $.get(BASE_URL + '/detail/' + id, function(res) {
            Swal.fire({
                title: 'Detail Stok Masuk',
                width: 650,
                html: `
                <div class="text-left">

                    <div class="mb-3 p-2 border rounded bg-light">
                        <small class="text-muted">Dibuat oleh</small>
                        <h5 class="mb-0 text-primary gap-2">
                            <i class="fas fa-user mr-1"></i>
                            ${res.user ?? '-'}
                        </h5>
                    </div>

                    <table class="table table-bordered table-sm mb-0 text-start">
                        <tr>
                            <th width="35%" class="text-start">Kode Barang</th>
                            <td>${res.code ?? '-'}</td>
                        </tr>
                        <tr>
                            <th >Nama Barang</th>
                            <td>${res.nama ?? '-'}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge bg-success">
                                    Masuk
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Qty Masuk</th>
                            <td>${res.qty ?? '-'}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Transaksi</th>
                            <td>${res.created_at ?? '-'}</td>
                        </tr>
                        <tr>
                            <th>Keterangan</th>
                            <td>${res.keterangan ?? '-'}</td>
                        </tr>
                    </table>

                </div>
            `,
                confirmButtonText: 'Tutup'
            });
        });
    });

    $(document).on('click', '.btn-edit', function() {
        let id = $(this).data('id');

        $.get(BASE_URL + '/detail/' + id, function(res) {
            $('#id').val(res.id);
            $('#barang_id').val(res.barang_id).trigger('change');
            $('#qty').val(Math.abs(res.selisih));
            $('#keterangan').val(res.keterangan);

            $('a[href="#tab-form"]').tab('show');
        });
    });

    $(document).on('click', '.btn-delete', function() {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Hapus data?',
            text: 'Stok akan dikembalikan seperti semula',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus'
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.post(BASE_URL + '/delete/' + id, function(res) {
                if (res.status === 'success') {
                    Swal.fire('Success', res.message, 'success');
                    table.ajax.reload(null, false);
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            });
        });
    });

    $('#btn_reset').on('click', function() {
        $('#form')[0].reset();
        $('#barang_id').val(null).trigger('change');
        $('#id').val('');

        setToday();
    });
</script>