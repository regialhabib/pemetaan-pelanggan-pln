import { filterPelanggan } from "../MapCore/markers.js";

const pelangganFilterState = {
    tarif: null,
    minDaya: null,
    maxDaya: null,
};

export function initPelangganFilter() {
    const tarifSelect = document.getElementById("filterTarif");
    const minDayaInput = document.getElementById("minDaya");
    const maxDayaInput = document.getElementById("maxDaya");
    const btnResetFilter = document.getElementById("resetFilter");
    const activeBadge = document.getElementById("activeFilterBadge");
    const countDisplay = document.getElementById("filteredCustomerCount");
    const dayaDisplay = document.getElementById("filteredTotalDaya");
    const tarifPills = document.querySelectorAll(".btn-tarif-pill");
    const dayaPresets = document.querySelectorAll(".btn-daya-preset");

    // Listen to live stats update from markers.js
    window.addEventListener("pelangganFilterUpdated", (e) => {
        const { total, filtered, totalDaya } = e.detail;
        if (countDisplay) {
            countDisplay.textContent = `${filtered} / ${total}`;
        }
        if (dayaDisplay) {
            const kva = (totalDaya / 1000).toLocaleString("id-ID", {
                maximumFractionDigits: 1,
            });
            dayaDisplay.textContent = `${kva} kVA`;
        }
    });

    function updateActiveBadge() {
        let activeCount = 0;
        if (pelangganFilterState.tarif) activeCount++;
        if (pelangganFilterState.minDaya !== null || pelangganFilterState.maxDaya !== null) activeCount++;

        if (activeBadge) {
            if (activeCount > 0) {
                activeBadge.textContent = activeCount;
                activeBadge.classList.remove("d-none");
            } else {
                activeBadge.classList.add("d-none");
            }
        }
    }

    function applyFilter() {
        pelangganFilterState.tarif = tarifSelect?.value || null;
        pelangganFilterState.minDaya = minDayaInput?.value
            ? Number(minDayaInput.value)
            : null;
        pelangganFilterState.maxDaya = maxDayaInput?.value
            ? Number(maxDayaInput.value)
            : null;

        filterPelanggan(pelangganFilterState);
        updateActiveBadge();
    }

    function syncTarifPills(selectedTarif) {
        tarifPills.forEach((btn) => {
            if (btn.getAttribute("data-tarif") === (selectedTarif || "")) {
                btn.classList.add("active");
            } else {
                btn.classList.remove("active");
            }
        });
    }

    // Pill Klik Handler
    tarifPills.forEach((btn) => {
        btn.addEventListener("click", () => {
            const val = btn.getAttribute("data-tarif");
            if (tarifSelect) {
                tarifSelect.value = val;
            }
            syncTarifPills(val);
            applyFilter();
        });
    });

    // Preset Daya Klik Handler
    dayaPresets.forEach((preset) => {
        preset.addEventListener("click", () => {
            const min = preset.getAttribute("data-min");
            const max = preset.getAttribute("data-max");

            const isAlreadyActive = preset.classList.contains("active");

            dayaPresets.forEach((p) => p.classList.remove("active"));

            if (isAlreadyActive) {
                // Toggle off
                if (minDayaInput) minDayaInput.value = "";
                if (maxDayaInput) maxDayaInput.value = "";
            } else {
                preset.classList.add("active");
                if (minDayaInput) minDayaInput.value = min;
                if (maxDayaInput) maxDayaInput.value = max === "1000000" ? "" : max;
            }
            applyFilter();
        });
    });

    function resetFilter() {
        if (tarifSelect) tarifSelect.value = "";
        if (minDayaInput) minDayaInput.value = "";
        if (maxDayaInput) maxDayaInput.value = "";

        syncTarifPills("");
        dayaPresets.forEach((p) => p.classList.remove("active"));

        applyFilter();
    }

    btnResetFilter?.addEventListener("click", resetFilter);
    tarifSelect?.addEventListener("change", () => {
        syncTarifPills(tarifSelect.value);
        applyFilter();
    });

    minDayaInput?.addEventListener("input", () => {
        dayaPresets.forEach((p) => p.classList.remove("active"));
        applyFilter();
    });

    maxDayaInput?.addEventListener("input", () => {
        dayaPresets.forEach((p) => p.classList.remove("active"));
        applyFilter();
    });

    // Pre-fill from URL params (e.g., from Dashboard clicks)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has("tarif") && tarifSelect) {
        const urlTarif = urlParams.get("tarif");
        tarifSelect.value = urlTarif;
        syncTarifPills(urlTarif);
        applyFilter();
    }
}
