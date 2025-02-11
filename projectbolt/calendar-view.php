<?php
require_once 'includes/header.php';
?>

<div class="container">
    <h1>Class Calendar</h1>
    <div id="calendar"></div>
</div>

<script>
    // JavaScript code to initialize and manage the calendar
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: [
                // Example events, replace with dynamic data from the server
                {
                    title: 'Class 1',
                    start: '2023-10-01',
                    description: 'Details about Class 1'
                },
                {
                    title: 'Class 2',
                    start: '2023-10-05',
                    description: 'Details about Class 2'
                }
            ],
            eventClick: function(info) {
                alert('Event: ' + info.event.title + '\n' + info.event.extendedProps.description);
            }
        });
        calendar.render();
    });
</script>

<?php require_once 'includes/footer.php'; ?>
