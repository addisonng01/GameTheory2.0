require('dotenv').config();

const BASE_URL = process.env.BASE_URL || 'http://localhost:3000/api';

document.addEventListener("DOMContentLoaded", async () => {
    try {
        const gameInfo = await fetchRedBlackCardInfo();
        if (gameInfo) {
            updateCardInfo(gameInfo);
            updatePlayerStats(gameInfo);
        }
    } catch (error) {
        console.error("Error initializing Red Card Black Card information:", error);
    }
});

// Fetch Red Card Black Card game information
async function fetchRedBlackCardInfo() {
    try {
        const response = await fetch(`${BASE_URL}/red_black_card_params`);
        if (!response.ok) {
            throw new Error(`Failed to fetch Red Card Black Card parameters: ${response.statusText}`);
        }
        return await response.json(); // Assume the API returns a well-structured object with all necessary data
    } catch (error) {
        console.error("Error fetching Red Card Black Card info:", error);
        return null;
    }
}

// Update card information in the DOM
function updateCardInfo(gameInfo) {
    document.getElementById("blackTotal").textContent = gameInfo.totalBlackCards || 0;
    document.getElementById("blackAverage").textContent = ((gameInfo.totalBlackCards || 0) / gameInfo.totalRounds).toFixed(2);
    document.getElementById("redTotal").textContent = gameInfo.totalRedCards || 0;
    document.getElementById("redAverage").textContent = ((gameInfo.totalRedCards || 0) / gameInfo.totalRounds).toFixed(2);
}

// Update player statistics in the DOM
function updatePlayerStats(gameInfo) {
    document.getElementById("highestName").textContent = gameInfo.highestScorePlayer?.player_name || 'N/A';
    document.getElementById("highestScore").textContent = gameInfo.highestScorePlayer?.total_score || 0;
    document.getElementById("lowestName").textContent = gameInfo.lowestScorePlayer?.player_name || 'N/A';
    document.getElementById("lowestScore").textContent = gameInfo.lowestScorePlayer?.total_score || 0;

    document.getElementById("highestBlackName").textContent = gameInfo.highestBlackCardPlayer?.player_name || 'N/A';
    document.getElementById("highestBlackScore").textContent = gameInfo.highestBlackCardPlayer?.black_card_count || 0;
    document.getElementById("lowestBlackName").textContent = gameInfo.lowestBlackCardPlayer?.player_name || 'N/A';
    document.getElementById("lowestBlackScore").textContent = gameInfo.lowestBlackCardPlayer?.black_card_count || 0;

    document.getElementById("highestRedName").textContent = gameInfo.highestRedCardPlayer?.player_name || 'N/A';
    document.getElementById("highestRedScore").textContent = gameInfo.highestRedCardPlayer?.red_card_count || 0;
    document.getElementById("lowestRedName").textContent = gameInfo.lowestRedCardPlayer?.player_name || 'N/A';
    document.getElementById("lowestRedScore").textContent = gameInfo.lowestRedCardPlayer?.red_card_count || 0;
}
