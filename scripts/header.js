document.addEventListener('DOMContentLoaded', function() {
    const header = document.querySelector('header');
    const bar = document.querySelector('.bar');
    const headerHeight = header.offsetHeight;
    
    // Set the bar's top position based on header height
    bar.style.top = `${headerHeight}px`;
    
    // Function to handle scroll events
    function handleScroll() {
      if (window.scrollY > 50) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
      
      // Update bar position if header height changes when scrolled
      const currentHeaderHeight = header.offsetHeight;
      bar.style.top = `${currentHeaderHeight}px`;
    }
    
    // Add scroll event listener
    window.addEventListener('scroll', handleScroll);
    
    // Handle resize events to recalculate heights
    window.addEventListener('resize', function() {
      const headerHeight = header.offsetHeight;
      bar.style.top = `${headerHeight}px`;
    });
  });
  