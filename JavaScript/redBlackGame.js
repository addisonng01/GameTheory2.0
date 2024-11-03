document.addEventListener("DOMContentLoaded", () => {
    displayInfoRedBlack();
});

async function displayInfoRedBlack() {
    try {
        const response = await fetch('/api/red-black-game'); // Adjust the endpoint as needed
        const gameData = await response.json();

        // Update header and user info
        document.getElementById("headerPlayerOne").textContent = gameData.playerOne.name;
        document.getElementById("headerPlayerTwo").textContent = gameData.playerTwo.name;
        document.getElementById("gameDate").textContent = new Date(gameData.date).toLocaleDateString("en-US");
        document.getElementById("playerOneName").textContent = gameData.playerOne.name;
        document.getElementById("playerTwoName").textContent = gameData.playerTwo.name;

        // Populate rounds table
        const roundsContainer = document.getElementById("redBlackRounds").querySelector("table");

        gameData.rounds.forEach((round, index) => {
            const row = roundsContainer.insertRow();
            row.insertCell(0).textContent = index + 1; // Round number
            row.insertCell(1).textContent = round.playerOneCard; // Player One Card
            row.insertCell(2).textContent = round.playerTwoCard; // Player Two Card
            row.insertCell(3).textContent = round.playerOnePoints; // Player One Points
            row.insertCell(4).textContent = round.playerTwoPoints; // Player Two Points
        });
    } catch (error) {
        console.error("Error fetching game data:", error);
    }
}
