const express = require('express');
const mysql = require('mysql2');
const cors = require('cors');
const dotenv = require('dotenv');
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
app.get('/reset-database', (req, res) => {
    const sqlResetScript = `
        -- Drop existing tables
        DROP TABLE IF EXISTS session_partner;
        DROP TABLE IF EXISTS partner_pairing;
        DROP TABLE IF EXISTS question_submission;
        DROP TABLE IF EXISTS rb_player_score;
        DROP TABLE IF EXISTS player_round_history;
        DROP TABLE IF EXISTS student_game_session;
        DROP TABLE IF EXISTS red_black_session;
        DROP TABLE IF EXISTS question_for_student;
        DROP TABLE IF EXISTS login_credential;
        DROP TABLE IF EXISTS group_student;
        DROP TABLE IF EXISTS round_score;
        DROP TABLE IF EXISTS group_trade;
        DROP TABLE IF EXISTS trade_partnership;
        DROP TABLE IF EXISTS group_pairing;
        DROP TABLE IF EXISTS prod_goal_profile;
        DROP TABLE IF EXISTS session_instance;
        DROP TABLE IF EXISTS student_profile;
        DROP TABLE IF EXISTS game_session;
        DROP TABLE IF EXISTS red_black_card_param;
        DROP TABLE IF EXISTS wheat_steel_param;
        DROP TABLE IF EXISTS game_catalog;
        DROP TABLE IF EXISTS teacher_profile;

        -- Create tables (Game catalog, Red/Black Card Parameters, etc.)
        CREATE TABLE game_catalog (
            game_id INT PRIMARY key AUTO_INCREMENT,
            game_title VARCHAR(50)
        );

        CREATE TABLE red_black_card_param (
            red_black_id INT PRIMARY key AUTO_INCREMENT,
            game_id INT,
            game_instruction_txt VARCHAR(4000),
            red_point_value SMALLINT,
            black_point_value SMALLINT,
            total_round_num TINYINT,
            hidden_round_num TINYINT,
            FOREIGN KEY (game_id) REFERENCES game_catalog(game_id) ON DELETE SET NULL
        );

        CREATE TABLE question_for_student (
            question_id INT PRIMARY KEY AUTO_INCREMENT,
            game_id INT,
            question_txt VARCHAR(4000),
            create_dt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            mod_dt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (game_id) REFERENCES game_catalog(game_id) ON DELETE SET NULL
        );

        CREATE TABLE wheat_steel_param (
            wheat_steel_id INT PRIMARY KEY AUTO_INCREMENT,
            game_id INT,
            game_instruction_txt VARCHAR(4000),
            total_round_num TINYINT,
            no_trade_round_num TINYINT,
            FOREIGN KEY (game_id) REFERENCES game_catalog(game_id) ON DELETE SET NULL
        );

        CREATE TABLE teacher_profile (
            teacher_id INT PRIMARY KEY AUTO_INCREMENT,
            first_nm VARCHAR(30),
            last_nm VARCHAR(30),
            email VARCHAR(100),
            organization_nm VARCHAR(100)
        );

        CREATE TABLE game_session (
            game_session_id INT PRIMARY KEY AUTO_INCREMENT,
            teacher_id INT,
            game_id INT,
            red_black_param INT,
            wheat_steel_param INT,
            is_active CHAR(1),
            create_dt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            expiration_dt TIMESTAMP NOT NULL,
            join_game_code VARCHAR(12),
            FOREIGN KEY (teacher_id) REFERENCES teacher_profile(teacher_id),
            FOREIGN KEY (game_id) REFERENCES game_catalog(game_id),
            FOREIGN KEY (red_black_param) REFERENCES red_black_card_param(red_black_id),
            FOREIGN KEY (wheat_steel_param) REFERENCES wheat_steel_param(wheat_steel_id)
        );

        -- Insert data into the tables
        INSERT INTO game_catalog (game_title)
            VALUES ('Class Simulation: Red Card Black Card'),
                   ('Class Simulation: Wheat & Steel');

        INSERT INTO red_black_card_param (game_id, game_instruction_txt, red_point_value, black_point_value, total_round_num, hidden_round_num)
            VALUES (1, 'You have a red card and a black card. In the first four rounds...', 50, 150, 12, 4);

        INSERT INTO question_for_student (game_id, question_txt)
            VALUES (1, 'Describe your strategy at the beginning of this exercise'),
                   (1, 'Describe how it changed near the end of the exercise');
    `;

    db.query(sqlResetScript, (err, result) => {
        if (err) {
            console.error('Error executing SQL script:', err);
            return res.status(500).send('Failed to reset database');
        }
        res.send('Database has been reset successfully');
    });
});

// Start server
const server = app.listen(PORT, () => {
    console.log(`Server is running on port ${PORT}`);
});

module.exports = { app, server };
