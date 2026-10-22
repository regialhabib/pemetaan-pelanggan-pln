export function showLoader() {
    const loader = document.getElementById("loader");
    if (loader) {
        loader.style.display = "flex";
        updateProgress(0, "Memulai proses...");
    }
}

export function hideLoader() {
    const loader = document.getElementById("loader");
    if (loader) {
        loader.style.display = "none";
    }
}

export function updateProgress(percentage, message = null) {
    const progressBar = document.getElementById("progressBar");
    const progressText = document.getElementById("progressText");
    const loaderDetail = document.getElementById("loaderDetail");

    if (!progressBar || !progressText) return;

    const clamped = Math.min(100, Math.max(0, percentage));
    progressBar.style.width = `${clamped}%`;
    progressBar.setAttribute("aria-valuenow", clamped);
    progressText.textContent = `${Math.round(clamped)}%`;

    if (message && loaderDetail) {
        loaderDetail.textContent = message;
    }
}
