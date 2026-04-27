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
                    data: 'team_leader',
                    defaultContent: '-'
                },
                {
                    data: 'area',
                    defaultContent: '-'
                },
                {
                    data: 'user',
                    defaultContent: '-'
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
            url: BASE_URL + '/save',
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

    $('#btn_reset').on('click', function() {
        $('#form')[0].reset();
        $('#barang_id').val(null).trigger('change');

        setToday();
    });
</script>