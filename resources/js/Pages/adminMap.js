import "../MapCore/map-vendor.js";
import { loadMapPage } from "../MapCore/mapInit.js";
import { fetchPelanggan } from "../Services/api.js";
import { addMarkersBatch, resetMapView } from "../MapCore/markers.js";
import { updateProgress } from "../UI/loader.js";

loadMapPage(async (map) => {
    updateProgress(30, "Mengambil data pelanggan...");
    const pelanggan = await fetchPelanggan();

    updateProgress(50, "Menyiapkan marker...");
    addMarkersBatch(map, pelanggan);
    resetMapView(false);
}, "Gagal memuat data");
