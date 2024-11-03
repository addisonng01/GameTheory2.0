document.addEventListener("DOMContentLoaded", () => {
    fetchGameData();
});

async function fetchGameData() {
    try {
        const response = await fetch('/api/game-data'); // Adjust the endpoint to your server
        const gameData = await response.json();

        // Update game information in the HTML
        document.querySelector('.box2').textContent = `Game (${gameData.gameName})`;
        document.querySelector('.box4').innerHTML = `Game Name: ${gameData.gameName} Game Date: ${gameData.gameDate}`;
        
        const playersList = gameData.pairs.map(pair => `${pair.player1} & ${pair.player2}`).join(', ');
        document.querySelector('.box5').innerHTML = `
            Players: ${gameData.totalPlayers}<br/>
            Pairs: ${gameData.totalPairs}<br/>
            Rounds: ${gameData.totalRounds}<br/><br/>
            ${playersList}<br/>
            Total Number of Black Cards Played: ${gameData.blackCardsPlayed}<br/>
            Average Number of Black Cards Played Per Round: ${gameData.blackAverage}<br/>
            Total Number of Red Cards Played: ${gameData.redCardsPlayed}<br/>
            Average Number of Red Cards Played Per Round: ${gameData.redAverage}<br/><br/>
            Player with Highest Score: ${gameData.highestScorer.name} - ${gameData.highestScorer.score}<br/>
            Player with Lowest Score: ${gameData.lowestScorer.name} - ${gameData.lowestScorer.score}<br/>
            Player with Highest Number of Black Cards: ${gameData.highestBlack.name} - ${gameData.highestBlack.count}<br/>
            Player with Lowest Number of Black Cards: ${gameData.lowestBlack.name} - ${gameData.lowestBlack.count}<br/>
            Player with Highest Number of Red Cards: ${gameData.highestRed.name} - ${gameData.highestRed.count}<br/>
            Player with Lowest Number of Red Cards: ${gameData.lowestRed.name} - ${gameData.lowestRed.count}
        `;
    } catch (error) {
        console.error("Error fetching game data:", error);
    }
}
