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
                    data: 'qty',
                    className: 'text-center'
                },
                {
                    data: 'keterangan',
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

    $('#btn_reset').on('click', function() {
        $('#form')[0].reset();
        $('#barang_id').val(null).trigger('change');
        $('#id').val('');

        setToday();
    });
</script>