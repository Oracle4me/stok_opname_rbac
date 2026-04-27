<script>
    const BASE_URL = "<?= base_url('admin/role') ?>";
    let table;

    $(document).ready(function() {

        table = $('#datatable').DataTable({
            processing: true,
            ajax: {
                url: BASE_URL,
                type: 'GET',
                dataSrc: 'data'
            },
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'nama'
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(row) {
                        let btn = '';
                        <?php if (can('role_edit')): ?>
                            btn += `
                        <button class="btn btn-sm btn-success btn-edit"
                            data-id="${row.id}"
                            data-nama="${row.nama}"
                            data-active="${row.is_active}">
                            <i class="bx bx-pencil"></i>
                        </button>`;
                        <?php endif; ?>

                        <?php if (can('role_delete')): ?>
                            btn += `
                        <button class="btn btn-sm btn-danger btn-delete"
                        data-id="${row.id}">
                            <i class="bx bx-trash"></i>
                        </button>`;
                        <?php endif; ?>

                        return btn;
                    }
                }
            ]
        });

        // Submit
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
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",

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
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },

                error: function(xhr) {
                    Swal.close();
                    console.log(xhr.responseText);

                    Swal.fire('Error', 'Terjadi kesalahan server', 'error');
                }
            });
        });

    });

    // Edit
    $(document).on('click', '.btn-edit', function() {

        let id = $(this).data('id');
        let nama = $(this).data('nama');
        let active = $(this).data('active');

        $('#formTitle').text($('#formTitle').data('edit'));
        
        $('#id').val(id);
        $('#nama').val(nama);
        $('#is_active').prop('checked', active == 1);

        $('a[href="#tab-form"]').tab('show');
    });

    // Delete
    $(document).on('click', '.btn-delete', function() {

        let id = $(this).data('id');

        Swal.fire({
            title: 'Yakin?',
            text: "Role akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({
                    url: BASE_URL + '/delete/' + id,
                    method: "POST",
                    dataType: "json",

                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire('Berhasil', res.message, 'success');
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    },

                    error: function(xhr) {
                        console.log(xhr.responseText);
                        Swal.fire('Error', 'Terjadi kesalahan server', 'error');
                    }
                });

            }
        });
    });

    // Reset Form
    $('#btn_reset').on('click', function() {
        $('#form')[0].reset();
        $('#formTitle').text($('#formTitle').data('add'));
        $('#id').val('');
    })
</script>