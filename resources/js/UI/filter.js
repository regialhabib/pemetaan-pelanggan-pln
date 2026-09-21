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

    // Pre-fill from URL params (e.g., from Dashboard clicks)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('tarif') && tarifSelect) {
        tarifSelect.value = urlParams.get('tarif');
    }

    function applyFilter() {
        pelangganFilterState.tarif = tarifSelect.value || null;
        pelangganFilterState.minDaya = minDayaInput.value
            ? Number(minDayaInput.value)
            : null;
        pelangganFilterState.maxDaya = maxDayaInput.value
            ? Number(maxDayaInput.value)
            : null;
        filterPelanggan(pelangganFilterState);

    }

    function resetFilter() {
        tarifSelect.value = null;
        minDayaInput.value = null;
        maxDayaInput.value = null;
        applyFilter();
    }

    btnResetFilter?.addEventListener("click", resetFilter);
    tarifSelect?.addEventListener("change", applyFilter);
    minDayaInput?.addEventListener("input", applyFilter);
    maxDayaInput?.addEventListener("input", applyFilter);

    // Initial filter if URL params exist
    if (urlParams.has('tarif')) {
        applyFilter();
    }
}
