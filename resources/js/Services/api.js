import { CONFIG, getCsrfToken } from "../Config/index.js";

async function fetchJson(url, errorMessage, options = {}) {
    const res = await fetch(url, {
        ...options,
        headers: { Accept: "application/json", ...options.headers },
    });
    const json = await res.json().catch(() => ({}));
    if (!res.ok || !json.success) {
        throw new Error(json.message || errorMessage);
    }
    return json.data;
}

export function fetchPelanggan() {
    return fetchJson(`${CONFIG.apiBase}/pelanggans`, "Gagal mengambil data pelanggan");
}

/**
 * Tanpa id: tugas terbaru milik petugas yang login. Dengan id: tugas tertentu (monitoring admin).
 */
export function fetchKunjunganPelanggan(idTugas = null) {
    const url = `${CONFIG.baseUrl}/tugas-kunjungan/pelanggans${idTugas ? `/${idTugas}` : ""}`;
    return fetchJson(url, "Gagal mengambil data kunjungan pelanggan");
}

export function updateStatusKunjungan(idDetail) {
    return fetchJson(`${CONFIG.apiBase}/kunjungan/update-status/${idDetail}`, "Gagal mengubah status kunjungan", {
        method: "POST",
        headers: { "X-CSRF-TOKEN": getCsrfToken() },
    });
}
