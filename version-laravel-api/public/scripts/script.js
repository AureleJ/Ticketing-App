/**
 * Affiche un toast de notification.
 * @param {string} title - Le titre du toast
 * @param {string} message - Le message à afficher
 * @param {string} [type="success"] - Le type de toast: success, error, info ou warning
 */
function showToast(title, message, type = "success") {
    const app = document.querySelector(".app-container");
    let toastList = document.querySelector(".toast-list");
    
    if (toastList === null) {
        const newToastList = document.createElement("div");
        newToastList.classList.add("toast-list");
        
        if (app) {
            app.appendChild(newToastList);
            toastList = newToastList;
        } else {
            console.error("No app container found");
            return;
        }
    }

    const icon = {
        success: "ph-check",
        error: "ph-x",
        warning: "ph-warning",
        info: "ph-info",
    }[type] || "ph-check";

    const toast = `
        <div class="toast ${type} text-sm" id="toast">
            <div class="content">
                <div class="toast-header">
                    <i class="ph-bold ${icon} toast-icon"></i>
                    <h3 class="title">${title}</h3>
                </div>
                <p class="message">${message}</p>
            </div>
        </div>
    `;

    toastList.insertAdjacentHTML("afterbegin", toast);

    setTimeout(() => {
        const element = document.getElementById("toast");
        if (element) {
            element.classList.add("fade-out");
            setTimeout(() => {
                if (element.parentNode) {
                    element.remove();
                }
            }, 400);
        }
    }, 3000);
}

function togglePopup(id) {
    const popup = document.getElementById(id);
    if (popup) popup.classList.toggle("hidden");
}

// --- Tickets ---

async function addTicket(formData) {
    const ticketForm = document.getElementById("ticket-form");
    
    const response = await fetch("/api/tickets", {
        method: "POST",
        headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": ticketForm.querySelector('input[name="_token"]').value,
        },
        body: JSON.stringify(formData),
    });

    const json = await response.json();

    if (response.ok) {
        addTicketToTable(json.ticket);
        ticketForm.reset();
        togglePopup("ticket-popup");
        showToast("Ticket #" + json.ticket.id + " créé", json.message);
    } else {
        showToast("Erreur", json.message, "error");
    }
}

async function editTicket(ticketId, formData) {
    const ticketForm = document.getElementById("ticket-form");
    
    const response = await fetch("/api/tickets/" + ticketId, {
        method: "PUT",
        headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": ticketForm.querySelector('input[name="_token"]').value,
        },
        body: JSON.stringify(formData),
    });

    const json = await response.json();

    if (response.ok) {
        console.log(json.ticket);
        modifyTicketInTable(json.ticket);
        togglePopup("ticket-popup");
        showToast("Ticket #" + json.ticket.id + " modifié", json.message);
    } else {
        showToast("Erreur", json.message, "error");
    }
}

function getTicketFormData() {
    const ticketForm = document.getElementById("ticket-form");
    
    return {
        title: ticketForm.querySelector('input[name="title"]').value,
        project_id: ticketForm.querySelector('select[name="project_id"]').value,
        assigned_id: ticketForm.querySelector('select[name="assigned_id"]').value,
        priority: ticketForm.querySelector('select[name="priority"]').value,
        type: ticketForm.querySelector('select[name="type"]').value,
        description: ticketForm.querySelector('textarea[name="description"]').value,
        status: (ticketForm.getAttribute("data-mode") === "create") ? "open" : ticketForm.querySelector('select[name="status"]').value,
    };
}

function addTicketToTable(ticket) {
    const ticketTable = document.getElementById("ticket-table");
    if (!ticketTable) return;
    
    const tbody = ticketTable.querySelector("tbody");
    const date = new Date(ticket.created_at).toLocaleDateString("fr-FR");

    const row = document.createElement("tr");
    row.className = "ticket-row";
    row.setAttribute("onclick", "window.location='/tickets/" + ticket.id + "'");

    row.innerHTML = `
        <td class="font-mono text-muted">#${ticket.id}</td>
        <td><div class="text-title line-text">${ticket.title}</div></td>
        <td>
            <div class="flex-center-y gap-sm">
                <div class="user-avatar small ${ticket.client.avatar_color}">${ticket.client.initials}</div>
                <span class="text-sm line-text">${ticket.client.company}</span>
            </div>
        </td>
        <td>
            <div class="flex-center-y gap-sm">
                <div class="user-avatar small ${ticket.assignee.avatar_color}">${ticket.assignee.initials}</div>
                <span class="text-sm line-text">${ticket.assignee.full_name}</span>
            </div>
        </td>
        <td><span class="text-sm line-text">${date}</span></td>
        <td><span class="badge line-text ${ticket.status_class}">${ticket.status_label}</span></td>
        <td><span class="badge line-text ${ticket.type_class}">${ticket.type_label}</span></td>
        <td class="font-bold text-sm ${ticket.priority_class}">${ticket.priority_label}</td>
        <td class="text-right"><i class="ph-bold ph-caret-right text-muted"></i></td>`;

    tbody.insertAdjacentElement("afterbegin", row);
}

