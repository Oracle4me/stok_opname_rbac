<script>
    const BASE_URL = "<?= base_url('admin/laporan') ?>";

    const canRekap = <?= can('laporan_rekap_view') ? 'true' : 'false' ?>;
    const canDetail = <?= can('laporan_detail_view') ? 'true' : 'false' ?>;
    const canMutasi = <?= can('laporan_mutasi_view') ? 'true' : 'false' ?>;
    const canExport = <?= can('laporan_export') ? 'true' : 'false' ?>;

    $(document).ready(function() {
        if (canRekap) initRekap();
        if (canDetail) initDetail();
        if (canMutasi) initMutasi();
    });

    function getExportButtons(type) {
        if (!canExport) return [];

        return [{
            text: `
                <span style="
                    display:inline-flex;
                    align-items:center;
                    gap:6px;
                    font-size:13px;
                    font-weight:600;
                    color:#145A32;
                ">
                    <i class="fas fa-file-excel" style="color:#1D6F42;"></i>
                    Export Excel
                </span>
            `,
            className: 'btn btn-sm',
            action: function() {
                window.location.href = `${BASE_URL}/export/${type}`;
            },
            attr: {
                style: `
                    background:#E9F7EF;
                    border:1px solid #B7E4C7;
                    border-radius:8px;
                    padding:6px 12px;
                    box-shadow:0 1px 3px rgba(0,0,0,0.08);
                    cursor:pointer;
                `
            }
        }];
    }

    function initRekap() {
        $('#table-rekap').DataTable({
            ajax: BASE_URL + '/rekap',
            columns: [{
                    data: 'nama'
                },
                {
                    data: 'stok_awal',
                    className: 'text-center'
                },
                {
                    data: 'penggunaan',
                    className: 'text-center text-danger'
                },
                {
                    data: 'tambahan',
                    className: 'text-center text-success'
                },
                {
                    data: 'sisa',
                    className: 'text-center fw-bold'
                }
            ],
            dom: '<"d-flex justify-content-between mb-2"Bf>rtip',
            buttons: getExportButtons('rekap')
        });
    }

    function initDetail() {
        $('#table-detail').DataTable({
            ajax: BASE_URL + '/detail',
            columns: [{
                    data: 'tanggal'
                },
                {
                    data: 'nama'
                },
                {
                    data: 'team_leader'
                },
                {
                    data: 'qty',
                    className: 'text-center'
                },
                {
                    data: 'area'
                },
                {
                    data: 'note',
                }
            ],
            dom: '<"d-flex justify-content-between mb-2"Bf>rtip',
            buttons: getExportButtons('detail')
        });
    }

    function initMutasi() {
        $('#table-mutasi').DataTable({
            ajax: BASE_URL + '/mutasi',
            columns: [{
                    data: 'created_at'
                },
                {
                    data: null,
                    render: r => `${r.code} - ${r.nama}`
                },
                {
                    data: 'tipe',
                    className: 'text-center',
                    render: function(t) {
                        if (t === 'masuk') return '<span class="badge bg-primary">Masuk</span>';
                        if (t === 'keluar') return '<span class="badge bg-danger">Keluar</span>';
                        if (t === 'opname') return '<span class="badge bg-warning text-dark">Opname</span>';
                        return t;
                    }
                },
                {
                    data: 'keterangan_display',
                    defaultContent: '-',
                },
                {
                    data: 'qty_before',
                    className: 'text-center'
                },
                {
                    data: 'selisih',
                    className: 'text-center',
                    render: v => `<span class="${v > 0 ? 'text-success' : 'text-danger'}">${v}</span>`
                },
                {
                    data: 'qty_after',
                    className: 'text-center'
                },
                {
                    data: 'user',
                    defaultContent: '-'
                }
            ],
            dom: '<"d-flex justify-content-between mb-2"Bf>rtip',
            buttons: getExportButtons('mutasi')
        });
    }
</script>