function showSimpleErrorToast(message) {
    // 1. Ambil element toast
    const toastEl = document.getElementById('liveToast');
    if (!toastEl) {
        console.warn('Toast element tidak ditemukan');
        return;
    }
    
    // 2. Update title dan message
    const titleEl = toastEl.querySelector('.toast-header strong');
    if (titleEl) {
        titleEl.textContent = 'Error';
    }
    
    const bodyEl = toastEl.querySelector('.toast-body');
    if (bodyEl) {
        bodyEl.textContent = message; // "Gagal memuat data"
    }
    
    // 3. Update timestamp
    const timeEl = toastEl.querySelector('.toast-header small');
    if (timeEl) {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        timeEl.textContent = timeString;
    }
    
    // 4. Update style jadi merah (error)
    toastEl.classList.remove('text-bg-success', 'text-bg-warning', 'text-bg-info');
    toastEl.classList.add('text-bg-danger');
    
    // 5. Tampilkan toast
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}

export { showSimpleErrorToast };