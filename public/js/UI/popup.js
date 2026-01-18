import { highlightMarker } from "../Map/markers.js";
import { startNavigation } from "../Map/Routing/routingController.js";

const popupHtml = `<div class="popup-pelanggan">
    <div class="fw-bold mb-1">piw</div>

    <div class="mb-2">
        <span class="badge bg-primary">23</span>
        <span class="badge bg-secondary">232 VA</span>
    </div>

    <div class="d-grid gap-1">
        <button
            class="btn btn-sm btn-outline-primary btn-navigate"
            data-id="i"
        >
            🧭 Lakukan Navigasi
        </button>

        <button class="btn btn-sm btn-outline-secondary btn-highlight">
            ✨ Highlight
        </button>
    </div>
</div>;
`;

export function renderPopupHTML(p) {
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

    <div class="address">
      📍 ${p.alamat}
    </div>
  </div>

  <div class="dark-card-footer">
    <button class="btn primary btn-navigate">  <span class="label">Rute Navigasi</span>
    <span class="spinner d-none spinner-border spinner-border-sm"></span></button>
  </div>
</div>
    `;
}

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
                    await startNavigation(map, marker);
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
