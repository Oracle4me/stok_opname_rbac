<script src="<?= base_url('assets/libs/apexcharts/apexcharts.min.js') ?>"></script>
<script src="<?= base_url('assets/js/pages/apexcharts.init.js') ?>"></script>
<script>
    const BASE_URL = "<?= base_url('admin') ?>";

    $(document).ready(function() {

        $('.counter-value').each(function() {
            let $this = $(this),
                countTo = $this.attr('data-target');

            $({
                countNum: 0
            }).animate({
                countNum: countTo
            }, {
                duration: 1000,
                easing: 'swing',
                step: function() {
                    $this.text(Math.floor(this.countNum));
                },
                complete: function() {
                    $this.text(this.countNum);
                }
            });
        });

        $('#table-transaksi').DataTable({
            processing: true,
            scrollX: false,
            ajax: BASE_URL + '/dashboard/transaksi',
            columns: [{
                    data: 'created_at'
                },
                {
                    data: 'nama'
                },
                {
                    data: 'tipe',
                    render: function(data) {
                        return data === 'masuk' ?
                            '<span class="badge bg-success">Masuk</span>' :
                            '<span class="badge bg-danger">Keluar</span>';
                    }
                },
                {
                    data: 'selisih',
                    render: function(data) {
                        return Math.abs(data);
                    }
                }
            ]
        });


        $.get(BASE_URL + '/dashboard/chart-stok', function(res) {

            let tanggal = [];
            let masuk = [];
            let keluar = [];

            res.forEach(function(r) {
                tanggal.push(r.tanggal);
                masuk.push(r.masuk);
                keluar.push(r.keluar);
            });

            let options = {
                chart: {
                    type: 'line',
                    height: 350
                },
                series: [{
                        name: 'Stok Masuk',
                        data: masuk
                    },
                    {
                        name: 'Stok Keluar',
                        data: keluar
                    }
                ],
                xaxis: {
                    categories: tanggal
                }
            };

            new ApexCharts(document.querySelector("#chart-stok"), options).render();
        });

    });
</script>