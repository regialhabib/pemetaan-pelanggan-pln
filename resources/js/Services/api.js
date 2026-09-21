import { CONFIG } from "../Config/index.js";

async function fetchJson(url, errorMessage, options = {}) {
    const res = await fetch(url, options);
    const json = await res.json();
    if (!json.success) {
        throw new Error(json.message || errorMessage);
    }
    return json.data || json; // Some APIs might not return data object on success
}

export function fetchPelanggan() {
    return fetchJson(`${CONFIG.apiBase}/pelanggans`, "Gagal mengambil data pelanggan");
}

export function fetchKunjunganPelanggan() {
    return fetchJson(`/tugas-kunjungan/pelanggans`, "Gagal mengambil data kunjungan pelanggan");
}

export function fetchKunjunganPelangganById() {
    const id = window.location.pathname.split("/").pop();
    return fetchJson(`/tugas-kunjungan/pelanggans/${id}`, "Gagal mengambil data kunjungan pelanggan");
}

export function updateStatusKunjungan(idDetail) {
    return fetchJson(`/api/kunjungan/update-status/${idDetail}`, "Gagal mengubah status kunjungan", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
        }
    });
}
