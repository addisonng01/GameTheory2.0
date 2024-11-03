const axios = require('axios');
require('dotenv').config();

const BASE_URL = process.env.BASE_URL || 'http://localhost:3000/api'; // Adjust the base URL if needed

// Function to get Red-Black Game Statistics
const getRedBlackGameStatistics = async () => {
  try {
    const response = await axios.get(`${BASE_URL}/rb_player_scores`);
    const { totalPlayers, highScore, lowScore, avgScore } = response.data;

    console.log(`Total Players: ${totalPlayers}`);
    console.log(`Highest Score: ${highScore}`);
    console.log(`Lowest Score: ${lowScore}`);
    console.log(`Average Score: ${avgScore}`);
  } catch (error) {
    console.error('Error fetching Red-Black Game Statistics:', error.message);
  }
};

// Function to get Wheat-Steel Game Statistics
const getWheatSteelGameStatistics = async () => {
  try {
    const response = await axios.get(`${BASE_URL}/round_scores?game_id=2`);
    const { wheatTotal, wheatAverage, steelTotal, steelAverage } = response.data;

    console.log(`Total Wheat Produced: ${wheatTotal}`);
    console.log(`Average Wheat Produced: ${wheatAverage}`);
    console.log(`Total Steel Produced: ${steelTotal}`);
    console.log(`Average Steel Produced: ${steelAverage}`);
  } catch (error) {
    console.error('Error fetching Wheat-Steel Game Statistics:', error.message);
  }
};

// Function to fetch game titles and dates
const getGameTitlesAndDates = async () => {
  try {
    const response = await axios.get(`${BASE_URL}/games`);
    const games = response.data;

    games.forEach(game => {
      console.log(`Game Name: ${game.game_title}, Date: ${game.create_dt}`);
    });
  } catch (error) {
    console.error('Error fetching game titles and dates:', error.message);
  }
};

// Execute all functions
const fetchAllGameStatistics = async () => {
  await getRedBlackGameStatistics();
  await getWheatSteelGameStatistics();
  await getGameTitlesAndDates();
};

// Start the fetch process
fetchAllGameStatistics();