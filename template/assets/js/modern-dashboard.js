// Modern Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all collapse elements
    initializeCollapseElements();
});

function initializeCollapseElements() {
    // Get all collapse triggers
    const collapseTriggers = document.querySelectorAll('[data-toggle="collapse"]');
    
    collapseTriggers.forEach(trigger => {
        // Get target element
        const targetId = trigger.getAttribute('data-target') || trigger.getAttribute('href');
        const target = document.querySelector(targetId);
        
        if (!target) return;
        
        // Add click event listener
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Toggle collapsed class on header
            const header = trigger.closest('.card-header');
            if (header) {
                header.classList.toggle('collapsed');
            }
            
            // Toggle collapse
            const isCollapsed = target.classList.contains('show');
            
            if (isCollapsed) {
                // Collapse the element
                target.style.height = target.scrollHeight + 'px';
                // Force reflow
                target.offsetHeight;
                target.style.height = '0px';
                target.classList.remove('show');
            } else {
                // Expand the element
                target.classList.add('show');
                target.style.height = target.scrollHeight + 'px';
                
                // Remove the height style after transition
                target.addEventListener('transitionend', function handler() {
                    target.style.height = '';
                    target.removeEventListener('transitionend', handler);
                });
            }
        });
        
        // Initialize collapse state
        if (!target.classList.contains('show')) {
            target.style.height = '0px';
        }
    });
}

// Function to toggle all sections
function toggleAllSections(action) {
    const sections = document.querySelectorAll('.collapse');
    const headers = document.querySelectorAll('.card-header');
    
    sections.forEach(section => {
        if (action === 'expand' && !section.classList.contains('show')) {
            section.classList.add('show');
            section.style.height = section.scrollHeight + 'px';
        } else if (action === 'collapse' && section.classList.contains('show')) {
            section.style.height = '0px';
            section.classList.remove('show');
        }
    });
    
    headers.forEach(header => {
        if (action === 'expand') {
            header.classList.remove('collapsed');
        } else if (action === 'collapse') {
            header.classList.add('collapsed');
        }
    });
}
