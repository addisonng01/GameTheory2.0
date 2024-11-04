const express = require('express');
const mysql = require('mysql2');
const bodyParser = require('body-parser');

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(bodyParser.json());

// MySQL connection
const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: 'j&hghasfdk(&5H53HG&^8&*%^$&jnb%&*(&^%$hFGHJKJHGFCV234567%&%',
    database: 'css_game_theory'
});

// Connect to the database
db.connect(err => {
    if (err) {
        console.error('Database connection failed:', err);
        return;
    }
    console.log('Connected to the MySQL database.');
});

// Game Catalog Routes
app.get('/api/games', (req, res) => {
    db.query('SELECT * FROM game_catalog', (err, results) => {
        if (err) return res.status(500).send(err);
        res.json(results);
    });
});

app.post('/api/games', (req, res) => {
    const { game_title } = req.body;
    db.query('INSERT INTO game_catalog (game_title) VALUES (?)', [game_title], (err, results) => {
        if (err) return res.status(500).send(err);
        res.status(201).json({ gameId: results.insertId });
    });
});

// Red/Black Card Parameters Routes
app.get('/api/redblackparams', (req, res) => {
    db.query('SELECT * FROM red_black_card_param', (err, results) => {
        if (err) return res.status(500).send(err);
        res.json(results);
    });
});

app.post('/api/redblackparams', (req, res) => {
    const { game_id, game_instruction_txt, red_point_value, black_point_value, total_round_num, hidden_round_num } = req.body;
    db.query('INSERT INTO red_black_card_param (game_id, game_instruction_txt, red_point_value, black_point_value, total_round_num, hidden_round_num) VALUES (?, ?, ?, ?, ?, ?)',
        [game_id, game_instruction_txt, red_point_value, black_point_value, total_round_num, hidden_round_num],
        (err, results) => {
            if (err) return res.status(500).send(err);
            res.status(201).json({ redBlackId: results.insertId });
        }
    );
});

// Wheat/Steel Parameters Routes
app.get('/api/wheatsteelparams', (req, res) => {
    db.query('SELECT * FROM wheat_steel_param', (err, results) => {
        if (err) return res.status(500).send(err);
        res.json(results);
    });
});

app.post('/api/wheatsteelparams', (req, res) => {
    const { game_id, game_instruction_txt, total_round_num, no_trade_round_num } = req.body;
    db.query('INSERT INTO wheat_steel_param (game_id, game_instruction_txt, total_round_num, no_trade_round_num) VALUES (?, ?, ?, ?)',
        [game_id, game_instruction_txt, total_round_num, no_trade_round_num],
        (err, results) => {
            if (err) return res.status(500).send(err);
            res.status(201).json({ wheatSteelId: results.insertId });
        }
    );
});

// Teacher Profile Routes
app.get('/api/teachers', (req, res) => {
    db.query('SELECT * FROM teacher_profile', (err, results) => {
        if (err) return res.status(500).send(err);
        res.json(results);
    });
});

app.post('/api/teachers', (req, res) => {
    const { first_nm, last_nm, email, organization_nm } = req.body;
    db.query('INSERT INTO teacher_profile (first_nm, last_nm, email, organization_nm) VALUES (?, ?, ?, ?)',
        [first_nm, last_nm, email, organization_nm],
        (err, results) => {
            if (err) return res.status(500).send(err);
            res.status(201).json({ teacherId: results.insertId });
        }
    );
});

// Game Session Routes
app.get('/api/gamesessions', (req, res) => {
    db.query('SELECT * FROM game_session', (err, results) => {
        if (err) return res.status(500).send(err);
        res.json(results);
    });
});

app.post('/api/gamesessions', (req, res) => {
    const { teacher_id, game_id, red_black_param, wheat_steel_param, expiration_dt, join_game_code } = req.body;
    db.query('INSERT INTO game_session (teacher_id, game_id, red_black_param, wheat_steel_param, is_active, expiration_dt, join_game_code) VALUES (?, ?, ?, ?, "Y", ?, ?)',
        [teacher_id, game_id, red_black_param, wheat_steel_param, expiration_dt, join_game_code],
        (err, results) => {
            if (err) return res.status(500).send(err);
            res.status(201).json({ sessionId: results.insertId });
        }
    );
});

