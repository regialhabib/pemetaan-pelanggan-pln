const HTML_ESCAPES = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#39;",
};

/**
 * Escape teks sebelum disisipkan ke template HTML (data pelanggan berasal dari impor Excel).
 */
export function escapeHtml(value) {
    return String(value ?? "").replace(/[&<>"']/g, (char) => HTML_ESCAPES[char]);
}

/**
 * Tampilkan spinner pada tombol yang berisi `.label` dan `.spinner`.
 */
export function setButtonLoading(btn, loading) {
    btn.querySelector(".label")?.classList.toggle("d-none", loading);
    btn.querySelector(".spinner")?.classList.toggle("d-none", !loading);
    btn.disabled = loading;
}
