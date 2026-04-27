var T = function () {
    let trayek = {}

    var T_ = this;
    var dashboard = null;
    var polyline = null;

    var map = null;
    var formActive = null;
    var dragActiveIndex = null;
    var dropdownItem = [];
    var points = [];
    var pointsBusStop = [];
    var layerGroup = null;

    var areaWaypoint = [];
    var initialWaypoint = [];
    var markerWaypoint = null;
    var polyLatLng = null
    var waypointLatLng = null;
    var vertexPoint = [];
    var editedPolyline = [];

    var alphabet = [];
    var ajaxUrl = '/operasional/ajax';

    var myPolylineEditor = null
    var layerMarkerGroup = null
    var arrCircle = null;
    var isGenerate = true;

    trayek.load = function (dashboard, ajaxUrl = '/operasional/ajax') {
        T.dashboard = dashboard;
        T.layerGroup = L.featureGroup()
        T.myPolylineEditor = L.Editable.PolylineEditor.extend({
            onFeatureAdd: function () {
                this.tools.editLayer.addLayer(this.editLayer);
                if (this.feature.dragging) this.feature.dragging.disable();
            },
        });

        T.polyline = null;
        T.map = null;
        T.formActive = null;
        T.dragActiveIndex = null;

        T.dropdownItem = [];
        T.points = [[], []];
        T.pointsBusStop = [];

        T.areaWaypoint = [];
        T.initialWaypoint = [];
        T.markerWaypoint = null;
        T.polyLatLng = null
        T.waypointLatLng = null;
        T.vertexPoint = [];
        T.editedPolyline = [];
        T.isGenerate = true;

        T.alphabet = new Array(200).fill(null).map((_, i) => i + 1);
        // ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "A1", "B1", "C1", "D1", "E1", "F1", "G1", "H1", "I1", "J1", "K1", "L1", "M1", "N1", "O1", "P1", "Q1", "R1", "S1", "T1", "U1", "V1", "W1", "X1", "Y1", "Z1", "A2", "B2", "C2", "D2", "E2", "F2", "G2", "H2", "I2", "J2", "K2", "L2", "M2", "N2", "O2", "P2", "Q2", "R2", "S2", "T2", "U2", "V2", "W2", "X2", "Y2", "Z2"];
        T.ajaxUrl = ajaxUrl;


        T.layerMarkerGroup = L.featureGroup();
        T.arrCircle = [];

        $(function () {
            $(document).on("click", ".item-point", function (e) {
                if (e.target.className.split(" ").some(r => { return ['checkbox-point', 'uil-edit', 'uil-times'].includes(r) }) == true) return;
                const index = $('.item-point').index($(this));

                if (T.points[index] && T.points[index].hasOwnProperty('marker')) {
                    T.points[index].marker.openPopup();
                    T.map.flyTo(T.points[index].marker._latlng, T.map.getZoom(), { animate: 0.1, paddingTopLeft: [350, 0] })
                }
            })
            .on("dragstart", ".item-point", function (e) {
                const $this = $(this);
                const items = $('.item-point');
                T.dragActiveIndex = items.index($(this));

                setTimeout(() => $this.addClass("dragging"), 0);
            }).on("dragend", ".item-point", function (e) {
                $(this).removeClass("dragging");

                $(".bullet").each(function (index) { $(this).html(T.alphabet[index]); });

                const items = $('.item-point');
                const index = items.index($(this));
                const enc = $(this).find('span').data('restore');

                var draggingIndex = T.points.findIndex(item => {
                    if (item && item.hasOwnProperty('data')) {
                        return item.data.enc == enc;
                    } else {
                        return false;
                    }
                });

                if (draggingIndex == -1) {
                    draggingIndex = T.dragActiveIndex;
                }

                const draggingItem = T.points[draggingIndex];

                T.points.splice(draggingIndex, 1);
                T.points.splice(index, 0, draggingItem);

                T.reMarker()

                if(T.isGenerate){
                    T.getRoutes();
                }
            }).on("dragover", ".sortable-point", function (e) {
                e.preventDefault();

                const list = document.querySelector(".sortable-point");
                const draggingItem = list.querySelector(".dragging");
                const siblings = [...list.querySelectorAll(".item-point:not(.dragging)")];

                let nextSibling = siblings.find(sibling => {
                    return e.target.offsetTop + e.target.offsetHeight / 2 <= sibling.offsetTop + sibling.offsetHeight / 2;
                });

                list.insertBefore(draggingItem, nextSibling);
            }).on("dragenter", ".sortable-point", function (e) {
                e.preventDefault();
            })
        });
    }

    trayek.set_control = (toggleRute = true) => {
        T.map.addLayer(T.layerMarkerGroup);

        let menu = '<button type="button" class="btn btn-sm btn-light active" data-toggle="tooltip" title="Toggle Dialog" id="toggle-dialog">'+
                      '    <i class="fa fa-bars"></i>'+
                      '</button>';

        menu += '<button type="button" class="btn btn-sm btn-light active" data-toggle="tooltip" title="Toggle Generate" id="toggle-generate">'+
                      '    <i class="fa fa-cogs"></i>'+
                      '</button>';

        if(toggleRute){
            menu += '<button type="button" class="btn btn-sm btn-light" data-toggle="tooltip" title="Toggle Edit Rute" id="toggle-rute">'+
                      '    <i class="fa fa-route"></i>'+
                      '</button>';
        }

        L.control.custom({
            position: 'topright',
            content : menu,
            classes : 'btn-group-vertical btn-group-sm',
            style   :
            {
                margin: '10px',
                padding: '0px 0 0 0',
                cursor: 'pointer'
            },
            events:
            {
                click: function(data)
                {   
                    let button = $(data.target).closest('button');

                    if(button.prop('id') == 'toggle-dialog'){
                        button.toggleClass('active');

                        $('#widget-add-trayek').toggle();
                    }else if(button.prop('id') == 'toggle-rute'){
                        if(T.polyline != null) {
                            button.toggleClass('active');

                            if(button.hasClass('active')){
                                enableEditMode()
                            }else{
                                disableEditMode()
                            }
                        }else{
                            Swal.fire('Peringatan', "Tidak ada garis trip", 'warning');
                        }
                    }else if(button.prop('id') == 'toggle-generate'){
                        button.toggleClass('active');

                        T.isGenerate = button.hasClass('active');
                    }
                },
                dblclick: function(data)
                {
                    console.log('wrapper div element dblclicked');
                    console.log(data);
                },
                contextmenu: function(data)
                {
                    console.log('wrapper div element contextmenu');
                    console.log(data);
                },
            }
        })
        .addTo(T.map);
    }

    trayek.reset = () => {
        if (T.polyline != null) { T.layerGroup.removeLayer(T.polyline); }
        if (T.markerWaypoint != null) { T.layerGroup.removeLayer(T.markerWaypoint); }
        T.points.forEach((el, index) => {
            T.layerGroup.removeLayer(el.marker);
        })

        T.polyline = null;
        T.formActive = null;
        T.dropdownItem = [];
        T.points = [[], []];

        T.areaWaypoint = [];
        T.initialWaypoint = [];
        T.markerWaypoint = null;
        T.polyLatLng = null
        T.waypointLatLng = null;
    }

    trayek.waypoint_form = function (index, data) {
        return `<li class="item item-point" draggable="true">
                    <div class="item-point-details">
                        <i class="uil uil-draggabledots"></i>
                        <div class="blt ${data.bs_stop == '1' ? 'bullet' : 'bullet-outline'}">${data.bs_stop == '1' ? T.alphabet[index] : 'A'}</div>
                        <input class="input-point" type="" name="" value="${data.bs_nm}" style="display:none">
                        <span data-restore="${data.enc}" style="display:block">${data.bs_nm}</span>
                        <div class="action-point">
							<input type="checkbox" class="checkbox-point tooltipp hidden" data-bs-toggle="tooltip" data-bs-placement="top" title="Titik Bus Stop?" ${data.bs_stop == '1' ? 'checked' : ''}>
							<a href="#" class="p-1 edit-point tooltipp hidden" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Point"><i class="fas fa-edit fa-xs" style="color: #63e6be;"></i></a>
							<a href="#" class="p-1 rename-point tooltipp hidden" data-bs-toggle="tooltip" data-bs-placement="top" title="Rename Point"><i class="fas fa-spell-check fa-xs" style="color: #5156be;"></i></a>
							<a href="#" class="p-1 remove-point tooltipp hidden" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus Point"><i class="fas fa-trash-alt fa-xs" style="color: #fd625e;"></i></a>
                        </div>
                    </div>
                </li>`;
    }

    trayek.form = function (x) {
        return `<li class="item item-point" draggable="true">
                    <div class="item-point-details">
                        <i class="uil uil-draggabledots"></i>
                        <div class="blt bullet">${T.alphabet[x]}</div>
                        <input class="input-point" type="" name="" value="">
                        <span></span>
                        <div class="action-point">
							<input type="checkbox" class="checkbox-point tooltipp hidden" data-bs-toggle="tooltip" data-bs-placement="top" title="Titik Bus Stop?" checked>
							<a href="#" class="p-1 edit-point tooltipp hidden" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Point"><i class="fas fa-edit fa-xs" style="color: #63e6be;"></i></a>
							<a href="#" class="p-1 rename-point tooltipp hidden" data-bs-toggle="tooltip" data-bs-placement="top" title="Rename Point"><i class="fas fa-spell-check fa-xs" style="color: #5156be;"></i></a>
							<a href="#" class="p-1 remove-point tooltipp hidden" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus Point"><i class="fas fa-trash-alt fa-xs" style="color: #fd625e;"></i></a>
                        </div>
                    </div>
                </li>`;
    }

    trayek.info_window = function (element) {
        return element.bs_nm;
    }

    trayek.dropdown_item = function (data, index) {
        return `<li class="dropdown-item dropdown-items" data-index="${index}">
                    <div class="name">${data.bs_nm}</div>
                    <div class="type">${data.bs_stop == "1" ? 'bus stop' : 'waypoint'}, digunakan pada ${data.used} rute</div>
                    <div class="address">${data.addr}</div>
                </li>`;
    }

    trayek.marker_icon = function (alpha, bs_stop) {
        return L.divIcon({
            className: 'custom-icon',
            html: '<div class="' + (bs_stop == '1' ? 'bullet-map' : 'bullet-outline-map') + '">' + (bs_stop == '1' ? alpha : '.') + '</div>',
            iconSize: ["unset", "unset"],
            iconAnchor: (bs_stop == '1' ? [15, 15] : [12, 12])
        });
    }

    trayek.waypoint_icon = function () {
        return L.divIcon({
            className: 'custom-icon',
            html: '<div class="waypoint-map"><i class="uil uil-plus"></i></div>',
            iconSize: ["unset", "unset"],
            iconAnchor: [15, 15]
        });
    }

    trayek.debounce = function (func, delay) {
        let debounceTimer
        return function () {
            const context = this
            const args = arguments
            clearTimeout(debounceTimer)
            debounceTimer
                = setTimeout(() => func.apply(context, args), delay)
        }
    }

    trayek.getBusStop = function (param, func) {
        $.ajax({
            type: "POST",
            url: T.dashboard.baseUrl + T.ajaxUrl +'/jsonSearchBusStop',
            data: {
                "paramName": param,
                [T.dashboard.csrfName]: T.dashboard.csrfHash
            },
            dataType: "json",
            success: function (response) {
                func(response);
            }
        });
    }

    trayek.getRoutes = function (zoom = 13, fly = true) {
        if (T.points.length > 1 && T.points.every(point => { return point.hasOwnProperty('data') })) {
            const coordinate = T.points.map(point => { return `${point.data.bs_lat},${point.data.bs_lng}` }).join("%7C");
            $.ajax({
                type: "get",
                url: T.dashboard.baseUrl + T.ajaxUrl +'/jsonGetRoutesfromPoints2/' + coordinate,
                dataType: "json",
                success: function (response) {
                    const encodedLine = response.data.paths[0].points;
                    const encodedWaypoint = response.data.paths[0].snapped_waypoints;
                    $('#kmtempuh').text((response.data.paths[0].distance/1000).toFixed(2));

                    T.polyLatLng = L.PolylineUtil.decode(encodedLine);
                    T.waypointLatLng = L.PolylineUtil.decode(encodedWaypoint);

                    if (T.polyline != null) { T.layerGroup.removeLayer(T.polyline); }

                    var latLngOri = L.PolylineUtil.decode(encodedLine);

                    // T.editedPolyline.forEach( (x, index) => {
                    //     var latLngEdited = L.PolylineUtil.decode(x.polyline);
                    //     var startIndex = latLngOri.findIndex( ll => { return ll[0] == x.start.lat && ll[1] == x.start.lng })
                    //     var endIndex = latLngOri.findIndex( ll => { return ll[0] == x.end.lat && ll[1] == x.end.lng })

                    //     latLngOri.splice(startIndex, (endIndex - startIndex) + 1)
                    //     latLngOri.splice(startIndex, 0, ...latLngEdited);
                    // })

                    T.polyline = L.polyline(latLngOri, {
                        stroke: true,
                        color: $("#input-color").val(),
                        weight: 4,
                        fill: false,
                        fillOpacity: 1
                    }).addTo(T.layerGroup);

                    T.polyline.on('mouseover', T.wpMouseOver);
                    T.polyline.on('dblclick', function(e){
                        alert('dbl');
                    })

                    T.reMarker();

                    if(fly){
                        T.map.flyToBounds(T.layerGroup.getBounds(), { duration: 0.5, maxZoom: zoom, paddingTopLeft: [350, 0] });
                    }

                    $('#toggle-rute').removeClass('active');
                    disableEditMode();
                }
            });
        }
    }

    trayek.moveBusStop = (e) => {
        const latlng = e.target._latlng;
        const currentMarkerIndex = T.points.findIndex(point => {
            return point.marker.options.icon == e.target.options.icon;
        })

        const data = T.points[currentMarkerIndex].data
        data.bs_stop = T.points[currentMarkerIndex].data.bs_stop;
        data.bs_lat = latlng.lat;
        data.bs_lng = latlng.lng;

        T.points[currentMarkerIndex].data = data;
        T.points[currentMarkerIndex].marker.bindPopup(T.info_window(data), { offset: L.point(-5, -5) }).bindTooltip(T.info_window(data), { offset: L.point(-5, -20), direction: 'top' });
        $('.item-point').eq(currentMarkerIndex).find('span').data('restore', data.enc);
        $('.item-point').eq(currentMarkerIndex).find('span').html(data.bs_nm);

        T.reMarker();

        if(T.isGenerate){
            T.getRoutes(T.map.getZoom(), false);
        }
    }

    trayek.wpMouseOver = (e) => {
        // const icon = T.waypoint_icon();

        // if (T.markerWaypoint === null) {
        //     T.markerWaypoint = L.marker(e.latlng, { icon: icon, draggable: true }).on('dragstart', T.wpDragStart).on('dragend', T.wpDragEnd).addTo(T.layerGroup);
        // } else {
        //     T.markerWaypoint.setLatLng(e.latlng);
        // }
    }

    trayek.wpDragStart = (e) => {
        // const latlng = e.target._latlng;
        // T.initialWaypoint = [latlng.lat, latlng.lng];
    }

    trayek.wpDragEnd = (e) => {
        // const latlng = e.target._latlng;
        // const nearestIndex = T.nearest(T.initialWaypoint, T.polyLatLng);

        // T.areaWaypoint = [];
        // T.waypointLatLng.forEach((el, index) => {
        //     if (index < T.waypointLatLng.length - 1) {
        //         const firstIndex = T.polyLatLng.findIndex(latLng => { return latLng[0] == el[0] && latLng[1] == el[1] });
        //         const secondIndex = T.polyLatLng.findIndex(latLng => { return latLng[0] == T.waypointLatLng[index + 1][0] && latLng[1] == T.waypointLatLng[index + 1][1] });

        //         T.areaWaypoint[index + 1] = [firstIndex, secondIndex];
        //     }
        // });

        // const areaIndex = T.areaWaypoint.findIndex(a => {
        //     if (a) {
        //         return a[0] <= nearestIndex && a[1] >= nearestIndex;
        //     } else {
        //         return false;
        //     }
        // });

        // // console.log(T.points);

        // trayek.getBusStop(`${latlng.lat},${latlng.lng}`, function (response) {
        //     if (response.bus_stop.length > 0) {
        //         const form = T.waypoint_form(25, response.bus_stop[0]);
        //         const data = [];
        //         data.data = response.bus_stop[0];
        //         data.marker = T.markerWaypoint.off('dragstart', T.wpDragStart).off('dragend', T.wpDragEnd).on('dragend', T.moveBusStop).bindPopup(T.info_window(data.data), { offset: L.point(-5, -5) }).bindTooltip(T.info_window(data.data), { offset: L.point(-5, -20), direction: 'top' });
        //         T.points.splice(areaIndex, 0, data);

        //         T.markerWaypoint = null;

        //         $(".sortable-point > li:nth-of-type(" + areaIndex + ")").after(form);
        //         $(".bullet").each(function (index) { $(this).html(T.alphabet[index]); });

        //         T.reMarker();

        //         if(T.isGenerate){
        //             T.getRoutes(T.map.getZoom(), false);
        //         }
        //     }
        // })
    }

    trayek.vertexClick = (e) => {
        // if(T.pl !== undefined && T.pl !== null) {
        //     T.layerGroup.removeLayer(T.pl)
        // }

        // if(T.vertexPoint.some( x => { return x.lat.toFixed(5) == e.latlng.lat.toFixed(5) && x.lng.toFixed(5) == e.latlng.lng.toFixed(5) })) {
        //     e.cancel();
        // }
    }

    trayek.vertexDragStart = (e) => {
        // T.arrCircle.forEach( marker => { T.layerMarkerGroup.removeLayer(marker); })

        // if(T.pl !== undefined && T.pl !== null) {
        //     T.layerGroup.removeLayer(T.pl)
        // }
    }

    trayek.vertexDragEnd = (e) => {

        // const indexOf = T.polyline.getLatLngs().findIndex( x => {
        //     return e.vertex.latlng.lat.toFixed(5) == x.lat.toFixed(5) && e.vertex.latlng.lng.toFixed(5) == x.lng.toFixed(5)
        // });

        // reSegment( (segmentedPolyline) => {
        //     const startEndSegment = segmentedPolyline.filter( x => { 
        //         return x.start.index <= indexOf && x.end.index >= indexOf 
        //     })[0];

        //     if(startEndSegment){
        //         const segment = extract(startEndSegment.start.coordinate, startEndSegment.end.coordinate)
        //         const pl = L.polyline(segment, { color: 'blue', weight: 8 }) //.addTo(T.layerGroup);
        //         T.pl = pl

        //         // T.arrCircle.push(L.circle(startEndSegment.start.coordinate, { radius: 50, fillColor:'yellow' }).bindPopup('index: start').addTo(T.layerMarkerGroup));
        //         // T.arrCircle.push(L.circle(startEndSegment.end.coordinate, { radius: 50, fillColor:'yellow' }).bindPopup('index: end').addTo(T.layerMarkerGroup));

        //         const start = { lat: startEndSegment.start.coordinate.lat, lng: startEndSegment.start.coordinate.lng }
        //         const end = { lat: startEndSegment.end.coordinate.lat, lng: startEndSegment.end.coordinate.lng }
        //         const edited = { start: start, end: end, polyline: pl.encodePath() }
        //         const indexOfEdited = T.editedPolyline.findIndex( x => { return x.start.lat == start.lat && x.start.lng == start.lng && x.end.lat == end.lat && x.end.lng == end.lng })

        //         if(indexOfEdited > -1) {
        //             T.editedPolyline.splice(indexOfEdited, 1, edited)
        //         }else{
        //             T.editedPolyline.push(edited)
        //         }
        //     }
        // });
    }

    trayek.reMarker = () => {
        T.pointsBusStop = T.points.filter(x => { return x.data && x.data.hasOwnProperty('bs_stop') && x.data.bs_stop === '1' });
        T.points.forEach((element, index) => {
            if (element && element.hasOwnProperty('marker')) {
                const ind = T.pointsBusStop.findIndex(x => { return x == element });
                const icon = T.marker_icon(T.alphabet[ind], element.data.bs_stop);
                element.marker.setIcon(icon);
            }
        });
    }

    trayek.nearest = (point, vs) => {
        var currIndex = -1;
        var distance = 9999999999999999;

        vs.forEach((el, index) => {
            var new_distance = getDistance(point, el);
            currIndex = (new_distance < distance) ? index : currIndex;
            distance = (new_distance < distance) ? new_distance : distance;
        });

        return currIndex;
    };

    reSegment = (func) => {
        const latlngs = T.polyline.getLatLngs();
        var index = 0;
        var pointsToVertex = [];
        var segmentedPolyline = [];

        T.points.forEach( x => {
            var _closest = closestLatLng(latlngs, L.latLng(x.data.bs_lat, x.data.bs_lng))
            // x.segmentIndex = _closest.index;

            pointsToVertex.push(_closest);
        });

        while(pointsToVertex[index+1]){
            segmentedPolyline.push({ start: pointsToVertex[index], end: pointsToVertex[index+1]})

            index++;
        }

        func(segmentedPolyline)
    }

    enableEditMode = () => {
        T.layerGroup.removeLayer(T.markerWaypoint);
        T.markerWaypoint = null;

        T.polyline.enableEdit();
        T.polyline.off("mouseover", T.wpMouseOver).on("editable:vertex:dragstart", T.vertexDragStart).on("editable:vertex:dragend", T.vertexDragEnd).on("editable:vertex:click", T.vertexClick);

        const latlngs = T.polyline.getLatLngs()

        // latlngs.forEach( (x, index) => {
        //     T.arrCircle.push(L.circle(x, { color: 'blue', radius: 8, fillColor:'green' }).bindPopup('index: ' + index).addTo(T.layerMarkerGroup));
        // })

        T.points.forEach( x => {
            var _closest = closestLatLng(latlngs, L.latLng(x.data.bs_lat, x.data.bs_lng))
            x.segmentIndex = _closest.index;

            T.vertexPoint.push(_closest.coordinate);
        })

        T.vertexPoint.forEach( (x, index) => {
            // x.__vertex.dragging.disable();
            T.arrCircle.push(L.circle(x, { color: 'blue', radius: 10, fillColor: 'blue' }).bindPopup('index: ' + index).addTo(T.layerMarkerGroup));
        })
    }

    disableEditMode = () => {
        T.polyline.disableEdit();
        T.polyline.on("mouseover", T.wpMouseOver).off("editable:vertex:dragstart", T.vertexDragStart).off("editable:vertex:dragend", T.vertexDragEnd).on("editable:vertex:click", T.vertexClick);
        
        T.arrCircle.forEach( marker => { T.layerMarkerGroup.removeLayer(marker); })
    }

    getDistance = (origin, destination) => {
        var lon1 = toRadian(origin[1]),
            lat1 = toRadian(origin[0]),
            lon2 = toRadian(destination[1]),
            lat2 = toRadian(destination[0]);

        var deltaLat = lat2 - lat1;
        var deltaLon = lon2 - lon1;

        var a = Math.pow(Math.sin(deltaLat / 2), 2) + Math.cos(lat1) * Math.cos(lat2) * Math.pow(Math.sin(deltaLon / 2), 2);
        var c = 2 * Math.asin(Math.sqrt(a));
        var EARTH_RADIUS = 6371;

        return c * EARTH_RADIUS * 1000;
    }

    closestLatLng = (arr, latlng) => {
        var i = 0
        var distance = Number.MAX_SAFE_INTEGER
        var index = 0

        while(arr[i]){
            var ll = arr[i]
            var newDistance = L.GeometryUtil.distance(T.map, latlng, ll);

            if(newDistance <= distance) {
                distance = newDistance
                index = i
            }

            i++
        }

        return { coordinate: arr[index], index: index }
    }

    toRadian = (degree) => {
        return degree * Math.PI / 180;
    }

    extract = (start, end) => {
        var latlngs = T.polyline.getLatLngs();
        var arr = []
        var add = false
        var looping = true
        var i = 0;

        while(looping) {
            var x = latlngs[i];

            if(x.lat.toFixed(5) == start.lat.toFixed(5) && x.lng.toFixed(5) == start.lng.toFixed(5)){
                add = true
            }

            if(add) {
                arr.push(x)
            }

            if(x.lat.toFixed(5) == end.lat.toFixed(5) && x.lng.toFixed(5) == end.lng.toFixed(5)) {
                looping = false
            }

            i++
        }

        return arr;
    }

    cleanStringify = (object) => {
        if (object && typeof object === 'object') {
            object = copyWithoutCircularReferences([object], object);
        }

        return JSON.stringify(object);

        function copyWithoutCircularReferences(references, object) {
            var cleanObject = {};
            Object.keys(object).forEach(function(key) {
                var value = object[key];
                if (value && typeof value === 'object') {
                    if (references.indexOf(value) < 0) {
                        references.push(value);
                        cleanObject[key] = copyWithoutCircularReferences(references, value);
                        references.pop();
                    } else {
                        cleanObject[key] = '###_Circular_###';
                    }
                } else if (typeof value !== 'function') {
                    cleanObject[key] = value;
                }
            });

            return cleanObject;
        }
    }

    // trayek.dragElement = (elmnt) => {
    //     var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;

    //     const dragMouseDown = (e) => {
    //         e = e || window.event;
    //         e.preventDefault();
    //         // get the mouse cursor position at startup:
    //         pos3 = e.clientX;
    //         pos4 = e.clientY;
    //         console.log(pos3+' '+pos4);
    //         document.onmouseup = closeDragElement;
    //         // call a function whenever the cursor moves:
    //         document.onmousemove = elementDrag;
    //     }

    //     const elementDrag = (e) => {
    //         e = e || window.event;
    //         e.preventDefault();
    //         // calculate the new cursor position:
    //         pos1 = pos3 - e.clientX;
    //         pos2 = pos4 - e.clientY;
    //         pos3 = e.clientX;
    //         pos4 = e.clientY;
    //         // set the element's new position:

    //         elmnt.style.top = ((elmnt.offsetTop - pos2)<65?65:(elmnt.offsetTop - pos2)) + "px";
    //         elmnt.style.left = ((elmnt.offsetLeft - pos1)<20?20:(elmnt.offsetLeft - pos1)) + "px";
    //         // console.log(elmnt.style.top);
    //         // console.log(elmnt.style.left);
    //     }

    //     const closeDragElement = () => {
    //         /* stop moving when mouse button is released:*/
    //         document.onmouseup = null;
    //         document.onmousemove = null;
    //     }

    //     if (document.getElementById(elmnt.id + "-header")) {
    //         /* if present, the header is where you move the DIV from:*/
    //         document.getElementById(elmnt.id + "-header").onmousedown = dragMouseDown;
    //         console.log('dragMouseDownHeader');
    //     } else {
    //         /* otherwise, move the DIV from anywhere inside the DIV:*/
    //         elmnt.onmousedown = dragMouseDown;
    //         console.log('dragMouseDown');
    //     }
    // }

    var _foo1 = function () {
        console.log('foo 1 without param');
    }

    var _foo2 = function (a, b) {
        console.log(a);
        console.log(b);
    }

    return trayek;
}();