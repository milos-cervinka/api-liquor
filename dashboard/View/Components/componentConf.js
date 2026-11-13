// https://medium.com/front-end-weekly/how-to-build-reusable-html-components-without-component-based-frameworks-2f7747f4c5db
const templatePath = "/Web/API/dashboard/View/Components/Templates";
const handler = new ComponentHandler(templatePath);

document.addEventListener("DOMContentLoaded", function() {
    handler.register('main-header', 'header.html');
    handler.register('main-sidebar', 'sidebar.html');
});