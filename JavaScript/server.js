const express = require('express');
const mysql = require('mysql2');
const cors = require('cors');
const dotenv = require('dotenv');
const fs = require('fs');
const path = require('path');
dotenv.config();

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors());
app.use(express.json());

// MySQL connection setup
const db = mysql.createConnection({
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || 'j&hghasfdk(&5H53HG&^8&*%^$&jnb%&*(&^%$hFGHJKJHGFCV234567%&%',
    database: process.env.DB_NAME || 'css_game_theory'
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

// Start server
const server = app.listen(PORT, () => {
    console.log(`Server is running on port ${PORT}`);
});

module.exports = { app, server };
