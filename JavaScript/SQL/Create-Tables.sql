-- Create tables in dependency order

-- Table: game_catalog
CREATE TABLE game_catalog (
    game_id INT PRIMARY KEY AUTO_INCREMENT,
    game_title VARCHAR(50)
);

-- Table: red_black_card_params
CREATE TABLE red_black_card_params (
    red_black_id INT PRIMARY KEY AUTO_INCREMENT,
    game_id INT,
    game_instruction_txt VARCHAR(4000),
    red_point_value SMALLINT,
    black_point_value SMALLINT,
    total_round_num TINYINT,
    hidden_round_num TINYINT,
    FOREIGN KEY (game_id) REFERENCES game_catalog(game_id) ON DELETE SET NULL
);

-- Table: wheat_steel_params
CREATE TABLE wheat_steel_params (
    ws_param_id INT PRIMARY KEY AUTO_INCREMENT,
    game_id INT,
    total_rounds TINYINT,
    max_trade_rounds TINYINT,
    initial_wheat SMALLINT,
    initial_steel SMALLINT,
    FOREIGN KEY (game_id) REFERENCES game_catalog(game_id) ON DELETE SET NULL
);

-- Table: oligopoly_params
CREATE TABLE oligopoly_params (
    oligopoly_param_id INT PRIMARY KEY AUTO_INCREMENT,
    game_id INT,
    total_rounds TINYINT,
    base_price DECIMAL(10,2),
    max_competitors TINYINT,
    FOREIGN KEY (game_id) REFERENCES game_catalog(game_id) ON DELETE SET NULL
);

-- Table: question_for_student
CREATE TABLE question_for_student (
    question_id INT PRIMARY KEY AUTO_INCREMENT,
    game_id INT,
    question_txt VARCHAR(4000),
    create_dt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    mod_dt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (game_id) REFERENCES game_catalog(game_id) ON DELETE SET NULL
);

-- Table: teacher_profile
CREATE TABLE teacher_profile (
    teacher_id INT PRIMARY KEY AUTO_INCREMENT,
    first_nm VARCHAR(30),
    last_nm VARCHAR(30),
    email VARCHAR(100),
    organization_nm VARCHAR(100)
);

