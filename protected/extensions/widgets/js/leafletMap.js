var LeafletMap = function()
{
    this.o = {
        uniqId: 0,
        centerLat: 0,
        centerLng: 0,
        zoom: 0,
        useCollapse: false,
        markers: []
    }
    this.map = 0;

    this.init = function(params)
    {
        $.extend(this.o, params);

        this.initMap();
        if (this.o.useCollapse)
            this.initExpandBtn();
    }

    this.initMap = function()
    {
        var self = this;

        var tiles = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            maxZoom: 18
        });
        var center = L.latLng(self.o.centerLat, self.o.centerLng);
        self.map = L.map('js-leaflet-map-'+self.o.uniqId, {
            //center: center,
            zoom: self.o.zoom,
            scrollWheelZoom: false,
            layers: [tiles],
            zoomAnimation: true
        });
        var markers = L.markerClusterGroup();
        var mArr = self.o.markers;

        var minLat = null;
        var maxLat = null;
        var minLng = null;
        var maxLng = null;

        for (var i = mArr.length - 1; i >= 0; i--) {
            if (minLat==null || mArr[i].lat<minLat)     minLat = mArr[i].lat;
            if (maxLat==null || mArr[i].lat>maxLat)     maxLat = mArr[i].lat;
            if (minLng==null || mArr[i].lng<minLng)     minLng = mArr[i].lng;
            if (maxLng==null || mArr[i].lng>maxLng)     maxLng = mArr[i].lng;

            var m = L.marker(L.latLng( mArr[i].lat, mArr[i].lng ));
            m.bindPopup('<a href="'+mArr[i].link+'">' + mArr[i].title + '</a>');
            markers.addLayer(m);
        };
        self.map.addLayer(markers);
        self.map.fitBounds([[minLat, minLng], [maxLat, maxLng]]);
    }

    this.initExpandBtn = function()
    {
        var self = this;

        var $expandBtn = $('#js-expand-btn-'+self.o.uniqId);
        var $collapseBtn = $('#js-collapse-btn-'+self.o.uniqId);
        var $map = $('#js-leaflet-map-'+self.o.uniqId);
        $expandBtn.click(function() {
            $expandBtn.addClass('hide');
            $collapseBtn.removeClass('hide');
            $map.addClass('opened');
            setTimeout(function() {
                return self.map.invalidateSize(true);
            }, 200);
            return false;
        });
        $collapseBtn.click(function() {
            $expandBtn.removeClass('hide');
            $collapseBtn.addClass('hide');
            $map.removeClass('opened');
            setTimeout(function() {
                return self.map.invalidateSize(true);
            }, 200);
            return false;
        });
    }
}