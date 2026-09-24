import { updateProgress, updateCounter } from "../UI/loader.js";

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
let switchControlInstance = null;


export function addMarkersBatch(map, pelangganList) {
    mapRef = map;
    const total = pelangganList.length;

    if (markersLayer) {
        markersLayer.clearLayers();
    }
    markers = [];

    markersLayer = L.markerClusterGroup({
        chunkedLoading: true,
        maxClusterRadius: 50,
        disableClusteringAtZoom: 16,
        spiderfyOnMaxZoom: true
    });
    updateCounter(total, total); // Final count

    const markersToAdd = [];
    for (let i = 0; i < total; i++) {
        const p = pelangganList[i];

        let markerOptions = {};
        if (p.status_kunjungan) {
            let color = "#f46a6a"; // Default: belum (merah)
            let iconClass = "bx-x";
            
            if (p.status_kunjungan === "sudah") {
                color = "#34c38f"; // hijau
                iconClass = "bx-check-double";
            } else if (p.status_kunjungan === "diproses") {
                color = "#f1b44c"; // kuning
                iconClass = "bx-time-five";
            }
            
            markerOptions.icon = L.divIcon({
                className: 'custom-status-icon',
                html: `<div style="background-color: ${color}; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; border: 2px solid white; box-shadow: 0 3px 6px rgba(0,0,0,0.3);">
                        <i class="bx ${iconClass} font-size-18"></i>
                       </div>`,
                iconSize: [32, 32],
                iconAnchor: [16, 16],
                popupAnchor: [0, -16]
            });
        }

        const marker = L.marker([p.latitude, p.longitude], markerOptions).bindPopup(
            renderPopupHTML(p),
            { closeButton: true },
        );

        marker.data = p; 
        markers.push(marker);
        markersToAdd.push(marker);
    }
    
    markersLayer.addLayers(markersToAdd);

    heatmapLayer = initHeatmapLayer(pelangganList);

    // Tambahkan kontrol Switch kustom untuk Heatmap
    const SwitchControl = L.Control.extend({
        options: { position: "bottomleft" },
        onAdd: function () {
            const div = L.DomUtil.create("div", "leaflet-control shadow-lg");
            div.style.fontFamily = "inherit";
            div.style.borderRadius = "30px";
            div.style.backgroundColor = "rgba(30, 41, 59, 0.94)";
            div.style.backdropFilter = "blur(10px)";
            div.style.WebkitBackdropFilter = "blur(10px)";
            div.style.border = "1px solid rgba(255, 255, 255, 0.15)";
            div.style.padding = "8px 18px";
            div.style.marginBottom = "25px";
            div.style.marginLeft = "15px";
            div.style.transition = "all 0.3s ease";
            
            div.innerHTML = `
                <div class="form-check form-switch form-switch-md mb-0 d-flex align-items-center" style="padding-left: 2.2em;">
                    <input class="form-check-input" type="checkbox" id="heatmapSwitch" style="cursor: pointer; margin-left: -2.2em;">
                    <label class="form-check-label fw-medium ms-2 mb-0 font-size-13 text-white" for="heatmapSwitch" style="cursor: pointer;">
                        Heatmap
                    </label>
                </div>
            `;
            
            L.DomEvent.disableClickPropagation(div);
            
            const checkbox = div.querySelector("#heatmapSwitch");
            checkbox.addEventListener("change", function(e) {
                if (e.target.checked) {
                    mapRef.removeLayer(markersLayer);
                    if (heatmapLayer) {
                        mapRef.addLayer(heatmapLayer);
                        renderHeatmapIfActive(mapRef);
                    }
                } else {
                    if (heatmapLayer) mapRef.removeLayer(heatmapLayer);
                    mapRef.addLayer(markersLayer);
                }
            });
            
            return div;
        }
    });
    
    if (switchControlInstance) {
        map.removeControl(switchControlInstance);
    }
    switchControlInstance = new SwitchControl();
    map.addControl(switchControlInstance);
    
    // Tampilkan marker secara default
    map.addLayer(markersLayer);

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

    const initialTotalDaya = pelangganList.reduce((sum, p) => sum + (Number(p.daya) || 0), 0);
    window.dispatchEvent(new CustomEvent("pelangganFilterUpdated", {
        detail: {
            total: total,
            filtered: total,
            totalDaya: initialTotalDaya
        }
    }));
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
    const validMarkers = [];

    markersLayer.clearLayers();

    markers.forEach((marker) => {
        const p = marker.data;

        const matchTarif = !tarif || p.golongan_tarif === tarif;
        const matchMin = minDaya === null || p.daya >= minDaya;
        const matchMax = maxDaya === null || p.daya <= maxDaya;

        if (matchTarif && matchMin && matchMax) {
            validMarkers.push(marker);
            pelangganFiltered.push(p);
        }
    });

    markersLayer.addLayers(validMarkers);
    // UPDATE DATA HEATMAP (AMAN)
    setHeatmapData(pelangganFiltered);

    // RENDER JIKA AKTIF
    renderHeatmapIfActive(mapRef);

    // Dispatch event untuk update widget counter UI
    const totalDayaSum = pelangganFiltered.reduce((sum, p) => sum + (Number(p.daya) || 0), 0);
    window.dispatchEvent(new CustomEvent("pelangganFilterUpdated", {
        detail: {
            total: markers.length,
            filtered: pelangganFiltered.length,
            totalDaya: totalDayaSum
        }
    }));

    return {
        total: markers.length,
        filtered: pelangganFiltered.length,
        totalDaya: totalDayaSum
    };
}

export function resetMapView() {
    if (!mapRef) return;
    
    // Gunakan array markers asli untuk menghitung bounds yang valid
    if (markers && markers.length > 0) {
        try {
            const group = L.featureGroup(markers);
            const bounds = group.getBounds();
            if (bounds && bounds.isValid()) {
                mapRef.fitBounds(bounds, { padding: [50, 50], maxZoom: 16 });
                return;
            }
        } catch (e) {
            console.error("Gagal menghitung bounds marker:", e);
        }
    }
    
    // Fallback jika tidak ada marker
    mapRef.setView([-1.6161, 103.583], 14);
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
        color: "#556ee6",
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
