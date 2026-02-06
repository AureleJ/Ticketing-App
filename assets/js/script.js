const toast = document.querySelector("#toast");

function showToast(message) {
    if (!toast) return;
    toast.innerText = message;
    toast.classList.remove("hidden");
    setTimeout(() => {
        toast.classList.add("hidden");
    }, 3000);
}

function togglePopup(id) {
    var popup = document.getElementById(id);
    if (popup) {
        popup.classList.toggle("hidden");
    }
}

/* -- Project Form -- */

const projectForm = document.querySelector("#project-form");
const projectGrid = document.querySelector("#projects-grid");

if (projectForm) {
    projectForm.addEventListener("submit", function (event) {
        event.preventDefault();

        let infos = {
            name: document.querySelector("#nom-projet").value,
            client: document.querySelector("#client").value,
        };

        const card = `
            <a href="../projects/project-details.html" class="glass-panel project-card">
                <div>
                    <h3 class="font-bold mb-xs">${infos.name}</h3>
                    <div class="text-muted text-sm">${infos.client}</div>
                </div>

                <div class="mt-sm">
                    <div class="flex-between text-xs text-muted mb-sm">
                        <span class="text-warning">84%</span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-fill" style="width: 84%; background: var(--warning-color);"></div>
                    </div>
                </div>
            </a>
        `;

        if (projectGrid) projectGrid.insertAdjacentHTML("beforeend", card);
        projectForm.reset();
        togglePopup("project-popup");
        showToast("Projet ajouté avec succès !");
    });
}

/* -- Ticket Form -- */

const ticketForm = document.querySelector("#ticket-form");
const ticketTable = document.querySelector("#ticket-table");

if (ticketForm) {
    ticketForm.addEventListener("submit", function (event) {
        event.preventDefault();

        let infos = {
            subject: document.querySelector("#subject").value,
            id: document.querySelector("#project-id").value,
            client: document.querySelector("#client").value,
            priority: document.querySelector("#priority").value,
            status: "Urgent",
            description: document.querySelector("#description").value,
        };

        if (!infos.subject || !infos.id || !infos.client || !infos.priority || !infos.description) {
            showToast("Veuillez remplir tous les champs !");
            return;
        }

        const prioritiesText = {
            high: 'text-danger',
            medium: 'text-warning',
            low: '',
        };

        const priorities = {
            high: 'Haute',
            medium: 'Moyenne',
            low: 'Basse'
        };

        const row = `
            <tr onclick="window.location='../tickets/ticket-details.html'">
                <td class="font-mono text-muted">#${Math.floor(Math.random() * 9000) + 1000}</td>
                <td>
                    <div class="text-title">${infos.subject}</div>
                </td>
                <td>
                    <div class="flex-center-y gap-sm">
                        <div class="user-avatar small yellow">${infos.client.charAt(0)}</div>
                        <span class="text-sm">${infos.client}</span>
                    </div>
                </td>
                <td><span class="badge">Non défini</span></td>
                <td class="${prioritiesText[infos.priority]} font-bold text-sm">${priorities[infos.priority]}</td>
            </tr>
        `;

        if (ticketTable) ticketTable.querySelector("tbody").insertAdjacentHTML("beforeend", row);

        ticketForm.reset();
        togglePopup("ticket-popup");
        showToast("Ticket ajouté avec succès !");
    });
}