document.addEventListener("DOMContentLoaded", () => {
    fetchUsernames();
});

async function fetchUsernames() {
    try {
        const response = await fetch('/api/usernames'); // Adjust the endpoint as needed
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const usernames = await response.json();
        const usernameColumn = document.getElementById('usernameColumn');

        // Clear existing usernames in case this function is called multiple times
        usernameColumn.innerHTML = '';

        // Dynamically create username elements based on fetched data
        usernames.forEach((username, index) => {
            const usernameDiv = document.createElement('div');
            usernameDiv.className = 'username';
            usernameDiv.textContent = username; // Create usernames from the fetched data
            usernameColumn.appendChild(usernameDiv);
        });
    } catch (error) {
        console.error("Error fetching usernames:", error);
    }
}
