import { filterPelanggan, getPelangganStats } from "../MapCore/markers.js";

const NO_MAX_DAYA = "1000000";

/**
 * Panel filter tarif & daya beserta ringkasan jumlah pelanggan / total daya yang tampil.
 */
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

    function renderStats({ total, filtered, totalDaya }) {
        if (countDisplay) {
            countDisplay.textContent = `${filtered} / ${total}`;
        }
        if (dayaDisplay) {
            const kva = (totalDaya / 1000).toLocaleString("id-ID", { maximumFractionDigits: 1 });
            dayaDisplay.textContent = `${kva} kVA`;
        }
    }

    function readFilterState() {
        return {
            tarif: tarifSelect?.value || null,
            minDaya: minDayaInput?.value ? Number(minDayaInput.value) : null,
            maxDaya: maxDayaInput?.value ? Number(maxDayaInput.value) : null,
        };
    }

    function updateActiveBadge({ tarif, minDaya, maxDaya }) {
        if (!activeBadge) return;

        const activeCount = (tarif ? 1 : 0) + (minDaya !== null || maxDaya !== null ? 1 : 0);
        activeBadge.textContent = activeCount;
        activeBadge.classList.toggle("d-none", activeCount === 0);
    }

    function applyFilter() {
        const state = readFilterState();
        const stats = filterPelanggan(state);
        if (stats) renderStats(stats);
        updateActiveBadge(state);
    }

    function setTarif(tarif) {
        if (tarifSelect) tarifSelect.value = tarif;
        tarifPills.forEach((btn) => btn.classList.toggle("active", btn.dataset.tarif === tarif));
    }

    function setDayaRange(min, max) {
        if (minDayaInput) minDayaInput.value = min;
        if (maxDayaInput) maxDayaInput.value = max === NO_MAX_DAYA ? "" : max;
    }

    function clearDayaPresets() {
        dayaPresets.forEach((preset) => preset.classList.remove("active"));
    }

    tarifPills.forEach((btn) => {
        btn.addEventListener("click", () => {
            setTarif(btn.dataset.tarif);
            applyFilter();
        });
    });

    dayaPresets.forEach((preset) => {
        preset.addEventListener("click", () => {
            const isAlreadyActive = preset.classList.contains("active");
            clearDayaPresets();

            if (isAlreadyActive) {
                setDayaRange("", "");
            } else {
                preset.classList.add("active");
                setDayaRange(preset.dataset.min, preset.dataset.max);
            }
            applyFilter();
        });
    });

    btnResetFilter?.addEventListener("click", () => {
        setTarif("");
        setDayaRange("", "");
        clearDayaPresets();
        applyFilter();
    });

    tarifSelect?.addEventListener("change", () => {
        setTarif(tarifSelect.value);
        applyFilter();
    });

    [minDayaInput, maxDayaInput].forEach((input) => {
        input?.addEventListener("input", () => {
            clearDayaPresets();
            applyFilter();
        });
    });

    renderStats(getPelangganStats());

    // Pre-fill dari URL (mis. klik dari Dashboard)
    const urlTarif = new URLSearchParams(window.location.search).get("tarif");
    if (urlTarif && tarifSelect) {
        setTarif(urlTarif);
        applyFilter();
    }
}
