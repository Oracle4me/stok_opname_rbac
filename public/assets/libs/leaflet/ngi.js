var map, featureList;

var proto_initIcon = L.Marker.prototype._initIcon;
var proto_setPos = L.Marker.prototype._setPos;

var oldIE = (L.DomUtil.TRANSFORM === 'msTransform');

L.Marker.addInitHook(function () {
  var iconOptions = this.options.icon && this.options.icon.options;
  var iconAnchor = iconOptions && this.options.icon.options.iconAnchor;
  if (iconAnchor) {
    iconAnchor = (iconAnchor[0] + 'px ' + iconAnchor[1] + 'px');
  }
  this.options.rotationOrigin = this.options.rotationOrigin || iconAnchor || 'center bottom' ;
  this.options.rotationAngle = this.options.rotationAngle || 0;

  // Ensure marker keeps rotated during dragging
  this.on('drag', function(e) { e.target._applyRotation(); });
});

L.Marker.include({
  _initIcon: function() {
    proto_initIcon.call(this);
  },

  _setPos: function (pos) {
    proto_setPos.call(this, pos);
    this._applyRotation();
  },

  _applyRotation: function () {
    if(this.options.rotationAngle) {
      this._icon.style[L.DomUtil.TRANSFORM+'Origin'] = this.options.rotationOrigin;

      if(oldIE) {
        // for IE 9, use the 2D rotation
        this._icon.style[L.DomUtil.TRANSFORM] = 'rotate(' + this.options.rotationAngle + 'deg)';
      } else {
        // for modern browsers, prefer the 3D accelerated version
        this._icon.style[L.DomUtil.TRANSFORM] += ' rotateZ(' + this.options.rotationAngle + 'deg)';
      }
    }
  },

  setRotationAngle: function(angle) {
    this.options.rotationAngle = angle;
    this.update();
    return this;
  },

  setRotationOrigin: function(origin) {
    this.options.rotationOrigin = origin;
    this.update();
    return this;
  }
});

$(window).resize(function() {
  console.log('window resize');
  sizeLayerControl();
});

$(document).on("click", ".feature-row", function(e) {
  console.log('.feature-row click');
  $(document).off("mouseout", ".feature-row", clearHighlight);
  sidebarClick(parseInt($(this).attr("id"), 10));

});

if ( !("ontouchstart" in window) ) {
  $(document).on("mouseover", ".feature-row", function(e) {
      console.log('.feature-row mouseover');
  });
}

$(document).on("mouseout", ".feature-row", clearHighlight);



function animateSidebar() {
  $("#sidebar").animate({
    width: "toggle"
  }, 350, function() {
    map.invalidateSize();
  });
}

function sizeLayerControl() {
  $(".leaflet-control-layers").css("max-height", $("#map").height() - 50);
}

function clearHighlight() {
  console.log('clearHighlight');
  highlight.clearLayers();
}

function sidebarClick(id) {
  console.log('sidebarClick');
  var layer = markerClusters.getLayer(id);
  map.setView([layer.getLatLng().lat, layer.getLatLng().lng], 17);
  layer.fire("click");
  /* Hide sidebar and go to the map on small screens */
  if (document.body.clientWidth <= 767) {
    $("#sidebar").hide();
    map.invalidateSize();
  }
}

function syncSidebar() {
  console.log('syncSidebar');
  $("#feature-list tbody").empty();
  
}

