import { filterPelanggan } from "../Map/markers.js";

const pelangganFilterState = {
    tarif: null,
    minDaya: null,
    maxDaya: null,
};

export function initPelangganFilter() {
    const tarifSelect = document.getElementById("filterTarif");
    const minDayaInput = document.getElementById("minDaya");
    const maxDayaInput = document.getElementById("maxDaya");

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

    tarifSelect?.addEventListener("change", applyFilter);
    minDayaInput?.addEventListener("input", applyFilter);
    maxDayaInput?.addEventListener("input", applyFilter);
}
