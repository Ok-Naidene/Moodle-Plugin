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
 * The main mod_digitalisation configuration form.
 *
 * @package     mod_digitalisation
 * @copyright   2024 Your Name <you@example.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

# Update and create a summary for the `mod_form.php` file to add a dropdown menu for selecting games.

// Ensure the script is being run within Moodle.
defined('MOODLE_INTERNAL') || die();

require_once("$CFG->dirroot/course/moodleform_mod.php");

/**
 * Form for adding or editing a Digitalisation activity instance.
 */
class mod_digitalisation_mod_form extends moodleform_mod {
    /**
     * Defines the form for creating or editing an activity instance.
     */
    public function definition() {
        $mform = $this->_form;

        // General settings
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Activity name
        $mform->addElement('text', 'name', get_string('digitalisationname', 'mod_digitalisation'), ['size' => '64']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        // Intro and intro format (standard Moodle activity settings)
        $this->standard_intro_elements();

        // Game selection dropdown
        $mform->addElement('select', 'game', get_string('selectgame', 'mod_digitalisation'), [
            'accidentForm.html' => get_string('game_accidentform', 'mod_digitalisation'),
            'DnD.html' => get_string('game_dnd', 'mod_digitalisation'),
            'hazardLearn.html' => get_string('game_hazardlearn', 'mod_digitalisation'),
            'Hazards.html' => get_string('game_hazards', 'mod_digitalisation'),
            'noActivity.html' => get_string('game_noactivity', 'mod_digitalisation'),
            'pcbuLearn.html' => get_string('game_pcbuLearn', 'mod_digitalisation'),
            'pcbuAssign.html' => get_string('game_pcbuAssign', 'mod_digitalisation'),
            'control.html' => get_string('game_control', 'mod_digitalisation'),
            'reportIncident.html' => get_string('game_reportIncident', 'mod_digitalisation'),
        ]);
        $mform->setType('game', PARAM_TEXT);
        $mform->setDefault('game', 'noActivity.html');

        // Standard grading settings
        $this->standard_grading_coursemodule_elements();

        // Standard activity settings (availability, etc.)
        $this->standard_coursemodule_elements();

        // Grade settings
        $mform->addElement('header', 'gradesettings', get_string('gradesettings', 'mod_digitalisation'));
        $mform->addElement('select', 'grade', get_string('grade', 'mod_digitalisation'), range(0, 100));
        $mform->setDefault('grade', 100);
        $mform->setType('grade', PARAM_INT);

        // Add action buttons (Save and Cancel)
        $this->add_action_buttons();
    }
}