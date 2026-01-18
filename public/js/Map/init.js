import { CONFIG } from "../Config/mapConfig.js";

window.map = null;

// Buat tile layer instances
const osmStandard = L.tileLayer(
    "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
    {
        attribution: "&copy; OpenStreetMap contributors",
        maxZoom: 19,
        minZoom: 10,
    }
);

const esriSatelit = L.tileLayer(
    "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}",
    {
        attribution:
            "Tiles &copy; Esri &mdash; Source: Esri, DigitalGlobe, GeoEye, Earthstar Geographics",
        maxZoom: 19,
        minZoom: 10,
    }
);

// Base maps collection
const baseMaps = {
    "OSM Standard": osmStandard,
    Satelit: esriSatelit,
};

// Initialize map
function initMap() {
    // Create map instance
    window.map = L.map("map", {
        center: CONFIG.mapCenter,
        zoom: CONFIG.defaultZoom,
        layers: [osmStandard], // Default layer
    });

    // Add scale control
    L.control.scale({ imperial: false }).addTo(window.map);

    // Add layer control (untuk ganti base map)
    L.control.layers(baseMaps).addTo(window.map);

    return window.map; // Return untuk chaining jika perlu
}

export { initMap };
