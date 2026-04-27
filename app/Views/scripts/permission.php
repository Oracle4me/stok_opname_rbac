<script>
const BASE_URL = "<?= base_url('admin') ?>";

function loadRole() {
    $.ajax({
        url: BASE_URL + '/role',
        method: 'GET',
        dataType: 'json',
        success: function(res) {
            let html = '<option value="">Pilih Role</option>';

            res.data.forEach(function(role) {
                if (role.is_active == 1) {
                    html += `<option value="${role.id}">${role.nama}</option>`;
                }
            });

            $('#role_select').html(html);
        }
    });
}

$(document).on('click', '.toggle-all', function() {
    const group = $(this).data('group');

    const items = $(`.permission-toggle[data-group="${group}"]`);

    let allChecked = true;

    items.each(function() {
        if (!$(this).is(':checked')) {
            allChecked = false;
        }
    });

    items.prop('checked', !allChecked).trigger('change');
});

function loadPermissions(role_id) {
    $('#permission_container').html('<div class="text-center">Loading...</div>');

    $.ajax({
        url: BASE_URL + '/permissions/get/' + role_id,
        method: 'GET',
        dataType: 'json',
        success: function(res) {

            let html = '';

            res.data.forEach(function(group) {

                html += `
                <div class="card mb-3 shadow-sm border-0">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 fw-bold">
                                <i class="bx bx-shield me-1"></i> ${group.group}
                            </h6>
                            <button 
                                class="btn btn-sm btn-outline-primary toggle-all"
                                data-group="${group.group}">
                                Toggle Semua
                            </button>
                        </div>

                        <div class="row">
                `;

                group.permissions.forEach(function(item) {
                    html += `
                    <div class="col-md-3 col-6 mb-3">
                        <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2 bg-light permission-card">
                            <span>${item.label}</span>
                            <div class="form-check form-switch m-0">
                                <input 
                                    type="checkbox"
                                    class="form-check-input permission-toggle"
                                    data-permission="${item.permission_id}"
                                    data-group="${group.group}"
                                    ${item.value == 1 ? 'checked' : ''}
                                >
                            </div>
                        </div>
                    </div>
                    `;
                });

                html += `
                        </div>
                    </div>
                </div>
                `;
            });

            $('#permission_container').html(html);
        },

        error: function(xhr) {
            $('#permission_container').html('<div class="text-danger text-center">Gagal load data</div>');
            console.log(xhr.responseText);
        }
    });
}

$('#role_select').on('change', function() {
    const role_id = $(this).val();

    if (!role_id) {
        $('#permission_container').html('<div class="text-muted text-center">Pilih role terlebih dahulu</div>');
        return;
    }

    loadPermissions(role_id);
});

$(document).on('change', '.permission-toggle', function() {
    const role_id = $('#role_select').val();
    const permission_id = $(this).data('permission');
    const value = $(this).is(':checked') ? 1 : 0;

    $.ajax({
        url: BASE_URL + '/permissions/update',
        method: 'POST',
        data: {
            role_id: role_id,
            permission_id: permission_id,
            value: value
        },
        success: function(res) {
            console.log('Saved:', res);

            // 🔥 optional notif
            // Swal.fire({
            //     icon: 'success',
            //     title: 'Tersimpan',
            //     timer: 800,
            //     showConfirmButton: false
            // });
        },
        error: function(xhr) {
            console.log(xhr.responseText);
        }
    });
});
$(document).ready(function() {
    loadRole();
});
</script>