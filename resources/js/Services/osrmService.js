const OSRM_BASE = "https://router.project-osrm.org";

function toOsrmCoordinates(latLngs) {
    return latLngs.map((latLng) => `${latLng.lng},${latLng.lat}`).join(";");
}

export async function fetchRoute(origin, destination) {
    const url = `${OSRM_BASE}/route/v1/driving/${toOsrmCoordinates([origin, destination])}?overview=full&geometries=geojson`;
    const res = await fetch(url);
    if (!res.ok) throw new Error("OSRM request failed");
    const data = await res.json();
    if (!data.routes?.length) throw new Error("Route not found");
    return data.routes[0];
}

/**
 * Urutan kunjungan teroptimasi dari titik pertama. `waypoints` dikembalikan sesuai urutan input,
 * dengan `waypoint_index` = urutan kunjungan.
 */
export async function fetchOptimizedTrip(latLngs) {
    const url = `${OSRM_BASE}/trip/v1/driving/${toOsrmCoordinates(latLngs)}?roundtrip=false&source=first&destination=last&overview=full&geometries=geojson`;
    const res = await fetch(url);
    const data = await res.json();
    if (data.code !== "Ok") throw new Error(data.message || "OSRM Trip Error");
    return data;
}
