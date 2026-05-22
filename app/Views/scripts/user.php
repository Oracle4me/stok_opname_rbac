<script src="<?= base_url('assets/js/pages/pass-addon.init.js') ?>"></script>
<script>
    const BASE_URL = "<?= base_url('admin') ?>";
    let table;

    $(document).ready(function() {
        // Select2 Pilih Role
        $('#select_role').select2({
            placeholder: 'Pilih Role',
            width: '100%',
            ajax: {
                url: BASE_URL + '/role/select',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term || ''
                    };
                },
                processResults: function(data) {
                    return data;
                }
            }
        });

        // Datatables
        table = $('#datatable').DataTable({
            processing: true,
            ajax: {
                url: BASE_URL + '/users',
                type: 'GET',
                dataSrc: 'data'
            },
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'username'
                },
                {
                    data: 'nama'
                },
                {
                    data: 'email'
                },
                {
                    data: 'role_nama',
                    defaultContent: '-'
                },

                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function(row) {

                        let btn = '';

                        <?php if (can('user_edit')): ?>
                            btn += `
                    <button class="btn btn-sm btn-success me-1 btn-edit"
                        data-id="${row.id}"
                        data-username="${encodeURIComponent(row.username)}"
                        data-nama="${encodeURIComponent(row.nama)}"
                        data-email="${encodeURIComponent(row.email)}"
                        data-role="${row.role_id}"
                        data-status="${row.status}">
                        <i class="bx bx-pencil"></i>
                    </button>`;
                        <?php endif; ?>

                        <?php if (can('user_delete')): ?>
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

    });

    // Submit Form logic
    $('#form').on('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Loading...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: BASE_URL + '/users/save',
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

                    $('#select_role').val(null).trigger('change');

                    table.ajax.reload(null, false);

                    $('a[href="#tab-data"]').tab('show');

                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            },

            error: function(xhr) {
                console.log(xhr.responseText);
                Swal.fire('Error', 'Terjadi kesalahan server', 'error');
            }
        });
    });

    // Edit Form 
    $(document).on('click', '.btn-edit', function() {

        let id = $(this).data('id');
        let username = decodeURIComponent($(this).data('username'));
        let nama = decodeURIComponent($(this).data('nama'));
        let email = decodeURIComponent($(this).data('email'));
        let role_id = $(this).data('role');
        let status = $(this).data('status');

        $('#formTitle').text($('#formTitle').data('edit'));
        $('#id').val(id);
        $('#username').val(username);
        $('#nama').val(nama);
        $('#email').val(email);

        const $role = $('select[name="role_id"]');
        $role.val(null).trigger('change');

        if (role_id) {
            let option = new Option('Loading...', role_id, true, true);
            $role.append(option).trigger('change');

            $.get(BASE_URL + '/role/select', function(res) {
                let found = res.results.find(r => r.id == role_id);

                if (found) {
                    let newOption = new Option(found.text, found.id, true, true);
                    $role.append(newOption).trigger('change');
                }
            });
        }

        $('#stat').prop('checked', status == 1);
        $('#password').removeAttr('required').val('');

        $('a[href="#tab-form"]').tab('show');
    });

    // Soft delete 
    $(document).on('click', '.btn-delete', function() {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Yakin?',
            text: 'User akan dihapus',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus'
        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({
                    url: BASE_URL + '/users/delete/' + id,
                    method: "POST",
                    dataType: "json",

                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire('Berhasil', res.message, 'success');
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire('Error', res.message, 'error');
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

        $('#password').attr('required', true);
        $('#select_role').empty().val(null).trigger('change');
    })
</script>