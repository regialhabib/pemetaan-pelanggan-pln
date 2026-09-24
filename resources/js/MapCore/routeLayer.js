import { renderPopupHTML } from "../UI/popup.js";
import { lokasiSaya } from "../Services/locationService.js";
import { fetchOptimizedTrip, fetchRoute } from "../Services/osrmService.js";
import { switchToMarkerMode, highlightMarker, getAllMarkers } from "./markers.js";
import { updateProgress, updateCounter } from "../UI/loader.js";

import { STATUS_KUNJUNGAN } from "../Config/index.js";

let routeLayer = null;
let activeRouteData = null;
let routeMarkersLayer = L.layerGroup();
let routeMarkers = [];

export function clearRoute(map) {
    if (routeLayer && map) {
        map.removeLayer(routeLayer);
        routeLayer = null;
        activeRouteData = null;
    }
    if (routeMarkersLayer && map) {
        routeMarkersLayer.clearLayers();
        map.removeLayer(routeMarkersLayer);
        routeMarkers = [];
    }
}

export async function startRouting(map, destinationLatLng) {
    if (!map) return;
    switchToMarkerMode();
    clearRoute(map);

    try {
        const origin = await getOriginLatLng();
        const route = await fetchRoute(origin, destinationLatLng);
        renderSingleRoute(map, route);
    } catch (err) {
        console.error("Routing dibatalkan:", err.message);
    }
}

async function getOriginLatLng() {
    if (!navigator.geolocation) throw new Error("Geolocation tidak didukung browser");
    return new Promise((resolve, reject) => {
        navigator.geolocation.getCurrentPosition(
            (pos) => resolve(L.latLng(pos.coords.latitude, pos.coords.longitude)),
            (err) => reject(err),
            { enableHighAccuracy: true, timeout: 55000, maximumAge: 30000 }
        );
    });
}

function renderSingleRoute(map, route) {
    const geojson = { type: "Feature", geometry: route.geometry };
    routeLayer = L.geoJSON(geojson, {
        style: { color: "#556ee6", weight: 5, opacity: 0.9 }
    }).addTo(map);

    activeRouteData = { distance: route.distance, duration: route.duration };
    map.fitBounds(routeLayer.getBounds(), { padding: [40, 40] });
}

// ======================== PETUGAS TRIP ROUTING ========================

export async function rencanakanRutePetugas(map, pelanggan) {
    try {
        const pos = await getOriginLatLng();
        const lokasiPetugas = { lat: pos.lat, lng: pos.lng };
        
        let koordinat = [`${lokasiPetugas.lng},${lokasiPetugas.lat}`];
        pelanggan.forEach(p => koordinat.push(`${p.longitude},${p.latitude}`));
        
        const data = await fetchOptimizedTrip(koordinat.join(";"));
        tampilkanRuteOSRM(map, data, pelanggan);
    } catch (err) {
        console.error("Gagal merencanakan rute", err);
    }
}

function tampilkanRuteOSRM(map, data, dataPelangganAsli) {
    clearRoute(map);
    
    let route = data.trips[0].geometry;
    routeLayer = L.geoJSON(route, {
        style: { color: "#556ee6", weight: 5, opacity: 0.7 },
    }).addTo(map);

    const total = data.waypoints.length;
    updateCounter(total, total);

    for (let i = 0; i < total; i++) {
        const wp = data.waypoints[i];

        if (wp.waypoint_index === 0) {
            lokasiSaya(map);
            continue;
        }

        let dataP = dataPelangganAsli[wp.waypoint_index - 1];
        let lat = wp.location[1];
        let lng = wp.location[0];
        let nomorKunjungan = wp.waypoint_index;
        
        let warnaMarker = dataP.status_kunjungan === STATUS_KUNJUNGAN.SUDAH ? "#34c38f" : 
                         (dataP.status_kunjungan === STATUS_KUNJUNGAN.DIPROSES ? "#f1b44c" : "#556ee6");
                         
        let icon = L.divIcon({
            className: "custom-marker",
            html: `<div style="background:${warnaMarker};color:white;border-radius:50%;width:32px;height:32px;text-align:center;line-height:32px;font-weight:bold;border:2px solid white;box-shadow:0 2px 5px rgba(0,0,0,0.3);">${nomorKunjungan}</div>`,
            iconSize: [32, 32],
            iconAnchor: [16, 16],
        });

        const marker = L.marker([lat, lng], { icon }).bindPopup(renderPopupHTML(dataP));
        marker.data = dataP;
        routeMarkers.push(marker);
        routeMarkersLayer.addLayer(marker);
    }

    routeMarkersLayer.addTo(map);
    map.fitBounds(routeLayer.getBounds(), { padding: [40, 40] });
}
