document.addEventListener('DOMContentLoaded', function() {
    setUpSearch();
    setUpDropdown();
});

function setUpSearch() {
    const searchButton = document.getElementById('searchButton');
    const searchInput = document.getElementById('searchInput');

    // Open on click
    searchButton.addEventListener('click', function(e) {
        e.stopPropagation(); // Prevent event from bubbling
        searchInput.classList.toggle('active');
        
        // Focus the input when opened
        if (searchInput.classList.contains('active')) {
            setTimeout(() => searchInput.focus(), 100);
        }
    });

   
    searchInput.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    // close on click
    document.addEventListener('click', function(e) {
        if (!searchButton.contains(e.target) && !searchInput.contains(e.target)) {
            searchInput.classList.remove('active');
        }
    });

    // close on Esc
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            searchInput.classList.remove('active');
        }
    });
}

function setUpDropdown() {
    const dropdownButton = document.getElementById('dropdown-button');
    const dropdownContent = document.getElementById('dropdown-content');

    dropdownButton.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdownContent.classList.toggle('show');
    });

    document.addEventListener('click', function(e) {
        if (!dropdownButton.contains(e.target) && !dropdownContent.contains(e.target)) {
            dropdownContent.classList.remove('show');
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            dropdownContent.classList.remove('show');
        }
    });
}