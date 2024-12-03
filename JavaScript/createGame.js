require('dotenv').config();

const BASE_URL = process.env.BASE_URL || 'http://localhost:3000/api'; // Adjust the base URL if needed

document.getElementById("createGameForm").addEventListener("submit", async (event) => {
    event.preventDefault();
    const gameTitle = document.getElementById("gameTitle").value;

    try {
        const response = await fetch(`${BASE_URL}/games`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ game_title: gameTitle })
        });

        if (!response.ok) {
            throw new Error(`Failed to create game: ${response.statusText}`);
        }

        const result = await response.json();
        alert(`Game created successfully! Game ID: ${result.gameId}`);
        document.getElementById("gameTitle").value = ""; // Clear the input field
    } catch (error) {
        console.error("Error creating game:", error);
        alert("Failed to create game. Please try again.");
    }
});

// Function to get Red-Black Game Statistics
const getRedBlackGameStatistics = async () => {
    try {
        const response = await fetch(`${BASE_URL}/rb_player_scores`);
        if (!response.ok) {
            throw new Error(`Error fetching Red-Black Game Statistics: ${response.statusText}`);
        }
        const { totalPlayers, highScore, lowScore, avgScore } = await response.json();

        console.log(`Total Players: ${totalPlayers}`);
        console.log(`Highest Score: ${highScore}`);
        console.log(`Lowest Score: ${lowScore}`);
        console.log(`Average Score: ${avgScore}`);
    } catch (error) {
        console.error(error.message);
    }
};

// Function to get Wheat-Steel Game Statistics
const getWheatSteelGameStatistics = async () => {
    try {
        const response = await fetch(`${BASE_URL}/round_scores?game_id=2`);
        if (!response.ok) {
            throw new Error(`Error fetching Wheat-Steel Game Statistics: ${response.statusText}`);
        }
        const { wheatTotal, wheatAverage, steelTotal, steelAverage } = await response.json();

        console.log(`Total Wheat Produced: ${wheatTotal}`);
        console.log(`Average Wheat Produced: ${wheatAverage}`);
        console.log(`Total Steel Produced: ${steelTotal}`);
        console.log(`Average Steel Produced: ${steelAverage}`);
    } catch (error) {
        console.error(error.message);
    }
};

// Function to fetch game titles and dates
const getGameTitlesAndDates = async () => {
    try {
        const response = await fetch(`${BASE_URL}/games`);
        if (!response.ok) {
            throw new Error(`Error fetching game titles and dates: ${response.statusText}`);
        }
        const games = await response.json();

        games.forEach(game => {
            console.log(`Game Name: ${game.game_title}, Date: ${game.create_dt}`);
        });
    } catch (error) {
        console.error(error.message);
    }
};

// Execute all functions
const fetchAllGameStatistics = async () => {
    await getRedBlackGameStatistics();
    await getWheatSteelGameStatistics();
    await getGameTitlesAndDates();
};

document.addEventListener("DOMContentLoaded", async () => {
  // Start the fetch process
  fetchAllGameStatistics();
});
