<?php
require_once('../../config.php');
require_login();

$context = context_course::instance($COURSE->id);
require_capability('mod/digitalisation:view', $context);

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['positions'])) {
    throw new invalid_parameter_exception('Invalid data received.');
}

global $DB, $USER;

foreach ($data['positions'] as $position) {
    $record = new stdClass();
    $record->userid = $USER->id;
    $record->labelid = $position['id'];
    $record->left = $position['left'];
    $record->top = $position['top'];
    $record->timecreated = time();

    $DB->insert_record('digitalisation_positions', $record);
}

echo json_encode(['success' => true]);
?>
