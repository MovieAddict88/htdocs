document.addEventListener('DOMContentLoaded', function () {

    // --- MOBILE MENU TOGGLE ---
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');
    const mainPanel = document.querySelector('.main-panel');

    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('open');
    });

    // Close sidebar when a link is clicked or when clicking outside
    document.addEventListener('click', (e) => {
        if (sidebar.classList.contains('open')) {
            if (!sidebar.contains(e.target) && e.target !== menuToggle) {
                sidebar.classList.remove('open');
            }
        }
    });


    // --- THEME TOGGLE ---
    const themeToggles = document.querySelectorAll('.theme-toggle');
    const sunIcon = 'fa-sun';
    const moonIcon = 'fa-moon';

    // Function to set the theme
    const setTheme = (theme) => {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        themeToggles.forEach(toggle => {
            const icon = toggle.querySelector('i');
            if (theme === 'dark') {
                icon.classList.remove(sunIcon);
                icon.classList.add(moonIcon);
            } else {
                icon.classList.remove(moonIcon);
                icon.classList.add(sunIcon);
            }
        });
    };

    // Event listener for all theme toggle buttons
    themeToggles.forEach(toggle => {
        toggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            setTheme(newTheme);
        });
    });

    // Load saved theme from localStorage
    const savedTheme = localStorage.getItem('theme') || 'light';
    setTheme(savedTheme);


    // --- ACTIVE LINK ON SCROLL ---
    const sections = document.querySelectorAll('.panel');
    const navLinks = document.querySelectorAll('.nav-links a');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href').substring(1) === entry.target.id) {
                        link.classList.add('active');
                    }
                });
            }
        });
    }, { rootMargin: '-50% 0px -50% 0px' }); // Activates when section is in the middle of the viewport

    sections.forEach(section => {
        observer.observe(section);
    });


    // --- SMOOTH SCROLLING ---
    navLinks.forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
            }

            // Close sidebar on mobile after clicking a link
            if (sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        });
    });

});