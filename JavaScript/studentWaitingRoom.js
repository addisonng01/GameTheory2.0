document.addEventListener("DOMContentLoaded", function() {
    const totalUsernames = 20; // Set the total number of usernames you want to create
    const usernameColumn = document.getElementById('usernameColumn');

    // Dynamically create username elements based on index
    for (let i = 1; i <= totalUsernames; i++) {
        const usernameDiv = document.createElement('div');
        usernameDiv.className = 'username';
        usernameDiv.textContent = `Username${i}`; // Create usernames based on the index
        usernameColumn.appendChild(usernameDiv);
    }
});