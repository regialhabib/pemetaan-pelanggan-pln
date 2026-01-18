// ===== assets/js/UI/loader.js =====
// Fungsi untuk menampilkan loader
function showLoader() {
    const loader = document.getElementById('loader');
    if (loader) {
        loader.style.display = 'flex';
        resetProgress();
    }
}

// Fungsi untuk menyembunyikan loader
function hideLoader() {
    const loader = document.getElementById('loader');
    if (loader) {
        loader.style.display = 'none';
    }
}

// Fungsi untuk reset progress
function resetProgress() {
    updateProgress(0, "Memulai proses...");
}

// Fungsi untuk update progress
function updateProgress(percentage, message = null) {
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const loaderDetail = document.getElementById('loaderDetail');
    
    if (progressBar && progressText) {
        // Update persentase
        percentage = Math.min(100, Math.max(0, percentage)); // Clamp 0-100
        progressBar.style.width = `${percentage}%`;
        progressBar.setAttribute('aria-valuenow', percentage);
        progressText.textContent = `${Math.round(percentage)}%`;
        
        // Update pesan detail
        if (message && loaderDetail) {
            loaderDetail.textContent = message;
        }
    }
}

// Fungsi untuk update counter
function updateCounter(current, total) {
    const currentCount = document.getElementById('currentCount');
    const totalCount = document.getElementById('totalCount');
    
    if (currentCount && totalCount) {
        currentCount.textContent = current;
        totalCount.textContent = total;
    }
}

export { showLoader, hideLoader, updateProgress, updateCounter };