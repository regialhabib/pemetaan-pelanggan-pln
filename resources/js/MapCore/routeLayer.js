import { getCurrentLatLng, startLiveTracking } from "../Services/locationService.js";
import { fetchOptimizedTrip, fetchRoute } from "../Services/osrmService.js";
import { switchToMarkerMode, getAllMarkers } from "./markers.js";
import { COLORS, STATUS_KUNJUNGAN, getStatusMeta } from "../Config/index.js";

let routeLayer = null;

/**
 * Hapus garis rute aktif. Memicu event `route:cleared` di map (dipakai tombol "Hapus Rute").
 */
export function clearRoute(map) {
    if (!routeLayer) return;

    map.removeLayer(routeLayer);
    routeLayer = null;
    map.fire("route:cleared");
}

function drawRoute(map, geometry, opacity) {
    clearRoute(map);

    routeLayer = L.geoJSON(geometry, {
        style: { color: COLORS.primary, weight: 5, opacity },
    }).addTo(map);

    map.fitBounds(routeLayer.getBounds(), { padding: [40, 40] });
    map.fire("route:shown");
}

export async function startRouting(map, destinationLatLng) {
    switchToMarkerMode();

    const origin = await getCurrentLatLng();
    const route = await fetchRoute(origin, destinationLatLng);
    drawRoute(map, route.geometry, 0.9);
}

function createVisitOrderIcon(order, status) {
    const color = status === STATUS_KUNJUNGAN.BELUM ? COLORS.primary : getStatusMeta(status).color;

    return L.divIcon({
        className: "custom-marker",
        html: `<div class="map-pin map-pin-number" style="background-color: ${color};">${order}</div>`,
        iconSize: [32, 32],
        iconAnchor: [16, 16],
        popupAnchor: [0, -16],
    });
}

/**
 * Rute kunjungan teroptimasi dari lokasi petugas ke seluruh pelanggan di peta,
 * lalu beri nomor urut kunjungan pada marker.
 */
export async function rencanakanRutePetugas(map) {
    const markers = getAllMarkers();
    if (!markers.length) return;

    const origin = await getCurrentLatLng();
    const trip = await fetchOptimizedTrip([origin, ...markers.map((marker) => marker.getLatLng())]);

    drawRoute(map, trip.trips[0].geometry, 0.7);

    // waypoints[0] adalah lokasi petugas; waypoints[i] milik markers[i - 1].
    trip.waypoints.slice(1).forEach((waypoint, i) => {
        const marker = markers[i];
        marker.setIcon(createVisitOrderIcon(waypoint.waypoint_index, marker.data.status_kunjungan));
    });

    startLiveTracking(map);
}
