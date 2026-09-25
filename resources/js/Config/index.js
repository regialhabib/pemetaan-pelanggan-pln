export function getBaseUrl() {
  const meta = document.querySelector('meta[name="base-url"]');
  if (!meta) {
    throw new Error('Meta base-url tidak ditemukan');
  }
  return meta.getAttribute('content');
}

export function getRole() {
  const meta = document.querySelector('meta[name="role"]');
  if (!meta) {
    throw new Error('Meta role tidak ditemukan');
  }
  return meta.getAttribute('content');
}

export const CONFIG = {
    mapCenter: [-1.6161, 103.583], // Pusat Kota Jambi
    defaultZoom: 12, // Skala kota, meminimalisir transisi zoom-out yang kasar
    apiBase: `${getBaseUrl()}/api`
};

export const STATUS_KUNJUNGAN = {
    SUDAH: "sudah_dikunjungi",
    DIPROSES: "diproses",
    BELUM: "belum_dikunjungi"
};
