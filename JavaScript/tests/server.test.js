const request = require('supertest');
const { app, server } = require('../server');
const mysql = require('mysql2');

describe('API Routes', () => {
    let db;

    beforeAll(done => {
        // Initialize database connection for testing
        db = mysql.createConnection({
            host: 'localhost',
            user: 'root',
            password: 'j&hghasfdk(&5H53HG&^8&*%^$&jnb%&*(&^%$hFGHJKJHGFCV234567%&%',
            database: 'css_game_theory'
        });

        db.connect(done);
    });

    afterAll(done => {
        // Close database and server connections after tests
        db.end();
        server.close(done);
    });

    // GET /api/games tests
    describe('GET /api/games', () => {
        it('should fetch all games', async () => {
            const res = await request(app).get('/api/games');
            expect(res.statusCode).toBe(200);
            expect(Array.isArray(res.body)).toBe(true);
        });
    });

    // POST /api/games tests
    describe('POST /api/games', () => {
        it('should insert a new game into game_catalog', async () => {
            const newGame = { game_title: 'New Test Game' };
            const res = await request(app).post('/api/games').send(newGame);
            expect(res.statusCode).toBe(201);
            expect(res.body).toHaveProperty('gameId');

            // Verify the game was added to the database
            db.query('SELECT * FROM game_catalog WHERE game_id = ?', [res.body.gameId], (err, results) => {
                expect(results.length).toBe(1);
                expect(results[0].game_title).toBe(newGame.game_title);
            });
        });
    });

    // GET /api/redblackparams tests
    describe('GET /api/redblackparams', () => {
        it('should fetch all Red/Black card parameters', async () => {
            const res = await request(app).get('/api/redblackparams');
            expect(res.statusCode).toBe(200);
            expect(Array.isArray(res.body)).toBe(true);
        });
    });

    // POST /api/redblackparams tests
    describe('POST /api/redblackparams', () => {
        it('should insert new Red/Black card parameters', async () => {
            const redBlackParam = {
                game_id: 1,
                game_instruction_txt: 'Test instructions',
                red_point_value: 10,
                black_point_value: -10,
                total_round_num: 5,
                hidden_round_num: 2
            };
            const res = await request(app).post('/api/redblackparams').send(redBlackParam);
            expect(res.statusCode).toBe(201);
            expect(res.body).toHaveProperty('redBlackId');

            // Verify the red/black card parameters were added to the database
            db.query('SELECT * FROM red_black_card_param WHERE red_black_id = ?', [res.body.redBlackId], (err, results) => {
                expect(results.length).toBe(1);
                expect(results[0].game_instruction_txt).toBe(redBlackParam.game_instruction_txt);
                expect(results[0].red_point_value).toBe(redBlackParam.red_point_value);
                expect(results[0].black_point_value).toBe(redBlackParam.black_point_value);
            });
        });
    });

    // GET /api/teachers tests
    describe('GET /api/teachers', () => {
        it('should fetch all teacher profiles', async () => {
            const res = await request(app).get('/api/teachers');
            expect(res.statusCode).toBe(200);
            expect(Array.isArray(res.body)).toBe(true);
        });
    });

    // POST /api/teachers tests
    describe('POST /api/teachers', () => {
        it('should insert a new teacher profile', async () => {
            const newTeacher = {
                first_nm: 'John',
                last_nm: 'Doe',
                email: 'johndoe@example.com',
                organization_nm: 'Test University'
            };
            const res = await request(app).post('/api/teachers').send(newTeacher);
            expect(res.statusCode).toBe(201);
            expect(res.body).toHaveProperty('teacherId');

            // Verify the teacher was added to the database
            db.query('SELECT * FROM teacher_profile WHERE teacher_id = ?', [res.body.teacherId], (err, results) => {
                expect(results.length).toBe(1);
                expect(results[0].first_nm).toBe(newTeacher.first_nm);
                expect(results[0].last_nm).toBe(newTeacher.last_nm);
                expect(results[0].email).toBe(newTeacher.email);
                expect(results[0].organization_nm).toBe(newTeacher.organization_nm);
            });
        });
    });

    // Additional tests for other endpoints can be added here...

});
