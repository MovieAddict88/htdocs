document.addEventListener('DOMContentLoaded', function () {

    // --- PWA Service Worker Registration ---
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').then(registration => {
                console.log('Service Worker registered with scope:', registration.scope);
            }).catch(error => {
                console.log('Service Worker registration failed:', error);
            });
        });
    }

    // --- Theme Switcher ---
    const themeToggle = document.getElementById('checkbox');
    const body = document.body;

    // Function to apply the saved theme
    const applyTheme = () => {
        const isDarkMode = localStorage.getItem('darkMode') === 'true';
        themeToggle.checked = isDarkMode;
        if (isDarkMode) {
            body.classList.add('dark-mode');
            body.classList.remove('light-mode');
        } else {
            body.classList.remove('dark-mode');
            body.classList.add('light-mode');
        }
    };

    // Event listener for the toggle
    themeToggle.addEventListener('change', () => {
        if (themeToggle.checked) {
            body.classList.add('dark-mode');
            body.classList.remove('light-mode');
            localStorage.setItem('darkMode', 'true');
        } else {
            body.classList.remove('dark-mode');
            body.classList.add('light-mode');
            localStorage.setItem('darkMode', 'false');
        }
    });

    // Apply theme on initial load
    applyTheme();


    // --- Mobile Navigation ---
    const mobileNavToggle = document.getElementById('mobile-nav-toggle');
    const sidebar = document.getElementById('sidebar');

    if (mobileNavToggle && sidebar) {
        mobileNavToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        // Optional: Close sidebar when clicking outside of it
        document.addEventListener('click', function(event) {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickOnToggle = mobileNavToggle.contains(event.target);

            if (!isClickInsideSidebar && !isClickOnToggle && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        });
    }

    // --- Lazy Loading Images ---
    const lazyImages = document.querySelectorAll('img.lazy');

    if ('IntersectionObserver' in window) {
        let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    let lazyImage = entry.target;
                    lazyImage.src = lazyImage.dataset.src;
                    lazyImage.classList.remove('lazy');
                    lazyImageObserver.unobserve(lazyImage);
                }
            });
        });

        lazyImages.forEach(function(lazyImage) {
            lazyImageObserver.observe(lazyImage);
        });
    } else {
        // Fallback for older browsers
        let lazyLoad = function() {
            lazyImages.forEach(function(lazyImage) {
                if (lazyImage.getBoundingClientRect().top <= window.innerHeight && lazyImage.getBoundingClientRect().bottom >= 0 && getComputedStyle(lazyImage).display !== 'none') {
                    lazyImage.src = lazyImage.dataset.src;
                    lazyImage.classList.remove('lazy');
                }
            });
        };

        document.addEventListener('scroll', lazyLoad);
        window.addEventListener('resize', lazyLoad);
        window.addEventListener('orientationchange', lazyLoad);
        lazyLoad(); // initial check
    }
});