import { updateProgress, updateCounter } from "../UI/loader.js";
import { delay } from "../Helpers/async.js";
import {
    initHeatmapLayer,
    setHeatmapData,
    renderHeatmapIfActive,
    updateHeatmapRadiusByZoom,
} from "./heatmap.js";
import { renderPopupHTML, bindPopupEvents } from "../UI/popup.js";

let markersLayer;
let markers = [];
let heatmapLayer;
let mapRef;
let activeHighlightRing = null;
export async function addMarkersBatch(map, pelangganList) {
    mapRef = map;
    const total = pelangganList.length;
    const batchSize = 50;
    const progressStart = 60;
    const progressEnd = 90;

    if (markersLayer) {
        markersLayer.clearLayers();
    }

    markersLayer = L.layerGroup();

    for (let i = 0; i < total; i++) {
        const p = pelangganList[i];

        const marker = L.marker([p.latitude, p.longitude]).bindPopup(
            renderPopupHTML(p),
            { closeButton: true }
        );

        marker.data = p; //   KUNCI FILTER
        markers.push(marker);
        markersLayer.addLayer(marker);

        // 🔄 PROGRESS OTOMATIS
        const percent =
            progressStart +
            Math.floor(((i + 1) / total) * (progressEnd - progressStart));

        updateProgress(
            percent,
            `Memproses pelanggan ${i + 1} dari ${total}...`
        );

        updateCounter(i + 1, total);

        // 🧠 beri napas UI
        if ((i + 1) % batchSize === 0) {
            await delay(0);
        }
    }

    heatmapLayer = initHeatmapLayer(pelangganList);

    const baseLayers = {
        "Mode Marker (Detail)": markersLayer,
        "Mode Heatmap (Agregasi)": heatmapLayer,
    };

    L.control
        .layers(baseLayers, null, {
            collapsed: true,
        })
        .addTo(map);

    map.on("overlayadd", (e) => {
        if (e.layer === heatmapLayer) {
            map.removeLayer(markersLayer);
        }

        if (e.layer === markersLayer) {
            map.removeLayer(heatmapLayer);
        }
    });

    map.on("zoomend", () => {
        if (map.hasLayer(heatmapLayer)) {
            updateHeatmapRadiusByZoom(mapRef);
            renderHeatmapIfActive(mapRef);
        }
    });

    //  SAAT PINDAH KE HEATMAP → RENDER SEKALI
    map.on("baselayerchange", (e) => {
        if (e.layer === heatmapLayer) {
            renderHeatmapIfActive(mapRef);
        }
    });

    map.addLayer(markersLayer);
}

export function filterPelanggan({
    tarif = null,
    minDaya = null,
    maxDaya = null,
}) {
    if (!markersLayer) {
        return;
    }
    const pelangganFiltered = [];
    markers.forEach((marker) => {
        const p = marker.data;

        const matchTarif = !tarif || p.golongan_tarif === tarif;
        const matchMin = minDaya === null || p.daya >= minDaya;
        const matchMax = maxDaya === null || p.daya <= maxDaya;

        if (matchTarif && matchMin && matchMax) {
            markersLayer.addLayer(marker);
            pelangganFiltered.push(p);
        } else {
            markersLayer.removeLayer(marker);
        }
    });
    // UPDATE DATA HEATMAP (AMAN)
    setHeatmapData(pelangganFiltered);

    // RENDER JIKA AKTIF
    renderHeatmapIfActive(mapRef);
}

export function switchToMarkerMode() {
    if (!mapRef || !markersLayer) return;

    // matikan heatmap jika aktif
    if (heatmapLayer && mapRef.hasLayer(heatmapLayer)) {
        mapRef.removeLayer(heatmapLayer);
    }

    // pastikan marker aktif
    if (!mapRef.hasLayer(markersLayer)) {
        mapRef.addLayer(markersLayer);
    }
}

export function highlightMarker(marker) {
    if (!marker) return;

    const map = marker._map;
    if (!map) return;

    const latlng = marker.getLatLng();

    // buka popup marker
    marker.openPopup();

    // hapus ring lama (jika ada)
    if (activeHighlightRing) {
        map.removeLayer(activeHighlightRing);
        activeHighlightRing = null;
    }

    // buat ring highlight
    activeHighlightRing = L.circleMarker(latlng, {
        radius: 18,
        color: "#0d6efd",
        weight: 3,
        fill: false,
        opacity: 1,
    }).addTo(map);

    // auto remove setelah 2 detik
    setTimeout(() => {
        if (activeHighlightRing) {
            map.removeLayer(activeHighlightRing);
            activeHighlightRing = null;
        }
    }, 2000);
}

export function getAllMarkers() {
    return markers;
}
