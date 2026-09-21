import { CONFIG } from "../Config/index.js";



// Buat tile layer instances
const osmStandard = L.tileLayer(
    "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
    {
        attribution: "&copy; OpenStreetMap contributors",
        maxZoom: 19,
        minZoom: 10,
    },
);

const esriSatelit = L.tileLayer(
    "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}",
    {
        attribution:
            "Tiles &copy; Esri &mdash; Source: Esri, DigitalGlobe, GeoEye, Earthstar Geographics",
        maxZoom: 19,
        minZoom: 10,
    },
);

// Base maps collection
const baseMaps = {
    "OSM Standard": osmStandard,
    Satelit: esriSatelit,
};

// Initialize map
function initMap() {
    // Create map instance
    const map = L.map("map", {
        center: CONFIG.mapCenter,
        zoom: CONFIG.defaultZoom,
        layers: [osmStandard], // Default layer
    });
    
    map.zoomControl.remove();
    L.control
        .zoom({
            position: "bottomright",
        })
        .addTo(map);

    // Add scale control
    L.control.scale({ imperial: false }).addTo(map);

    // Add layer control (untuk ganti base map)
    L.control.layers(baseMaps, null, { position: "bottomleft" }).addTo(map);

    return map;
}

export { initMap };
