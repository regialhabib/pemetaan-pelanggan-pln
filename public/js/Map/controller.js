// assets/js/map/controller.js
import { initMap } from "./init.js";
import { fetchPelanggan } from "../Data/pelanggan.js";
import { addMarkersBatch } from "./markers.js";
import { showLoader, hideLoader, updateProgress } from "../UI/loader.js";
import { delay } from "../Helpers/async.js";
import { showSimpleErrorToast } from "../UI/toast.js";
import { initPelangganFilter } from "../UI/filter.js";
import { initSearch } from "../UI/search.js";
import { bindPopupEvents } from "../UI/popup.js";
import { initRouting } from "./routing.js";
import { addMyLocationControl } from "../UI/showLocation.js";
import { addClearRouteControl } from "./Routing/routingController.js";
let map;

export async function loadMapPage() {
    try {
        showLoader();

        updateProgress(10, "Menginisialisasi peta...");
        map = initMap();

        updateProgress(30, "Mengambil data pelanggan...");
        const pelanggan = await fetchPelanggan();

        updateProgress(50, "Menyiapkan marker...");
        await delay(200);

        await addMarkersBatch(map, pelanggan);
        initPelangganFilter();
        bindPopupEvents(map);

        updateProgress(100, "Semua data berhasil dimuat");
        await delay(400);
        initRouting(map);
        initSearch();
        addMyLocationControl(map);
        addClearRouteControl(map);
    } catch (err) {
        console.error(err);
        updateProgress(100, "Terjadi kesalahan saat memuat data");
        showSimpleErrorToast("Gagal memuat data");
    } finally {
        hideLoader();
    }
}

export { map };
