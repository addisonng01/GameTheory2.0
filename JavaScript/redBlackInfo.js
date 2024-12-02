const axios = require('axios');
require('dotenv').config();

const BASE_URL = process.env.BASE_URL || 'http://localhost:3000/api'; // Adjust the base URL if needed

document.addEventListener("DOMContentLoaded", () => {
    displayInfoRedBlack();
});

async function displayInfoRedBlack() {
    try {
        const response = await axios.get(`${BASE_URL}/red-black-game-info`); // Adjust the endpoint as needed
        const gameInfo = response.data; // Axios automatically parses JSON responses

        // Update game information
        document.getElementById("gameDate_1").textContent = new Date(gameInfo.date).toLocaleDateString("en-US");
        document.getElementById("playerNum_1").textContent = gameInfo.players.length; // Assuming gameInfo has a players array
        document.getElementById("pairNum_1").textContent = gameInfo.pairs.length; // Assuming gameInfo has a pairs array
        document.getElementById("roundNum_1").textContent = gameInfo.rounds.length; // Assuming gameInfo has a rounds array

        // Update player links
        const teamInfoDiv = document.getElementById("teamInfo");
        teamInfoDiv.innerHTML += gameInfo.pairs.map(pair => 
            `<a href="https://cssgametheory.com/CSSGametheory/HTML/admin/redBlackPairView.html?d=${encodeURIComponent(gameInfo.date)}&p1=${encodeURIComponent(pair.playerOne)}&p2=${encodeURIComponent(pair.playerTwo)}">${pair.playerOne} & ${pair.playerTwo}</a>`).join(', ');

        // Update card info
        document.getElementById("blackTotal").textContent = gameInfo.blackCardsTotal;
        document.getElementById("blackAverage").textContent = (gameInfo.blackCardsTotal / gameInfo.rounds.length).toFixed(2);
        document.getElementById("redTotal").textContent = gameInfo.redCardsTotal;
        document.getElementById("redAverage").textContent = (gameInfo.redCardsTotal / gameInfo.rounds.length).toFixed(2);

        // Update player stats
        document.getElementById("highestName").textContent = gameInfo.highestScorePlayer.name;
        document.getElementById("highestScore").textContent = gameInfo.highestScorePlayer.score;
        document.getElementById("lowestName").textContent = gameInfo.lowestScorePlayer.name;
        document.getElementById("lowestScore").textContent = gameInfo.lowestScorePlayer.score;

        document.getElementById("highestBlackName").textContent = gameInfo.highestBlackCardsPlayer.name;
        document.getElementById("highestBlackScore").textContent = gameInfo.highestBlackCardsPlayer.count;
        document.getElementById("lowestBlackName").textContent = gameInfo.lowestBlackCardsPlayer.name;
        document.getElementById("lowestBlackScore").textContent = gameInfo.lowestBlackCardsPlayer.count;

        document.getElementById("highestRedName").textContent = gameInfo.highestRedCardsPlayer.name;
        document.getElementById("highestRedScore").textContent = gameInfo.highestRedCardsPlayer.count;
        document.getElementById("lowestRedName").textContent = gameInfo.lowestRedCardsPlayer.name;
        document.getElementById("lowestRedScore").textContent = gameInfo.lowestRedCardsPlayer.count;
    } catch (error) {
        console.error("Error fetching game info:", error);
    }
}
