import { startLiveTracking } from "../Location/locationService.js";
import { getLastPosition } from "../Location/locationStore.js";
import { drawRoute, clearRoute, hasActiveRoute } from "./routingService.js";

export function startNavigation(map, marker) {
    // 1️⃣ Aktifkan live tracking (watchPosition)
    startLiveTracking(map);

    // 2️⃣ TUNGGU posisi pertama dari tracking
    const wait = setInterval(() => {
        const pos = getLastPosition();

        if (pos) {
            clearInterval(wait);

            // 3️⃣ Routing pakai posisi tracking
            drawRoute(map, { lat: pos[0], lng: pos[1] }, marker.getLatLng());
        }
    }, 300);
}

let controlInstance = null;
let controlContainer = null;

export function addClearRouteControl(map) {
    if (controlInstance) return;

    const ClearRouteControl = L.Control.extend({
        options: { position: "bottomright" },

        onAdd() {
            const btn = L.DomUtil.create(
                "button",
                "btn btn-danger shadow clear-route-btn d-none"
            );

            btn.innerHTML = "✖";

            btn.onclick = (e) => {
                e.stopPropagation();
                if (!hasActiveRoute()) return;
                clearRoute(map);
                hideClearRouteButton();
            };

            controlContainer = btn;
            return btn;
        },
    });

    controlInstance = new ClearRouteControl();
    map.addControl(controlInstance);
}

export function showClearRouteButton() {
    if (!controlContainer) return;
    controlContainer.classList.remove("d-none");
}

export function hideClearRouteButton() {
    if (!controlContainer) return;
    controlContainer.classList.add("d-none");
}
