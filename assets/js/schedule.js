// JavaScript for schedule page
document.addEventListener('DOMContentLoaded', function() {
    // Add click event listener to all details toggles
    document.querySelectorAll('.details-toggle').forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const detailsContent = this.nextElementSibling;
            const chevron = this.querySelector('i');

            // Toggle visibility
            if (detailsContent.style.display === 'none') {
                detailsContent.style.display = 'block';
                chevron.classList.remove('fa-chevron-down');
                chevron.classList.add('fa-chevron-up');
            } else {
                detailsContent.style.display = 'none';
                chevron.classList.remove('fa-chevron-up');
                chevron.classList.add('fa-chevron-down');
            }
        });
    });
});
