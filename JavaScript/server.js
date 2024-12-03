const express = require('express');
const mysql = require('mysql2');
const cors = require('cors');
const dotenv = require('dotenv').config();
const fs = require('fs');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors());
app.use(express.json());

// MySQL connection setup
const db = mysql.createConnection({
    host: process.env.DB_HOST || "localhost",
    user: process.env.DB_USER,
    password: process.env.DB_PASS,
    database: process.env.DB_NAME
});

db.connect(err => {
    if (err) {
        console.error('Error connecting to the database:', err);
        process.exit(1);
    }
    console.log('Connected to the MySQL database');
});

// API endpoints
app.get('/api/games', (req, res) => {
    db.query('SELECT * FROM game_catalog', (error, results) => {
        if (error) return res.status(500).json({ error });
        res.status(200).json(results);
    });
});

app.post('/api/games', (req, res) => {
    const { game_title } = req.body;
    db.query('INSERT INTO game_catalog (game_title) VALUES (?)', [game_title], (error, results) => {
        if (error) return res.status(500).json({ error });
        res.status(201).json({ gameId: results.insertId });
    });
});

// Additional endpoints for Red/Black card parameters, Wheat/Steel parameters, and teacher profiles

// GET Red/Black card parameters
app.get('/api/redblackparams', (req, res) => {
    db.query('SELECT * FROM red_black_card_params', (err, results) => {
        if (err) {
            console.error('Database query error:', err);
            return res.status(500).send('Database query failed');
        }
        res.status(200).json(results);
    });
});

// POST Red/Black card parameters
app.post('/api/redblackparams', (req, res) => {
    const { game_id, game_instruction_txt, red_point_value, black_point_value, total_round_num, hidden_round_num } = req.body;
    const query = 'INSERT INTO red_black_card_params (game_id, game_instruction_txt, red_point_value, black_point_value, total_round_num, hidden_round_num) VALUES (?, ?, ?, ?, ?, ?)';
    db.query(query, [game_id, game_instruction_txt, red_point_value, black_point_value, total_round_num, hidden_round_num], (err, results) => {
        if (err) {
            console.error('Database insert error:', err);
            return res.status(500).send('Failed to insert Red/Black card parameters');
        }
        res.status(201).json({ redBlackId: results.insertId });
    });
});

// Endpoint to fetch Red-Black game statistics
app.get('/api/rb_player_scores', (req, res) => {
    const query = `
        SELECT 
            COUNT(DISTINCT student_game_session.student_profile_id) AS totalPlayers,
            MAX(rb_player_score.score) AS highScore,
            MIN(rb_player_score.score) AS lowScore,
            AVG(rb_player_score.score) AS avgScore
        FROM rb_player_score
        JOIN student_game_session 
            ON rb_player_score.session_instance_id = student_game_session.session_instance_id
    `;
    db.query(query, (error, results) => {
        if (error) {
            console.error('Database query error:', error);
            return res.status(500).json({ error: 'Failed to fetch Red-Black game statistics' });
        }
        res.status(200).json(results[0]);
    });
});

// Endpoint to fetch Wheat-Steel game statistics
app.get('/api/round_scores', (req, res) => {
    const { game_id } = req.query;
    if (!game_id) {
        return res.status(400).json({ error: 'Game ID is required' });
    }

    const query = `
        SELECT 
            SUM(round_score.wheat_score) AS wheatTotal,
            AVG(round_score.wheat_score) AS wheatAverage,
            SUM(round_score.steel_score) AS steelTotal,
            AVG(round_score.steel_score) AS steelAverage
        FROM round_score
        WHERE round_score.game_session_id IN (
            SELECT game_session_id FROM game_session WHERE game_catalog_id = ?
        )
    `;
    db.query(query, [game_id], (error, results) => {
        if (error) {
            console.error('Database query error:', error);
            return res.status(500).json({ error: 'Failed to fetch Wheat-Steel game statistics' });
        }
        res.status(200).json(results[0]);
    });
});

app.get('/api/game-dates', async (req, res) => {
    const { gameType } = req.query;

    if (!gameType) {
        return res.status(400).json({ message: 'Missing gameType parameter' });
    }

    let query;
    if (gameType === 'Red Card Black Card') {
        query = 'SELECT game_date FROM red_black_card_games ORDER BY game_date DESC';
    } else if (gameType === 'Wheat & Steel') {
        query = 'SELECT game_date FROM wheat_steel_games ORDER BY game_date DESC';
    } else {
        return res.status(400).json({ message: 'Invalid gameType' });
    }

    try {
        const [dates] = await db.execute(query);
        if (dates.length === 0) {
            return res.status(404).json({ message: `No dates found for ${gameType}` });
        }
        res.status(200).json(dates.map(date => date.game_date));
    } catch (error) {
        console.error('Error fetching game dates:', error);
        res.status(500).json({ message: 'Error fetching game dates' });
    }
});

