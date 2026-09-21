let locationState = {
    active: false,
    marker: null,
    accuracyCircle: null,
    watchId: null,
    lastLatLng: null,
};

export function setWatchId(id) {
    locationState.watchId = id;
}

export function updatePosition(latlng) {
    locationState.lastLatLng = latlng;
}

export function getLastPosition() {
    return locationState.lastLatLng;
}

export function clearTracking(map) {
    if (locationState.watchId !== null) {
        navigator.geolocation.clearWatch(locationState.watchId);
    }

    if (locationState.marker && map) map.removeLayer(locationState.marker);
    if (locationState.accuracyCircle && map) map.removeLayer(locationState.accuracyCircle);

    locationState.active = false;
    locationState.marker = null;
    locationState.accuracyCircle = null;
    locationState.watchId = null;
}

export function isTrackingActive() {
    return locationState.active;
}

export function getCurrentLocationLayers() {
    return {
        marker: locationState.marker,
        accuracyCircle: locationState.accuracyCircle,
    };
}

export function setLocation(marker, circle) {
    locationState.active = true;
    locationState.marker = marker;
    locationState.accuracyCircle = circle;
}

export function clearLocation(map) {
    if (locationState.marker && map) map.removeLayer(locationState.marker);
    if (locationState.accuracyCircle && map) map.removeLayer(locationState.accuracyCircle);

    locationState.active = false;
    locationState.marker = null;
    locationState.accuracyCircle = null;
}

export function hasLocation() {
    return locationState.active;
}

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

export function lokasiSaya(map) {
    startLiveTracking(map);

    const wait = setInterval(() => {
        const pos = getLastPosition();
        if (!pos) return;
        clearInterval(wait);
    }, 300);
}

export function startLiveTracking(map) {
    if (!navigator.geolocation) {
        console.error("Geolocation not supported");
        return;
    }

    if (isTrackingActive()) return;

    const watchId = navigator.geolocation.watchPosition(
        (pos) => {
            const latlng = [pos.coords.latitude, pos.coords.longitude];
            updatePosition(latlng);

            if (!isTrackingActive()) {
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
        },
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
