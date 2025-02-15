// Sidebar Control
document.addEventListener('DOMContentLoaded', function() {
    // Helper function to check if element is a menu item
    function isMenuItem(element) {
        return element.matches('.sidebar-menu a') || 
               element.matches('.sidebar-menu li') || 
               element.matches('.treeview-menu a');
    }

    const body = document.querySelector('body');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const mainSidebar = document.querySelector('.main-sidebar');
    const menuItems = document.querySelectorAll('.sidebar-menu a:not(.sidebar-toggle)');
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);

    // Handle clicks on the sidebar
    document.querySelector('.main-sidebar').addEventListener('click', function(e) {
        const clickedElement = e.target;
        
        // If clicked element is a menu item or its child
        if (isMenuItem(clickedElement) || clickedElement.closest('.sidebar-menu a')) {
            if (window.innerWidth <= 768) {
                // Small delay to allow the click to register
                setTimeout(() => {
                    closeSidebar();
                }, 150);
            }
        }
    });

    // Initialize sidebar state from localStorage
    const sidebarState = localStorage.getItem('sidebarState');
    if (sidebarState === 'collapsed') {
        body.classList.add('sidebar-collapse');
    }

    // Toggle sidebar
    sidebarToggle.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        body.classList.toggle('sidebar-open');
    });

    // Close sidebar when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.main-sidebar') && 
            !e.target.closest('.sidebar-toggle') && 
            body.classList.contains('sidebar-open')) {
            body.classList.remove('sidebar-open');
        }
    });

    // Close sidebar when clicking overlay
    overlay.addEventListener('click', function() {
        closeSidebar();
    });

    // Close sidebar on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && body.classList.contains('sidebar-open')) {
            closeSidebar();
        }
    });

    function closeSidebar() {
        body.classList.remove('sidebar-open');
        overlay.style.display = 'none';
        if (window.innerWidth <= 768) {
            mainSidebar.style.transform = 'translate(-230px, 0)';
        }
    }

    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.innerWidth > 767) {
                body.classList.remove('sidebar-open');
                overlay.style.display = 'none';
                mainSidebar.style.transform = '';
            }
        }, 250);
    });

    // Handle swipe gestures on mobile
    let touchStartX = 0;
    let touchEndX = 0;
    
    document.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    }, false);

    document.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, false);

    function handleSwipe() {
        const swipeThreshold = 100;
        const swipeDistance = touchEndX - touchStartX;

        if (Math.abs(swipeDistance) > swipeThreshold) {
            if (swipeDistance > 0) { // Swipe right
                body.classList.add('sidebar-open');
            } else { // Swipe left
                body.classList.remove('sidebar-open');
            }
        }
    }
});
