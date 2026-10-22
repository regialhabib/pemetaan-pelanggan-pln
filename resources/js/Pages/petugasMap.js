import "../MapCore/map-vendor.js";
import { loadMapPage } from "../MapCore/mapInit.js";
import { fetchKunjunganPelanggan } from "../Services/api.js";
import { addMarkersBatch, resetMapView } from "../MapCore/markers.js";
import { rencanakanRutePetugas } from "../MapCore/routeLayer.js";
import { getRole } from "../Config/index.js";
import { updateProgress } from "../UI/loader.js";
import { showSimpleErrorToast } from "../UI/toast.js";
import { addMonitoringStatsControl, addMyLocationControl, addClearRouteControl } from "../UI/controls.js";

loadMapPage(async (map) => {
    const isPetugas = getRole() === "petugas";

    updateProgress(30, "Mengambil data pelanggan...");
    // Petugas: tugas terbaru miliknya. Admin: tugas dari URL /tugas-kunjungan/map/{id}.
    const pelanggan = await fetchKunjunganPelanggan(isPetugas ? null : window.location.pathname.split("/").pop());
    if (!pelanggan.length) {
        throw new Error("Tidak ada tugas kunjungan");
    }

    updateProgress(50, "Menyiapkan marker...");
    addMarkersBatch(map, pelanggan, { cluster: !isPetugas });
    resetMapView(false);

    if (!isPetugas) {
        addMonitoringStatsControl(map, pelanggan);
        return;
    }

    addMyLocationControl(map);
    addClearRouteControl(map);

    // Rute dihitung di latar belakang (menunggu GPS) agar marker langsung tampil.
    rencanakanRutePetugas(map).catch((err) => {
        console.error("Gagal merencanakan rute", err);
        showSimpleErrorToast("Gagal merencanakan rute. Pastikan GPS aktif dan izin lokasi diberikan.");
    });
}, "Belum ada data tugas kunjungan");
