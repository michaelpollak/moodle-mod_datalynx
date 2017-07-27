<?php
// This file is part of mod_datalynx for Moodle - http://moodle.org/
//
// It is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// It is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 *
 * @package datalynxfield
 * @copyright 2015 Ivan Šakić
 * @license http:// Www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
defined('MOODLE_INTERNAL') or die();

require_once('../../../config.php');
require_once('filterform_form.php');
require_once('../filter/filter_class.php');
require_once("../mod_class.php");

$urlparams = new stdClass();
$urlparams->d = required_param('d', PARAM_INT);
$urlparams->id = required_param('id', PARAM_INT);
$urlparams->action = optional_param('action', 'edit', PARAM_ALPHA);
$urlparams->confirmed = optional_param('confirmed', false, PARAM_BOOL);

$datalynx = new datalynx($urlparams->d);

require_login($datalynx->data->course, false, $datalynx->cm);

$datalynx->set_page('filterform/filterform_edit', array('urlparams' => $urlparams));

require_sesskey();
require_capability('mod/datalynx:managetemplates', $datalynx->context);

$returnurl = new moodle_url('/mod/datalynx/filterform/index.php', array('d' => $datalynx->id()));

switch ($urlparams->action) {
    case "edit":
        $mform = new datalynx_field_filterform_form($datalynx);

        if ($mform->is_cancelled()) {
            redirect($returnurl);
        } else {
            if ($data = $mform->get_data()) {
                if (!$data->id) {
                    $id = mod_datalynx_advanced_filter_form::insert_filterform($data);
                } else {
                    mod_datalynx_advanced_filter_form::update_filterform($data);
                }
                redirect($returnurl);
            }
        }

        $datalynx->print_header(
                array('tab' => 'filterforms', 'nonotifications' => true, 'urlparams' => $urlparams));

        if ($urlparams->id) {
            $data = mod_datalynx_advanced_filter_form::get_filterform($urlparams->id);
            $mform->set_data($data);
            echo html_writer::tag('h2', get_string('editingfilterform', 'datalynx', $data->name),
                    array('class' => 'mdl-align'));
        } else {
            echo html_writer::tag('h2', get_string('newfilterform', 'datalynx'),
                    array('class' => 'mdl-align'));
        }

        $mform->display();
        $datalynx->print_footer();

        break;

    case "duplicate":
        if ($urlparams->confirmed) {
            mod_datalynx_advanced_filter_form::duplicate_filterform($urlparams->id);
            redirect($returnurl);
        } else {
            $data = mod_datalynx_advanced_filter_form::get_filterform($urlparams->id);
            $urlparams->confirmed = true;
            $datalynx->print_header(
                    array('tab' => 'filterforms', 'nonotifications' => true,
                            'urlparams' => $urlparams));
            echo html_writer::tag('h2',
                    get_string('duplicatingfilterform', 'datalynx', $data->name),
                    array('class' => 'mdl-align'));
            echo $OUTPUT->confirm(get_string('confirmfilterformduplicate', 'datalynx'),
                    new moodle_url('filterform_edit.php', (array) $urlparams), $returnurl);
            $datalynx->print_footer();
        }
        break;

    case "delete":
        if ($urlparams->confirmed) {
            mod_datalynx_advanced_filter_form::delete_filterform($urlparams->id);
            redirect($returnurl);
        } else {
            $data = mod_datalynx_advanced_filter_form::get_filterform($urlparams->id);
            $urlparams->confirmed = true;
            $datalynx->print_header(
                    array('tab' => 'filterforms', 'nonotifications' => true,
                            'urlparams' => $urlparams));
            echo html_writer::tag('h2', get_string('deletingfilterform', 'datalynx', $data->name),
                    array('class' => 'mdl-align'));
            echo $OUTPUT->confirm(get_string('confirmfilterformdelete', 'datalynx'),
                    new moodle_url('filterform_edit.php', (array) $urlparams), $returnurl);
            $datalynx->print_footer();
        }
        break;

    default:
        redirect($returnurl);
        break;
}
