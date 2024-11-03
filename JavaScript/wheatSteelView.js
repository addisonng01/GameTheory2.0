document.addEventListener("DOMContentLoaded", () => {
    fetchGameData();
});

async function fetchGameData() {
    try {
        const response = await fetch('/api/wheat-steel-game/data'); // Adjust the endpoint as needed
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const gameData = await response.json();

        // Update game information in the HTML
        document.getElementById('gameName_2').textContent = gameData.name;
        document.getElementById('gameDate_2').textContent = new Date(gameData.date).toLocaleDateString();

        // Update team information
        document.getElementById('playerNum_2').textContent = gameData.playerCount || 0;
        document.getElementById('teamsFour').textContent = gameData.teamsOfFour || 0;
        document.getElementById('periodNum').textContent = gameData.periodCount || 0;

        // Update game results
        document.getElementById('bothGoalNames').textContent = gameData.bothGoals.join(', ') || "None";
        document.getElementById('oneGoalNames').textContent = gameData.oneGoal.join(', ') || "None";
        document.getElementById('noGoalNames').textContent = gameData.noGoals.join(', ') || "None";

        // Update wheat information
        document.getElementById('wheatProduceTotal').textContent = gameData.wheat.produced.total || 0;
        document.getElementById('wheatProduceAverage').textContent = gameData.wheat.produced.average || 0;
        document.getElementById('wheatConsumeTotal').textContent = gameData.wheat.consumed.total || 0;
        document.getElementById('wheatConsumeAverage').textContent = gameData.wheat.consumed.average || 0;
        document.getElementById('wheatTradeTotal').textContent = gameData.wheat.traded.total || 0;
        document.getElementById('wheatTradeAverage').textContent = gameData.wheat.traded.average || 0;

        // Update steel information
        document.getElementById('steelProduceTotal').textContent = gameData.steel.produced.total || 0;
        document.getElementById('steelProduceAverage').textContent = gameData.steel.produced.average || 0;
        document.getElementById('steelConsumeTotal').textContent = gameData.steel.consumed.total || 0;
        document.getElementById('steelConsumeAverage').textContent = gameData.steel.consumed.average || 0;
        document.getElementById('steelTradeTotal').textContent = gameData.steel.traded.total || 0;
        document.getElementById('steelTradeAverage').textContent = gameData.steel.traded.average || 0;

    } catch (error) {
        console.error("Error fetching game data:", error);
        alert("There was an error fetching the game data. Please try again later.");
    }
}
