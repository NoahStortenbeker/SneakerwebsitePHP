document.addEventListener('DOMContentLoaded', function() {
    const searchToggle = document.querySelector('.search-toggle');
    const searchContainer = document.querySelector('.search-container');
    const searchClose = document.querySelector('.search-close');
    const searchInput = document.querySelector('.search-input');
    
    // Toggle search bar when search icon is clicked
    searchToggle.addEventListener('click', function(e) {
      e.preventDefault();
      searchContainer.classList.add('active');
      // Focus on the input field
      setTimeout(() => {
        searchInput.focus();
      }, 300);
    });
    
    // Close search bar when close button is clicked
    searchClose.addEventListener('click', function() {
      searchContainer.classList.remove('active');
    });
    
    // Close search bar when clicking outside
    document.addEventListener('click', function(e) {
      if (!searchContainer.contains(e.target) && 
          !searchToggle.contains(e.target) && 
          searchContainer.classList.contains('active')) {
        searchContainer.classList.remove('active');
      }
    });
    
    // Close search bar when pressing Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && searchContainer.classList.contains('active')) {
        searchContainer.classList.remove('active');
      }
    });
    
    // Handle search form submission
    const searchForm = document.querySelector('.search-form');
    searchForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const searchTerm = searchInput.value.trim();
      
      if (searchTerm) {
        // You can replace this with your actual search functionality
        console.log('Searching for:', searchTerm);
        
        // Example: redirect to search results page
        // window.location.href = `/search?q=${encodeURIComponent(searchTerm)}`;
        
        // For now, just close the search bar
        searchContainer.classList.remove('active');
      }
    });
  });
  