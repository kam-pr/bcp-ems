document.getElementById('menuIcon').addEventListener('click', function () {
    const sidePanel = document.getElementById('sidePanel');
    const content = document.querySelector('.content');

    sidePanel.classList.toggle('collapsed');
    content.classList.toggle('expanded');
});