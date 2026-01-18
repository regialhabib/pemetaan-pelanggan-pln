import {
    setLocation,
    getLastPosition ,
    setWatchId,
    clearTracking,
    isTrackingActive,
    updatePosition,
    getCurrentLocationLayers,
} from "./locationStore.js";

export function showMyLocation(map) {
    startLiveTracking(map);

    const wait = setInterval(() => {
        const pos = getLastPosition();
        if (!pos) return;

        clearInterval(wait);

        map.setView(pos, 14, {
            animate: true,
            duration: 0.5,
        });
    }, 300);
}

export function startLiveTracking(map) {
    if (!navigator.geolocation) {
        console.error("Geolocation not supported");
        return;
    }

    // 🔒 jangan buat watcher baru
    if (isTrackingActive()) return;

    const watchId = navigator.geolocation.watchPosition(
        (pos) => {
            const latlng = [pos.coords.latitude, pos.coords.longitude];
            updatePosition(latlng);

            if (!isTrackingActive()) {
                // 🔵 marker pertama
                const marker = L.circleMarker(latlng, {
                    radius: 6,
                    color: "#0d6efd",
                    weight: 2,
                    fillColor: "#0d6efd",
                    fillOpacity: 1,
                }).addTo(map);

                const accuracy = L.circle(latlng, {
                    radius: pos.coords.accuracy,
                    color: "#0d6efd",
                    weight: 1,
                    fillColor: "#0d6efd",
                    fillOpacity: 0.15,
                }).addTo(map);

                setLocation(marker, accuracy);
            } else {
                const { marker, accuracyCircle } = getCurrentLocationLayers();

                marker.setLatLng(latlng);
                accuracyCircle.setLatLng(latlng);
                accuracyCircle.setRadius(pos.coords.accuracy);
            }
        },
        (err) => {
            console.error("[GPS ERROR]", err.message);
        },
        {
            enableHighAccuracy: true,
            maximumAge: 1000,
            timeout: 10000,
        }
    );

    setWatchId(watchId);
}

export function stopLiveTracking(map) {
    clearTracking(map);
}

export function centerToMyLocation(map) {
    const { marker } = getCurrentLocationLayers();
    if (!marker) return;

    map.panTo(marker.getLatLng(), { animate: true });
}
