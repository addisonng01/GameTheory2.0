const mysql = require('mysql2');
require('dotenv').config();

const connection = mysql.createConnection({
  host: process.env.DB_HOST,
  user: process.env.DB_USER,
  password: process.env.DB_PASS,
  database: process.env.DB_NAME,
});

// Red-Black Game Statistics
connection.query(
    'SELECT COUNT(*) AS totalPlayers, MAX(score_total) AS highScore, MIN(score_total) AS lowScore, AVG(score_total) AS avgScore FROM rb_player_score;',
    (err, results) => {
      if (err) throw err;
      const { totalPlayers, highScore, lowScore, avgScore } = results[0];
      
      console.log(`Total Players: ${totalPlayers}`);
      console.log(`Highest Score: ${highScore}`);
      console.log(`Lowest Score: ${lowScore}`);
      console.log(`Average Score: ${avgScore}`);
    }
  );
  

  // Wheat-Steel Game Statistics
connection.query(
    `SELECT SUM(wheat_produced) AS wheatTotal, AVG(wheat_produced) AS wheatAverage,
            SUM(steel_produced) AS steelTotal, AVG(steel_produced) AS steelAverage
     FROM round_score WHERE game_id = 2;`,
    (err, results) => {
      if (err) throw err;
      const { wheatTotal, wheatAverage, steelTotal, steelAverage } = results[0];
  
      console.log(`Total Wheat Produced: ${wheatTotal}`);
      console.log(`Average Wheat Produced: ${wheatAverage}`);
      console.log(`Total Steel Produced: ${steelTotal}`);
      console.log(`Average Steel Produced: ${steelAverage}`);
    }
  );

  // Fetch game title and date
connection.query(
    'SELECT game_title, create_dt FROM game_catalog JOIN game_session ON game_catalog.game_id = game_session.game_id;',
    (err, results) => {
      if (err) throw err;
      results.forEach(game => {
        console.log(`Game Name: ${game.game_title}, Date: ${game.create_dt}`);
      });
    }
  );
  
  connection.end();