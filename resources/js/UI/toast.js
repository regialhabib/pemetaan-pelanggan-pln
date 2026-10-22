export function showSimpleErrorToast(message) {
    const toastEl = document.getElementById("liveToast");
    if (!toastEl) {
        console.warn("Toast element tidak ditemukan");
        return;
    }

    const titleEl = toastEl.querySelector(".toast-header strong");
    if (titleEl) titleEl.textContent = "Error";

    const bodyEl = toastEl.querySelector(".toast-body");
    if (bodyEl) bodyEl.textContent = message;

    const timeEl = toastEl.querySelector(".toast-header small");
    if (timeEl) {
        timeEl.textContent = new Date().toLocaleTimeString("id-ID", { hour: "2-digit", minute: "2-digit" });
    }

    toastEl.classList.remove("text-bg-success", "text-bg-warning", "text-bg-info");
    toastEl.classList.add("text-bg-danger");

    bootstrap.Toast.getOrCreateInstance(toastEl).show();
}
