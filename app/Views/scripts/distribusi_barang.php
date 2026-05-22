<script>
    const BASE_URL = "<?= base_url('admin/distribusi') ?>";

    let table;

    $(document).ready(function() {

        setToday();

        loadBarang();

        table = $('#datatable').DataTable({
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
                    data: 'qty',
                    className: 'text-center'
                },
                {
                    data: 'note',
                    defaultContent: '-'
                },
                {
                    data: 'area',
                    defaultContent: '-'
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function(row) {
                        let buttons = '';

                        <?php if (can('distribusi_barang_detail')): ?>
                            buttons += `
                                <button class="btn btn-sm btn-info btn-detail" data-id="${row.id}" title="Detail">
                                    <i class="bx bx-show"></i>
                                </button>
                            `;
                        <?php endif; ?>

                        <?php if (can('distribusi_barang_edit')): ?>
                            buttons += `
                                <button class="btn btn-sm btn-warning btn-edit" data-id="${row.id}" title="Edit">
                                    <i class="bx bx-pencil"></i>
                                </button>
                            `;
                        <?php endif; ?>

                        <?php if (can('distribusi_barang_delete')): ?>
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
        $.get("<?= base_url('admin/stok-masuk/data-barang') ?>", function(res) {

            let data = res.map(item => ({
                id: item.id,
                text: `${item.code} - ${item.nama}` +
                    (item.is_locked == 1 ? ' (Sedang Opname)' : ''),
                disabled: item.is_locked == 1
            }));

            $('#barang_id').select2({
                placeholder: 'Pilih Barang',
                width: '100%',
                allowClear: true,
                data: data
            });

        });
    }

    $('#form').submit(function(e) {
        e.preventDefault();
        let id = $('#id').val();

        let url = id ?
            BASE_URL + '/update/' + id :
            BASE_URL + '/save';

        let data = {
            tanggal: $('#tanggal').val(),
            barang_id: $('#barang_id').val(),
            qty: $('#qty').val(),
            team_leader: $('#team_leader').val(),
            area: $('#area').val(),
            note: $('#note').val()
        };

        if (!data.barang_id) {
            Swal.fire('Error', 'Pilih barang dulu', 'error');
            return;
        }

        if (!data.team_leader) {
            Swal.fire('Error', 'Team Leader wajib diisi', 'error');
            return;
        }

        if (!data.qty || data.qty <= 0) {
            Swal.fire('Error', 'Qty tidak valid', 'error');
            return;
        }

        Swal.fire({
            title: 'Loading...',
            text: 'Sedang menyimpan data',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

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
                    $('#barang_id').val(null).trigger('change');

                    $('a[href="#tab-data"]').tab('show');
                    table.ajax.reload(null, false);
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            },

            error: function(xhr) {
                Swal.close();
                Swal.fire('Error', 'Terjadi kesalahan server', 'error');
            }
        });
    });

    $(document).on('click', '.btn-edit', function() {
        let id = $(this).data('id');

        $.get(BASE_URL + '/detail/' + id, function(res) {
            $('#id').val(res.id);
            $('#tanggal').val(res.tanggal);
            $('#barang_id').val(res.barang_id).trigger('change');
            $('#qty').val(res.qty);
            $('#team_leader').val(res.team_leader);
            $('#area').val(res.area);
            $('#note').val(res.note);

            $('a[href="#tab-form"]').tab('show');
        });
    });

    $(document).on('click', '.btn-detail', function() {
        let id = $(this).data('id');

        $.get(BASE_URL + '/detail/' + id, function(res) {
            Swal.fire({
                title: 'Detail Distribusi',
                width: 650,
                html: `
                <div class="text-left">
                    <div class="mb-3 p-2 border rounded bg-light">
                        <small class="text-muted">Distribusi dibuat oleh</small>
                        <h5 class="mb-0 text-primary">
                            <i class="bx bx-user"></i>
                            ${res.user ?? '-'}
                        </h5>
                    </div>

                    <table class="table table-bordered table-sm mb-0 text-start">
                        <tr>
                            <th width="35%">Kode Barang</th>
                            <td>${res.code ?? '-'}</td>
                        </tr>
                        <tr>
                            <th>Nama Barang</th>
                            <td>${res.nama ?? '-'}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge bg-danger">
                                    Keluar
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Qty Distribusi</th>
                            <td>${res.qty ?? 0}</td>
                        </tr>
                        <tr>
                            <th>Team Leader</th>
                            <td>${res.team_leader ?? '-'}</td>
                        </tr>
                        <tr>
                            <th>Area</th>
                            <td>${res.area ?? '-'}</td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td>${res.tanggal ?? '-'}</td>
                        </tr>
                        <tr>
                            <th>Catatan</th>
                            <td>${res.note ?? '-'}</td>
                        </tr>
                    </table>

                </div>
            `
            });
        });
    });

    $(document).on('click', '.btn-delete', function() {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Hapus distribusi?',
            text: 'Stok akan dikembalikan ke gudang',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus'
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.post(BASE_URL + '/delete/' + id, function(res) {
                if (res.status === 'success') {
                    Swal.fire('Berhasil', res.message, 'success');
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