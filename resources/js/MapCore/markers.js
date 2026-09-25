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
        maxClusterRadius: 80, // Ditingkatkan ke 80 agar panning di zoom menengah lebih ringan
        spiderfyOnMaxZoom: true,
        zoomToBoundsOnClick: true
    });
    updateCounter(total, total); // Final count

    const markersToAdd = [];
    for (let i = 0; i < total; i++) {
        const p = pelangganList[i];

        let isTask = false;
        let markerOptions = {};
        
        if (p.status_kunjungan) {
            isTask = true;
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

        let marker;
        if (isTask) {
            marker = L.marker([p.latitude, p.longitude], markerOptions).bindPopup(
                () => renderPopupHTML(p),
                { closeButton: true }
            );
        } else {
            // Revert back to L.marker. Leaflet MarkerCluster uses CSS3 GPU hardware acceleration for L.marker animations.
            // Using Canvas for clusters causes CPU-bound full-canvas redraws during zoom animations, causing hangs.
            marker = L.marker([p.latitude, p.longitude]).bindPopup(
                () => renderPopupHTML(p),
                { closeButton: true }
            );
        }

        marker.data = p; 
        markers.push(marker);
        markersToAdd.push(marker);
    }
    
    markersLayer.addLayers(markersToAdd);

    heatmapLayer = initHeatmapLayer(pelangganList);

    // Hubungkan dengan tombol Heatmap di map-floating-bar
    const heatmapBtn = document.getElementById("heatmapToggleBtn");
    if (heatmapBtn) {
        // Hindari duplikasi event listener jika addMarkersBatch dipanggil ulang
        const newHeatmapBtn = heatmapBtn.cloneNode(true);
        heatmapBtn.parentNode.replaceChild(newHeatmapBtn, heatmapBtn);
        
        // Cek status saat ini
        let isHeatmapActive = heatmapLayer && mapRef.hasLayer(heatmapLayer);
        
        newHeatmapBtn.addEventListener("click", () => {
            isHeatmapActive = !isHeatmapActive;
            if (isHeatmapActive) {
                mapRef.removeLayer(markersLayer);
                if (heatmapLayer) {
                    mapRef.addLayer(heatmapLayer);
                    renderHeatmapIfActive(mapRef);
                }
                newHeatmapBtn.style.backgroundColor = "rgba(85, 110, 230, 0.94)"; // Primary color
                newHeatmapBtn.style.borderColor = "rgba(85, 110, 230, 0.94)";
                
                const textSpan = newHeatmapBtn.querySelector("span:last-child");
                if (textSpan) textSpan.textContent = "Marker";
                
                const icon = newHeatmapBtn.querySelector("i");
                if (icon) icon.className = "bx bx-map-pin";
            } else {
                if (heatmapLayer) mapRef.removeLayer(heatmapLayer);
                mapRef.addLayer(markersLayer);
                newHeatmapBtn.style.backgroundColor = ""; // Kembali ke CSS class
                newHeatmapBtn.style.borderColor = "";
                
                const textSpan = newHeatmapBtn.querySelector("span:last-child");
                if (textSpan) textSpan.textContent = "Heatmap";
                
                const icon = newHeatmapBtn.querySelector("i");
                if (icon) icon.className = "bx bx-layer";
            }
        });
    }
    
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

export function resetMapView(animate = true) {
    if (!mapRef) return;
    
    // Gunakan array markers asli untuk menghitung bounds yang valid
    if (markers && markers.length > 0) {
        try {
            const group = L.featureGroup(markers);
            const bounds = group.getBounds();
            if (bounds && bounds.isValid()) {
                mapRef.fitBounds(bounds, { padding: [50, 50], maxZoom: 16, animate: animate });
                return;
            }
        } catch (e) {
            console.error("Gagal menghitung bounds marker:", e);
        }
    }
    
    // Fallback jika tidak ada marker
    mapRef.setView([-1.6161, 103.583], 12, { animate: animate });
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
    
    // Reset toggle UI
    const heatmapBtn = document.getElementById("heatmapToggleBtn");
    if (heatmapBtn) {
        heatmapBtn.style.backgroundColor = ""; 
        heatmapBtn.style.borderColor = "";
        
        const textSpan = heatmapBtn.querySelector("span:last-child");
        if (textSpan) textSpan.textContent = "Heatmap";
        
        const icon = heatmapBtn.querySelector("i");
        if (icon) icon.className = "bx bx-layer";
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
