/**
 * Radius & blur heatmap per batas zoom (zoom <= maxZoom).
 */
const HEATMAP_ZOOM_STYLES = [
    { maxZoom: 10, radius: 45, blur: 30 },
    { maxZoom: 12, radius: 35, blur: 24 },
    { maxZoom: 14, radius: 28, blur: 18 },
    { maxZoom: 16, radius: 20, blur: 12 },
    { maxZoom: Infinity, radius: 12, blur: 7 },
];

let heatmapLayer = null;
let lastPoints = [];

function buildHeatmapPoints(pelangganList) {
    return pelangganList.filter((p) => p.latitude && p.longitude).map((p) => [p.latitude, p.longitude]);
}

export function initHeatmapLayer(pelangganList) {
    lastPoints = buildHeatmapPoints(pelangganList);
    heatmapLayer = L.heatLayer(lastPoints, { blur: 20, maxZoom: 16 });

    return heatmapLayer;
}

/**
 * Simpan titik hasil filter; layer baru di-render ulang saat heatmap aktif.
 */
export function setHeatmapData(pelangganFiltered) {
    lastPoints = buildHeatmapPoints(pelangganFiltered);
    renderHeatmapIfActive();
}

export function renderHeatmapIfActive() {
    if (!heatmapLayer?._map) return;

    const zoom = heatmapLayer._map.getZoom();
    const { radius, blur } = HEATMAP_ZOOM_STYLES.find((style) => zoom <= style.maxZoom);
    heatmapLayer.setOptions({ radius, blur });
    heatmapLayer.setLatLngs(lastPoints);
}
