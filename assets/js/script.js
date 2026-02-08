const templates = {
    tickets: [
        {
            id: "#1024",
            subject: "BUG 1",
            client: "Bio Store",
            assigned: "Aurele Joblet",
            date: "Il y a 2h",
            status: "Non traité",
            priority: "high",
            type: "Inclus",
            description: "Description du ticket"
        },
        {
            id: "#1025",
            subject: "BUG 2",
            client: "Bio Store",
            assigned: "Aurele Joblet",
            date: "Il y a 5h",
            status: "Non traité",
            priority: "medium",
            type: "Facturable",
            description: "Description du ticket"
        },
        {
            id: "#1026",
            subject: "BUG 3",
            client: "Tech Consult",
            assigned: "Jean Dev",
            date: "Il y a 5j",
            status: "Non traité",
            priority: "low",
            type: "Inclus",
            description: "Description du ticket"
        }
    ],

    projects: [
        {
            name: "Création d'une application mobile",
            client: "BioStore SAS",
            progress: 45,
            status: "En cours"
        },
        {
            name: "Maintenance du site web",
            client: "Tech Consult",
            progress: 15,
            status: "En cours"
        }
    ]
};

const toast = document.querySelector("#toast");
const ticketTableBody = document.querySelector("#ticket-table tbody");
const projectTableBody = document.querySelector("#project-table tbody");
const projectGrid = document.querySelector("#projects-grid");

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

function createTicket(id, subject, client, assigned, date, status, type, priority) {
    const typeClass = type === 'Facturable' ? 'badge-urgent' : '';

    const prioritiesText = {
        high: 'text-danger',
        medium: 'text-warning',
        low: '',
    };

    const prioritiesLabels = {
        high: 'Haute',
        medium: 'Moyenne',
        low: 'Basse'
    };

    return `
        <tr onclick="window.location='ticket-details.html'">
            <td class="font-mono text-muted">${id}</td>
            <td><div class="text-title">${subject}</div></td>
            <td>
                <div class="flex-center-y gap-sm">
                    <div class="user-avatar small yellow">${client[0]}</div>
                    <span class="text-sm">${client}</span>
                </div>
            </td>
            <td>
                <div class="flex-center-y gap-sm">
                    <div class="user-avatar small blue">${assigned[0]}</div>
                    <span class="text-sm">${assigned}</span>
                </div>
            </td>
            <td><span class="text-sm">${date}</span></td>
            <td><span class="badge">${status}</span></td>
            <td><span class="badge ${typeClass}">${type}</span></td>
            <td class="${prioritiesText[priority]} font-bold text-sm">${prioritiesLabels[priority]}</td>
            <td class="text-right"><i class="ph-bold ph-caret-right text-muted"></i></td>
        </tr>
    `;
}

function createProjectCard(name, client, progress) {
    return `
        <a href="project-details.html" class="glass-panel project-card">
            <div>
                <h3 class="font-bold mb-xs">${name}</h3>
                <div class="text-muted text-sm">${client}</div>
            </div>
            <div class="mt-sm">
                <div class="flex-between text-xs text-muted mb-sm">
                    <span class="text-warning">${progress}%</span>
                </div>
                <div class="progress-track">
                    <div class="progress-fill" style="width: ${progress}%; background: var(--warning-color);"></div>
                </div>
            </div>
        </a>
    `;
}

function createProjectRow(name, client, progress) {
    return `
        <tr onclick="window.location='project-details.html'">
            <td>
                <div class="text-title">${name}</div>
            </td>
            <td>
                <div class="user-infos">
                    <div class="user-avatar yellow">${client[0]}</div>
                    <div class="user-name">${client}</div>
                </div>
            </td>
            <td>
                <div class="mt-auto">
                    <div class="flex-between text-xs text-muted mb-sm">
                        <span class="text-warning">${progress}%</span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-fill" style="width: ${progress}%; background: var(--warning-color);"></div>
                    </div>
                </div>
            </td>
            <td class="text-right"><i class="ph-bold ph-caret-right text-muted"></i></td>
        </tr>
    `;
}

document.addEventListener("DOMContentLoaded", () => {
    if (ticketTableBody && templates.tickets) {
        templates.tickets.forEach(ticket => {
            const html = createTicket(
                ticket.id, ticket.subject, ticket.client,
                ticket.assigned, ticket.date, ticket.status,
                ticket.type, ticket.priority
            );

            ticketTableBody.insertAdjacentHTML("beforeend", html);
        });
    }

    if (projectGrid && templates.projects) {
        templates.projects.forEach(project => {
            const html = createProjectCard(
                project.name, project.client, project.progress,
            );

            projectGrid.insertAdjacentHTML("beforeend", html);
        });
    }

    if (projectTableBody && templates.projects) {
        templates.projects.forEach(project => {
            const html = createProjectRow(
                project.name, project.client, project.progress,
            );

            projectTableBody.insertAdjacentHTML("beforeend", html);
        });
    }
});

/* --- Forms Ticket --- */

const ticketForm = document.querySelector("#ticket-form");
if (ticketForm) {
    ticketForm.addEventListener("submit", function (e) {
        e.preventDefault();

        const subject = document.querySelector("#subject").value;
        const client = document.querySelector("#client").value;
        const assigned = document.querySelector("#assigned").value;
        const priority = document.querySelector("#priority").value;
        const typeSelect = document.querySelector("#type").value;

        const typeLabel = typeSelect === 'factureable' ? 'Facturable' : 'Inclus';

        const newId = "#" + (Math.floor(Math.random() * 9000) + 1000);

        const newRow = createTicket(newId, subject, client, assigned, "À l'instant", "Non traité", typeLabel, priority);

        ticketTableBody.insertAdjacentHTML("afterbegin", newRow);

        ticketForm.reset();
        togglePopup("ticket-popup");
        showToast("Ticket créé avec succès !");
    });
}

/* --- Bar de recherche --- */

const searchInput = document.querySelector('.search-wrapper input');
if (searchInput) {
    searchInput.addEventListener('input', (e) => {
        const value = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#ticket-table tbody tr');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(value) ? '' : 'none';
        });
    });
}