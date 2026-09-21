let heatmapLayer = null;
let lastPoints = [];
function buildHeatmapPoints(pelangganList) {
    return pelangganList
        .filter((p) => p.latitude && p.longitude)
        .map((p) => [
            p.latitude,
            p.longitude,
            // default intensity = 1
        ]);
}

function initHeatmapLayer(pelangganList) {
     lastPoints = buildHeatmapPoints(pelangganList);

    heatmapLayer = L.heatLayer(lastPoints, {
        // radius: 35, 
        blur: 20,
        maxZoom: 16,
    });

    return heatmapLayer;
}

// UPDATE DATA SAJA
function setHeatmapData(pelangganFiltered) {
    lastPoints = buildHeatmapPoints(pelangganFiltered);
}

// RENDER HANYA SAAT AKTIF
function renderHeatmapIfActive(map) {
    if (!heatmapLayer) return;
    if (!map.hasLayer(heatmapLayer)) return;

    heatmapLayer.setLatLngs(lastPoints);
}

function updateHeatmapRadiusByZoom(map) {
    if (!heatmapLayer) return;

    const zoom = map.getZoom();
    let radius, blur;

    if (zoom <= 10) {
        radius = 45; blur = 30;
    } else if (zoom <= 12) {
        radius = 35; blur = 24;
    } else if (zoom <= 14) {
        radius = 28; blur = 18;
    } else if (zoom <= 16) {
        radius = 20; blur = 12;
    } else {
        radius = 12; blur = 7;
    }

    heatmapLayer.setOptions({ radius, blur });
}

export { initHeatmapLayer, setHeatmapData, renderHeatmapIfActive, updateHeatmapRadiusByZoom };
