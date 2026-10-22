import { startRouting } from "../MapCore/routeLayer.js";
import { getRole, getStatusMeta, STATUS_KUNJUNGAN } from "../Config/index.js";
import { updateStatusKunjungan } from "../Services/api.js";
import { escapeHtml, setButtonLoading } from "./html.js";
import { showSimpleErrorToast } from "./toast.js";

export function renderPopupHTML(p) {
    const isPetugas = getRole() === "petugas";

    const phoneHtml = p.no_hp
        ? `<div class="popup-phone"><span><i class="bx bx-phone me-1"></i> ${escapeHtml(p.no_hp)}</span></div>`
        : "";

    let statusPill = "";
    if (p.status_kunjungan) {
        const { badgeClass, label } = getStatusMeta(p.status_kunjungan);
        statusPill = `<span class="badge ${badgeClass} font-size-11 mb-2">${label}</span>`;
    }

    const routeButtonHtml = isPetugas
        ? `
            <button type="button" class="btn primary btn-navigate d-flex align-items-center justify-content-center gap-1">
                <i class="bx bx-directions font-size-16"></i>
                <span class="label">Rute ke Sini</span>
                <span class="spinner d-none spinner-border spinner-border-sm"></span>
            </button>
        `
        : "";

    const canUpdateStatus = isPetugas && p.id_detail_tugas && p.status_kunjungan !== STATUS_KUNJUNGAN.SUDAH;
    const updateStatusHtml = canUpdateStatus
        ? `
            <button type="button" class="btn btn-success w-100 btn-update-status" data-id-detail="${escapeHtml(p.id_detail_tugas)}">
                <i class="bx bx-check-double me-1"></i>
                <span class="label">Update Status Kunjungan</span>
                <span class="spinner d-none spinner-border spinner-border-sm"></span>
            </button>
        `
        : "";

    return `
        <div class="dark-card">
            <div class="dark-card-header">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="idpel-tag">
                        <i class="bx bx-barcode"></i> ${escapeHtml(p.id_pelanggan || "PLN")}
                    </span>
                    <button type="button" class="btn-copy-idpel" data-idpel="${escapeHtml(p.id_pelanggan)}" title="Salin IDPEL">
                        <i class="bx bx-copy"></i>
                    </button>
                </div>
                <h4 class="customer-name">${escapeHtml(p.nama)}</h4>
            </div>

            <div class="dark-card-body">
                ${statusPill}

                <div class="popup-pill-row">
                    <span class="popup-pill tarif">
                        <i class="bx bx-tag-alt"></i> ${escapeHtml(p.golongan_tarif)}
                    </span>
                    <span class="popup-pill daya">
                        <i class="bx bx-bolt-circle"></i> ${escapeHtml(p.daya)} VA
                    </span>
                </div>

                ${phoneHtml}

                <div class="address">
                    <i class="bx bx-map-pin text-danger font-size-15 flex-shrink-0 mt-1"></i>
                    <span>${escapeHtml(p.alamat)}</span>
                </div>
            </div>

            <div class="dark-card-footer">
                ${routeButtonHtml}
                ${updateStatusHtml}
            </div>
        </div>
    `;
}

async function copyIdpel(btn) {
    if (!navigator.clipboard) return;

    await navigator.clipboard.writeText(btn.dataset.idpel);

    const icon = btn.querySelector("i");
    if (icon) {
        icon.className = "bx bx-check text-success";
        setTimeout(() => {
            icon.className = "bx bx-copy";
        }, 1500);
    }
}

async function navigateTo(map, btn, latlng) {
    setButtonLoading(btn, true);
    try {
        await startRouting(map, latlng);
    } catch (err) {
        console.error("Routing dibatalkan:", err);
        showSimpleErrorToast("Gagal membuat rute. Pastikan GPS aktif dan izin lokasi diberikan.");
    } finally {
        setButtonLoading(btn, false);
    }
}

async function confirmUpdateStatus(btn) {
    const result = await Swal.fire({
        title: "Perbarui Status Kunjungan?",
        text: "Pastikan Anda sudah tiba atau menyelesaikan inspeksi di lokasi pelanggan.",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#34c38f",
        cancelButtonColor: "#f46a6a",
        cancelButtonText: "Batal",
        confirmButtonText: "Ya, Lanjutkan",
    });

    if (!result.isConfirmed) return;

    setButtonLoading(btn, true);
    try {
        await updateStatusKunjungan(btn.dataset.idDetail);
        await Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: "Status kunjungan berhasil diperbarui.",
            timer: 1500,
            showConfirmButton: false,
        });
        location.reload();
    } catch (error) {
        Swal.fire("Gagal", error.message, "error");
        setButtonLoading(btn, false);
    }
}

/**
 * Hubungkan tombol-tombol di dalam popup (salin IDPEL, rute, update status) setiap popup dibuka.
 */
export function bindPopupEvents(map) {
    map.on("popupopen", (e) => {
        const popupEl = e.popup.getElement();
        const latlng = e.popup.getLatLng();
        if (!popupEl) return;

        popupEl.querySelector(".btn-copy-idpel")?.addEventListener("click", (event) => copyIdpel(event.currentTarget));
        popupEl.querySelector(".btn-navigate")?.addEventListener("click", (event) => navigateTo(map, event.currentTarget, latlng));
        popupEl.querySelector(".btn-update-status")?.addEventListener("click", (event) => confirmUpdateStatus(event.currentTarget));
    });
}
