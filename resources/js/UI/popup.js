import { highlightMarker } from "../MapCore/markers.js";
import { startRouting } from "../MapCore/routeLayer.js";
import { getRole, STATUS_KUNJUNGAN } from "../Config/index.js";
import { updateStatusKunjungan } from "../Services/api.js";

// Helper copy IDPEL
window.copyIdpel = function (idpel, btn) {
    if (!navigator.clipboard) return;
    navigator.clipboard.writeText(idpel).then(() => {
        const icon = btn.querySelector("i");
        if (icon) {
            icon.className = "bx bx-check text-success";
            setTimeout(() => {
                icon.className = "bx bx-copy";
            }, 1500);
        }
    });
};

export function renderPopupHTML(p) {
    const role = getRole();
    const isPetugas = role === "petugas";

    // Format phone / WhatsApp
    let phoneHtml = "";
    if (p.no_hp) {
        phoneHtml = `
            <div class="popup-phone">
                <span><i class="bx bx-phone me-1"></i> ${p.no_hp}</span>
            </div>
        `;
    }

    // Status Badge if available (in Tugas Kunjungan view)
    let statusPill = "";
    if (p.status_kunjungan) {
        let badgeColor = "bg-primary";
        let statusLabel = "Belum Dikunjungi";

        if (p.status_kunjungan === STATUS_KUNJUNGAN.SUDAH) {
            badgeColor = "bg-success";
            statusLabel = "Sudah Dikunjungi";
        } else if (p.status_kunjungan === STATUS_KUNJUNGAN.DIPROSES) {
            badgeColor = "bg-warning text-dark";
            statusLabel = "Sedang Diproses";
        }

        statusPill = `<span class="badge ${badgeColor} font-size-11 mb-2">${statusLabel}</span>`;
    }

    // Petugas update button
    let petugasActionHtml = "";
    if (isPetugas && p.id_detail_tugas) {
        const isCompleted = p.status_kunjungan === STATUS_KUNJUNGAN.SUDAH;
        if (!isCompleted) {
            petugasActionHtml = `
                <button class="btn btn-success w-100" onclick="handleSelesaiKunjungan(${p.id_detail_tugas}, this)">
                    <i class="bx bx-check-double me-1"></i>
                    <span class="label">Update Status Kunjungan</span>
                    <span class="spinner d-none spinner-border spinner-border-sm"></span>
                </button>
            `;
        }
    }

    return `
        <div class="dark-card">
            <div class="dark-card-header">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="idpel-tag">
                        <i class="bx bx-barcode"></i> ${p.id_pelanggan || "PLN"}
                    </span>
                    <button type="button" class="btn-copy-idpel" onclick="copyIdpel('${p.id_pelanggan || ""}', this)" title="Salin IDPEL">
                        <i class="bx bx-copy"></i>
                    </button>
                </div>
                <h4 class="customer-name">${p.nama}</h4>
            </div>

            <div class="dark-card-body">
                ${statusPill}

                <div class="popup-pill-row">
                    <span class="popup-pill tarif">
                        <i class="bx bx-tag-alt"></i> ${p.golongan_tarif}
                    </span>
                    <span class="popup-pill daya">
                        <i class="bx bx-bolt-circle"></i> ${p.daya} VA
                    </span>
                </div>

                ${phoneHtml}

                <div class="address">
                    <i class="bx bx-map-pin text-danger font-size-15 flex-shrink-0 mt-1"></i>
                    <span>${p.alamat}</span>
                </div>
            </div>

            <div class="dark-card-footer">
                ${isPetugas ? `
                <button class="btn primary btn-navigate d-flex align-items-center justify-content-center gap-1">
                    <i class="bx bx-directions font-size-16"></i>
                    <span class="label">Rute ke Sini</span>
                    <span class="spinner d-none spinner-border spinner-border-sm"></span>
                </button>
                ` : ''}
                ${petugasActionHtml}
            </div>
        </div>
    `;
}

// Handler inline konfirmasi selesai kunjungan untuk petugas
window.handleSelesaiKunjungan = async function (idDetail, btnElement) {
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

    if (result.isConfirmed) {
        const label = btnElement.querySelector(".label");
        const spinner = btnElement.querySelector(".spinner");
        label?.classList.add("d-none");
        spinner?.classList.remove("d-none");
        btnElement.disabled = true;

        try {
            await updateStatusKunjungan(idDetail);
            Swal.fire({
                icon: "success",
                title: "Berhasil!",
                text: "Status kunjungan berhasil diperbarui.",
                timer: 1500,
                showConfirmButton: false,
            }).then(() => location.reload());
        } catch (error) {
            Swal.fire("Gagal", error.message, "error");
            label?.classList.remove("d-none");
            spinner?.classList.add("d-none");
            btnElement.disabled = false;
        }
    }
};

function setRoutingLoading(btn, loading) {
    const label = btn.querySelector(".label");
    const spinner = btn.querySelector(".spinner");
    if (label) label.style.display = loading ? "none" : "inline";
    if (spinner) spinner.classList.toggle("d-none", !loading);
    btn.disabled = loading;
}

export function bindPopupEvents(map) {
    map.on("popupopen", (e) => {
        const popupEl = e.popup.getElement();
        if (!popupEl) return;

        const marker = e.popup._source;
        if (!marker) return;

        // Tombol navigasi rute
        const btnNav = popupEl.querySelector(".btn-navigate");
        if (btnNav) {
            btnNav.onclick = async () => {
                setRoutingLoading(btnNav, true);
                try {
                    await startRouting(map, marker.getLatLng());
                } finally {
                    setRoutingLoading(btnNav, false);
                }
            };
        }

        // Tombol highlight jika ada
        const btnHighlight = popupEl.querySelector(".btn-highlight");
        if (btnHighlight) {
            btnHighlight.addEventListener("click", () => {
                highlightMarker(marker);
            });
        }
    });
}
