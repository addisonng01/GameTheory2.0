document.addEventListener("DOMContentLoaded", () => {
    displayInfo();
});

async function displayInfo() {
    try {
        // Adjust the endpoint to match your API that retrieves student information from the MySQL database
        const response = await fetch('/api/student-info?studentId=123'); // Use actual student ID
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const studentInfo = await response.json();

        // Update student information
        document.getElementById("studentHeader").textContent = `Student (${studentInfo.firstName} ${studentInfo.lastName})`;
        document.getElementById("firstNameDisplay").textContent = studentInfo.firstName;
        document.getElementById("lastNameDisplay").textContent = studentInfo.lastName;
        document.getElementById("emailDisplay").textContent = studentInfo.email;

        // Update Red/Black game statistics
        document.getElementById("redBlackNum").textContent = studentInfo.redBlackGamesPlayed;
        document.getElementById("redBlackHigh").textContent = studentInfo.redBlackHighScore;
        document.getElementById("redBlackLow").textContent = studentInfo.redBlackLowScore;
        document.getElementById("redBlackGPA").textContent = studentInfo.redBlackGPA.toFixed(2); // Assuming GPA is a number

        // Update Wheat & Steel game statistics
        document.getElementById("wheatSteelNum").textContent = studentInfo.wheatSteelGamesPlayed;
        document.getElementById("wheatHigh").textContent = studentInfo.wheatHighProduction;
        document.getElementById("wheatGoalNum").textContent = studentInfo.wheatGoalsMet;
        document.getElementById("steelHigh").textContent = studentInfo.steelHighProduction;
        document.getElementById("steelGoalNum").textContent = studentInfo.steelGoalsMet;

    } catch (error) {
        console.error("Error fetching student info:", error);
    }
}
