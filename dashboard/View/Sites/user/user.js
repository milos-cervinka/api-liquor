let currentSessionRole = 'guest';
let currentSessionUserId = 0;

function escapeHtml(str) 
{
    if (str === null || str === undefined) return '';
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}

function showAlert(message, type = 'success') 
{
    const alertBox = document.getElementById('alertBox');
    if (alertBox) 
    {
        alertBox.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${escapeHtml(message)}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
    }
}

function attachEvents() 
{
    document.querySelectorAll('.save-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            const btnEl = e.currentTarget;
            const id = btnEl.getAttribute('data-id');
            const select = document.querySelector(`.role-select[data-id="${id}"]`);
            const role = select ? select.value : '';
            btnEl.disabled = true;

            try 
            {
                const res = await fetch('/Web/API/dashboard/Controller/userController.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: parseInt(id), role: role })
                });

                const result = await res.json();

                if (res.ok && result.success) 
                {
                    showAlert('Role update success', 'success');
                } 
                else 
                {
                    showAlert(result.error || 'Error changing role', 'danger');
                }
            } 
            catch (err) 
            {
                showAlert('Server error', 'danger');
            } 
            finally 
            {
                btnEl.disabled = false;
                btnEl.textContent = 'Save';
            }
        });
    });
}

async function loadUsers() 
{
    const tbody = document.getElementById('usersTableBody');
    if (!tbody) return;

    try 
    {
        const res = await fetch('/Web/API/dashboard/Controller/userController.php');
        const data = await res.json();

        if (!res.ok || data.error) 
        {
            throw new Error(data.details || data.error || `HTTP error ${res.status}`);
        }

        currentSessionRole   = data.currentUserRole;
        currentSessionUserId = data.currentUserId;
        tbody.innerHTML = '';

        if (!data.users || data.users.length === 0) 
        {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No users in db</td></tr>';
            return;
        }

        data.users.forEach(u => {
            const tr = document.createElement('tr');
            const avatar = u.picture ? `<img src="${escapeHtml(u.picture)}" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;">` : `<div class="rounded-circle bg-secondary text-white d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px;font-weight:bold;">${escapeHtml(u.name.charAt(0))}</div>`;
            
            const isAdmin = (currentSessionRole === 'admin');
            const isSelf  = (parseInt(u.id) === parseInt(currentSessionUserId));
            const canEdit = isAdmin && !isSelf;

            tr.innerHTML = `
                <td>${avatar}</td>
                <td>
                    <strong>${escapeHtml(u.name)}</strong>
                    ${isSelf ? '<span class="badge bg-primary ms-2">You</span>' : ''}
                </td>
                <td>${escapeHtml(u.email)}</td>
                <td>
                    <select class="form-select form-select-sm role-select" data-id="${u.id}" ${!canEdit ? 'disabled' : ''}>
                        <option value="user" ${u.role === 'user' ? 'selected' : ''}>User</option>
                        <option value="admin" ${u.role === 'admin' ? 'selected' : ''}>Admin</option>
                    </select>
                </td>
                <td>
                    <button class="btn btn-sm ${isSelf ? 'btn-outline-secondary' : 'btn-primary'} save-btn" data-id="${u.id}" ${!canEdit ? 'disabled' : ''}>
                        ${isSelf ? 'Your profile' : 'save'}
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        attachEvents();
    } 
    catch (e) 
    {
        tbody.innerHTML = `<tr><td colspan="5" class="text-danger text-center py-3"><strong>Error:</strong> ${escapeHtml(e.message)}</td></tr>`;
    }
}

if (document.readyState === 'loading') 
{
    document.addEventListener('DOMContentLoaded', loadUsers);
} 
else 
{
    loadUsers();
}