// Red/Black Session Routes
app.get('/api/redblacksessions', (req, res) => {
    db.query('SELECT * FROM red_black_session', (err, results) => {
        if (err) return res.status(500).send(err);
        res.json(results);
    });
});

app.post('/api/redblacksessions', (req, res) => {
    const { game_session_id, rounds_complete, round_concluded } = req.body;
    db.query('INSERT INTO red_black_session (game_session_id, rounds_complete, round_concluded) VALUES (?, ?, ?)',
        [game_session_id, rounds_complete, round_concluded],
        (err, results) => {
            if (err) return res.status(500).send(err);
            res.status(201).json({ rbSessionId: results.insertId });
        }
    );
});

// Login Credential Routes
app.get('/api/logins', (req, res) => {
    db.query('SELECT * FROM login_credential', (err, results) => {
        if (err) return res.status(500).send(err);
        res.json(results);
    });
});

app.post('/api/logins', (req, res) => {
    const { teacher_id, username, user_password } = req.body;
    db.query('INSERT INTO login_credential (teacher_id, username, user_password) VALUES (?, ?, ?)',
        [teacher_id, username, user_password],
        (err, results) => {
            if (err) return res.status(500).send(err);
            res.status(201).json({ credentialId: results.insertId });
        }
    );
});

// Student Profile Routes
app.get('/api/students', (req, res) => {
    db.query('SELECT * FROM student_profile', (err, results) => {
        if (err) return res.status(500).send(err);
        res.json(results);
    });
});

app.post('/api/students', (req, res) => {
    const { first_nm, last_nm, email } = req.body;
    db.query('INSERT INTO student_profile (first_nm, last_nm, email) VALUES (?, ?, ?)',
        [first_nm, last_nm, email],
        (err, results) => {
            if (err) return res.status(500).send(err);
            res.status(201).json({ studentId: results.insertId });
        }
    );
});

// Session Instance Routes
app.get('/api/sessions', (req, res) => {
    db.query('SELECT * FROM session_instance', (err, results) => {
        if (err) return res.status(500).send(err);
        res.json(results);
    });
});

app.post('/api/sessions', (req, res) => {
    const { student_id, teacher_id, login_attempts, active_session, expiration_dt } = req.body;
    db.query('INSERT INTO session_instance (student_id, teacher_id, login_attempts, active_session, expiration_dt) VALUES (?, ?, ?, ?, ?)',
        [student_id, teacher_id, login_attempts, active_session, expiration_dt],
        (err, results) => {
            if (err) return res.status(500).send(err);
            res.status(201).json({ sessionId: results.insertId });
        }
    );
});

// Student Game Session Routes
app.get('/api/studentgamesessions', (req, res) => {
    db.query('SELECT * FROM student_game_session', (err, results) => {
        if (err) return res.status(500).send(err);
        res.json(results);
    });
});

app.post('/api/studentgamesessions', (req, res) => {
    const { game_session_id, session_id, user_id, active_participant } = req.body;
    db.query('INSERT INTO student_game_session (game_session_id, session_id, user_id, active_participant) VALUES (?, ?, ?, ?)',
        [game_session_id, session_id, user_id, active_participant],
        (err, results) => {
            if (err) return res.status(500).send(err);
            res.status(201).json({ linkId: results.insertId });
        }
    );
});

// Question Submission Routes
app.get('/api/questionSubmissions', (req, res) => {
    db.query('SELECT * FROM question_submission', (err, results) => {
        if (err) return res.status(500).send(err);
        res.json(results);
      });
  });
  
  app.post('/api/questionSubmissions', (req, res) => {
      const { student_id, session_id, question_text, submission_time } = req.body;
      db.query('INSERT INTO question_submission (student_id, session_id, question_text, submission_time) VALUES (?, ?, ?, ?)',
          [student_id, session_id, question_text, submission_time],
          (err, results) => {
              if (err) return res.status(500).send(err);
              res.status(201).json({ submissionId: results.insertId });
          }
      );
  });
  
  // Server start
  app.listen(PORT, () => {
      console.log(`Server is running on port ${PORT}`);
  });