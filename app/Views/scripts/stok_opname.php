<script>
    const BASE_URL = "<?= base_url('admin/stok/opname') ?>";

    let table;
    let selectedItems = {};


    $(document).ready(function() {
        setToday();
        // Select2 Barang
        $('#select-barang').select2({
            placeholder: 'Cari barang...',
            width: '100%',
            ajax: {
                url: BASE_URL + '/search-barang',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.map(item => ({
                            id: item.id,
                            text: item.code + ' - ' + item.nama,
                            stok: item.stok,
                            code: item.code,
                            nama: item.nama
                        }))
                    };
                }
            }
        });

        let columns = [{
                data: 'tanggal'
            },
            {
                data: 'keterangan',
                defaultContent: '-'
            },

            {
                data: 'status',
                className: 'text-center',
                render: function(data) {
                    return data === 'final' ?
                        `<span class="badge bg-primary">Final</span>` :
                        `<span class="badge bg-warning text-dark">Draft</span>`;
                }
            },

            {
                data: 'user',
                defaultContent: '-'
            }
        ];

        <?php if (can('stok_edit') || can('stok_final') || can('stok_delete_draft')): ?>
            columns.push({
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(row) {

                    if (row.status === 'final') {
                        return `
                    <button class="btn btn-sm btn-info btn-detail" data-id="${row.id}">
                        Detail
                    </button>
                `;
                    }

                    let buttons = '';

                    <?php if (can('stok_edit')): ?>
                        buttons += `
                    <button class="btn btn-sm btn-primary btn-edit" data-id="${row.id}">
                        Edit
                    </button>
                `;
                    <?php endif; ?>

                    <?php if (can('stok_delete_draft')): ?>
                        buttons += `
                    <button class="btn btn-sm btn-danger btn-delete" data-id="${row.id}">
                        Hapus
                    </button>
                `;
                    <?php endif; ?>

                    return buttons || '-';
                }
            });
        <?php endif; ?>

        table = $('#table-opname-data').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            autoWidth: false,
            order: [
                [0, 'desc']
            ],
            ajax: {
                url: BASE_URL + '/data',
                type: 'GET'
            },
            columns: columns
        });

    });

    // Set tanggal
    function setToday() {
        const today = new Date().toLocaleDateString('en-CA', {
            timeZone: 'Asia/Jakarta'
        });

        $('#tanggal').attr('max', today);
        $('#tanggal').val(today);
    }


    // Tambah Barang
    $('#btn-add-barang').click(function() {
        let data = $('#select-barang').select2('data')[0];
        if (!data) {
            Swal.fire('Error', 'Pilih barang dulu', 'error');
            return;
        }

        if (selectedItems[data.id]) {
            Swal.fire('Info', 'Barang sudah ditambahkan', 'info');
            return;
        }

        selectedItems[data.id] = true;
        let stok = parseInt(data.stok ?? 0);

        let row = `
    <tr>
        <td>${data.code}</td>
        <td>${data.nama}</td>
        <td class="stok-sistem text-center">${stok}</td>
        <td class="text-center">
            <input type="number" class="form-control stok-fisik text-center" value="${stok}">
        </td>
        <td class="selisih text-center">0</td>
        <td>
            <input type="text" class="form-control keterangan">
        </td>
        <td class="text-center">
            <button class="btn btn-sm btn-danger btn-remove">
                <i class="bx bx-trash"></i>
            </button>
        </td>
        <input type="hidden" class="barang-id" value="${data.id}">
    </tr>
    `;

        $('#table-opname tr:contains("Belum ada barang")').remove();
        $('#table-opname').append(row);
        $('#select-barang').val(null).trigger('change');
    });

    $(document).on('click', '.btn-detail', function() {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Loading...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.get(BASE_URL + '/detail/' + id, function(res) {

            Swal.close();

            // header
            $('#detail-tanggal').text(res.header.tanggal);
            $('#detail-keterangan').text(res.header.keterangan || '-');

            let html = '';

            res.detail.forEach(item => {
                let selisihClass = '';
                if (item.selisih < 0) selisihClass = 'text-danger';
                else if (item.selisih > 0) selisihClass = 'text-success';

                html += `
            <tr>
                <td>${item.code}</td>
                <td>${item.nama}</td>
                <td class="text-center">${item.stok_sistem}</td>
                <td class="text-center">${item.stok_fisik}</td>
                <td class="text-center ${selisihClass}">
                    ${item.selisih}
                </td>
                <td>${item.keterangan ?? '-'}</td>
            </tr>`;
            });

            $('#detail-table').html(html);

            $('#modal-detail').modal('show');
        });
    });


    $(document).on('click', '.btn-remove', function() {

        let row = $(this).closest('tr');
        let id = row.find('.barang-id').val();

        delete selectedItems[id];
        row.remove();

        if ($('#table-opname tr').length === 0) {
            $('#table-opname').html(`
        <tr>
            <td colspan="7" class="text-center text-muted">
                Belum ada barang dipilih
            </td>
        </tr>`);
        }
    });

    // Auto hitung stok fisik
    $(document).on('input', '.stok-fisik', function() {

        let row = $(this).closest('tr');
        let sistem = parseInt(row.find('.stok-sistem').text()) || 0;
        let fisik = parseInt($(this).val()) || 0;
        let selisih = fisik - sistem;
        let el = row.find('.selisih');

        el.text(selisih);
        el.removeClass('text-danger text-success');

        if (selisih < 0) el.addClass('text-danger');
        else if (selisih > 0) el.addClass('text-success');

        if (selisih !== 0) row.addClass('table-warning');
        else row.removeClass('table-warning');
    });

    // Edit Button
    $(document).on('click', '.btn-edit', function() {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Loading...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.get(BASE_URL + '/detail/' + id, function(res) {

            Swal.close();

            selectedItems = {};

            $('#id').val(res.header.id);
            $('#tanggal').val(res.header.tanggal);
            $('#keterangan').val(res.header.keterangan);

            $('.nav-link[href="#tab-form"]').tab('show');

            let html = '';

            res.detail.forEach(item => {
                selectedItems[item.barang_id] = true;
                html += `
            <tr>
                <td>${item.code}</td>
                <td>${item.nama}</td>
                <td class="stok-sistem text-center">${item.stok_sistem}</td>
                <td class="text-center">
                    <input type="number" class="form-control stok-fisik text-center" value="${item.stok_fisik}">
                </td>
                <td class="selisih text-center">
                    ${item.selisih}
                </td>
                <td>
                    <input type="text" class="form-control keterangan" value="${item.keterangan ?? ''}">
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-danger btn-remove">
                        <i class="bx bx-trash"></i>
                    </button>
                </td>
                <input type="hidden" class="barang-id" value="${item.barang_id}">
            </tr>`;
            });

            $('#table-opname').html(html);

            setTimeout(() => {
                $('.stok-fisik').trigger('input');
            }, 50);
        });
    });

    // Save Opname
    function saveOpname(status) {
        let items = [];

        $('#table-opname tr').each(function() {
            let row = $(this);
            let val = row.find('.stok-fisik').val();
            if (!val) return;
            let stok_sistem = parseInt(row.find('.stok-sistem').text()) || 0;
            let stok_fisik = parseInt(val) || 0;

            items.push({
                barang_id: row.find('.barang-id').val(),
                stok_sistem,
                stok_fisik,
                selisih: stok_fisik - stok_sistem,
                keterangan: row.find('.keterangan').val()
            });
        });

        if (items.length === 0) {
            Swal.fire('Error', 'Belum ada barang', 'error');
            return;
        }

        if (status === 'final') {
            let hasChange = items.some(i => i.selisih !== 0);
            if (!hasChange) {
                Swal.fire('Info', 'Tidak ada perubahan stok', 'info');
                return;
            }
        }

        Swal.fire({
            title: 'Loading...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: BASE_URL + '/save',
            method: 'POST',
            data: JSON.stringify({
                id: $('#id').val(),
                tanggal: $('#tanggal').val(),
                nama_barang: $('#nama_barang').val(),
                status,
                items
            }),
            contentType: 'application/json',
            success: function(res) {
                Swal.close();
                Swal.fire('Success', res.message, 'success');

                selectedItems = {};
                $('#id').val('');
                $('#table-opname').html('');

                table.ajax.reload(null, false);

                $('.nav-link[href="#tab-data"]').tab('show');
            }
        });
    }

    // Save Draft
    $('#btn-draft').click(() => saveOpname('draft'));
    $('#btn-final').click(function() {
        Swal.fire({
            title: 'Yakin?',
            text: 'Stok akan diperbarui!',
            icon: 'warning',
            showCancelButton: true
        }).then(res => {
            if (res.isConfirmed) saveOpname('final');
        });
    });


    $(document).on('click', '.btn-delete', function() {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Yakin?',
            text: 'Draft stok opname akan dihapus',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus'
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: BASE_URL + '/delete/' + id,
                method: 'POST',
                success: function(res) {
                    Swal.fire('Success', res.message, 'success');
                    table.ajax.reload(null, false);
                },
                error: function() {
                    Swal.fire('Error', 'Gagal menghapus data', 'error');
                }
            });
        });
    });
</script>