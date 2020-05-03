SELECT 
sm.sub_module,
ROUND(COUNT(c.is_correct)) AS score,
(SELECT 
items 
FROM
question_details 
WHERE exam_schedule_id = te.exam_schedule_id
GROUP BY items) AS total_items

FROM choices AS c 

LEFT JOIN trainee_questions AS tq
ON tq.choice_id = c.choice_id

LEFT JOIN trainee_exams AS te
ON te.trainee_exam_id = tq.trainee_exam_id

LEFT JOIN exam_schedules AS es
ON es.exam_schedule_id = te.exam_schedule_id

LEFT JOIN trainees AS t
ON t.trainee_id = te.trainee_id

LEFT JOIN dealers AS d
ON d.dealer_id = t.dealer_id

LEFT JOIN questions AS q
ON q.question_id = tq.question_id

LEFT JOIN sub_modules AS sm
ON sm.sub_module_id = q.sub_module_id

WHERE te.exam_schedule_id = 38
AND d.dealer_id = 8
AND c.is_correct = 1

GROUP BY q.sub_module_id