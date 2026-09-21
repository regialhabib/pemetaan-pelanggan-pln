import { highlightMarker } from "../MapCore/markers.js";
import { startRouting } from "../MapCore/routeLayer.js";
import { getRole, getBaseUrl, STATUS_KUNJUNGAN } from "../Config/index.js";

let role = getRole();
let hidden = role !== "petugas" ? "d-none" : "";

export function renderPopupHTML(p) {
    let info = "";
    let status = p.status_kunjungan === STATUS_KUNJUNGAN.SUDAH ? "d-none" : "";
    return `
            <div class="dark-card">
    <div class="dark-card-header">
        <h3>${p.nama}</h3>
    
    </div>

    <div class="dark-card-body">
        <div class="info-row">
        <span class="label">Golongan Tarif</span>
        <span class="value">${p.golongan_tarif}</span>
        </div>

        <div class="info-row">
        <span class="label">Daya</span>
        <span class="value">${p.daya} VA</span>
        </div>
        ${info}

        <div class="address">
        📍 ${p.alamat}
        </div>
    

    </div>

    <div class="dark-card-footer ${hidden} gap-3">
        <button class="btn primary btn-navigate mb-2">  <span class="label">Rute</span>
        <span class="spinner d-none spinner-border spinner-border-sm"></span></button>
        <button class="btn btn-success ${status}" onclick="handleSelesaiKunjungan(${p.id_detail_tugas}, this)">
      <span class="label">Update Status</span>
      <span class="spinner d-none spinner-border spinner-border-sm"></span>
    </button>
    </div>
    </div>
        `;
}
import { updateStatusKunjungan } from "../Services/api.js";

// Kita tempelkan fungsi ke window agar bisa diakses oleh onclick inline
window.handleSelesaiKunjungan = async function (idDetail, btnElement) {
    const result = await Swal.fire({
        title: "Konfirmasi",
        text: "Pastikan Anda sudah berada di lokasi pelanggan",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#198754",
        cancelButtonText: "Batal",
        confirmButtonText: "Ya, Selesai",
    });

    if (result.isConfirmed) {
        const label = btnElement.querySelector(".label");
        const spinner = btnElement.querySelector(".spinner");
        label.classList.add("d-none");
        spinner.classList.remove("d-none");
        btnElement.disabled = true;

        try {
            await updateStatusKunjungan(idDetail);
            Swal.fire("Berhasil!", "Status kunjungan berhasil diubah.", "success")
                .then(() => location.reload());
        } catch (error) {
            Swal.fire("Error!", error.message, "error");
            label.classList.remove("d-none");
            spinner.classList.add("d-none");
            btnElement.disabled = false;
        }
    }
};
function setRoutingLoading(btn, loading) {
    btn.querySelector(".label").style.display = loading ? "none" : "inline";
    btn.querySelector(".spinner").classList.toggle("d-none", !loading);
    btn.disabled = loading;
}

export function bindPopupEvents(map) {
    map.on("popupopen", (e) => {
        const popupEl = e.popup.getElement();
        if (!popupEl) return;

        const marker = e.popup._source;
        if (!marker) return;

        // tombol navigasi
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

        // tombol highlight saja
        const btnHighlight = popupEl.querySelector(".btn-highlight");
        if (btnHighlight) {
            btnHighlight.addEventListener("click", () => {
                highlightMarker(marker);
            });
        }
    });
}
