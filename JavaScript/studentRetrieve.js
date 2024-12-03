document.addEventListener("DOMContentLoaded", () => {
    fetchStudents();
});

async function fetchStudents() {
    try {
        // Adjust the endpoint to match your API that retrieves student information from the MySQL database
        const response = await fetch('/api/teachers'); // Updated to the 'teachers' API endpoint
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const teachers = await response.json();
        const studentNamesDiv = document.getElementById("studentNames");
        const table = document.createElement("table");

        // Create rows of student links dynamically
        teachers.forEach((teacher, index) => {
            if (index % 3 === 0) {
                // Create a new row for every 3 students
                const row = document.createElement("tr");
                table.appendChild(row);
            }

            const cell = document.createElement("td");
            cell.innerHTML = `<a href="teacherView.html?fname=${encodeURIComponent(teacher.first_nm)}&lname=${encodeURIComponent(teacher.last_nm)}">${teacher.first_nm} ${teacher.last_nm}</a>`;
            table.lastChild.appendChild(cell);
        });

        studentNamesDiv.appendChild(table);
    } catch (error) {
        console.error("Error fetching teacher names:", error);
    }
}
