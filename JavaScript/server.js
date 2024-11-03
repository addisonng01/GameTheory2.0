const express = require('express');
const http = require('http');
const socketIO = require('socket.io');
const mysql = require('mysql');

// App setup
const app = express();
const server = http.createServer(app);
const io = socketIO(server);

// Static files
app.use(express.static(__dirname + '/HTML/admin'));
app.use('/JavaScript', express.static(__dirname + '/JavaScript'));

// MySQL Database setup
const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',         // Use your MySQL username
    password: 'password', // Use your MySQL password
    database: 'your_database' // Use your actual database name
});

// Connect to MySQL
db.connect((err) => {
    if (err) {
        console.error('Error connecting to MySQL:', err.message);
    } else {
        console.log('Connected to MySQL database');
    }
});

// API endpoint to fetch game dates
app.get('/api/game-dates', (req, res) => {
    const gameType = req.query.gameType;
    const query = 'SELECT game_date FROM games WHERE game_type = ? ORDER BY game_date ASC';

    db.query(query, [gameType], (error, results) => {
        if (error) {
            console.error('Error fetching game dates:', error.message);
            return res.status(500).json({ error: 'Database query error' });
        }
        // Return the dates in a JSON array
        res.json(results.map(result => result.game_date));
    });
});

// Socket setup for real-time communication
io.on('connection', (socket) => {
    console.log('New user connected');

    socket.on('chat message', (msg) => {
        io.emit('chat message', msg); // Broadcasts the message to all connected clients
    });

    socket.on('disconnect', () => {
        console.log('User disconnected');
    });
});

// Server listening
server.listen(5500, () => {
    console.log('Server is listening on port 5500');
});
