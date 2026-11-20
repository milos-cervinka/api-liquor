// https://medium.com/front-end-weekly/how-to-build-reusable-html-components-without-component-based-frameworks-2f7747f4c5db
// Source - https://stackoverflow.com/a/77835274
// Posted by fmacdee, modified by community. See post 'Timeline' for change history
// Retrieved 2026-10-13, License - CC BY-SA 4.0

class ComponentHandler
{
    constructor(basePath) 
    {
        this.basePath = basePath;
    }

    async register(name, templateFile) 
    {
        try 
        {
            let response = await fetch(this.basePath + '/' + templateFile);
            if (response.ok) 
            {
                let html = await response.text();
                class CustomElement extends HTMLElement 
                {
                    async connectedCallback() 
                    {
                        this.innerHTML = html;

                        if (name === 'main-sidebar') 
                        {
                            try 
                            {
                                const res = await fetch('/Web/API/dashboard/Controller/menuController.php');
                                const modules = await res.json();
                                const menu = this.querySelector('#dynamic-menu');
                                
                                if (menu) 
                                {
                                    menu.innerHTML = '';
                                    modules.forEach(mod => {
                                        let lowerMod = mod.toLowerCase();
                                        let link = `/Web/API/dashboard/View/Sites/${lowerMod}/${lowerMod}.html`;
                                        
                                        if (lowerMod === 'dashboard') 
                                        {
                                            link = `/Web/API/index.html`;
                                        }

                                        menu.innerHTML += `
                                            <li class="nav-item">
                                                <a class="nav-link" href="${link}">
                                                    ${mod}
                                                </a>
                                            </li>
                                        `;
                                    });
                                }
                            } 
                            catch (e) 
                            {
                                console.error(e);
                            }
                        }
                    }
                }
                
                if (!customElements.get(name)) 
                {
                    customElements.define(name, CustomElement);
                }
            }
        } 
        catch (error) 
        {
            console.error("Loader Error:", error);
        }
    }
}

async function initHeaderAuth() 
{
    const container = document.getElementById('userProfileNav');
    if (!container) return;

    try 
    {
        const res = await fetch('/Web/API/dashboard/Controller/authController.php?action=me');
        const data = await res.json();

        if (data.logged && data.user) 
        {
            const avatarHtml = data.user.picture ? `<img src="${data.user.picture}" class="rounded-circle me-2" style="width:28px;height:28px;object-fit:cover;">`: `<span class="badge bg-secondary rounded-circle me-2">${data.user.name.charAt(0)}</span>`;

            container.innerHTML = `
                <div class="d-flex align-items-center text-white gap-2">
                    ${avatarHtml}
                    <span class="small fw-semibold">${data.user.name}</span>
                    <span class="badge bg-${data.user.role === 'admin' ? 'danger' : 'secondary'}">${data.user.role.toUpperCase()}</span>
                    <a class="btn btn-sm btn-outline-light ms-2" href="/Web/API/dashboard/Controller/authController.php?action=logout">Logout</a>
                </div>
            `;
        } 
        else 
        {
            renderGoogleSignInButton(container);
        }
    } 
    catch (e) 
    {
        console.error('Error while initializing header:', e);
    }
}

function renderGoogleSignInButton(container) 
{
    if (typeof google === 'undefined' || !google.accounts || !google.accounts.id) 
    {
        setTimeout(() => renderGoogleSignInButton(container), 100);
        return;
    }

    container.innerHTML = '';

    google.accounts.id.initialize({
        client_id: '620105565533-c1jec3k2ihhad5g266fl6k546o3d3jnk.apps.googleusercontent.com',
        login_uri: 'https://cervimi24.sps-prosek.cz/Web/API/dashboard/Controller/authController.php',
        ux_mode: 'redirect',
        auto_prompt: false
    });

    google.accounts.id.renderButton(
        container,
        {
            type: 'standard',
            theme: 'filled_black',
            size: 'medium',
            text: 'signin',
            shape: 'rectangular',
            logo_alignment: 'left'
        }
    );
}

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(initHeaderAuth, 150);
});

const templatePath = "/Web/API/dashboard/View/Components/Templates";
const handler = new ComponentHandler(templatePath);

document.addEventListener("DOMContentLoaded", function() {
    handler.register('main-header', 'header.html');
    handler.register('main-sidebar', 'sidebar.html');
});

