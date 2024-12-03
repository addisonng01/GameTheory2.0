document.addEventListener("DOMContentLoaded", () => {
    fetchGameData();
});

async function fetchGameData() {
    try {
        const response = await fetch('/api/wheatSteel/game/data'); // Adjusted API endpoint for consistency
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const gameData = await response.json();

        // Update game information in the HTML dynamically
        updateGameInfo(gameData);

        // Update team information
        updateTeamInfo(gameData);

        // Update game results
        updateGameResults(gameData);

        // Update wheat information
        updateResourceInfo('wheat', gameData.wheat);

        // Update steel information
        updateResourceInfo('steel', gameData.steel);

    } catch (error) {
        console.error("Error fetching game data:", error);
        alert("There was an error fetching the game data. Please try again later.");
    }
}

// Function to update general game info
function updateGameInfo(gameData) {
    document.getElementById('gameName_2').textContent = gameData.name;
    document.getElementById('gameDate_2').textContent = new Date(gameData.date).toLocaleDateString();
}

// Function to update team information
function updateTeamInfo(gameData) {
    document.getElementById('playerNum_2').textContent = gameData.playerCount || 0;
    document.getElementById('teamsFour').textContent = gameData.teamsOfFour || 0;
    document.getElementById('periodNum').textContent = gameData.periodCount || 0;
}

// Function to update game results
function updateGameResults(gameData) {
    document.getElementById('bothGoalNames').textContent = gameData.bothGoals.join(', ') || "None";
    document.getElementById('oneGoalNames').textContent = gameData.oneGoal.join(', ') || "None";
    document.getElementById('noGoalNames').textContent = gameData.noGoals.join(', ') || "None";
}

// Function to update resource information (wheat/steel)
function updateResourceInfo(resourceType, resourceData) {
    document.getElementById(`${resourceType}ProduceTotal`).textContent = resourceData.produced.total || 0;
    document.getElementById(`${resourceType}ProduceAverage`).textContent = resourceData.produced.average || 0;
    document.getElementById(`${resourceType}ConsumeTotal`).textContent = resourceData.consumed.total || 0;
    document.getElementById(`${resourceType}ConsumeAverage`).textContent = resourceData.consumed.average || 0;
    document.getElementById(`${resourceType}TradeTotal`).textContent = resourceData.traded.total || 0;
    document.getElementById(`${resourceType}TradeAverage`).textContent = resourceData.traded.average || 0;
}
