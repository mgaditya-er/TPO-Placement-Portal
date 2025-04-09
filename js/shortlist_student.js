document.addEventListener('DOMContentLoaded', function() {
    // Apply filter event listener
    
    document.getElementById('applyFilterBtn').addEventListener('click', function() {
        const course = document.getElementById('course').value;
        const year = document.getElementById('year').value;
        const percentage = document.getElementById('percentage').value;
        const jobId = document.getElementById('job_id').value;

        // Build the query string
        let url = `http://localhost/api/shortlist_student.php?percentage=${percentage}`;
        if (course) url += `&course=${course}`;
        if (year) url += `&year=${year}`;
        if (jobId) url += `&job_id=${encodeURIComponent(jobId)}`;

        console.log("Fetching students with URL:", url);

        // Fetch the filtered students
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (!data || data.length === 0) {
                    alert('No students found for the given filter criteria.');
                }
                const studentTableBody = document.getElementById('studentTableBody');
                studentTableBody.innerHTML = ''; // Clear previous data

                // Create a list to store the shortlisted students
                data.forEach(student => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${student.enrollment_no}</td>
                        <td>${student.name}</td>
                        <td>${student.age}</td>
                        <td>${student.address}</td>
                        <td>${student.course}</td>
                        <td>${student.year}</td>
                        <td>${student.percentage}</td>
                        <td><button class="btn btn-info">View Profile</button></td>
                        <td><input type="checkbox" class="shortlist-checkbox" data-enrollment="${student.enrollment_no}" /></td>
                    `;
                    studentTableBody.appendChild(row);
                });

                // Show the 'Send Notifications' button if there are shortlisted students
                const shortlistCheckboxes = document.querySelectorAll('.shortlist-checkbox');
                const sendNotificationBtn = document.getElementById('sendNotificationBtn');
                sendNotificationBtn.style.display = shortlistCheckboxes.length > 0 ? 'inline-block' : 'none';
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                alert('Error occurred while fetching data. Please try again.');
            });
    });

    // Send Notifications to the shortlisted students
    document.getElementById('sendNotificationBtn').addEventListener('click', function() {
        const selectedStudents = [];
        const message = document.getElementById('notificationMessage').value.trim();

        document.querySelectorAll('.shortlist-checkbox:checked').forEach(checkbox => {
            const enrollmentNo = checkbox.getAttribute('data-enrollment');
            selectedStudents.push(enrollmentNo);
        });

        if (selectedStudents.length > 0 && message !== "") {
            // Prepare the data to send in the POST request
            const data = {
                students: selectedStudents,
                message: message
            };

            fetch('http://localhost/api/send_notifications.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Notifications sent to the shortlisted students.');
                    } else {
                        alert('Failed to send notifications: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error sending notifications:', error);
                    alert('Error occurred while sending notifications. Please try again.');
                });
        } else {
            alert('Please select at least one student and enter a message.');
        }


        // Function to load Job IDs dynamically
    function loadJobIds() {
        fetch('http://localhost/api/get_jobs.php') // Update this to your actual jobs endpoint
            .then(response => response.json())
            .then(jobs => {
                const jobSelect = document.getElementById('job_id');
                jobs.forEach(job => {
                    const option = document.createElement('option');
                    option.value = job.job_id;
                    option.textContent = `Job ${job.job_id} - ${job.title || 'Untitled'}`;
                    jobSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading job IDs:', error);
            });
    }
    });
});