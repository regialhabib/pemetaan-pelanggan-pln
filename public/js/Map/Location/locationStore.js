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

    if (locationState.marker) map.removeLayer(locationState.marker);
    if (locationState.accuracyCircle)
        map.removeLayer(locationState.accuracyCircle);

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
    if (locationState.marker) map.removeLayer(locationState.marker);
    if (locationState.accuracyCircle)
        map.removeLayer(locationState.accuracyCircle);

    locationState.active = false;
    locationState.marker = null;
    locationState.accuracyCircle = null;
}

export function hasLocation() {
    return locationState.active;
}
