function toggleProfileSidebar() {
    const userProfileSidebar = document.querySelector('.user-profile-sidebar');
    userProfileSidebar.classList.toggle('open');
}

function openEditProfileModal() {
    const editProfileModal = new bootstrap.Modal(document.getElementById('editProfileModal'));
    editProfileModal.show();
}

function loadFile(event) {
    var output = document.getElementById('modalProfileImage');
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function() {
        URL.revokeObjectURL(output.src) // free memory
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Store classes by day
    const classesByDay = JSON.parse(document.getElementById('classesByDay').textContent);
    
    // Handle day click
    document.querySelectorAll('.calendar-day').forEach(day => {
        day.addEventListener('click', function() {
            const dayName = this.dataset.day;
            const dayClasses = classesByDay[dayName];
            
            if (dayClasses && dayClasses.length > 0) {
                showDayClasses(dayName, dayClasses);
            }
        });
    });
    
    function showDayClasses(day, classes) {
        document.getElementById('selectedDay').textContent = day;
        
        const classList = document.getElementById('dayClassesList');
        classList.innerHTML = classes.map(classItem => `
            <div class="d-flex align-items-center border-bottom py-3">
                <img src="${classItem.instructor_image}" 
                     class="instructor-image me-3">
                <div class="flex-grow-1">
                    <h5 class="mb-1">${classItem.name}</h5>
                    <p class="text-secondary mb-1">${classItem.instructor_name}</p>
                    <p class="mb-0">
                        ${new Date(classItem.start_date).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})} 
                        | ${classItem.duration} min 
                        | ${classItem.level}
                    </p>
                </div>
                <a href="booking.php?class_id=${classItem.id}" 
                   class="btn btn-secondary">Book</a>
            </div>
        `).join('');
        
        new bootstrap.Modal(document.getElementById('dayClassesModal')).show();
    }

    // Handle user profile button click
    const userProfileButton = document.querySelector('.user-profile-button');
    userProfileButton.addEventListener('click', toggleProfileSidebar);
});