const LOCATION_COLOR = "#0d6efd";

const tracking = {
    watchId: null,
    marker: null,
    accuracyCircle: null,
    centerOnNextFix: false,
};

function getCurrentPosition(options) {
    return new Promise((resolve, reject) => {
        if (!navigator.geolocation) {
            reject(new Error("Geolocation tidak didukung browser"));
            return;
        }
        navigator.geolocation.getCurrentPosition(resolve, reject, options);
    });
}

export async function getCurrentLatLng() {
    const pos = await getCurrentPosition({ enableHighAccuracy: true, timeout: 55000, maximumAge: 30000 });
    return L.latLng(pos.coords.latitude, pos.coords.longitude);
}

function updateLocationLayers(map, pos) {
    const latlng = [pos.coords.latitude, pos.coords.longitude];

    if (!tracking.marker) {
        tracking.marker = L.circleMarker(latlng, {
            radius: 6,
            color: LOCATION_COLOR,
            weight: 2,
            fillColor: LOCATION_COLOR,
            fillOpacity: 1,
        }).addTo(map);

        tracking.accuracyCircle = L.circle(latlng, {
            radius: pos.coords.accuracy,
            color: LOCATION_COLOR,
            weight: 1,
            fillColor: LOCATION_COLOR,
            fillOpacity: 0.15,
        }).addTo(map);
    } else {
        tracking.marker.setLatLng(latlng);
        tracking.accuracyCircle.setLatLng(latlng).setRadius(pos.coords.accuracy);
    }

    if (tracking.centerOnNextFix) {
        tracking.centerOnNextFix = false;
        map.setView(latlng, 14, { animate: true, duration: 0.5 });
    }
}

export function startLiveTracking(map) {
    if (!navigator.geolocation) {
        console.error("Geolocation not supported");
        return;
    }

    if (tracking.watchId !== null) return;

    tracking.watchId = navigator.geolocation.watchPosition(
        (pos) => updateLocationLayers(map, pos),
        (err) => console.error("[GPS ERROR]", err.message),
        { enableHighAccuracy: true, maximumAge: 1000, timeout: 10000 },
    );
}

/**
 * Mulai live tracking lalu pusatkan peta ke lokasi petugas begitu posisi diketahui.
 */
export function showMyLocation(map) {
    if (tracking.marker) {
        map.setView(tracking.marker.getLatLng(), 14, { animate: true, duration: 0.5 });
        return;
    }

    tracking.centerOnNextFix = true;
    startLiveTracking(map);
}
