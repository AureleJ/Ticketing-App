const app = document.querySelector(".app-container");

function showToast(message, type = "success") {
    const toast = `
    <div class="glass-panel toast ${type} text-sm" id="toast">
        ${message}
    </div>
    `;
    app.insertAdjacentHTML("beforeend", toast);

    setTimeout(() => {
        document.getElementById("toast").remove();
    }, 3000);
}

function togglePopup(id) {
    const popup = document.getElementById(id);
    if (popup) popup.classList.toggle("hidden");
}

document.addEventListener("DOMContentLoaded", function () {
    const ticketForm = document.getElementById("ticket-form");
    const ticketTable = document.getElementById("ticket-table");

    if (!ticketForm || !ticketTable) {
        return;
    }

    const tbody = ticketTable.querySelector("tbody");

    ticketForm.addEventListener("submit", async function (e) {
        e.preventDefault();

        const title = ticketForm.querySelector('input[name="title"]').value;
        const projectId = ticketForm.querySelector('select[name="project_id"]').value;
        const assignedId = ticketForm.querySelector('select[name="assigned_id"]').value;
        const priority = ticketForm.querySelector('select[name="priority"]').value;
        const type = ticketForm.querySelector('select[name="type"]').value;
        const description = ticketForm.querySelector('textarea[name="description"]').value;

        const response = await fetch("/tickets", {
            method: "POST",
            headers: {
                "Accept": "application/json",
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": ticketForm.querySelector('input[name="_token"]').value,
            },
            body: JSON.stringify({
                title: title,
                project_id: projectId,
                assigned_id: assignedId,
                priority: priority,
                type: type,
                description: description,
            }),
        });

        const json = await response.json();

        if (response.ok) {
            addTicketToTable(json.ticket);
            ticketForm.reset();
            togglePopup("ticket-popup");
            showToast(json.message);
        } else {
            showToast(json.message, "error");
        }
    });

    function addTicketToTable(ticket) {
        const date = new Date(ticket.created_at).toLocaleDateString("fr-FR");

        const row = document.createElement("tr");
        row.className = "ticket-row";
        row.setAttribute(
            "onclick",
            "window.location='/tickets/" + ticket.id + "'",
        );

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
});

document.addEventListener("DOMContentLoaded", function () {
    const projectForm = document.getElementById("project-form");
    const projectsGrid = document.getElementById("projects-grid");
    const projectTable = document.getElementById("project-table");

    if (!projectForm) {
        return;
    }

    projectForm.addEventListener("submit", async function (e) {
        e.preventDefault();

        const name = projectForm.querySelector('input[name="name"]').value;
        const description = projectForm.querySelector('textarea[name="description"]').value;
        const clientId = projectForm.querySelector('select[name="client_id"]').value;
        const ownerId = projectForm.querySelector('select[name="owner_id"]').value;
        const status = projectForm.querySelector('select[name="status"]').value;
        const progress = projectForm.querySelector('input[name="progress"]').value;
        const budgetH = projectForm.querySelector('input[name="budget_h"]').value;
        const totalH = projectForm.querySelector('input[name="total_h"]').value;

        const teamCheckboxes = projectForm.querySelectorAll('input[name="team[]"]:checked');
        const team = [];
        teamCheckboxes.forEach(function (checkbox) {
            team.push(checkbox.value);
        });

        const response = await fetch("/projects", {
            method: "POST",
            headers: {
                "Accept": "application/json",
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": projectForm.querySelector('input[name="_token"]').value,
            },
            body: JSON.stringify({
                name: name,
                description: description,
                client_id: clientId,
                owner_id: ownerId,
                status: status,
                progress: progress,
                budget_h: budgetH,
                total_h: totalH,
                team: team,
            }),
        });

        const json = await response.json();

        if (response.ok) {
            if (projectTable)
                addProjectToTable(json.project);
            
            if (projectsGrid)
                addProjectToGrid(json.project);

            projectForm.reset();
            togglePopup("project-popup");
            showToast(json.message);
        } else {
            showToast(json.message, "error");
        }
    });

    function addProjectToGrid(project) {
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
});

document.addEventListener("DOMContentLoaded", function () {
    const clientForm = document.getElementById("client-form");
    const clientsGrid = document.getElementById("clients-grid");

    if (!clientForm) {
        return;
    }

    clientForm.addEventListener("submit", async function (e) {
        e.preventDefault();

        const company = clientForm.querySelector('input[name="company"]').value;
        const name = clientForm.querySelector('input[name="name"]').value;
        const email = clientForm.querySelector('input[name="email"]').value;
        const phone = clientForm.querySelector('input[name="phone"]').value;
        const status = clientForm.querySelector('select[name="status"]').value;

        const response = await fetch("/clients", {
            method: "POST",
            headers: {
                "Accept": "application/json",
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": clientForm.querySelector('input[name="_token"]').value,
            },
            body: JSON.stringify({
                company: company,
                name: name,
                email: email,
                phone: phone,
                status: status,
            }),
        });

        const json = await response.json();

        if (response.ok) {
            addClientToGrid(json.client);
            clientForm.reset();
            togglePopup("client-popup");
            showToast(json.message);
        } else {
            showToast(json.message, "error");
        }
    });

    function addClientToGrid(client) {
        if (!clientsGrid) {
            return;
        }

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
});