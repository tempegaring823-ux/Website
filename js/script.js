// admin-script.js

document.addEventListener('DOMContentLoaded', () => {
    // Memilih semua item navigasi di sidebar
    const navItems = document.querySelectorAll('.sidebar-nav .nav-item');
    // Memilih semua bagian konten di dashboard
    const sections = document.querySelectorAll('.main-content .content-section');

    // Menambahkan event listener ke setiap item navigasi
    navItems.forEach(item => {
        item.addEventListener('click', (e) => {
            const href = item.getAttribute('href');

            if (href.startsWith('#')) {
                e.preventDefault();

                // Hapus kelas 'active' dari semua item navigasi
                navItems.forEach(nav => nav.classList.remove('active'));
                // Tambahkan kelas 'active' ke item yang baru diklik
                item.classList.add('active');

                // Sembunyikan semua bagian konten
                sections.forEach(sec => sec.classList.remove('active'));

                // Tampilkan bagian konten yang sesuai
                const targetId = href.substring(1);
                const targetSection = document.getElementById(targetId);
                if (targetSection) {
                    targetSection.classList.add('active');
                }
            }
        });
    });

    // 🔥 Tambahan: cek hash saat pertama kali load
    if (window.location.hash) {
        const targetId = window.location.hash.substring(1);
        const targetSection = document.getElementById(targetId);
        if (targetSection) {
            // reset semua
            navItems.forEach(nav => nav.classList.remove('active'));
            sections.forEach(sec => sec.classList.remove('active'));

            // aktifkan nav yang sesuai
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
            // Toggle class 'active' pada menu navigasi untuk menampilkan/menyembunyikan
            navLinks.classList.toggle('active');
            
            // Toggle class 'is-active' pada tombol hamburger untuk animasi X
            menuToggle.classList.toggle('is-active');
        });
    }
});