function modifyTicketInTable(ticket) {
    const statusBadge = document.getElementById("status-badge");
    const labelBadge = document.getElementById("label-badge");
    const descriptionP = document.getElementById("description");
    const clientAvatar = document.getElementById("client-avatar");
    const clientCompany = document.getElementById("client-company");
    const ticketTitle = document.getElementById("title");
    const prioritySpan = document.getElementById("priority");
    const assignedAvatar = document.getElementById("assigned-avatar");
    const assignedName = document.getElementById("assigned-name");
    const editDate = document.getElementById("date");

    if (editDate) {
        const date = new Date(ticket.updated_at).toLocaleDateString("fr-FR", {
            day: "2-digit",
            month: "2-digit",
            year: "2-digit",
            hour: "2-digit",
            minute: "2-digit",
        });
        editDate.textContent = "Modifié le " + date;
    }

    if (statusBadge) {
        statusBadge.textContent = ticket.status_label;
        statusBadge.className = "badge " + ticket.status_class;
    }
    if (labelBadge) {
        labelBadge.textContent = ticket.type_label;
        labelBadge.className = "badge " + ticket.type_class;
    }
    if (descriptionP) {
        descriptionP.textContent = ticket.description;
    }
    if (clientAvatar) {
        clientAvatar.textContent = ticket.client.initials;
        clientAvatar.className = "user-avatar small " + ticket.client.avatar_color;
    }
    if (clientCompany) {
        clientCompany.textContent = ticket.client.company;
    }
    if (ticketTitle) {
        ticketTitle.textContent = ticket.title;
    }
    if (prioritySpan) {
        prioritySpan.textContent = ticket.priority_label;
        prioritySpan.className = "font-bold text-sm " + ticket.priority_class;
    }
    if (assignedAvatar) {
        assignedAvatar.textContent = ticket.assignee.initials;
        assignedAvatar.className = "user-avatar small " + ticket.assignee.avatar_color;
    }
    if (assignedName) {
        assignedName.textContent = ticket.assignee.full_name;
    }
}

function initTicketForm() {
    const ticketForm = document.getElementById("ticket-form");
    if (!ticketForm) return;

    ticketForm.addEventListener("submit", async function (e) {
        e.preventDefault();

        const mode = ticketForm.getAttribute("data-mode");
        const formData = getTicketFormData();
        console.log(formData);

        if (mode === "create") {
            await addTicket(formData);
        } else if (mode === "edit") {
            const ticketId = ticketForm.getAttribute("data-ticket-id");
            await editTicket(ticketId, formData);
        }
    });
}

// --- Projets ---

async function addProject(formData) {
    const projectForm = document.getElementById("project-form");
    
    const response = await fetch("/api/projects", {
        method: "POST",
        headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": projectForm.querySelector('input[name="_token"]').value,
        },
        body: JSON.stringify(formData),
    });

    const json = await response.json();

    if (response.ok) {
        const projectTable = document.getElementById("project-table");
        const projectsGrid = document.getElementById("projects-grid");

        if (projectTable) addProjectToTable(json.project);
        if (projectsGrid) addProjectToGrid(json.project);

        projectForm.reset();
        togglePopup("project-popup");
        showToast("Projet #" + json.project.id + " créé", json.message);
    } else {
        showToast("Erreur", json.message, "error");
    }
}

function getProjectFormData() {
    const projectForm = document.getElementById("project-form");
    
    const teamCheckboxes = projectForm.querySelectorAll('input[name="team[]"]:checked');
    const team = Array.from(teamCheckboxes).map(cb => cb.value);

    return {
        name: projectForm.querySelector('input[name="name"]').value,
        description: projectForm.querySelector('textarea[name="description"]').value,
        client_id: projectForm.querySelector('select[name="client_id"]').value,
        owner_id: projectForm.querySelector('select[name="owner_id"]').value,
        status: projectForm.querySelector('select[name="status"]').value,
        progress: projectForm.querySelector('input[name="progress"]').value,
        budget_h: projectForm.querySelector('input[name="budget_h"]').value,
        total_h: projectForm.querySelector('input[name="total_h"]').value,
        team: team,
    };
}

