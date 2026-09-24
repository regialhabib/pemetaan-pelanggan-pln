import '../MapCore/map-vendor.js';
import { initMap } from "../MapCore/mapInit.js";
import { fetchKunjunganPelanggan, fetchKunjunganPelangganById } from "../Services/api.js";
import { showLoader, hideLoader, updateProgress } from "../UI/loader.js";
import { getRole } from "../Config/index.js";

import { showSimpleErrorToast } from "../UI/toast.js";
import { initPelangganFilter } from "../UI/filter.js";
import { initSearch } from "../UI/search.js";
import { bindPopupEvents } from "../UI/popup.js";
import { addMonitoringStatsControl, addMyLocationControl, addResetViewControl, addClearRouteControl } from "../UI/controls.js";
import { rencanakanRutePetugas } from "../MapCore/routeLayer.js";
import { addMarkersBatch } from "../MapCore/markers.js";

let map;
let pelanggan;

async function loadMapPage() {
    try {
        showLoader();

        updateProgress(10, "Menginisialisasi peta...");
        map = initMap();

        const role = getRole();
        updateProgress(30, "Mengambil data pelanggan...");
        if (role === "petugas") {
             pelanggan = await fetchKunjunganPelanggan();
        } else {
            pelanggan = await fetchKunjunganPelangganById();
        }

        updateProgress(50, "Menyiapkan marker...");
        if (role === "petugas") {
            await rencanakanRutePetugas(map, pelanggan);
        } else {
            addMarkersBatch(map, pelanggan);
            // Auto fit bounds for admin monitoring
            const group = L.featureGroup(pelanggan.map(p => L.marker([p.latitude, p.longitude])));
            if (group.getLayers().length > 0) {
                map.fitBounds(group.getBounds(), { padding: [50, 50] });
            }
            // Tambahkan kotak statistik khusus Admin
            addMonitoringStatsControl(map, pelanggan);
        }
        initPelangganFilter();

        bindPopupEvents(map);

        updateProgress(100, "Semua data berhasil dimuat");

        initSearch(map);
        addResetViewControl(map);

        // Petugas-only controls
        if (role === "petugas") {
            addMyLocationControl(map);
            addClearRouteControl(map);
        }
    } catch (err) {
        console.error(err);
        updateProgress(100, "Terjadi kesalahan saat memuat data");
        showSimpleErrorToast("Belum ada data tugas kunjungan");
    } finally {
        hideLoader();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    loadMapPage();
});

export { map };
