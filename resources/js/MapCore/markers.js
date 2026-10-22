import { initHeatmapLayer, setHeatmapData, renderHeatmapIfActive } from "./heatmap.js";
import { renderPopupHTML } from "../UI/popup.js";
import { CONFIG, COLORS, getStatusMeta } from "../Config/index.js";

let mapRef = null;
let markersLayer = null;
let heatmapLayer = null;
let markers = [];
let visibleMarkers = [];
let activeHighlightRing = null;

function createStatusIcon(status) {
    const { color, icon } = getStatusMeta(status);

    return L.divIcon({
        className: "custom-status-icon",
        html: `<div class="map-pin" style="background-color: ${color};"><i class="bx ${icon} font-size-18"></i></div>`,
        iconSize: [32, 32],
        iconAnchor: [16, 16],
        popupAnchor: [0, -16],
    });
}

function createMarker(p) {
    const options = p.status_kunjungan ? { icon: createStatusIcon(p.status_kunjungan) } : {};
    const marker = L.marker([p.latitude, p.longitude], options).bindPopup(() => renderPopupHTML(p), {
        closeButton: true,
    });

    marker.data = p;
    marker.searchText = [p.nama, p.id_pelanggan, p.alamat, p.no_hp].filter(Boolean).join(" ").toLowerCase();

    return marker;
}

/**
 * Isi layer marker; MarkerClusterGroup punya `addLayers` (batch), FeatureGroup biasa tidak.
 */
function fillMarkersLayer(list) {
    markersLayer.clearLayers();
    if (markersLayer.addLayers) {
        markersLayer.addLayers(list);
    } else {
        list.forEach((marker) => markersLayer.addLayer(marker));
    }
    visibleMarkers = list;
}

/**
 * Pasang marker pelanggan ke peta. `cluster: false` dipakai peta rute petugas agar nomor urut kunjungan tetap terlihat.
 */
export function addMarkersBatch(map, pelangganList, { cluster = true } = {}) {
    mapRef = map;
    markers = pelangganList.map(createMarker);
    markersLayer = cluster
        ? L.markerClusterGroup({
              chunkedLoading: true,
              maxClusterRadius: 80, // Ditingkatkan ke 80 agar panning di zoom menengah lebih ringan
              spiderfyOnMaxZoom: true,
              zoomToBoundsOnClick: true,
          })
        : L.featureGroup();

    fillMarkersLayer(markers);
    map.addLayer(markersLayer);

    heatmapLayer = initHeatmapLayer(pelangganList);
    document.getElementById("heatmapToggleBtn")?.addEventListener("click", () => {
        setHeatmapMode(!map.hasLayer(heatmapLayer));
    });
    map.on("zoomend", renderHeatmapIfActive);
}

function summarize(list) {
    return {
        total: markers.length,
        filtered: list.length,
        totalDaya: list.reduce((sum, marker) => sum + (Number(marker.data.daya) || 0), 0),
    };
}

export function getPelangganStats() {
    return summarize(visibleMarkers);
}

export function filterPelanggan({ tarif = null, minDaya = null, maxDaya = null }) {
    if (!markersLayer) {
        return null;
    }

    const filtered = markers.filter(({ data: p }) => {
        const matchTarif = !tarif || p.golongan_tarif === tarif;
        const matchMin = minDaya === null || p.daya >= minDaya;
        const matchMax = maxDaya === null || p.daya <= maxDaya;

        return matchTarif && matchMin && matchMax;
    });

    fillMarkersLayer(filtered);
    setHeatmapData(filtered.map((marker) => marker.data));

    return summarize(filtered);
}

export function resetMapView(animate = true) {
    if (!mapRef) return;

    const bounds = L.latLngBounds(markers.map((marker) => marker.getLatLng()));
    if (bounds.isValid()) {
        mapRef.fitBounds(bounds, { padding: [50, 50], maxZoom: 16, animate });
        return;
    }

    mapRef.setView(CONFIG.mapCenter, CONFIG.defaultZoom, { animate });
}

function updateHeatmapButton(active) {
    const btn = document.getElementById("heatmapToggleBtn");
    if (!btn) return;

    btn.classList.toggle("is-active", active);

    const textSpan = btn.querySelector("span:last-child");
    if (textSpan) textSpan.textContent = active ? "Marker" : "Heatmap";

    const icon = btn.querySelector("i");
    if (icon) icon.className = active ? "bx bx-map-pin" : "bx bx-layer";
}

function setHeatmapMode(active) {
    if (!mapRef || !markersLayer) return;

    if (active) {
        mapRef.removeLayer(markersLayer);
        mapRef.addLayer(heatmapLayer);
        renderHeatmapIfActive();
    } else {
        mapRef.removeLayer(heatmapLayer);
        mapRef.addLayer(markersLayer);
    }

    updateHeatmapButton(active);
}

export function switchToMarkerMode() {
    setHeatmapMode(false);
}

function showHighlightRing(latlng) {
    activeHighlightRing?.remove();

    const ring = L.circleMarker(latlng, {
        radius: 18,
        color: COLORS.primary,
        weight: 3,
        fill: false,
        opacity: 1,
    }).addTo(mapRef);
    activeHighlightRing = ring;

    setTimeout(() => {
        ring.remove();
        if (activeHighlightRing === ring) activeHighlightRing = null;
    }, 2000);
}

/**
 * Zoom ke marker (membuka cluster bila perlu), buka popup, dan beri ring sorotan.
 */
export function focusMarker(marker) {
    switchToMarkerMode();
    mapRef.setView(marker.getLatLng(), 17, { animate: false });

    const reveal = () => {
        marker.openPopup();
        showHighlightRing(marker.getLatLng());
    };

    if (markersLayer.zoomToShowLayer) {
        markersLayer.zoomToShowLayer(marker, reveal);
    } else {
        reveal();
    }
}

export function getAllMarkers() {
    return markers;
}

export function getVisibleMarkers() {
    return visibleMarkers;
}
