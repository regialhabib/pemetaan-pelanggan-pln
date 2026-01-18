// assets/js/data/pelanggan.service.js
import { CONFIG } from "../Config/mapConfig.js";

export async function fetchPelanggan() {
    const res = await fetch(`${CONFIG.apiBase}/pelanggans`);
    const json = await res.json();

    if (!json.success) {
        throw new Error(json.message || "Gagal mengambil data pelanggan");
    }

    return json.data;
}
