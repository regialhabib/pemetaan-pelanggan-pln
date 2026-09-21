import { showMyLocation } from "../Services/locationService.js";
import { clearRoute } from "../MapCore/routeLayer.js";

export function addMyLocationControl(map) {
    const MyLocation = L.Control.extend({
        options: { position: "bottomright" },
        onAdd() {
            const btn = L.DomUtil.create("button", "btn btn-light shadow my-location-btn");
            btn.innerHTML = "📍";
            btn.onclick = () => showMyLocation(map);
            return btn;
        },
    });
    map.addControl(new MyLocation());
}

let clearControlContainer = null;

export function addClearRouteControl(map) {
    const ClearRouteControl = L.Control.extend({
        options: { position: "bottomright" },
        onAdd() {
            const btn = L.DomUtil.create("button", "btn btn-danger shadow clear-route-btn d-none");
            btn.innerHTML = "✖";
            btn.onclick = (e) => {
                e.stopPropagation();
                clearRoute(map);
                hideClearRouteButton();
            };
            clearControlContainer = btn;
            return btn;
        },
    });
    map.addControl(new ClearRouteControl());
}

export function showClearRouteButton() {
    if (clearControlContainer) clearControlContainer.classList.remove("d-none");
}

export function hideClearRouteButton() {
    if (clearControlContainer) clearControlContainer.classList.add("d-none");
}
