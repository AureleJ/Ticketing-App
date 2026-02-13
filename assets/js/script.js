const app = document.querySelector(".app-container");

function showToast(message, type = 'success') {
    const toast = `
    <div class="glass-panel toast ${type} text-sm" id="toast">
        ${message}
    </div>
    `
    app.insertAdjacentHTML("beforeend", toast);
    
    setTimeout(() => {
        document.getElementById("toast").remove();
    }, 3000);
}

function togglePopup(id) {
    var popup = document.getElementById(id);
    if (popup) {
        popup.classList.toggle("hidden");
    }
}