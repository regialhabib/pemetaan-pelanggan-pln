import { showMyLocation } from "../Map/Location/locationService.js";

export function addMyLocationControl(map) {
    const MyLocation = L.Control.extend({
        options: { position: "bottomright" },

        onAdd() {
            const btn = L.DomUtil.create(
                "button",
                "btn btn-light shadow my-location-btn"
            );
            btn.innerHTML = "📍";

            btn.onclick = () => {
                showMyLocation(map);
            };

            return btn;
        },
    });

    map.addControl(new MyLocation());
}