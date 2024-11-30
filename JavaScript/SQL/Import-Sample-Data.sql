-- Enable foreign key checks
SET FOREIGN_KEY_CHECKS = 0;

-- Insert sample data into `game_catalog`
INSERT INTO game_catalog (game_title) VALUES
('Red Black Card Game'),
('Wheat and Steel'),
('Quiz Adventure');

-- Insert sample data into `red_black_card_params`
INSERT INTO red_black_card_params (game_id, game_instruction_txt, red_point_value, black_point_value, total_round_num, hidden_round_num) VALUES
(1, 'Red Black Game Instructions', 10, -5, 10, 3);

-- Insert sample data into `wheat_steel_params`
INSERT INTO wheat_steel_params (game_id, total_rounds, max_trade_rounds, initial_wheat, initial_steel) VALUES
(2, 15, 5, 100, 100);

-- Insert sample data into `oligopoly_params`
INSERT INTO oligopoly_params (game_id, total_rounds, base_price, max_competitors) VALUES
(3, 12, 20.00, 4);

-- Insert sample data into `teacher_profile`
INSERT INTO teacher_profile (first_nm, last_nm, email, organization_nm) VALUES
('Alice', 'Smith', 'alice.smith@school.edu', 'Riverdale High'),
('Bob', 'Johnson', 'bob.johnson@school.edu', 'Springfield Middle School');

-- Insert sample data into `student_profile`
INSERT INTO student_profile (first_nm, last_nm, email) VALUES
('Charlie', 'Brown', 'charlie.brown@school.edu'),
('Lucy', 'van Pelt', 'lucy.vanpelt@school.edu'),
('Linus', 'van Pelt', 'linus.vanpelt@school.edu');

-- Insert sample data into `login_credential`
INSERT INTO login_credential (teacher_id, username, user_password) VALUES
(1, 'asmith', 'securepassword'),
(2, 'bjohnson', 'anothersecurepassword');

-- Insert into `game_session` table first to ensure we have valid `game_session_id`
INSERT INTO game_session (teacher_id, game_id, red_black_param, wheat_steel_param, oligopoly_param, is_active, expiration_dt, join_game_code) VALUES
(1, 3, NULL, NULL, 1, 'Y', '2024-12-31 23:59:59', 'OLIGO123'); 

-- Insert sample data into `question_for_student`
INSERT INTO question_for_student (game_id, question_txt) VALUES
(1, 'What did you learn from the game?'),
(2, 'How would you improve your strategy?');

-- Now insert data into game-specific session tables after `game_session`
-- Insert sample data into `red_black_session`
INSERT INTO red_black_session (game_session_id, rounds_complete, round_concluded) VALUES
(1, 5, 'Y');

-- Insert sample data into `wheat_steel_session`
INSERT INTO wheat_steel_session (game_session_id, rounds_complete, round_concluded) VALUES
(1, 5, 'Y');

-- Insert sample data into `oligopoly_session`
INSERT INTO oligopoly_session (game_session_id, rounds_complete, round_concluded) VALUES
(LAST_INSERT_ID(), 8, 'N');

-- Insert sample data into `session_instance`
INSERT INTO session_instance (student_id, teacher_id, login_attempts, active_session, expiration_dt) VALUES
(1, 1, 3, 'Y', '2024-12-31 23:59:59'),
(2, 2, 1, 'N', '2024-11-30 23:59:59');

-- Insert data into `student_game_session` using valid `game_session_id`
INSERT INTO student_game_session (game_session_id, session_id, user_id, active_participant) VALUES
(LAST_INSERT_ID(), 1, 1, 'Y'),  -- Use LAST_INSERT_ID() for the newly inserted game_session_id
(LAST_INSERT_ID(), 2, 2, 'N');  -- Again, use LAST_INSERT_ID() to refer to the most recent session

-- Insert sample data into `student_submission` with session_id
INSERT INTO student_submission (session_id, student_id, game_session_id, submission_content) VALUES
(1, 1, 1, 'Teamwork is important.'),
(2, 2, 2, 'Focus on trading.');

-- Insert sample data into `prod_goal_profile`
INSERT INTO prod_goal_profile (profile_nm, wheat_goal_num, steel_goal_num, round_resource_num, wheat_rec_resource_num, steel_rec_resource_num) VALUES
('Profile A', 10, 20, 5, 3, 4);

-- Insert sample data into `group_pairing`
INSERT INTO group_pairing (profile_id, group_name, trading_complete, total_steel_cons, total_wheat_cons) VALUES
(1, 'Group A', 'Y', 15, 10);

-- Insert sample data into `group_student`
INSERT INTO group_student (session_id, user_id, group_id, is_leader) VALUES
(1, 1, 1, 'Y'),
(2, 2, 1, 'N');

-- Insert sample data into `player_round_history`
INSERT INTO player_round_history (user_id, session_id, link_id, round_num, card_selected, round_score) VALUES
(1, 1, 1, 1, 'Red', 10),
(2, 2, 2, 2, 'Black', -5);

-- Insert sample data into `round_score`
INSERT INTO round_score (group_id, round_num, wheat_produced, steel_produced, wheat_traded, steel_traded, wheat_cons, steel_cons) VALUES
(1, 1, 10, 20, 5, 10, 2, 3);

-- Insert sample data into `partner_pairing`
INSERT INTO partner_pairing (round_num) VALUES
(1), (2);

-- Insert sample data into `session_partner`
INSERT INTO session_partner (link_id, pairing_id, round_num) VALUES
(1, 1, 1),
(2, 2, 2);

-- Insert sample data into `rb_player_score`
INSERT INTO rb_player_score (session_id, user_id, score_total) VALUES
(1, 1, 50),
(2, 2, 40);

-- Insert sample data into `trade_partnership`
INSERT INTO trade_partnership (round_num, sent_total, sent_type) VALUES
(1, 10, 'Wheat'),
(2, 15, 'Steel');

-- Insert sample data into `group_trade`
INSERT INTO group_trade (group_id, trade_detail) VALUES
(1, '10 Wheat sent to Group B');

-- Verify data
SELECT * FROM game_session WHERE game_id = 3;
SELECT * FROM oligopoly_session;
SELECT * FROM student_submission;
