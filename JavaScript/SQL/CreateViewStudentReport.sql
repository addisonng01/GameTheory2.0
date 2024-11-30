CREATE VIEW student_submission_report AS
SELECT 
    ss.submission_id,
    sp.first_nm AS student_first_name,
    sp.last_nm AS student_last_name,
    gs.game_id,
    gs.join_game_code,
    ss.submission_content,
    ss.submitted_dt
FROM 
    student_submission ss
JOIN 
    student_profile sp ON ss.student_id = sp.student_id
JOIN 
    game_session gs ON ss.game_session_id = gs.game_session_id;