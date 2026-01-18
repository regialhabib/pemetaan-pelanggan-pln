import { switchToMarkerMode } from "./markers.js";

let mapRef = null;
let activeRouteLayer = null;
let activeRouteData = null;

/**
 * Inisialisasi routing (dipanggil sekali)
 */
export function initRouting(map) {
    mapRef = map;
}

/**
 * Ambil titik asal (sementara: static / fallback)
 * Bisa diganti GPS nanti
 */
async function getOriginLatLng({ highAccuracy = true, timeout = 55000 } = {}) {
    if (!navigator.geolocation) {
        throw new Error("Geolocation tidak didukung browser");
    }

    return new Promise((resolve, reject) => {
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const { latitude, longitude } = pos.coords;
                resolve(L.latLng(latitude, longitude));
            },
            (err) => {
                let message = "Gagal mendapatkan lokasi";

                switch (err.code) {
                    case err.PERMISSION_DENIED:
                        message = "Akses lokasi ditolak user";
                        break;
                    case err.POSITION_UNAVAILABLE:
                        message = "Lokasi tidak tersedia";
                        break;
                    case err.TIMEOUT:
                        message = "Permintaan lokasi timeout";
                        break;
                }

                console.error(message, err);
                reject(new Error(message));
            },
            {
                enableHighAccuracy: highAccuracy,
                timeout,
                maximumAge: 30000, // cache 30 detik
            }
        );
    });
}

/**
 * Hapus rute aktif
 */
export function clearRoute() {
    if (activeRouteLayer && mapRef) {
        mapRef.removeLayer(activeRouteLayer);
        activeRouteLayer = null;
        activeRouteData = null;
    }
}

/**
 * Mulai routing ke marker
 */
export async function startRouting(destinationLatLng) {
    if (!mapRef) return;

    switchToMarkerMode();
    clearRoute();

    try {
        const origin = await getOriginLatLng({ highAccuracy: true });
        const route = await fetchRoute(origin, destinationLatLng);
        renderRoute(route);
    } catch (err) {
       
        console.error("Routing dibatalkan:", err.message);
    }
}

/**
 * Request ke OSRM
 */
async function fetchRoute(origin, destination) {
    const url = `
https://router.project-osrm.org/route/v1/driving/
${origin.lng},${origin.lat};
${destination.lng},${destination.lat}
?overview=full&geometries=geojson
    `.trim();

    const res = await fetch(url);
    if (!res.ok) throw new Error("OSRM request failed");

    const data = await res.json();

    if (!data.routes || !data.routes.length) {
        throw new Error("Route not found");
    }

    return data.routes[0];
}

/**
 * Render rute ke peta
 */
function renderRoute(route) {
    const geojson = {
        type: "Feature",
        geometry: route.geometry,
    };

    activeRouteLayer = L.geoJSON(geojson, {
        style: {
            color: "#556ee6",
            weight: 5,
            opacity: 0.9,
        },
    }).addTo(mapRef);

    activeRouteData = {
        distance: route.distance,
        duration: route.duration,
    };

    // zoom agar rute terlihat utuh
    mapRef.fitBounds(activeRouteLayer.getBounds(), {
        padding: [40, 40],
    });
}

/**
 * Ambil info rute aktif (optional UI)
 */
export function getActiveRouteInfo() {
    return activeRouteData;
}