// POST /api/oil-game-round - Submit firm decisions for a given oil game round
app.post('/api/oil-game-round', async (req, res) => {
    const { currentRound, firmDecisions, sessionId } = req.body;

    if (!currentRound || !firmDecisions || firmDecisions.length !== 4 || !sessionId) {
        return res.status(400).json({ message: 'Invalid data' });
    }

    try {
        // Store decisions in player_round_history table (assuming this table tracks decisions and outcomes)
        await db.execute('INSERT INTO player_round_history (session_id, round_number, firm_decisions) VALUES (?, ?, ?)', [sessionId, currentRound, JSON.stringify(firmDecisions)]);

        // Calculate market price and profits (using existing logic from 'oligopoly_params' or 'wheat_steel_params')
        const totalOutput = firmDecisions.reduce((sum, decision) => sum + decision, 0);
        const marketPrice = Math.max(100 - totalOutput, 0); // Example price formula
        const profits = firmDecisions.map(q => (marketPrice - 20) * q);

        // Store the results in the round_score table
        await db.execute('INSERT INTO round_score (session_id, round_number, total_output, market_price, profits) VALUES (?, ?, ?, ?, ?)', [sessionId, currentRound, totalOutput, marketPrice, JSON.stringify(profits)]);

        // Return results
        res.status(200).json({
            totalOutput,
            marketPrice,
            profits
        });
    } catch (error) {
        console.error('Error processing oil game round:', error);
        res.status(500).json({ message: 'Error processing oil game round' });
    }
});


// GET /api/oil-game-progress - Fetch current round and firm decisions for the oil game
app.get('/api/oil-game-progress', async (req, res) => {
    const { sessionId } = req.query;
    if (!sessionId) {
        return res.status(400).json({ message: 'Session ID is required' });
    }

    try {
        // Fetch the current round from the game_session table
        const [sessionData] = await db.execute('SELECT current_round FROM game_session WHERE session_id = ?', [sessionId]);

        if (sessionData.length === 0) {
            return res.status(404).json({ message: 'Game session not found' });
        }

        const currentRound = sessionData[0].current_round;

        // Fetch the latest decisions from the player_round_history table for the current session and round
        const [roundData] = await db.execute('SELECT firm_decisions FROM player_round_history WHERE session_id = ? AND round_number = ?', [sessionId, currentRound]);

        const firmDecisions = roundData.length > 0 ? JSON.parse(roundData[0].firm_decisions) : [];

        res.status(200).json({ currentRound, firmDecisions });
    } catch (error) {
        console.error('Error fetching oil game progress:', error);
        res.status(500).json({ message: 'Error fetching oil game progress' });
    }
});

// GET /api/oligopoly/submissions - Fetch student submissions for the current round
app.get('/api/oligopoly/submissions', async (req, res) => {
    const { round } = req.query;
    if (!round) {
        return res.status(400).json({ message: 'Round is required' });
    }

    try {
        // Fetch submissions from the player_round_history table for the current round
        const [submissions] = await db.execute('SELECT student_id, decisions FROM player_round_history WHERE round_number = ?', [round]);

        if (submissions.length === 0) {
            return res.status(404).json({ message: 'No submissions found for this round' });
        }

        const decisions = submissions.map(submission => submission.decisions);
        res.status(200).json({ decisions });
    } catch (error) {
        console.error('Error fetching submissions:', error);
        res.status(500).json({ message: 'Error fetching submissions' });
    }
});

// POST /api/oligopoly/approve - Approve the next round
app.post('/api/oligopoly/approve', async (req, res) => {
    const { round } = req.body;
    if (!round) {
        return res.status(400).json({ message: 'Round is required' });
    }

    try {
        // Increment the round number in the oligopoly_session table
        await db.execute('UPDATE oligopoly_session SET current_round = ? WHERE session_id = ?', [round + 1, sessionId]);

        // Check if we have reached the end of the total rounds
        if (round >= totalRounds) {
            return res.status(200).json({ message: 'Game completed' });
        }

        res.status(200).json({ message: 'Next round approved' });
    } catch (error) {
        console.error('Error approving the next round:', error);
        res.status(500).json({ message: 'Error approving the next round' });
    }
});

