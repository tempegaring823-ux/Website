// admin-script.js

document.addEventListener('DOMContentLoaded', () => {
    const navItems = document.querySelectorAll('.sidebar-nav .nav-item');
    const sections = document.querySelectorAll('.main-content .content-section');


    navItems.forEach(item => {
        item.addEventListener('click', (e) => {
            const href = item.getAttribute('href');

            if (href.startsWith('#')) {
                e.preventDefault();

                navItems.forEach(nav => nav.classList.remove('active'));
                item.classList.add('active');

                sections.forEach(sec => sec.classList.remove('active'));

                const targetId = href.substring(1);
                const targetSection = document.getElementById(targetId);
                if (targetSection) {
                    targetSection.classList.add('active');
                }
            }
        });
    });

    if (window.location.hash) {
        const targetId = window.location.hash.substring(1);
        const targetSection = document.getElementById(targetId);
        if (targetSection) {
            navItems.forEach(nav => nav.classList.remove('active'));
            sections.forEach(sec => sec.classList.remove('active'));

            document.querySelector(`.sidebar-nav .nav-item[href="#${targetId}"]`)?.classList.add('active');
            targetSection.classList.add('active');
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const navLinks = document.getElementById('nav-links');

    if (menuToggle && navLinks) {
        menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            
            menuToggle.classList.toggle('is-active');
        });
    }
});
