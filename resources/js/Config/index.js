function getMetaContent(name) {
    const meta = document.querySelector(`meta[name="${name}"]`);
    if (!meta) {
        throw new Error(`Meta ${name} tidak ditemukan`);
    }
    return meta.getAttribute("content");
}

export function getRole() {
    return getMetaContent("role");
}

export function getCsrfToken() {
    return getMetaContent("csrf-token");
}

const baseUrl = getMetaContent("base-url");

export const CONFIG = {
    mapCenter: [-1.6161, 103.583], // Pusat Kota Jambi
    defaultZoom: 12, // Skala kota, meminimalisir transisi zoom-out yang kasar
    baseUrl,
    apiBase: `${baseUrl}/api`,
};

export const COLORS = {
    primary: "#556ee6",
    success: "#34c38f",
    warning: "#f1b44c",
    danger: "#f46a6a",
};

export const STATUS_KUNJUNGAN = {
    SUDAH: "sudah_dikunjungi",
    DIPROSES: "diproses",
    BELUM: "belum_dikunjungi",
};

/**
 * Tampilan tiap status kunjungan: warna marker, ikon, label popup, dan label statistik.
 */
export const STATUS_META = {
    [STATUS_KUNJUNGAN.SUDAH]: {
        color: COLORS.success,
        icon: "bx-check-double",
        label: "Sudah Dikunjungi",
        shortLabel: "Selesai",
        badgeClass: "bg-success",
        textClass: "text-success",
    },
    [STATUS_KUNJUNGAN.DIPROSES]: {
        color: COLORS.warning,
        icon: "bx-time-five",
        label: "Sedang Diproses",
        shortLabel: "Diproses",
        badgeClass: "bg-warning text-dark",
        textClass: "text-warning",
    },
    [STATUS_KUNJUNGAN.BELUM]: {
        color: COLORS.danger,
        icon: "bx-x",
        label: "Belum Dikunjungi",
        shortLabel: "Belum",
        badgeClass: "bg-primary",
        textClass: "text-danger",
    },
};

export function getStatusMeta(status) {
    return STATUS_META[status] ?? STATUS_META[STATUS_KUNJUNGAN.BELUM];
}
