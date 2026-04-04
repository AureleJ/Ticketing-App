const templates = {
    tickets: [
        {
            id: "#1024",
            subject: "Bug affichage menu mobile",
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
            client: "Bio Store",
            progress: 45,
            status: "En cours"
        },
        {
            name: "Maintenance du site web",
            client: "Tech Consult",
            progress: 15,
            status: "En cours"
        }
    ],

    clients: [
        {
            entreprise: "Bio Store",
            mail: "contact@biostore.com",
            contact: "Patrick F"
        },
        {
            entreprise: "Tech Consult",
            mail: "contact@techconsult.com",
            contact: "Bibi J"
        }
    ]
};

const ticketTableBody = document.querySelector("#ticket-table tbody");
const projectTableBody = document.querySelector("#project-table tbody");
const projectGrid = document.querySelector("#projects-grid");
const clientsGrid = document.querySelector("#clients-grid");

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
        <tr onclick="window.location='ticket-details.html'" class="ticket-row" data-type="${type}" data-status="${status}">
            <td class="font-mono text-muted">${id}</td>
            <td><div class="text-title line-text">${subject}</div></td>
            <td>
                <div class="flex-center-y gap-sm">
                    <div class="user-avatar small yellow">${client.charAt(0).toUpperCase()}</div>
                    <span class="text-sm line-text">${client}</span>
                </div>
            </td>
            <td>
                <div class="flex-center-y gap-sm">
                    <div class="user-avatar small blue">${assigned.charAt(0).toUpperCase()}</div>
                    <span class="text-sm line-text">${assigned}</span>
                </div>
            </td>
            <td><span class="text-sm line-text">${date}</span></td>
            <td><span class="badge line-text">${status}</span></td>
            <td><span class="badge line-text ${typeClass}">${type}</span></td>
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
                <div class="text-title line-text">${name}</div>
            </td>
            <td>
                <div class="user-infos">
                    <div class="user-avatar yellow">${client.charAt(0).toUpperCase()}</div>
                    <div class="user-name line-text">${client}</div>
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

function createClientCard(entreprise, mail, contact) {
    return `
        <a class="glass-panel client-card" href="clients-details.html">
            <div class="client-header">
                <div class="user-avatar large yellow">${entreprise.charAt(0).toUpperCase()}</div>
                <div>
                    <h3 class="text-lg font-bold">${entreprise}</h3>
                </div>
                <div class="ml-auto"><span class="badge badge-active">Actif</span></div>
            </div>
            <div class="client-body">
                <div class="contact-row"><i class="ph-bold ph-user"></i> <span>${contact}</span></div>
                <div class="contact-row"><i class="ph-bold ph-envelope-simple"></i> <span>${mail}</span></div>
            </div>
            <div class="client-footer">
                <div class="client-stat"><span>2</span> Projets</div>
                <div class="client-stat"><span>15</span> Tickets</div>
                <div class="btn-icon"><i class="ph-bold ph-caret-right"></i></div>
            </div>
        </a>
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

    if (clientsGrid && templates.clients) {
        templates.clients.forEach(client => {
            const html = createClientCard(
                client.entreprise, client.mail, client.contact,
            );
            clientsGrid.insertAdjacentHTML("beforeend", html);
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

/* --- Formulaires --- */

// Formulaire Ticket
const ticketForm = document.querySelector("#ticket-form");
if (ticketForm) {
    ticketForm.addEventListener("submit", function (e) {
        e.preventDefault();

        const subject = document.querySelector("#subject").value;
        const client = document.querySelector("#client").value;
        const assigned = document.querySelector("#assigned").value;
        const priority = document.querySelector("#priority").value;
        const typeSelect = document.querySelector("#type").value;

        const typeLabel = typeSelect === 'facturable' ? 'Facturable' : 'Inclus';

        const newId = "#" + (Math.floor(Math.random() * 9000) + 1000);

        const newRow = createTicket(newId, subject, client, assigned, "À l'instant", "Non traité", typeLabel, priority);

        ticketTableBody.insertAdjacentHTML("afterbegin", newRow);

        ticketForm.reset();
        togglePopup("ticket-popup");
        showToast("Ticket créé avec succès !");
    });
}

// Formulaire Projet
const projectForm = document.querySelector("#project-form");
if (projectForm) {
    projectForm.addEventListener("submit", function (e) {
        e.preventDefault();

        const name = document.querySelector("#nom-projet").value;
        const client = document.querySelector("#nom-client").value;

        if (projectGrid)
            projectGrid.insertAdjacentHTML("beforeend", createProjectCard(name, client, 0));

        if (projectTableBody)
            projectTableBody.insertAdjacentHTML("beforeend", createProjectRow(name, client, 0));

        projectForm.reset();
        togglePopup("project-popup");
        showToast("Projet créé avec succès !");
    });
}

const editProjectForm = document.querySelector("#edit-project-form");
if (editProjectForm) {
editProjectForm.addEventListener("submit", function (e) {
        e.preventDefault();

        editProjectForm.reset();
        togglePopup("edit-project-popup");
        showToast("Projet modifié avec succès !");
    });
}

// Formulaire Client
const clientForm = document.querySelector("#client-form");
if (clientForm) {
    clientForm.addEventListener("submit", function (e) {
        e.preventDefault();

        const entreprise = document.querySelector("#entreprise").value;
        const mail = document.querySelector("#mail").value;
        const contact = document.querySelector("#contact").value;

        const newCard = createClientCard(entreprise, mail, contact);

        clientsGrid.insertAdjacentHTML("beforeend", newCard);

        clientForm.reset();
        togglePopup("client-popup");
        showToast("Client créé avec succès !");
    });
}

/* --- Filtres et recherche --- */

const searchInput = document.querySelector('.search-wrapper input');
if (searchInput) {
    searchInput.addEventListener('input', (e) => {
        filterTickets();
    });
}

const filterType = document.querySelector('#filter-type');
if (filterType) {
    filterType.addEventListener('change', () => {
        filterTickets();
    });
}

function filterTickets() {
    const searchValue = searchInput ? searchInput.value.toLowerCase() : '';
    const typeValue = filterType ? filterType.value : 'Tout'; // 'Tout', 'Facturable', 'Inclus'

    const rows = document.querySelectorAll('#ticket-table tbody tr');

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        const rowType = row.getAttribute('data-type'); 

        const matchesSearch = text.includes(searchValue);
        const matchesType = (typeValue === 'Tout') || (rowType === typeValue);

        if (matchesSearch && matchesType) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}