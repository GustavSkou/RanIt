document.addEventListener('DOMContentLoaded', function() {
    const searchButton = document.getElementById('searchButton');
    const searchInput = document.getElementById('searchInput');

    // Toggle search bar when button is clicked
    searchButton.addEventListener('click', function(e) {
        e.stopPropagation(); // Prevent event from bubbling
        searchInput.classList.toggle('active');
        
        // Focus the input when opened
        if (searchInput.classList.contains('active')) {
            setTimeout(() => searchInput.focus(), 100);
        }
    });

    // Prevent closing when clicking inside the input
    searchInput.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    // Close search bar when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchButton.contains(e.target) && !searchInput.contains(e.target)) {
            searchInput.classList.remove('active');
        }
    });

    

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            searchInput.classList.remove('active');
        }
    });
});