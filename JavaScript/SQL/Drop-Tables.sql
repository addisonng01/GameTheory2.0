-- Drop child tables first to avoid foreign key constraint violations
DROP TABLE IF EXISTS session_partner;
DROP TABLE IF EXISTS partner_pairing;
DROP TABLE IF EXISTS trade_partnership;
DROP TABLE IF EXISTS group_trade;
DROP TABLE IF EXISTS round_score;
DROP TABLE IF EXISTS group_student;
DROP TABLE IF EXISTS group_pairing;
DROP TABLE IF EXISTS prod_goal_profile;
DROP TABLE IF EXISTS player_round_history;
DROP TABLE IF EXISTS rb_player_score;
DROP TABLE IF EXISTS student_submission;
DROP TABLE IF EXISTS student_game_session;
DROP TABLE IF EXISTS session_instance;

-- Drop tables with direct foreign key references to profiles and sessions
DROP TABLE IF EXISTS login_credential;
DROP TABLE IF EXISTS red_black_session;
DROP TABLE IF EXISTS wheat_steel_session;
DROP TABLE IF EXISTS oligopoly_session;
DROP TABLE IF EXISTS game_session;

-- Drop game-specific configuration and supporting tables
DROP TABLE IF EXISTS question_for_student;
DROP TABLE IF EXISTS red_black_card_params;
DROP TABLE IF EXISTS wheat_steel_params;
DROP TABLE IF EXISTS oligopoly_params;

-- Drop profiles and core catalog
DROP TABLE IF EXISTS student_profile;
DROP TABLE IF EXISTS teacher_profile;
DROP TABLE IF EXISTS game_catalog;