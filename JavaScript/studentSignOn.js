document.addEventListener("DOMContentLoaded", () => {
    displayStudentInformation();
});

async function displayStudentInformation() {
    try {
        // Fetch the student info based on the session instance for the current user
        const response = await fetch('/api/student-info-current'); // New endpoint to fetch data based on the active session
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const studentInfo = await response.json();

        // Update the DOM elements with the student information
        if (studentInfo.first_nm) document.getElementById('firstNameDisplay').innerHTML = studentInfo.first_nm;
        if (studentInfo.last_nm) document.getElementById('lastNameDisplay').innerHTML = studentInfo.last_nm;
        if (studentInfo.email) document.getElementById('emailDisplay').innerHTML = studentInfo.email;
    } catch (error) {
        console.error("Error fetching student information:", error);
    }
}
