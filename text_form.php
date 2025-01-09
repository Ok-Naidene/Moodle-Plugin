<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

class digitalisation_text_form extends moodleform {
    public function definition() {
        global $DB, $COURSE;

        $mform = $this->_form;

        // Fetch course context and student list dynamically.
        $context = context_course::instance($COURSE->id);
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'student']); // Get the student role ID.

        $students = $DB->get_records_sql_menu(
            "SELECT u.id, CONCAT(u.firstname, ' ', u.lastname) AS name
             FROM {user} u
             JOIN {role_assignments} ra ON ra.userid = u.id
             WHERE ra.roleid = :roleid AND ra.contextid = :contextid",
            ['roleid' => $roleid, 'contextid' => $context->id]
        );

        // Provide a fallback for no students.
        if (empty($students)) {
            $students = [0 => get_string('nostudents', 'mod_digitalisation')];
        }

        // Add dropdown for selecting a student.
        $mform->addElement('select', 'studentid', get_string('selectstudent', 'mod_digitalisation'), $students);
        $mform->setType('studentid', PARAM_INT);
        $mform->addRule('studentid', null, 'required', null, 'client');

        // Add feedback editor.
        $mform->addElement('editor', 'feedback', get_string('feedback', 'mod_digitalisation'));
        $mform->setType('feedback', PARAM_RAW);
        $mform->addRule('feedback', null, 'required', null, 'client');

        // Hidden fields to preserve parameters.
        $id = optional_param('id', 0, PARAM_INT);
        $d = optional_param('d', 0, PARAM_INT);
        $mform->addElement('hidden', 'id', $id);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'd', $d);
        $mform->setType('d', PARAM_INT);

        // Add submit button.
        $this->add_action_buttons(true, get_string('savefeedback', 'mod_digitalisation'));
    }
}
