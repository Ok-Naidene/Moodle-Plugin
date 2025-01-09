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
 * Plugin version and other meta-data are defined here.
 *
 * @package     mod_digitalisation
 * @copyright   2024 Fruition Horticulture <info@fruition.ac.nz>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 defined('MOODLE_INTERNAL') || die();
 
 $plugin->component = 'mod_digitalisation';
 $plugin->release = '0.1.0';
 $plugin->version = 202301000;
 $plugin->requires = 2014052300; // Requires Moodle 3.1 or higher
 $plugin->maturity = MATURITY_ALPHA;
 