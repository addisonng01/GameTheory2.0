document.addEventListener("DOMContentLoaded", () => {
    displayStudentInformation();
});

async function displayStudentInformation() {
    try {
        // Fetch the student info from the API
        const response = await fetch('/api/student-info'); // Adjust the endpoint as needed
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const studentInfo = await response.json();

        // Update the DOM elements with the student information
        if (studentInfo.firstName) document.getElementById('firstNameDisplay').innerHTML = studentInfo.firstName;
        if (studentInfo.lastName) document.getElementById('lastNameDisplay').innerHTML = studentInfo.lastName;
        if (studentInfo.email) document.getElementById('emailDisplay').innerHTML = studentInfo.email;
    } catch (error) {
        console.error("Error fetching student information:", error);
    }
}
