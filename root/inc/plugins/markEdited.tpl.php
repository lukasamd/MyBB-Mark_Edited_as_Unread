<?php
/**
 * This file is part of Mark Edited as Unread plugin for MyBB.
 * Copyright (C) 2010-2013 Lukasz Tkacz <lukasamd@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */ 
 
/**
 * Disallow direct access to this file for security reasons
 * 
 */
if (!defined("IN_MYBB")) exit;

/**
 * Plugin Activator Class
 * 
 */
class markEditedActivator
{

    private static $tpl = array();

    private static function getTpl()
    {
        global $db;

        self::$tpl[] = array(
            "tid" => NULL,
            "title" => 'markEdited_Body',
            "template" => $db->escape_string('
                </span>
                </td>
                </tr>
                <tr>
                  <td class="trow1" valign="top">
                    <strong>{$lang->markEditedTplTitle}</strong>
                  </td>
                  <td class="trow1">
                    <span class="smalltext">
                      <select name="markEdited_reason">
                        {$markEditedTplOptions}
                      </select>'),
            "sid" => "-1",
            "version" => "1.0",
            "dateline" => TIME_NOW,
        );

        self::$tpl[] = array(
            "tid" => NULL,
            "title" => 'markEdited_BodyQuick',
            "template" => $db->escape_string('
                <span class="smalltext" style="float:left;">
                    <strong>{$lang->markEditedTplTitle}</strong>
                    <select id="markEdited_reason" name="markEdited_reason">
                      {$markEditedTplOptions}
                    </select>
                </span>'),
            "sid" => "-1",
            "version" => "1.0",
            "dateline" => TIME_NOW,
        );
    }

    public static function activate()
    {
        global $db;
        self::deactivate();

        for ($i = 0; $i < sizeof(self::$tpl); $i++)
        {
            $db->insert_query('templates', self::$tpl[$i]);
        }
        find_replace_templatesets('postbit_editedby', '#' . preg_quote('.)') . '#', '.<!-- markEdited -->)');
        find_replace_templatesets('editpost', '#' . preg_quote('{$posticons}') . '#', '{$posticons}{$markEdited}');
        find_replace_templatesets('xmlhttp_inline_post_editor', '#' . preg_quote('text-align: right;">') . '#', 'text-align: right;">{$markEdited}');
    }

    public static function deactivate()
    {
        global $db;
        self::getTpl();

        for ($i = 0; $i < sizeof(self::$tpl); $i++)
        {
            $db->delete_query('templates', "title = '" . self::$tpl[$i]['title'] . "'");
        }

        include MYBB_ROOT . '/inc/adminfunctions_templates.php';
        find_replace_templatesets('postbit_editedby', '#' . preg_quote('<!-- markEdited -->') . '#', '');
        find_replace_templatesets('editpost', '#' . preg_quote('{$markEdited}') . '#', '');
        find_replace_templatesets('xmlhttp_inline_post_editor', '#' . preg_quote('{$markEdited}') . '#', '');
    }

}
