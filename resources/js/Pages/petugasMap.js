import { initMap } from "../MapCore/mapInit.js";
import { fetchKunjunganPelanggan, fetchKunjunganPelangganById } from "../Services/api.js";
import { showLoader, hideLoader, updateProgress } from "../UI/loader.js";
import { getRole } from "../Config/index.js";

import { showSimpleErrorToast } from "../UI/toast.js";
import { initPelangganFilter } from "../UI/filter.js";
import { initSearch } from "../UI/search.js";
import { bindPopupEvents } from "../UI/popup.js";
import { addMyLocationControl, addClearRouteControl } from "../UI/controls.js";
import { rencanakanRutePetugas } from "../MapCore/routeLayer.js";

let map;
let pelanggan;

async function loadMapPage() {
    try {
        showLoader();

        updateProgress(10, "Menginisialisasi peta...");
        map = initMap();

        updateProgress(30, "Mengambil data pelanggan...");
        if (getRole() === "petugas") {
             pelanggan = await fetchKunjunganPelanggan();
        } else {
            pelanggan = await fetchKunjunganPelangganById();
        }

        updateProgress(50, "Menyiapkan marker...");
        await rencanakanRutePetugas(map, pelanggan);
        initPelangganFilter();

        bindPopupEvents(map);

        updateProgress(100, "Semua data berhasil dimuat");

        initSearch(map);
        addMyLocationControl(map);
        addClearRouteControl(map);
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