function addProjectToGrid(project) {
    const projectsGrid = document.getElementById("projects-grid");
    if (!projectsGrid) return;

    const card = document.createElement("a");
    card.href = project.show_url;
    card.className = "glass-panel project-card";

    card.innerHTML = `
        <div>
            <div class="card-header">
                <div class="badge ${project.status_class}">${project.status_label}</div>
            </div>
            <h3 class="project-title">${project.name}</h3>
            <div class="user-infos mb-xs">
                <div class="user-avatar small ${project.client.avatar_color}">${project.client.initials}</div>
                <span class="text-sm text-muted">${project.client.company}</span>
            </div>
        </div>
        <div>
            <div class="flex-between text-xs text-muted mb-xs">
                <span>Progression</span>
                <span style="font-weight:600;">${project.progress}%</span>
            </div>
            <div class="progress-track">
                <div class="progress-fill" style="width:${project.progress}%; background:${project.progress_color};"></div>
            </div>
            <div class="card-footer">
                <div class="user-infos">
                    <div class="user-avatar small ${project.owner.avatar_color}">${project.owner.initials}</div>
                    <span class="text-xs text-muted">Resp: ${project.owner.full_name}</span>
                </div>
                <div class="text-xs text-muted flex-center-y gap-xs">
                    <i class="ph ph-calendar-blank"></i> ${project.created_at}
                </div>
            </div>
        </div>`;

    projectsGrid.insertAdjacentElement("afterbegin", card);
}

function addProjectToTable(project) {
    const projectTable = document.getElementById("project-table");
    if (!projectTable) return;

    const tbody = projectTable.querySelector("tbody");
    const row = document.createElement("tr");
    row.setAttribute("onclick", `window.location='${project.show_url}'`);

    row.innerHTML = `
        <td><div class="text-title line-text">${project.name}</div></td>
        <td>
            <div class="user-infos">
                <div class="user-avatar small ${project.client.avatar_color}">${project.client.initials}</div>
                <div class="user-name line-text">${project.client.name}</div>
            </div>
        </td>
        <td>
            <div class="mt-auto">
                <div class="flex-between text-xs text-muted mb-sm">
                    <span>${project.progress}%</span>
                </div>
                <div class="progress-track">
                    <div class="progress-fill" style="width:${project.progress}%; background:${project.progress_color};"></div>
                </div>
            </div>
        </td>
        <td class="text-right"><i class="ph-bold ph-caret-right text-muted"></i></td>`;

    tbody.insertAdjacentElement("afterbegin", row);
}

function initProjectForm() {
    const projectForm = document.getElementById("project-form");
    if (!projectForm) return;

    projectForm.addEventListener("submit", async function (e) {
        e.preventDefault();
        const formData = getProjectFormData();
        await addProject(formData);
    });
}

// --- Clients ---
async function addClient(formData) {
    const clientForm = document.getElementById("client-form");
    
    const response = await fetch("/api/clients", {
        method: "POST",
        headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": clientForm.querySelector('input[name="_token"]').value,
        },
        body: JSON.stringify(formData),
    });

    const json = await response.json();

    if (response.ok) {
        addClientToGrid(json.client);
        clientForm.reset();
        togglePopup("client-popup");
        showToast("Client #" + json.client.id + " créé", json.message);
    } else {
        showToast("Erreur", json.message, "error");
    }
}

function getClientFormData() {
    const clientForm = document.getElementById("client-form");
    
    return {
        company: clientForm.querySelector('input[name="company"]').value,
        name: clientForm.querySelector('input[name="name"]').value,
        email: clientForm.querySelector('input[name="email"]').value,
        phone: clientForm.querySelector('input[name="phone"]').value,
        status: clientForm.querySelector('select[name="status"]').value,
    };
}

function addClientToGrid(client) {
    const clientsGrid = document.getElementById("clients-grid");
    if (!clientsGrid) return;

    const card = document.createElement("a");
    card.href = client.show_url;
    card.className = "glass-panel client-card";

    card.innerHTML = `
        <div class="client-header">
            <div class="user-avatar large ${client.avatar_color}">${client.initials}</div>
            <div>
                <h3 class="text-lg font-bold">${client.company}</h3>
            </div>
            <div class="ml-auto">
                <span class="badge ${client.status_class}">${client.status_label}</span>
            </div>
        </div>
        <div class="client-body">
            <div class="contact-row"><i class="ph-bold ph-user"></i> <span>${client.name}</span></div>
            <div class="contact-row"><i class="ph-bold ph-envelope-simple"></i> <span>${client.email}</span></div>
        </div>
        <div class="client-footer">
            <div class="client-stat"><span>0</span> Projets</div>
            <div class="client-stat"><span>0</span> Tickets</div>
            <div class="btn-icon"><i class="ph-bold ph-caret-right"></i></div>
        </div>`;

    clientsGrid.insertAdjacentElement("afterbegin", card);
}

