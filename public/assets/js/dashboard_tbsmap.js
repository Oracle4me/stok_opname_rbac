class Dashboard {

    constructor() {
        var thisClass = this;
    }

    load(filter) {
        var thisClass = this;
        console.log('Dashboard load');
        this.iconPath = 'M409.133 109.203c-19.608 -33.592 -46.205 -60.189 -79.798 -79.796C295.736 9.801 259.058 0 219.273 0c-39.781 0 -76.47 9.801 -110.063 29.407c-33.595 19.604 -60.192 46.201 -79.8 79.796C9.801 142.8 0 179.489 0 219.267c0 39.78 9.804 76.463 29.407 110.062c19.607 33.592 46.204 60.189 79.799 79.798c33.597 19.605 70.283 29.407 110.063 29.407s76.47 -9.802 110.065 -29.407c33.593 -19.602 60.189 -46.206 79.795 -79.798c19.603 -33.596 29.403 -70.284 29.403 -110.062C438.533 179.485 428.732 142.795 409.133 109.203zM361.74 259.517l-29.123 29.129c-3.621 3.614 -7.901 5.424 -12.847 5.424c-4.948 0 -9.236 -1.81 -12.847 -5.424l-87.654 -87.653l-87.646 87.653c-3.616 3.614 -7.898 5.424 -12.847 5.424c-4.95 0 -9.233 -1.81 -12.85 -5.424l-29.12 -29.129c-3.617 -3.607 -5.426 -7.898 -5.426 -12.847c0 -4.942 1.809 -9.227 5.426 -12.848l129.62 -129.616c3.617 -3.617 7.898 -5.424 12.847 -5.424s9.238 1.807 12.846 5.424L361.74 233.822c3.613 3.621 5.424 7.905 5.424 12.848C367.164 251.618 365.357 255.909 361.74 259.517z';

        this.iconPathHalte = 'M132,409.6c-6.6,0-12-5.4-12-12V336h72v61.6c0,6.6-5.4,12-12,12H132z M204,72h-36v48h48V84C216,77.4,210.6,72,204,72z M144,72h-36c-6.6,0-12,5.4-12,12v36h48V72z M96,216h120v-72H96V216z M168,180c0,6.6,5.4,12,12,12s12-5.4,12-12c0-6.6-5.4-12-12-12S168,173.4,168,180z M120,180c0,6.6,5.4,12,12,12s12-5.4,12-12c0-6.6-5.4-12-12-12S120,173.4,120,180z M276,0H36C16.1,0,0,16.1,0,36v240c0,19.9,16.1,36,36,36h240c19.9,0,36-16.1,36-36V36C312,16.1,295.9,0,276,0z M240,84c0-19.9-16.1-36-36-36h-96c-19.9,0-36,16.1-36,36v144c0,6.6,5.4,12,12,12h12v12c0,6.6,5.4,12,12,12s12-5.4,12-12v-12h72v12c0,6.6,5.4,12,12,12s12-5.4,12-12v-12h12c6.6,0,12-5.4,12-12V84z';

        this.iconPathString2 = `<svg width="9" height="25" viewBox="0 0 152 427" xmlns="http://www.w3.org/2000/svg">
            <path d="M3.81409 407.709C3.81409 407.709 3.81409 407.709 3.81409 407.709L3.81409 19.5686C3.81409 19.0194 3.95767 18.4919 4.30452 18.0662C7.01398 14.7403 22.0491 0.029855 76.0287 0.029855C130.008 0.029855 145.043 14.7403 147.753 18.0662C148.1 18.4919 148.243 19.0194 148.243 19.5685L148.243 407.709C148.243 407.709 148.243 407.709 148.243 407.709C148.243 407.709 148.243 426.671 76.0287 426.671C3.81409 426.671 3.81409 407.709 3.81409 407.709Z" fill="#FF1744"/>
            <path d="M12.4857 417.298C11.5515 417.089 10.7232 416.552 10.1513 415.784L3.81396 407.277L3.81396 394.349H25.4853V420.206L12.4857 417.298Z" fill="#C4C4C4"/>
            <path d="M139.569 417.3C140.504 417.091 141.332 416.554 141.904 415.786L148.241 407.28V394.351H126.57V420.208L139.569 417.3Z" fill="#C4C4C4"/>
            <path d="M25.4762 420.207V413.743C25.4762 413.743 25.4762 420.207 76.0264 420.207C126.577 420.207 126.577 413.743 126.577 413.743V420.207C126.577 420.207 126.577 426.671 76.0264 426.671C25.4762 426.671 25.4762 420.207 25.4762 420.207Z" fill="#C4C4C4"/>
            <path d="M34.4527 397.79C33.449 397.674 32.6986 396.823 32.6986 395.813V393.378C32.6986 392.177 33.736 391.244 34.9293 391.381C41.6365 392.151 62.0201 394.35 76.0274 394.35C90.0346 394.35 110.418 392.151 117.125 391.381C118.319 391.244 119.356 392.177 119.356 393.378V395.813C119.356 396.823 118.606 397.674 117.602 397.79C111.44 398.504 90.3845 400.815 76.0274 400.815C61.6703 400.815 40.6143 398.504 34.4527 397.79Z" fill="white"/>
            <path d="M23.2114 65.4032C25.054 71.1147 26.8952 80.6278 28.2752 88.7074C28.9655 92.7484 29.5406 96.4328 29.9433 99.1068C30.1446 100.444 30.3027 101.528 30.4106 102.279C30.4645 102.654 30.5059 102.945 30.5337 103.143L30.5624 103.348L30.8177 103.317C31.0493 103.289 31.3911 103.248 31.8323 103.196C32.7147 103.092 33.9946 102.944 35.5845 102.765C38.7643 102.409 43.1842 101.934 48.1452 101.458C58.0658 100.508 70.1538 99.5565 78.812 99.5565C87.4707 99.5565 98.5156 100.508 107.393 101.458C111.833 101.934 115.731 102.409 118.52 102.766C119.914 102.944 121.031 103.092 121.8 103.196C122.184 103.248 122.481 103.289 122.682 103.317L122.889 103.346L122.917 103.143C122.945 102.945 122.986 102.654 123.04 102.279C123.148 101.528 123.306 100.444 123.508 99.1069C123.91 96.4328 124.485 92.7484 125.175 88.7074C126.555 80.6278 128.396 71.1147 130.239 65.4032C131.594 61.2013 130.478 57.4527 127.68 54.1833C124.878 50.9097 120.392 48.1201 115.022 45.8559C104.284 41.328 90.0487 38.9164 78.812 38.9163C67.5749 38.9163 52.2951 41.328 40.5118 45.857C34.6193 48.1218 29.6108 50.9126 26.418 54.1881C23.2287 57.46 21.8576 61.2069 23.2114 65.4032Z" fill="white" stroke="#E0E0E0" stroke-width="0.2"/>
            <path d="M12.5118 142.243C11.6607 142.243 10.979 141.544 11.0063 140.694C11.1529 136.13 11.5148 122.478 10.9529 113.154C10.3953 103.901 8.42314 90.5312 7.69661 85.8018C7.55641 84.8892 8.26216 84.0647 9.18551 84.0647H20.2767C21.0049 84.0647 21.626 84.5794 21.7478 85.2972C22.4092 89.1923 24.4279 101.478 25.0635 109.922C25.8587 120.486 25.3196 136.074 25.1253 140.821C25.0926 141.62 24.4342 142.243 23.6338 142.243H12.5118Z" fill="white"/>
            <line y1="-0.25" x2="15.8278" y2="-0.25" transform="matrix(0.912797 -0.408413 0.48777 0.872972 11.0364 109.921)" stroke="#E0E0E0" stroke-width="0.5"/>
            <path d="M139.546 142.243C140.397 142.243 141.079 141.544 141.051 140.694C140.905 136.13 140.543 122.478 141.105 113.154C141.662 103.901 143.634 90.5312 144.361 85.8018C144.501 84.8892 143.795 84.0647 142.872 84.0647H131.781C131.053 84.0647 130.432 84.5794 130.31 85.2972C129.648 89.1923 127.63 101.478 126.994 109.922C126.199 120.486 126.738 136.074 126.932 140.821C126.965 141.62 127.623 142.243 128.424 142.243H139.546Z" fill="white"/>
            <line y1="-0.25" x2="15.8278" y2="-0.25" transform="matrix(-0.912797 -0.408413 -0.48777 0.872972 141.021 109.921)" stroke="#E0E0E0" stroke-width="0.5"/>
            <ellipse rx="13.7556" ry="9.4858" transform="matrix(-0.74509 0.666964 -0.74509 -0.666964 17.3169 20.2612)" fill="white"/>
            <path d="M124.151 10.788C120.244 14.285 121.662 21.2236 127.317 26.2859C132.973 31.3483 140.724 32.6173 144.631 29.1204C148.537 25.6234 147.12 18.6848 141.464 13.6225C135.809 8.56012 128.058 7.2911 124.151 10.788Z" fill="white"/>
            <path d="M32.6986 17.2676V12.9581C32.6986 12.9581 64.6589 9.72594 76.0274 9.72594C87.3958 9.72594 118.808 12.9581 118.808 12.9581V17.2676C118.808 17.2676 87.3958 14.0354 76.0274 14.0354C64.6589 14.0354 32.6986 17.2676 32.6986 17.2676Z" fill="#BDBDBD"/>
        </svg>`;

        // this.buttonUserAccount = $('#useraccount').prop('innerHTML');
        // this.searchBox = `<span class="dropdown" id="menu-main"><span style="cursor:pointer" id="btn-sidenav">&#9776;</span></span>`;
        this.searchBox = `<span class="dropdown" id="menu-main">
                                <span style="cursor:pointer" id="btn-sidenav">
                                    <img class="align-text-top" src="/assets/img/Favicon_Teman_Bus.png" style="width: 30px; height: 26px;">
                                </span>
                            </span>`;

        // <div class="col-12 col-lg-3">
        //     <div class="row" style="overflow:auto">
        //     <div class="col"><select class="form-control" style="width: 100%;height:48px;" id="find_route"></select></div>
        //     </div>
        // </div>
        /*
        this.content1_ :
        <div class="col-12 col-lg-3">
                <div class="row" style="overflow:auto">
                <div class="col"><select class="form-control" style="width: 100%;height:48px" id="rute_mudik"></select></div>
                </div>
            </div>
        */
        this.content1_ = `<div class="row">
                            <div class="p-1 shadow-sm rounded-3" style="width: 40px;background: white;height: 44px;margin-top: 2px;border: 1px solid;border-color: #aaa;
                            ">`+ this.searchBox + `</div>
                            <div class="col-9 col-lg-3">
                                <div class="row" style="overflow:auto">
                                    <div class="col"><select class="form-control" style="width: 100%;height:48px;" id="filter_account_city"  placeholder="d"></select></div>
                                </div>
                            </div>
                        </div>`;

        this.currentLeftPoint = {};



        $(document).on('click', '#map #btn-sidenav', function () {
            // $('.jp-0').css({ width: "363px", left: 0 });
            location.href = '/';
        });

        $(document).on('click', '.jp-0-close', function () {
            // $('.jp-0').css({ width: "0px", left: '-20px' });
        });

        $('#call-modal').on('hidden.bs.modal', function () {
            //thisClass.deactivateCall();
            $('#btn-widget-call').trigger('click');
        })

        $(document).on('click', '#btn-set-rute', function () {
            if ($('#widget-04').is(":hidden")) {
                $('#widget-04').show();
                // $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
            } else {
                $('#widget-04').hide();
            }
        });

        $('#widget-01,#widget-02').hide();
        $(document).on('click', '#btn-bus-stat', function () {
            if ($('#widget-01').is(":hidden")) {
                $('#widget-01').show();
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
            } else {
                $('#widget-01').hide();
            }
        });

        $(document).on('click', '#btn-routes-stat', function () {
            if ($('#widget-02').is(":hidden")) {
                $('#widget-02').show();
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
            } else {
                $('#widget-02').hide();
            }
        });

        $(document).on('click', '#reload-monroute', function () {
            $('#datatable3').DataTable().clear().destroy();
            thisClass.monitoringRoute();
        });

        $(document).on('click', '.wg-close', function (e) {
            $(this).closest(".widget-box").hide();
        });

        $(document).on('click', '#btn-widget-call', function (e) {
            Call_.currentPosko = 'null';
            Call_.currentSatpel = 'null';
            thisClass.toggleEnableCall($(this));
        });

       
        $(document).on('submit', '#form-edit-bus-stop', function (e) {
            e.preventDefault();
            var datastring = $("#form-edit-bus-stop").serialize();
            datastring += '&' + thisClass.csrfName + '=' + thisClass.csrfHash;
            $.ajax({
                type: "POST",
                url: thisClass.baseUrl + '/main/action/renameBusstop',
                data: datastring,
                dataType: "json",
                success: function (data) {
                    Swal.fire('Sukses', data.message, 'success');
                    $('#find_route').trigger('select2:select');
                },
                error: function () {
                    alert('terjadi kesalahan');
                }
            });
        });

        $(document).on('click', '#btn-center', function () {
            thisClass.centerMap();
        });

        // $(document).on('click','#a-rute-mudik',function(e){
        //     topleftControl.updateContent(Dashboard.content2_);
        //     $('#hubdatsearch').val('Rute Mudik');
        //     try{
        //         $(".regular2").slick({
        //             dots: false,
        //             infinite: true,
        //             slidesToShow: 2,
        //             slidesToScroll: 1,
        //             centerMode: false
        //         });
        //     }catch(e){}
        // });

        $('.right-bar-toggle').on('click', function (e) {
            $('body').toggleClass('right-bar-enabled');
        });

        $(document).on('click', 'body', function (e) {
            if ($(e.target).closest('.right-bar-toggle, .right-bar').length > 0) {
                return;
            }
            $('body').removeClass('right-bar-enabled');
            return;
        });


        // var $dragging = null;

        // $(document.body).on("mousemove", function(e) {

        //     if ($dragging) {
        //       if(e.pageY<70) e.pageY = 70;
        //       if(e.pageX<0) e.pageX = 0;
        //         $dragging.offset({
        //             top: e.pageY,
        //             left: e.pageX
        //         });
        //     }
        // });

        // $(document.body).on("mousedown", ".dragthis", function (e) {

        //     //alert($dragging.closest('div[id^="widget-"').prop('outerHTML'));
        //     $dragging = $(e.currentTarget).closest('div[id^="widget-"');
        // });

        // $(document.body).on("mouseup", function (e) {
        //     $dragging = null;
        // });

        $(document).on('click', '.call-this-user', function (e) {
            thisClass.makeCall($(this).data('id'), $(this));
        });

        $(document).on('click', '.call-this-user-dashboard', function (e) {
            let posko_id = $(this).data('posko-id');
            let petugas_id = $(this).data('id');
            let satpel_id = $(this).data('satpel-id');
            if (posko_id != null) {
                Call_.currentPosko = posko_id;
                Call_.currentPetugasid = petugas_id;
                Call_.isOnCall = true;
            } else {
                Call_.currentSatpel = satpel_id;
                Call_.currentPetugasid = petugas_id;
                Call_.isOnCall = true;
            }

            thisClass.activateCall(function () {
                $(`.call-this-user[data-id="${petugas_id}"]`).click();
            });
        });

        $(document).on('keyup', '#cariuser', function (e) {
            thisClass.fillPetugas(thisClass.DataPetugas);
        });

        // setTimeout(thisClass.loadDataPetugas,5000);


    }

    monitoringRoute() {
        var thisClass = this;
        $.ajax({
            type: "POST",
            url: thisClass.baseUrl + '/main/ajax/jsonmonroute',
            data: {
                [thisClass.csrfName]: thisClass.csrfHash,
            },
            //contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (response) {
                thisClass.storeMonToTable(response);
                $('#widget-03').show();
            }
        }).done(function (response) {

        });
    }

    getFormData($form) {
        var thisClass = this;
        var unindexed_array = $form.serializeArray();
        var indexed_array = {};

        $.map(unindexed_array, function (n, i) {
            indexed_array[n['name']] = n['value'];
        });

        return indexed_array;
    }

    storeMonToTable(response) {
        var thisClass = this;
        var dataStart = 0;
        $('#datatable3').dataTable({
            width: '100%',
            "data": response,
            "pageLength": 5,
            "columns": [
                {
                    "data": "id", orderable: true,
                    render: function (a, type, data, index) {
                        return dataStart + index.row + 1
                    }
                },
                { "data": "group_nm" },
                { "data": "jml" },
                { "data": "today_inactive" },
                { "data": "today_active" },
                { "data": "today_active_less_minute" },
                { "data": "today_active_minute" },
                { "data": "today_active_less_hour" },
            ]
        });
    }

    setBusTooltip(busMarker, item) {
        var thisClass = this;
        var infoVehicle = '<table class"bus-tooltip">' +
            '<tr><td>Jenis</td><td>' + item.jenroute + '</td></tr>' +
            '<tr><td>GPS SN</td><td>' + item.gps_sn + '</td></tr>' +
            '<tr><td>Jalur</td><td>' + item.kor + '</td></tr>' +
            '<tr><td>Bus</td><td>' + item.name + '</td></tr>' +
            '<tr><td>No. Kendaraan</td><td>' + item.nopol + '</td></tr>' +
            '<tr><td>Angle</td><td>' + item.direction + '</td></tr>' +
            '<tr><td>Speed</td><td>' + item.speed + ' km/h</td></tr>' +
            '<tr><td>Position</td><td class="bus-pos"><span class="badge bg-soft-success text-success">' + arrBusStop[item.old_shel_t] + '</span>'+
            '<i class=" bx bxs-right-arrow-circle"></i> <span class="badge bg-soft-danger text-danger">' + arrBusStop[item.new_shel_t] + '</span></td></tr>' +
            '<tr><td>Toward</td><td><span class="badge bg-soft-danger text-danger">' + item.toward + '</span></td></tr>' +
            '<tr><td>Last Updated</td><td>' + $.timeago(item.gps_time) + ' <br/>' + item.gps_time + '</td></tr>' +
            '</table>';
        busMarker.bindPopup(infoVehicle);
    }

    setBusDataSource(datasource) {
        var thisClass = this;
        thisClass.vdata_bus = {};
        thisClass.vdata_bus.results = [];
        thisClass.objtype = [];
        thisClass.objtype.push('-');
        var ng = 0;

        $.each(datasource, function (index, obj) {
            if (thisClass.objtype[thisClass.objtype.length - 1] != obj.group_nm) {
                thisClass.vdata_bus.results.push({ text: obj.group_nm });
                ng = 1;
                thisClass.vdata_bus.results[thisClass.vdata_bus.results.length - 1].children = [{ id: obj.gps_sn, text: thisClass.html_logobus + ' ' + obj.nopol + ' <span style="display:none">' + obj.group_nm + ',' + obj.gps_sn + '</span><br/><small>' + $.timeago(obj.gps_time) + '</small>', gps_time: obj.gps_time }];
            } else {
                thisClass.vdata_bus.results[thisClass.vdata_bus.results.length - 1].children.push({ id: obj.gps_sn, text: thisClass.html_logobus + ' ' + obj.nopol + ' <span style="display:none">' + obj.group_nm + ',' + obj.gps_sn + '</span><br/><small>' + $.timeago(obj.gps_time) + '</small>', gps_time: obj.gps_time });
            }
            ng++;
            thisClass.objtype.push(obj.group_nm);
            return obj;
        });
        thisClass.vdata_bus.pagination = { more: true };
        $.each(thisClass.vdata_bus.results, function (index, item) {
            thisClass.vdata_bus.results[index].text = item.text + ' (' + item.children.length + ' armada)';
        });
    }

    centerMap() {
        var thisClass = this;
        map.setView(thisClass.vCurLatLng, 5);
    }

    makeid(length) {
        let result = '';
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        const charactersLength = characters.length;
        let counter = 0;
        while (counter < length) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
            counter += 1;
        }

        return result;
    }

    toggleEnableCall(this_) {
        var thisClass = this;
        if (!Call_.isOnCall) {
            this_.attr('title', 'Non Aktifkan Video Call');
            this_.css('color', 'royalblue');
            this_.find('i').removeClass('bx bx-video-off');
            this_.find('i').addClass('bx bx-video');
            thisClass.activateCall();
            Call_.isOnCall = true;
        } else {
            this_.attr('title', 'Aktifkan Video Call');
            this_.css('color', '');
            this_.find('i').removeClass('bx bx-video');
            this_.find('i').addClass('bx bx-video-off');
            thisClass.deactivateCall();
            Call_.isOnCall = false;
        }
    }

    saveLoading() {
        var thisClass = this;
        Swal.fire({
            title: "",
            icon: "info",
            text: "Proses menyimpan data, mohon ditunggu...",
            didOpen: function () {
                Swal.showLoading()
            }
        });
    }

    pageLoading(txtHTML) {
        var thisClass = this;
        txtHTML = txtHTML == null ? "Loading" : txtHTML;
        Swal.fire({
            title: "",
            //icon: "info",
            text: txtHTML,
            didOpen: function () {
                Swal.showLoading()
            }
        });
    }

    swalClose() {
        var thisClass = this;
        Swal.close();
    }

    updateLocation(bs_id, bs_lat, bs_lng) {
        var thisClass = this;
        // alert(thisClass.baseUrl);
        $.ajax({
            type: "POST",
            url: thisClass.baseUrl + '/main/action/saveUpdateLocation',
            data: {
                [thisClass.csrfName]: thisClass.csrfHash,
                bs_id: bs_id,
                bs_lat: bs_lat,
                bs_lng: bs_lng
            },
            dataType: "json",
            success: function (response) {
                Swal.close();
                Swal.fire('Sukses', response.message, 'success');
                $('#edit_trayek').trigger('select2:select');
            }
        });
    }


    getRandomRolor() {
        var thisClass = this;
        this.color = '';
        for (var i = 0; i < 6; i++) {
            this.color += Math.floor(Math.random() * 10);
        }
        return this.color;
    }

    storeBusToTable(datasource) {
        var thisClass = this;
        var dataStart = 0;
        $('#datatable').DataTable().clear().destroy();
        $('#datatable').dataTable({
            width: '100%',
            buttons: [
                {
                    extend: 'pdf',
                    text: 'Save current page',
                    exportOptions: {
                        modifier: {
                            page: 'current'
                        }
                    }
                }
            ],
            "data": datasource,
            "pageLength": 5,
            "columns": [
                {
                    "data": "id", orderable: true,
                    render: function (a, type, data, index) {
                        return dataStart + index.row + 1
                    }
                },
                { "data": "gps_sn", width: 200 },
                { "data": "group_nm" },
                { "data": "nopol" },
                { "data": "route_id" },

            ],
            fixedColumns: true
        });
    }

    storeRouteToTable(response) {
        var thisClass = this;
        var dataStart = 0;
        $('#datatable2').DataTable().clear().destroy();
        $('#datatable2').dataTable({
            "data": response,
            "pageLength": 5,
            "columns": [
                {
                    "data": "id", orderable: true,
                    render: function (a, type, data, index) {
                        return dataStart + index.row + 1
                    }
                },
                { "data": "jenroute" },
                { "data": "name" },
                { "data": "origin" },
                { "data": "toward" }
            ],
            scrollY: true,
            scrollX: true,
            scrollCollapse: true,
            columnDefs: [
                { width: '30%', targets: 1 }
            ],
            fixedColumns: false,
            responsive: true

        });
    }

}

var Dashboard_ = function () {
    let dashb = {}

    dashb.load = function (a, b) {
        console.log('Dashboard_ load');
        _foo1();
        _foo2(a, b);
    }

    var _foo1 = function () {
        console.log('foo 1 without param');
    }

    var _foo2 = function (a, b) {
        console.log(a);
        console.log(b);
    }

    return dashb;
}();

var Call_ = function () {
    let call = {}
    var currentPosko = 'null';
    var currentSatpel = 'null';
    var isOnCall = false;

    return call;
}();

var Map_ = function () {
    let map = {}
    var marker;

    map.removeMarker = function (map) {
        if (typeof this.marker != 'undefined') { map.removeLayer(this.marker) }
    }

    return map;
}();