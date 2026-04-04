<?php

require_once __DIR__ . '/../config/db.php';

$questions = [
    [
        'question_text' => 'A new project lands on your desk with very little structure. What do you do first?',
        'options' => [
            ['option_text' => 'Break the problem into pieces and test how each part works', 'category' => 'tech'],
            ['option_text' => 'Identify the opportunity, define a goal, and decide what to try first', 'category' => 'entrepreneurship'],
            ['option_text' => 'Ask who needs support most and what would make their situation easier', 'category' => 'healthcare'],
            ['option_text' => 'Sketch, draft, or prototype a version that captures the idea visually', 'category' => 'creative'],
        ],
    ],
    [
        'question_text' => 'Which type of feedback feels most rewarding to receive?',
        'options' => [
            ['option_text' => 'You made this clearer and easier for me to understand', 'category' => 'college'],
            ['option_text' => 'You helped me stick with it and improve my consistency', 'category' => 'fitness'],
            ['option_text' => 'You fixed something practical that was slowing everything down', 'category' => 'trades'],
            ['option_text' => 'You found a smarter way to make the system work', 'category' => 'tech'],
        ],
    ],
    [
        'question_text' => 'When learning something new, what style suits you best?',
        'options' => [
            ['option_text' => 'Experimenting, iterating, and learning from real-world responses', 'category' => 'entrepreneurship'],
            ['option_text' => 'Guided study, reflection, and explaining ideas back to others', 'category' => 'college'],
            ['option_text' => 'Hands-on repetition until the technique becomes reliable', 'category' => 'trades'],
            ['option_text' => 'Exploring references and reshaping ideas into something original', 'category' => 'creative'],
        ],
    ],
    [
        'question_text' => 'Which kind of responsibility sounds most natural to you?',
        'options' => [
            ['option_text' => 'Monitoring details carefully because mistakes could affect someone\'s wellbeing', 'category' => 'healthcare'],
            ['option_text' => 'Keeping people motivated and helping them follow through on a plan', 'category' => 'fitness'],
            ['option_text' => 'Coordinating priorities and making decisions when things are uncertain', 'category' => 'entrepreneurship'],
            ['option_text' => 'Maintaining accuracy and safety while working on physical systems', 'category' => 'trades'],
        ],
    ],
    [
        'question_text' => 'What kind of workday would drain you the least?',
        'options' => [
            ['option_text' => 'Solving puzzles quietly and improving processes or tools', 'category' => 'tech'],
            ['option_text' => 'Working with people through movement, coaching, and steady encouragement', 'category' => 'fitness'],
            ['option_text' => 'Helping learners think through choices and grow their confidence', 'category' => 'college'],
            ['option_text' => 'Building or repairing something tangible with your hands', 'category' => 'trades'],
        ],
    ],
    [
        'question_text' => 'If you were given one month to build something useful, what would you lean toward?',
        'options' => [
            ['option_text' => 'A tool that automates a task or reveals useful patterns', 'category' => 'tech'],
            ['option_text' => 'A small service or offer that could attract paying users', 'category' => 'entrepreneurship'],
            ['option_text' => 'A guide, workshop, or learning resource for others', 'category' => 'college'],
            ['option_text' => 'A visual campaign, video series, or brand concept', 'category' => 'creative'],
        ],
    ],
    [
        'question_text' => 'Which problem would you most want to solve?',
        'options' => [
            ['option_text' => 'Someone is confused and needs a concept explained in a better way', 'category' => 'college'],
            ['option_text' => 'Someone is physically struggling and needs a safer plan forward', 'category' => 'healthcare'],
            ['option_text' => 'Something mechanical or structural keeps failing and needs a practical fix', 'category' => 'trades'],
            ['option_text' => 'A message is not landing and needs a stronger creative angle', 'category' => 'creative'],
        ],
    ],
    [
        'question_text' => 'What does progress usually look like to you?',
        'options' => [
            ['option_text' => 'A measurable performance gain and stronger daily habits', 'category' => 'fitness'],
            ['option_text' => 'A cleaner workflow, fewer errors, and better technical results', 'category' => 'tech'],
            ['option_text' => 'A growing audience, more traction, or stronger demand', 'category' => 'entrepreneurship'],
            ['option_text' => 'A person recovering confidence, stability, or independence', 'category' => 'healthcare'],
        ],
    ],
    [
        'question_text' => 'Which setting would you be happiest returning to every week?',
        'options' => [
            ['option_text' => 'A studio-like space where you can shape ideas and polish outputs', 'category' => 'creative'],
            ['option_text' => 'A workshop or site where practical execution matters', 'category' => 'trades'],
            ['option_text' => 'A teaching or mentoring space where people are developing', 'category' => 'college'],
            ['option_text' => 'A clinic or care-focused environment where support is direct', 'category' => 'healthcare'],
        ],
    ],
    [
        'question_text' => 'How do you usually respond when something is not working?',
        'options' => [
            ['option_text' => 'Debug it systematically and isolate where the issue starts', 'category' => 'tech'],
            ['option_text' => 'Try a new angle quickly and see what the market or audience reacts to', 'category' => 'entrepreneurship'],
            ['option_text' => 'Adjust the routine, pacing, or coaching approach', 'category' => 'fitness'],
            ['option_text' => 'Refine the presentation, tone, or design until it feels right', 'category' => 'creative'],
        ],
    ],
    [
        'question_text' => 'Which kind of long-term growth sounds most appealing?',
        'options' => [
            ['option_text' => 'Becoming highly trusted for precision, safety, and practical skill', 'category' => 'trades'],
            ['option_text' => 'Becoming known for teaching, guidance, and clear thinking', 'category' => 'college'],
            ['option_text' => 'Becoming someone who can launch and grow new initiatives', 'category' => 'entrepreneurship'],
            ['option_text' => 'Becoming stronger at helping people improve health outcomes', 'category' => 'healthcare'],
        ],
    ],
    [
        'question_text' => 'What kind of challenge do you tolerate best?',
        'options' => [
            ['option_text' => 'Staying patient while diagnosing a technical issue with many moving parts', 'category' => 'tech'],
            ['option_text' => 'Repeating drills, tracking form, and improving performance gradually', 'category' => 'fitness'],
            ['option_text' => 'Handling uncertainty while convincing others to try something new', 'category' => 'entrepreneurship'],
            ['option_text' => 'Reworking creative details multiple times until the final version feels strong', 'category' => 'creative'],
        ],
    ],
    [
        'question_text' => 'If a friend asked for help, what would you most likely offer?',
        'options' => [
            ['option_text' => 'A practical fix or step-by-step repair approach', 'category' => 'trades'],
            ['option_text' => 'A training plan and encouragement to stay consistent', 'category' => 'fitness'],
            ['option_text' => 'A calm explanation and help comparing their options', 'category' => 'college'],
            ['option_text' => 'A way to improve their message, visuals, or presentation', 'category' => 'creative'],
        ],
    ],
    [
        'question_text' => 'Which outcome would make you most proud?',
        'options' => [
            ['option_text' => 'A tool you built saves people time or solves a repeated problem', 'category' => 'tech'],
            ['option_text' => 'Something you launched becomes sustainable and grows', 'category' => 'entrepreneurship'],
            ['option_text' => 'Someone you supported becomes healthier or more independent', 'category' => 'healthcare'],
            ['option_text' => 'A piece of work you created leaves a strong impression on people', 'category' => 'creative'],
        ],
    ],
    [
        'question_text' => 'Which phrase sounds most like you under pressure?',
        'options' => [
            ['option_text' => 'Let me check the details and find the root cause', 'category' => 'tech'],
            ['option_text' => 'Let me take the lead and decide the next move', 'category' => 'entrepreneurship'],
            ['option_text' => 'Let me make sure the person in front of me is safe and supported', 'category' => 'healthcare'],
            ['option_text' => 'Let me adapt the explanation so everyone can follow', 'category' => 'college'],
        ],
    ],
    [
        'question_text' => 'What do you find easier to stay consistent with?',
        'options' => [
            ['option_text' => 'Practice routines, physical goals, and progress tracking', 'category' => 'fitness'],
            ['option_text' => 'Learning technical tools and improving efficiency over time', 'category' => 'tech'],
            ['option_text' => 'Developing a craft through hands-on repetition and precision', 'category' => 'trades'],
            ['option_text' => 'Improving the tone, look, and feel of creative work', 'category' => 'creative'],
        ],
    ],
    [
        'question_text' => 'If you joined a team tomorrow, where would you probably add value fastest?',
        'options' => [
            ['option_text' => 'Structuring information and helping others understand what matters', 'category' => 'college'],
            ['option_text' => 'Spotting practical bottlenecks and fixing them hands-on', 'category' => 'trades'],
            ['option_text' => 'Creating momentum, pitching ideas, and organising execution', 'category' => 'entrepreneurship'],
            ['option_text' => 'Supporting people through care, reassurance, and careful attention', 'category' => 'healthcare'],
        ],
    ],
    [
        'question_text' => 'Which kind of project would you volunteer for first?',
        'options' => [
            ['option_text' => 'Improving an app, spreadsheet, or system so it works better', 'category' => 'tech'],
            ['option_text' => 'Helping plan an event, campaign, or small venture', 'category' => 'entrepreneurship'],
            ['option_text' => 'Designing visuals, content, or storytelling for an idea', 'category' => 'creative'],
            ['option_text' => 'Running a session that helps people build confidence and skill', 'category' => 'college'],
        ],
    ],
    [
        'question_text' => 'What kind of evidence do you trust most when deciding what to do next?',
        'options' => [
            ['option_text' => 'User behavior, experiments, and whether people actually respond', 'category' => 'entrepreneurship'],
            ['option_text' => 'Measured progress, form, and repeatable performance changes', 'category' => 'fitness'],
            ['option_text' => 'Clinical observations, care needs, and risk considerations', 'category' => 'healthcare'],
            ['option_text' => 'How clearly the message lands and whether the final output feels compelling', 'category' => 'creative'],
        ],
    ],
    [
        'question_text' => 'Which statement feels most true about how you like to work?',
        'options' => [
            ['option_text' => 'I enjoy improving things quietly through logic and iteration', 'category' => 'tech'],
            ['option_text' => 'I enjoy helping people learn and make sense of their next steps', 'category' => 'college'],
            ['option_text' => 'I enjoy working physically and seeing concrete results from effort', 'category' => 'trades'],
            ['option_text' => 'I enjoy guiding people toward stronger habits and performance', 'category' => 'fitness'],
        ],
    ],
];

$pdo->beginTransaction();
$pdo->exec('DELETE FROM quiz_options');
$pdo->exec('DELETE FROM quiz_questions');

$questionStmt = $pdo->prepare('INSERT INTO quiz_questions (question_text, sort_order) VALUES (:question_text, :sort_order)');
$optionStmt = $pdo->prepare('INSERT INTO quiz_options (question_id, option_text, category, score_value) VALUES (:question_id, :option_text, :category, 1)');

foreach ($questions as $index => $question) {
    $questionStmt->execute([
        'question_text' => $question['question_text'],
        'sort_order' => $index + 1,
    ]);

    $questionId = (int)$pdo->lastInsertId();

    foreach ($question['options'] as $option) {
        $optionStmt->execute([
            'question_id' => $questionId,
            'option_text' => $option['option_text'],
            'category' => $option['category'],
        ]);
    }
}

$pdo->commit();

echo json_encode([
    'questions' => (int)$pdo->query('SELECT COUNT(*) FROM quiz_questions')->fetchColumn(),
    'options' => (int)$pdo->query('SELECT COUNT(*) FROM quiz_options')->fetchColumn(),
], JSON_PRETTY_PRINT);
