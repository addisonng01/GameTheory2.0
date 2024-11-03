document.addEventListener("DOMContentLoaded", () => {
    fetchStudents();
});

async function fetchStudents() {
    try {
        const response = await fetch('/api/students'); // Adjust the endpoint as needed
        const students = await response.json();
        const studentNamesDiv = document.getElementById("studentNames");
        const table = document.createElement("table");

        // Create rows of student links dynamically
        students.forEach((student, index) => {
            if (index % 3 === 0) {
                // Create a new row for every 3 students
                const row = document.createElement("tr");
                table.appendChild(row);
            }

            const cell = document.createElement("td");
            cell.innerHTML = `<a href="studentView.html?fname=${encodeURIComponent(student.firstName)}&lname=${encodeURIComponent(student.lastName)}">${student.firstName} ${student.lastName}</a>`;
            table.lastChild.appendChild(cell);
        });

        studentNamesDiv.appendChild(table);
    } catch (error) {
        console.error("Error fetching student names:", error);
    }
}
