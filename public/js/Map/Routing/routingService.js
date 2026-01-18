import { showClearRouteButton } from "./routingController.js";
let routingControl = null;

export function drawRoute(map, origin, destination) {
    clearRoute(map);

    routingControl = L.Routing.control({
        waypoints: [
            L.latLng(origin.lat, origin.lng),
            L.latLng(destination.lat, destination.lng),
        ],
        routeWhileDragging: false,
        addWaypoints: false,
        draggableWaypoints: false,
        fitSelectedRoutes: true,
        show: true, // panel control
        createMarker: () => null,
    }).addTo(map);
    showClearRouteButton();
}

export function clearRoute(map) {
    if (!routingControl) return;

    map.removeControl(routingControl);
    routingControl = null;
}

export function hasActiveRoute() {
    return !!routingControl;
}
