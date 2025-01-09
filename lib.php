<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Library of interface functions and constants.
 *
 * @package     mod_digitalisation
 * @copyright   2024 Fruition Horticulture <info@fruition.ac.nz>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function digitalisation_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
        case FEATURE_SHOW_DESCRIPTION:
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        default:
            return null;
    }
}

function digitalisation_add_instance($instance, $mform = null) {
    global $DB;
    $instance->timecreated = time();
    $instance->timemodified = $instance->timecreated; // Ensure timemodified is set
    return $DB->insert_record('digitalisation', $instance);
}

function digitalisation_update_instance($instance, $mform = null) {
    global $DB;
    $instance->timemodified = time();
    $instance->id = $instance->instance;
    return $DB->update_record('digitalisation', $instance);
}

function digitalisation_get_available_games() {
    return [
        'hazards.html' => get_string('game_hazards', 'mod_digitalisation'),
        'hazardLearn.html' => get_string('game_hazardlearn', 'mod_digitalisation'),
        'accidentForm.html' => get_string('game_accidentform', 'mod_digitalisation'),
        'dnd.html' => get_string('game_dnd', 'mod_digitalisation'),
        'noActivity.html' => get_string('game_noactivity', 'mod_digitalisation'),
        'pcbuAssign.html' => get_string('game_pcbuAssign', 'mod_digitalisation'),
        'pcbuLearn.html' => get_string('game_pcbuLearn', 'mod_digitalisation'),
        'control.html' => get_string('game_control', 'mod_digitalisation'),
        'reportIncident.html' => get_string('game_reportIncident', 'mod_digitalisation'),
    ];
}

function digitalisation_save_assignment($data) {
    global $DB, $USER;

    $record = new stdClass();
    $record->courseid = $data->courseid; // Course ID
    $record->userid = $USER->id; // Current student submitting the assignment
    $record->title = $data->title; // Title of the assignment
    $record->content = $data->content['text']; // Content from editor
    $record->timecreated = time(); // Timestamp
    $record->timemodified = time(); // Timestamp for modification (same as creation for now)

    return $DB->insert_record('digitalisation_assignments', $record);
}

function digitalisation_get_user_grades($digitalisation, $userid = 0) {
    global $DB;

    $grades = [];
    $params = ['digitalisationid' => $digitalisation->id];
    
    if ($userid) {
        $params['userid'] = $userid;
    }

    $records = $DB->get_records('digitalisation_grades', $params);
    foreach ($records as $record) {
        $grades[$record->userid] = (object) [
            'userid' => $record->userid,
            'rawgrade' => $record->grade
        ];
    }

    return $grades;
}

// Safely assign grade to $data object and update grades
if (isset($data) && is_object($data)) {
    if (!isset($data->grade)) {
        $data->grade = $calculated_grade; // Ensure grade is set
    }
    digitalisation_update_grades($data, $USER->id);
} else {
    debugging('The $data object is not properly initialized, cannot assign grade.', DEBUG_DEVELOPER);
}

function digitalisation_grade_item_update($digitalisation, $grades = null) {
    global $CFG;
    require_once($CFG->libdir . '/gradelib.php');

    $item = [
        'itemname' => $digitalisation->name,
        'idnumber' => $digitalisation->cmidnumber,
    ];

    if ($grades === 'reset') {
        $item['reset'] = true;
    }

    return grade_update('mod/digitalisation', $digitalisation->course, 'mod', 'digitalisation', $digitalisation->id, 0, $grades, $item);
}

function digitalisation_update_grades($digitalisation, $userid = 0, $nullifnone = true) {
    global $CFG;
    require_once($CFG->libdir . '/gradelib.php');

    if (!isset($digitalisation->grade)) {
        debugging('Digitalisation object does not have a grade property.', DEBUG_DEVELOPER);
        return false;
    }

    $grades = [];
    if ($userid) {
        $grades[$userid] = ['userid' => $userid, 'rawgrade' => $digitalisation->grade];
    }

    return grade_update('mod/digitalisation', $digitalisation->course, 'mod', 'digitalisation', $digitalisation->id, 0, $grades);
}

function digitalisation_reset_userdata($data) {
    global $DB;

    $status = [];
    if (!empty($data->reset_digitalisation_grades)) {
        $DB->delete_records('digitalisation_grades', ['courseid' => $data->courseid]);
        $status[] = [
            'component' => get_string('modulenameplural', 'mod_digitalisation'),
            'item' => get_string('gradesdeleted', 'mod_digitalisation'),
            'error' => false,
        ];
    }

    return $status;
}
