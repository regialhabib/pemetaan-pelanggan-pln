import { CONFIG } from "../Config/index.js";
import { showLoader, hideLoader, updateProgress } from "../UI/loader.js";
import { showSimpleErrorToast } from "../UI/toast.js";
import { initPelangganFilter } from "../UI/filter.js";
import { initSearch } from "../UI/search.js";
import { bindPopupEvents } from "../UI/popup.js";
import { addResetViewControl } from "../UI/controls.js";

const TILE_OPTIONS = { maxZoom: 19, minZoom: 10 };

const baseMaps = {
    "OSM Standard": L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        ...TILE_OPTIONS,
        attribution: "&copy; OpenStreetMap contributors",
    }),
    Satelit: L.tileLayer(
        "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}",
        {
            ...TILE_OPTIONS,
            attribution: "Tiles &copy; Esri &mdash; Source: Esri, DigitalGlobe, GeoEye, Earthstar Geographics",
        },
    ),
};

function initMap() {
    const map = L.map("map", {
        center: CONFIG.mapCenter,
        zoom: CONFIG.defaultZoom,
        layers: [baseMaps["OSM Standard"]],
        zoomControl: false,
    });

    L.control.zoom({ position: "bottomright" }).addTo(map);
    L.control.scale({ imperial: false }).addTo(map);
    L.control.layers(baseMaps, null, { position: "bottomleft" }).addTo(map);

    return map;
}

/**
 * Alur muat halaman peta: loader, inisialisasi peta, `setup` khusus halaman (ambil data & pasang marker),
 * lalu kontrol bersama (filter, pencarian, popup, reset view).
 */
export function loadMapPage(setup, errorMessage) {
    document.addEventListener("DOMContentLoaded", async () => {
        showLoader();
        try {
            updateProgress(10, "Menginisialisasi peta...");
            const map = initMap();
            addResetViewControl(map);

            await setup(map);

            initPelangganFilter();
            bindPopupEvents(map);
            initSearch();

            updateProgress(100, "Semua data berhasil dimuat");
        } catch (err) {
            console.error(err);
            updateProgress(100, "Terjadi kesalahan saat memuat data");
            showSimpleErrorToast(errorMessage);
        } finally {
            hideLoader();
        }
    });
}