function initClientForm() {
    const clientForm = document.getElementById("client-form");
    if (!clientForm) return;

    clientForm.addEventListener("submit", async function (e) {
        e.preventDefault();
        const formData = getClientFormData();
        await addClient(formData);
    });
}

function initTicketFilters() {
    const filterSearch = document.getElementById("filter-search");
    const filterStatus = document.getElementById("filter-status");
    const filterType = document.getElementById("filter-type");
    const filterPriority = document.getElementById("filter-priority");
    const filterAssigned = document.getElementById("filter-assigned");

    if (filterSearch) filterSearch.addEventListener("input", filterTickets);
    if (filterStatus) filterStatus.addEventListener("change", filterTickets);
    if (filterType) filterType.addEventListener("change", filterTickets);
    if (filterPriority) filterPriority.addEventListener("change", filterTickets);
    if (filterAssigned) filterAssigned.addEventListener("change", filterTickets);
}

// Init
document.addEventListener("DOMContentLoaded", function () {
    initTicketForm();
    initProjectForm();
    initClientForm();
    initTicketFilters();
});

// --- Search et Filters ---
function filterTickets() {
    const searchTerm = document.getElementById("filter-search")?.value.toLowerCase() || "";
    const statusFilter = document.getElementById("filter-status")?.value || "";
    const typeFilter = document.getElementById("filter-type")?.value || "";
    const priorityFilter = document.getElementById("filter-priority")?.value || "";
    const assignedFilter = document.getElementById("filter-assigned")?.value || "";

    const ticketTable = document.getElementById("ticket-table");
    if (!ticketTable) return;

    const rows = ticketTable.querySelectorAll("tbody tr");
    let visibleCount = 0;

    rows.forEach(row => {
        const title = row.querySelector("td:nth-child(2)")?.textContent.toLowerCase() || "";
        const statusBadge = row.querySelector("td:nth-child(6)")?.textContent.trim() || "";
        const typeBadge = row.querySelector("td:nth-child(7)")?.textContent.trim() || "";
        console.log(typeBadge);
        const priorityText = row.querySelector("td:nth-child(8)")?.textContent.trim() || "";
        const assignedName = row.querySelector("td:nth-child(4)")?.textContent.trim() || "";

        let matches = true;

        if (searchTerm && !title.includes(searchTerm)) {
            matches = false;
        }

        if (statusFilter) {
            const statusMapping = {
                "open": "Ouvert",
                "in_progress": "En cours",
                "pending": "En attente",
                "closed": "Terminé"
            };
            const expectedStatus = statusMapping[statusFilter];
            if (statusBadge !== expectedStatus) {
                matches = false;
            }
        }

        if (typeFilter) {
            const typeMapping = {
                "non_facturable": "non facturable",
                "facturable": "facturable"
            };
            const expectedLabel = typeMapping[typeFilter];
            if (typeBadge.toLowerCase() !== expectedLabel.toLowerCase()) {
                matches = false;
            }
        }

        if (priorityFilter) {
            const priorityMapping = {
                "low": "Basse",
                "medium": "Moyenne",
                "high": "Haute"
            };
            const expectedPriority = priorityMapping[priorityFilter];
            if (priorityText !== expectedPriority) {
                matches = false;
            }
        }

        if (assignedFilter && !assignedName.includes(assignedFilter)) {
            matches = false;
        }

        row.style.display = matches ? "" : "none";
        if (matches) visibleCount++;
    });

    let emptyRow = ticketTable.querySelector("tbody tr[data-empty-message]");
    if (visibleCount === 0 && !emptyRow) {
        const tbody = ticketTable.querySelector("tbody");
        const row = document.createElement("tr");
        row.setAttribute("data-empty-message", "true");
        row.innerHTML = '<td colspan="9" class="text-center text-muted p-md">Aucun ticket ne correspond aux filtres.</td>';
        tbody.appendChild(row);
    } else if (visibleCount > 0 && emptyRow) {
        emptyRow.remove();
    }
}

function clearFilters() {
    const filterSearch = document.getElementById("filter-search");
    const filterStatus = document.getElementById("filter-status");
    const filterType = document.getElementById("filter-type");
    const filterPriority = document.getElementById("filter-priority");
    const filterAssigned = document.getElementById("filter-assigned");

    if (filterSearch) filterSearch.value = "";
    if (filterStatus) filterStatus.value = "";
    if (filterType) filterType.value = "";
    if (filterPriority) filterPriority.value = "";
    if (filterAssigned) filterAssigned.value = "";
    
    filterTickets();
}