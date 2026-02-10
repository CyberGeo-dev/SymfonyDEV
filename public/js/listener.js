document.addEventListener("DOMContentLoaded", () => {
    const toastEl = document.querySelector(".toast");
    if (toastEl) {
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    }
});
