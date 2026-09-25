import { showMyLocation } from "../Services/locationService.js";
import { clearRoute } from "../MapCore/routeLayer.js";
import { resetMapView } from "../MapCore/markers.js";

function getGlassStyle(isCircle = true, isDanger = false) {
    if (isDanger) {
        return `
            border-radius: 30px; padding: 10px 20px; display: flex; align-items: center; gap: 8px; font-weight: 600;
            background-color: #f46a6a; color: white; border: none; box-shadow: 0 4px 10px rgba(244, 106, 106, 0.4);
            cursor: pointer; transition: all 0.2s ease; font-size: 14px;
        `;
    }
    
    return `
        width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
        background-color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        cursor: pointer; transition: all 0.2s ease;
    `;
}

function applyHoverEffects(btn, isDanger = false) {
    if (isDanger) {
        btn.onmouseover = function() { this.style.transform = "translateY(-2px)"; this.style.boxShadow = "0 6px 12px rgba(244, 106, 106, 0.5)"; };
        btn.onmouseout = function() { this.style.transform = "translateY(0)"; this.style.boxShadow = "0 4px 10px rgba(244, 106, 106, 0.4)"; };
    } else {
        btn.onmouseover = function() { this.style.transform = "scale(1.1)"; this.style.backgroundColor = "#ffffff"; };
        btn.onmouseout = function() { this.style.transform = "scale(1)"; this.style.backgroundColor = "rgba(255, 255, 255, 0.95)"; };
    }
}

export function addMyLocationControl(map) {
    const MyLocation = L.Control.extend({
        options: { position: "bottomright" },
        onAdd() {
            const btn = L.DomUtil.create("button", "");
            btn.style.cssText = getGlassStyle(true, false);
            applyHoverEffects(btn, false);
            btn.innerHTML = '<i class="bx bx-current-location font-size-20 text-primary"></i>';
            btn.title = "Deteksi Lokasi Saya";
            btn.onclick = (e) => {
                e.stopPropagation();
                showMyLocation(map);
            };
            return btn;
        },
    });
    map.addControl(new MyLocation());
}

export function addResetViewControl(map) {
    const ResetControl = L.Control.extend({
        options: { position: "bottomright" },
        onAdd() {
            const btn = L.DomUtil.create("button", "");
            btn.style.cssText = getGlassStyle(true, false);
            applyHoverEffects(btn, false);
            btn.innerHTML = '<i class="bx bx-target-lock font-size-20 text-dark"></i>';
            btn.title = "Pusatkan ke Seluruh Pelanggan";
            btn.onclick = (e) => {
                e.stopPropagation();
                resetMapView();
            };
            return btn;
        },
    });
    map.addControl(new ResetControl());
}

let clearControlContainer = null;

export function addClearRouteControl(map) {
    const ClearRouteControl = L.Control.extend({
        options: { position: "bottomright" },
        onAdd() {
            const btn = L.DomUtil.create("button", "d-none");
            btn.style.cssText = getGlassStyle(false, true);
            applyHoverEffects(btn, true);
            btn.innerHTML = '<i class="bx bx-x font-size-18"></i> Hapus Rute';
            btn.onclick = (e) => {
                e.stopPropagation();
                clearRoute(map);
                hideClearRouteButton();
            };
            clearControlContainer = btn;
            return btn;
        },
    });
    map.addControl(new ClearRouteControl());
}

export function showClearRouteButton() {
    if (clearControlContainer) clearControlContainer.classList.remove("d-none");
}

export function hideClearRouteButton() {
    if (clearControlContainer) clearControlContainer.classList.add("d-none");
}

export function addMonitoringStatsControl(map, pelanggan) {
    if (!pelanggan || pelanggan.length === 0) return;

    let total = pelanggan.length;
    let sudah = 0;
    let diproses = 0;
    let belum = 0;

    pelanggan.forEach(p => {
        if (p.status_kunjungan === "sudah") sudah++;
        else if (p.status_kunjungan === "diproses") diproses++;
        else belum++;
    });

    const StatsControl = L.Control.extend({
        options: { position: "topright" },
        onAdd() {
            const div = L.DomUtil.create("div", "card shadow-lg");
            div.style.fontFamily = "inherit";
            div.style.minWidth = "240px";
            div.style.border = "1px solid rgba(255,255,255,0.4)";
            div.style.borderRadius = "1rem";
            div.style.margin = "15px";
            div.style.backgroundColor = "rgba(255, 255, 255, 0.90)";
            div.style.backdropFilter = "blur(12px)";
            div.style.WebkitBackdropFilter = "blur(12px)";

            div.innerHTML = `
                <div class="card-body p-3">
                    <h6 class="text-uppercase fw-semibold mb-3 text-primary d-flex align-items-center font-size-13">
                        <i class="bx bx-pie-chart-alt font-size-16 me-2"></i> Statistik Kunjungan
                    </h6>
                    
                    <div class="d-flex justify-content-between align-items-end border-bottom pb-2 mb-3">
                        <span class="text-muted fw-medium font-size-13">Total Pelanggan</span>
                        <strong class="font-size-16 text-dark">${total}</strong>
                    </div>
                    
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-soft-success text-success rounded-circle p-1 me-2" style="background-color: rgba(52, 195, 143, 0.18);">
                                <i class="bx bx-check-double font-size-14"></i>
                            </span>
                            <span class="font-size-13 fw-medium">Selesai</span>
                        </div>
                        <strong class="text-success">${sudah}</strong>
                    </div>
                    
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-soft-warning text-warning rounded-circle p-1 me-2" style="background-color: rgba(241, 180, 76, 0.18);">
                                <i class="bx bx-time-five font-size-14"></i>
                            </span>
                            <span class="font-size-13 fw-medium">Diproses</span>
                        </div>
                        <strong class="text-warning">${diproses}</strong>
                    </div>
                    
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-soft-danger text-danger rounded-circle p-1 me-2" style="background-color: rgba(244, 106, 106, 0.18);">
                                <i class="bx bx-x font-size-14"></i>
                            </span>
                            <span class="font-size-13 fw-medium">Belum</span>
                        </div>
                        <strong class="text-danger">${belum}</strong>
                    </div>
                </div>
            `;
            return div;
        },
    });
    map.addControl(new StatsControl());
}