// Game update endpoint
const gameUpdates = {};
app.get('/api/wheatSteel/:gameId/updates', (req, res) => {
    const gameId = req.params.gameId;
    const updates = gameUpdates[gameId] || [];
    res.json({ updates });
    gameUpdates[gameId] = [];
});

// Adds updates from polling to list
function addTradeUpdate(gameId, update) {
    if (!gameUpdates[gameId]) {
        gameUpdates[gameId] = [];
    }
    gameUpdates[gameId].push(update);
}

// Trade endpoint
app.post('/api/wheatSteel/:gameId/trade', (req, res) => {
    const { tradeTeamId, tradeWheat, tradeSteel } = req.body;
    const gameId = req.params.gameId;

    if (tradeWheat && tradeSteel) {
        addTradeUpdate(gameId, {
            type: 'tradeRequest',
            fromTeamId: req.teamId,
            toTeamId: tradeTeamId,
            tradeWheat,
            tradeSteel
        });
        res.json({ success: true });
    } else {
        res.json({ success: false, error: 'Invalid trade request' });
    }
});

// GET all teacher profiles
app.get('/api/teachers', (req, res) => {
    db.query('SELECT * FROM teacher_profile', (err, results) => {
        if (err) {
            console.error('Database query error:', err);
            return res.status(500).send('Database query failed');
        }
        res.status(200).json(results);
    });
});

// POST new teacher profile
app.post('/api/teachers', (req, res) => {
    const { first_nm, last_nm, email, organization_nm } = req.body;
    const query = `
        INSERT INTO teacher_profile (first_nm, last_nm, email, organization_nm)
        VALUES (?, ?, ?, ?)
    `;
    db.query(query, [first_nm, last_nm, email, organization_nm], (err, results) => {
        if (err) {
            console.error('Database insert error:', err);
            return res.status(500).send('Failed to insert teacher profile');
        }
        res.status(201).json({ teacherId: results.insertId });
    });
});

// Get the question for the student
app.get('/api/get-question', (req, res) => {
    db.query('SELECT question_txt FROM question_for_student LIMIT 1', (err, results) => {
        if (err) {
            console.error(err);
            return res.status(500).send('Error retrieving question');
        }
        res.json(results[0]);
    });
});

// Submit multiple student responses
app.post('/api/submit-responses', async (req, res) => {
    const { responses } = req.body;

    if (!responses || !Array.isArray(responses)) {
        return res.status(400).send('Invalid request data');
    }

    try {
        const sessionToken = req.session?.token;
        if (!sessionToken) {
            return res.status(401).send('User not authenticated');
        }

        const [sessionData] = await db.query(
            `SELECT si.session_id, si.student_id AS user_id, sgs.game_session_id
             FROM session_instance si
             JOIN student_game_session sgs ON si.session_id = sgs.session_id
             WHERE si.active_session = 'Y' AND si.session_id = ?`,
            [sessionToken]
        );

        if (!sessionData || sessionData.length === 0) {
            return res.status(404).send('Session not found');
        }

        const { session_id, user_id, game_session_id } = sessionData;

        const query = `
            INSERT INTO student_submission (question_id, response_txt, session_id, user_id, game_session_id)
            VALUES (?, ?, ?, ?, ?)
        `;
        const paramsArray = responses.map(({ question_id, response_txt }) => [
            question_id,
            response_txt,
            session_id,
            user_id,
            game_session_id,
        ]);

        const promises = paramsArray.map(params =>
            db.query(query, params)
        );

        await Promise.all(promises);

        res.status(200).send('Responses submitted successfully');
    } catch (error) {
        console.error('Error submitting responses:', error);
        res.status(500).send('Error submitting responses');
    }
});

// Get all responses for Red Card Black Card
app.get('/api/get-responses', (req, res) => {
    const redCardGameId = 1;

    const query = `
        SELECT q.question_txt, s.first_nm AS student_first_name, s.last_nm AS student_last_name,
               qs.response_txt, qs.submit_dt
        FROM student_submission qs
        JOIN question_for_student q ON qs.question_id = q.question_id
        JOIN student_profile s ON qs.user_id = s.student_id
        WHERE q.game_id = ?
        ORDER BY q.question_id, s.last_nm, s.first_nm;
    `;

    db.query(query, [redCardGameId], (err, results) => {
        if (err) {
            console.error(err);
            return res.status(500).send('Error retrieving responses');
        }
        res.json(results);
    });
});

// Start server
const server = app.listen(PORT, () => {
    console.log(`Server is running on port ${PORT}`);
});

module.exports = { app, server };
