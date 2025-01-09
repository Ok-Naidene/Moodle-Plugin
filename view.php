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
 * Prints an instance of mod_digitalisation.
 *
 * @package     mod_digitalisation
 * @copyright   2024 Your Name
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 require_once(__DIR__ . '/../../config.php');
 require_once(__DIR__ . '/lib.php');
 require_once($CFG->dirroot . '/mod/digitalisation/text_form.php'); // Include the form.
 
 $id = optional_param('id', 0, PARAM_INT);
 $d = optional_param('d', 0, PARAM_INT);
 
 if (!$id && !$d) {
     throw new moodle_exception('missingparameter', 'mod_digitalisation', '', 'Either id or d must be provided.');
 }
 
 // Fetch course module, course, and module instance.
 if ($id) {
     $cm = get_coursemodule_from_id('digitalisation', $id, 0, MUST_EXIST);
     $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
     $moduleinstance = $DB->get_record('digitalisation', ['id' => $cm->instance], '*', MUST_EXIST);
 } elseif ($d) {
     $moduleinstance = $DB->get_record('digitalisation', ['id' => $d], '*', MUST_EXIST);
     $course = $DB->get_record('course', ['id' => $moduleinstance->course], '*', MUST_EXIST);
     $cm = get_coursemodule_from_instance('digitalisation', $moduleinstance->id, $course->id, MUST_EXIST);
 }
 
 require_login($course, true, $cm);
 
 $modulecontext = context_module::instance($cm->id);
 
 // Trigger view event.
 $event = \mod_digitalisation\event\course_module_viewed::create([
     'objectid' => $moduleinstance->id,
     'context' => $modulecontext,
 ]);
 $event->trigger();
 
 // Set up the page.
 $PAGE->set_url('/mod/digitalisation/view.php', ['id' => $cm->id]);
 $PAGE->set_title(format_string($moduleinstance->name));
 $PAGE->set_heading(format_string($course->fullname));
 $PAGE->set_context($modulecontext);
 
 echo $OUTPUT->header();
 
 // Dynamically load the selected game.
 $game = $moduleinstance->game ?? 'noActivity.html';
 $gamePath = $CFG->wwwroot . '/mod/digitalisation/games/' . $game;
 
 echo html_writer::tag('iframe', '', [
     'src' => $gamePath,
     'width' => '100%',
     'height' => '600px',
     'frameborder' => '0',
     'allowfullscreen' => 'true',
 ]);
 
 // Render feedback form if the user has the capability.
 if (has_capability('mod/digitalisation:addfeedback', $modulecontext)) {
     $mform = new digitalisation_text_form();
     if ($mform->is_cancelled()) {
         // Handle form cancellation.
         redirect($PAGE->url);
     } else if ($data = $mform->get_data()) {
         // Handle form submission and save feedback.
         $record = new stdClass();
         $record->studentid = $data->studentid;
         $record->teacherid = $USER->id;
         $record->digitalisationid = $moduleinstance->id;
         $record->feedback = $data->feedback['text']; // Extract feedback text.
         $record->timecreated = time();
 
         $DB->insert_record('digitalisation_feedback', $record);
 
         // Notify user of success and redirect.
         redirect($PAGE->url, get_string('feedbacksaved', 'mod_digitalisation'), null, \core\output\notification::NOTIFY_SUCCESS);
     }
 
     // Display the form.
     $mform->display();
 }
 
 echo $OUTPUT->footer();
 