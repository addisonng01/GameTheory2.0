const express = require('express');
const mysql = require('mysql2');
const cors = require('cors');
const dotenv = require('dotenv').config();;
const fs = require('fs');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors());
app.use(express.json());

// MySQL connection setup
const db = mysql.createConnection({
    host: process.env.DB_HOST,
    user: process.env.DB_USER,
    password: process.env.DB_PASSWORD,
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
app.get('/api/redblackparams', (req, res) => {
    db.query('SELECT * FROM red_black_card_param', (err, results) => {
        if (err) {
            console.error('Database query error:', err);
            return res.status(500).send('Database query failed');
        }
        res.status(200).json(results);
    });
});

const gameUpdates = {};

// Game update endpoint, waits for page refresh and tracks them for change
app.get('/api/wheatSteel/:gameId/updates', (req, res) => {
    const gameId = req.params.gameId;
    //Retrieves updates
    const updates = gameUpdates[gameId] || [];
    res.json({ updates });
    //Clear list
    gameUpdates[gameId] = [];
});

//adds updates from polling to list
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

    // Validate trade and add update (this probably needs to be changed to match values from game)
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

app.post('/api/redblackparams', (req, res) => {
    const { game_id, game_instruction_txt, red_point_value, black_point_value, total_round_num, hidden_round_num } = req.body;
    const query = 'INSERT INTO red_black_card_param (game_id, game_instruction_txt, red_point_value, black_point_value, total_round_num, hidden_round_num) VALUES (?, ?, ?, ?, ?, ?)';
    db.query(query, [game_id, game_instruction_txt, red_point_value, black_point_value, total_round_num, hidden_round_num], (err, results) => {
        if (err) {
            console.error('Database insert error:', err);
            return res.status(500).send('Failed to insert Red/Black card parameters');
        }
        res.status(201).json({ redBlackId: results.insertId });
    });
});

// GET /api/teachers - Fetch all teacher profiles
app.get('/api/teachers', (req, res) => {
    db.query('SELECT * FROM teacher_profile', (err, results) => {
        if (err) {
            console.error('Database query error:', err);  // Log error for debugging
            return res.status(500).send('Database query failed');
        }
        res.status(200).json(results);
    });
});

// POST /api/teachers - Insert a new teacher profile
app.post('/api/teachers', (req, res) => {
    const { first_nm, last_nm, email, organization_nm } = req.body;
    const query = `
        INSERT INTO teacher_profile (first_nm, last_nm, email, organization_nm)
        VALUES (?, ?, ?, ?)
    `;
    db.query(query, [first_nm, last_nm, email, organization_nm], (err, results) => {
        if (err) {
            console.error('Database insert error:', err);  // Log error for debugging
            return res.status(500).send('Failed to insert teacher profile');
        }
        res.status(201).json({ teacherId: results.insertId });
    });
});

// Reset database endpoint
app.get('/reset-database', async (req, res) => {
    try {
        const sqlDir = path.resolve('./SQL', ''); // Directory where SQL files are located
        const sqlFiles = await fs.promises.readdir(sqlDir); // List all files in the SQL directory

        // Loop through each SQL file and execute its contents
        for (const file of sqlFiles) {
            try {
                console.log('Executing SQL file:', file); // Debug log
                const sqlText = await fs.promises.readFile(path.resolve(sqlDir, file), 'utf8'); // Read SQL file content

                // Execute SQL using mysql2's promise-based API
                await db.query(sqlText.trim()); // Ensure SQL is trimmed to avoid trailing spaces or newlines
                console.log(`Successfully executed ${file}`); // Debug log
            } catch (err) {
                console.error(`Error executing SQL file ${file}:`, err.message); // Log any errors
            }
        }

        res.send('Database has been reset successfully');
    } catch (err) {
        console.error('Error reading SQL directory or files:', err.message); // Log any directory or file errors
        res.status(500).send('Error reading SQL directory or files');
    }
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
app.post('/api/submit-responses', (req, res) => {
    const { responses } = req.body;
    if (!responses || !Array.isArray(responses)) {
        return res.status(400).send('Invalid request data');
    }

    const query = `
        INSERT INTO question_submission (question_id, response_txt, session_id, user_id, game_session_id)
        VALUES (?, ?, ?, ?, ?)
    `;
    const paramsArray = responses.map(({ question_id, response_txt }) => [
        question_id,
        response_txt,
        1, // Replace with real `session_id`
        1, // Replace with real `user_id`
        1  // Replace with real `game_session_id`
    ]);

    const promises = paramsArray.map(params =>
        new Promise((resolve, reject) => {
            db.query(query, params, (err, results) => {
                if (err) return reject(err);
                resolve(results);
            });
        })
    );

    Promise.all(promises)
        .then(() => res.status(200).send('Responses submitted successfully'))
        .catch(err => {
            console.error(err);
            res.status(500).send('Error submitting responses');
        });
});

// Get all responses for Red Card Black Card
app.get('/api/get-responses', (req, res) => {
    const redCardGameId = 1; // Replace with the actual game_id for Red Card Black Card

    const query = `
        SELECT 
            q.question_txt,
            s.first_nm AS student_first_name,
            s.last_nm AS student_last_name,
            qs.response_txt,
            qs.submit_dt
        FROM question_submission qs
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
