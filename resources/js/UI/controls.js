import { showMyLocation } from "../Services/locationService.js";
import { clearRoute } from "../MapCore/routeLayer.js";
import { resetMapView } from "../MapCore/markers.js";
import { STATUS_KUNJUNGAN, getStatusMeta } from "../Config/index.js";

/**
 * Buat kontrol tombol Leaflet; styling ada di `.map-ctrl-btn` (public/css/custom.css).
 */
function addButtonControl(map, { className = "", html, title = "", onClick }) {
    const ButtonControl = L.Control.extend({
        options: { position: "bottomright" },
        onAdd() {
            const btn = L.DomUtil.create("button", `map-ctrl-btn ${className}`);
            btn.type = "button";
            btn.innerHTML = html;
            btn.title = title;
            L.DomEvent.disableClickPropagation(btn);
            L.DomEvent.on(btn, "click", onClick);
            return btn;
        },
    });

    const control = new ButtonControl();
    map.addControl(control);

    return control.getContainer();
}

export function addMyLocationControl(map) {
    addButtonControl(map, {
        html: '<i class="bx bx-current-location font-size-20 text-primary"></i>',
        title: "Deteksi Lokasi Saya",
        onClick: () => showMyLocation(map),
    });
}

export function addResetViewControl(map) {
    addButtonControl(map, {
        html: '<i class="bx bx-target-lock font-size-20 text-dark"></i>',
        title: "Pusatkan ke Seluruh Pelanggan",
        onClick: () => resetMapView(),
    });
}

/**
 * Tombol "Hapus Rute" hanya tampil selama ada rute di peta.
 */
export function addClearRouteControl(map) {
    const btn = addButtonControl(map, {
        className: "map-ctrl-btn-danger d-none",
        html: '<i class="bx bx-x font-size-18"></i> Hapus Rute',
        onClick: () => clearRoute(map),
    });

    map.on("route:shown", () => btn.classList.remove("d-none"));
    map.on("route:cleared", () => btn.classList.add("d-none"));
}

const STATS_ROWS = [
    { status: STATUS_KUNJUNGAN.SUDAH, soft: "success" },
    { status: STATUS_KUNJUNGAN.DIPROSES, soft: "warning" },
    { status: STATUS_KUNJUNGAN.BELUM, soft: "danger" },
];

export function addMonitoringStatsControl(map, pelanggan) {
    if (!pelanggan.length) return;

    const counts = Object.fromEntries(STATS_ROWS.map(({ status }) => [status, 0]));
    pelanggan.forEach((p) => {
        const status = p.status_kunjungan in counts ? p.status_kunjungan : STATUS_KUNJUNGAN.BELUM;
        counts[status]++;
    });

    const rowsHtml = STATS_ROWS.map(({ status, soft }, i) => {
        const { icon, shortLabel, textClass } = getStatusMeta(status);
        const spacing = i < STATS_ROWS.length - 1 ? "mb-2" : "";

        return `
            <div class="d-flex align-items-center justify-content-between ${spacing}">
                <div class="d-flex align-items-center">
                    <span class="badge map-stats-badge-${soft} ${textClass} rounded-circle p-1 me-2">
                        <i class="bx ${icon} font-size-14"></i>
                    </span>
                    <span class="font-size-13 fw-medium">${shortLabel}</span>
                </div>
                <strong class="${textClass}">${counts[status]}</strong>
            </div>
        `;
    }).join("");

    const StatsControl = L.Control.extend({
        options: { position: "topright" },
        onAdd() {
            const div = L.DomUtil.create("div", "card shadow-lg map-stats-card");
            div.innerHTML = `
                <div class="card-body p-3">
                    <h6 class="text-uppercase fw-semibold mb-3 text-primary d-flex align-items-center font-size-13">
                        <i class="bx bx-pie-chart-alt font-size-16 me-2"></i> Statistik Kunjungan
                    </h6>

                    <div class="d-flex justify-content-between align-items-end border-bottom pb-2 mb-3">
                        <span class="text-muted fw-medium font-size-13">Total Pelanggan</span>
                        <strong class="font-size-16 text-dark">${pelanggan.length}</strong>
                    </div>

                    ${rowsHtml}
                </div>
            `;
            return div;
        },
    });

    map.addControl(new StatsControl());
}
