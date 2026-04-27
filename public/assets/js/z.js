var Z = function () {
    let _z = {}

    var Z_ = this;
    var layerPolyGroup = null;
    var map = null;
    var polygon = [];

    _z.load = () => {
        Z.polygon = [];
        Z.layerPolyGroup = L.featureGroup();
    }

    _z.reset = () => {
        if (Z.polygon.length > 0) { Z.polygon.forEach( poly => { Z.layerPolyGroup.removeLayer(poly) }); }
    }

    _z.draws = (polygon, map) => {
        Z.map = map;

        polygon.forEach( poly => { addPolygon(poly); });

        map.flyToBounds(Z.layerPolyGroup.getBounds(), { duration: 0.5, maxZoom: 13, paddingTopLeft: [350, 0] });
    }

    const addPolygon = (data) => {
        if(data.polygon) {
            poly = L.polygon(data.polygon, {
                color: data.color,
                data: {
                    "newshelter": data.ns,
                    "oldshelter": data.os
                }
            }).addTo(Z.layerPolyGroup);

            poly.bindPopup('<i class="fa fa-chevron-right angle"></i> : ' + data.os.bs_nm + '<br/>' + 
                            '<i class="fa fa-chevron-right angle"></i> : ' + data.ns.bs_nm + '</br>' + 
                            '<i class="fa fa-chevron-right angle"></i> : ' + parseFloat(data.angle).toFixed(2) + '</br>' + 
                            '<i class="fa fa-chevron-right angle"></i> : ' + data.lat + ',' + data.lng + '', 
                            { permanent: false, direction: 'top' });

            Z.polygon.push(poly);
        }else{
            poly = L.circle(data.circle, {radius: 5, fillColor: data.color }).bindPopup('Titik awal').addTo(Z.layerPolyGroup);

            Z.polygon.push(poly);
        }
    }

    return _z;
}();