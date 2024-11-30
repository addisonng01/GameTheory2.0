const request = require('supertest');
const { app, server } = require('../server');
const mysql = require('mysql2/promise');

describe('API Routes', () => {
    let db;

    beforeAll(async () => {
        // Initialize database connection for testing
        db = await mysql.createConnection({
            host: process.env.DB_HOST,
            user: process.env.DB_USER,
            password: process.env.DB_PASSWORD,
            database: process.env.DB_NAME
        });
    });

    afterAll(async () => {
        // Close database and server connections after tests
        await db.end();
        server.close();
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
        });
    });

    // GET /api/wheatSteel/:gameId/updates tests
    describe('GET /api/wheatSteel/:gameId/updates', () => {
        it('should fetch updates for a specific game ID', async () => {
            const gameId = 1; // Example game ID
            const res = await request(app).get(`/api/wheatSteel/${gameId}/updates`);
            expect(res.statusCode).toBe(200);
            expect(res.body).toHaveProperty('updates');
            expect(Array.isArray(res.body.updates)).toBe(true);
        });
    });

    // POST /api/wheatSteel/:gameId/trade tests
    describe('POST /api/wheatSteel/:gameId/trade', () => {
        it('should post a valid trade request', async () => {
            const gameId = 1; // Example game ID
            const tradeRequest = {
                tradeTeamId: 2,
                tradeWheat: 10,
                tradeSteel: 5
            };
            const res = await request(app)
                .post(`/api/wheatSteel/${gameId}/trade`)
                .send(tradeRequest);
            expect(res.statusCode).toBe(200);
            expect(res.body.success).toBe(true);
        });

        it('should return an error for invalid trade request', async () => {
            const gameId = 1; // Example game ID
            const invalidTradeRequest = {}; // Missing trade details
            const res = await request(app)
                .post(`/api/wheatSteel/${gameId}/trade`)
                .send(invalidTradeRequest);
            expect(res.statusCode).toBe(200);
            expect(res.body.success).toBe(false);
            expect(res.body.error).toBe('Invalid trade request');
        });
    });

    // Environment Variables Test
    describe('Environment Variables', () => {
        it('should load environment variables correctly', () => {
            expect(process.env.DB_HOST).toBeDefined();
            expect(process.env.DB_USER).toBeDefined();
            expect(process.env.DB_PASSWORD).toBeDefined();
            expect(process.env.DB_NAME).toBeDefined();
        });
    });
});