-- Table: student_profile
CREATE TABLE student_profile (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    first_nm VARCHAR(30),
    last_nm VARCHAR(30),
    email VARCHAR(100) UNIQUE NOT NULL,
    create_dt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    mod_dt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: login_credential
CREATE TABLE login_credential (
    credential_id INT PRIMARY KEY AUTO_INCREMENT,
    teacher_id INT,
    username VARCHAR(30),
    user_password VARCHAR(100),
    FOREIGN KEY (teacher_id) REFERENCES teacher_profile(teacher_id) ON DELETE SET NULL
);

-- Table: game_session
CREATE TABLE game_session (
    game_session_id INT PRIMARY KEY AUTO_INCREMENT,
    teacher_id INT,
    game_id INT,
    red_black_param INT,
    wheat_steel_param INT,
    oligopoly_param INT,
    is_active CHAR(1),
    create_dt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expiration_dt TIMESTAMP NOT NULL,
    join_game_code VARCHAR(12),
    FOREIGN KEY (teacher_id) REFERENCES teacher_profile(teacher_id),
    FOREIGN KEY (game_id) REFERENCES game_catalog(game_id),
    FOREIGN KEY (red_black_param) REFERENCES red_black_card_params(red_black_id),
    FOREIGN KEY (wheat_steel_param) REFERENCES wheat_steel_params(ws_param_id),
    FOREIGN KEY (oligopoly_param) REFERENCES oligopoly_params(oligopoly_param_id)
);

-- Creating the student_submission table with session_id column
CREATE TABLE student_submission (
    submission_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    student_id INT(11) NOT NULL,
    game_session_id INT(11) NOT NULL,
    session_id INT(11) NULL,  -- Add this column if you want to track sessions
    submission_content TEXT NOT NULL,
    submitted_dt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: red_black_session
CREATE TABLE red_black_session (
    rb_session_id INT PRIMARY KEY AUTO_INCREMENT,
    game_session_id INT,
    rounds_complete TINYINT,
    round_concluded CHAR(1),
    FOREIGN KEY (game_session_id) REFERENCES game_session(game_session_id) ON DELETE SET NULL
);

-- Table: wheat_steel_session
CREATE TABLE wheat_steel_session (
    ws_session_id INT PRIMARY KEY AUTO_INCREMENT,
    game_session_id INT,
    rounds_complete TINYINT,
    round_concluded CHAR(1),
    FOREIGN KEY (game_session_id) REFERENCES game_session(game_session_id) ON DELETE SET NULL
);

-- Table: oligopoly_session
CREATE TABLE oligopoly_session (
    oligopoly_session_id INT PRIMARY KEY AUTO_INCREMENT,
    game_session_id INT,
    rounds_complete TINYINT,
    round_concluded CHAR(1),
    FOREIGN KEY (game_session_id) REFERENCES game_session(game_session_id) ON DELETE SET NULL
);

-- Table: session_instance
CREATE TABLE session_instance (
    session_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    teacher_id INT,
    login_attempts TINYINT,
    active_session CHAR(1),
    create_dt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expiration_dt TIMESTAMP NOT NULL,
    FOREIGN KEY (teacher_id) REFERENCES teacher_profile(teacher_id) ON DELETE SET NULL,
    FOREIGN KEY (student_id) REFERENCES student_profile(student_id) ON DELETE SET NULL
);

-- Table: student_game_session
CREATE TABLE student_game_session (
    link_id INT PRIMARY KEY AUTO_INCREMENT,
    game_session_id INT,
    session_id INT,
    user_id INT,
    active_participant CHAR(1),
    FOREIGN KEY (game_session_id) REFERENCES game_session(game_session_id) ON DELETE SET NULL,
    FOREIGN KEY (session_id) REFERENCES session_instance(session_id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES session_instance(student_id) ON DELETE SET NULL
);

-- Table: partner_pairing
CREATE TABLE partner_pairing (
    pairing_id INT PRIMARY KEY AUTO_INCREMENT,
    round_num TINYINT,
    create_dt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: session_partner
CREATE TABLE session_partner (
    session_partner_id INT PRIMARY KEY AUTO_INCREMENT,
    link_id INT,
    pairing_id INT,
    round_num TINYINT,
    FOREIGN KEY (link_id) REFERENCES student_game_session(link_id),
    FOREIGN KEY (pairing_id) REFERENCES partner_pairing(pairing_id)
);

-- Table: player_round_history
CREATE TABLE player_round_history (
    round_history_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    session_id INT,
    link_id INT,
    round_num TINYINT,
    card_selected VARCHAR(5),
    round_score SMALLINT,
    FOREIGN KEY (user_id) REFERENCES session_instance(student_id),
    FOREIGN KEY (session_id) REFERENCES session_instance(session_id),
    FOREIGN KEY (link_id) REFERENCES student_game_session(link_id)
);

-- Table: rb_player_score
CREATE TABLE rb_player_score (
    score_id INT PRIMARY KEY AUTO_INCREMENT,
    session_id INT,
    user_id INT,
    score_total SMALLINT,
    FOREIGN KEY (user_id) REFERENCES session_instance(student_id),
    FOREIGN KEY (session_id) REFERENCES session_instance(session_id)
);

-- Table: prod_goal_profile
CREATE TABLE prod_goal_profile (
    profile_id INT PRIMARY KEY AUTO_INCREMENT,
    profile_nm VARCHAR(30),
    wheat_goal_num SMALLINT,
    steel_goal_num SMALLINT,
    round_resource_num SMALLINT,
    wheat_rec_resource_num SMALLINT,
    steel_rec_resource_num SMALLINT
);

-- Table: group_pairing
CREATE TABLE group_pairing (
    group_id INT PRIMARY KEY AUTO_INCREMENT,
    profile_id INT,
    group_name VARCHAR(30),
    trading_complete CHAR(1),
    total_steel_cons SMALLINT,
    total_wheat_cons SMALLINT,
    FOREIGN KEY (profile_id) REFERENCES prod_goal_profile(profile_id)
);

-- Table: group_student
CREATE TABLE group_student (
    gs_id INT PRIMARY KEY AUTO_INCREMENT,
    session_id INT,
    user_id INT,
    group_id INT,
    is_leader CHAR(1),
    FOREIGN KEY (user_id) REFERENCES session_instance(student_id),
    FOREIGN KEY (session_id) REFERENCES session_instance(session_id),
    FOREIGN KEY (group_id) REFERENCES group_pairing(group_id)
);

-- Table: round_score
CREATE TABLE round_score (
    round_score_id INT PRIMARY KEY AUTO_INCREMENT,
    group_id INT,
    round_num TINYINT,
    wheat_produced SMALLINT,
    steel_produced SMALLINT,
    wheat_traded SMALLINT,
    steel_traded SMALLINT,
    wheat_cons SMALLINT,
    steel_cons SMALLINT,
    FOREIGN KEY (group_id) REFERENCES group_pairing(group_id)
);

-- Table: trade_partnership
CREATE TABLE trade_partnership (
    partnership_id INT PRIMARY KEY AUTO_INCREMENT,
    round_num TINYINT,
    sent_total SMALLINT,
    sent_type VARCHAR(10)
);

-- Table: group_trade
CREATE TABLE group_trade (
    trade_id INT PRIMARY KEY AUTO_INCREMENT,
    group_id INT,
    trade_detail VARCHAR(255),
    FOREIGN KEY (group_id) REFERENCES group_pairing(group_id)
);
