import '../MapCore/map-vendor.js';
import { initMap } from "../MapCore/mapInit.js";
import { fetchPelanggan } from "../Services/api.js";
import { addMarkersBatch } from "../MapCore/markers.js";
import { showLoader, hideLoader, updateProgress } from "../UI/loader.js";

import { showSimpleErrorToast } from "../UI/toast.js";
import { initPelangganFilter } from "../UI/filter.js";
import { initSearch } from "../UI/search.js";
import { bindPopupEvents } from "../UI/popup.js";
import { addResetViewControl } from "../UI/controls.js";
let map;

async function loadMapPage() {
    try {
        showLoader();

        updateProgress(10, "Menginisialisasi peta...");
        map = initMap();

        updateProgress(30, "Mengambil data pelanggan...");
        const pelanggan = await fetchPelanggan();

        updateProgress(50, "Menyiapkan marker...");

        addMarkersBatch(map, pelanggan);
        initPelangganFilter();
        bindPopupEvents(map);

        updateProgress(100, "Semua data berhasil dimuat");
        
        initSearch(map);
        addResetViewControl(map);
    } catch (err) {
        console.error(err);
        updateProgress(100, "Terjadi kesalahan saat memuat data");
        showSimpleErrorToast("Gagal memuat data");
    } finally {
        hideLoader();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    loadMapPage();
});

export { map };
