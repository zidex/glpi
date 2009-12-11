<?php
/*
 * @version $Id$
 -------------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2009 by the INDEPNET Development Team.

 http://indepnet.net/   http://glpi-project.org
 -------------------------------------------------------------------------

 LICENSE

 This file is part of GLPI.

 GLPI is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 GLPI is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with GLPI; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 --------------------------------------------------------------------------
 */

// ----------------------------------------------------------------------
// Original Author of file:
// Purpose of file:
// ----------------------------------------------------------------------


define('GLPI_ROOT', '..');
include (GLPI_ROOT . "/inc/includes.php");

checkCentralAccess();

$fup = new TicketFollowup();
$track = new Ticket();

if (!isset($_GET['id'])) {
   $_GET['id'] = "";
}

commonHeader($LANG['title'][10],$_SERVER['PHP_SELF'],"maintain","tracking");

if (isset($_POST['update'])) {
   checkSeveralRightsOr(array('update_ticket'      => '1',
                              'assign_ticket'      => '1',
                              'steal_ticket'       => '1',
                              'comment_ticket'     => '1',
                              'comment_all_ticket' => '1'));

   $track->update($_POST);
   Event::log($_POST["id"], "tracking", 4, "tracking", $_SESSION["glpiname"]." ".$LANG['log'][21]);

   glpi_header($CFG_GLPI["root_doc"]."/front/ticket.form.php?id=".$_POST["id"]);

} else if (isset($_POST['add']) || isset($_POST['add_close']) || isset($_POST['add_reopen'])) {
   checkSeveralRightsOr(array('comment_ticket'     => '1',
                              'comment_all_ticket' => '1',
                              'show_assign_ticket' => '1'));
   $newID = $fup->add($_POST);

   Event::log($_POST["tickets_id"], "tracking", 4, "tracking",
              $_SESSION["glpiname"]." ".$LANG['log'][20]." $newID.");
   glpi_header($CFG_GLPI["root_doc"]."/front/ticket.form.php?id=".
               $_POST["tickets_id"]."&glpi_tab=1&itemtype=Ticket");

} else if (isset($_POST["update_followup"])) {
   checkRight("comment_all_ticket","1");
   $fup->update($_POST);

   Event::log($_POST["tickets_id"], "tracking", 4, "tracking",
              $_SESSION["glpiname"]."  ".$LANG['log'][21]." ".$_POST["id"].".");
   glpi_header($CFG_GLPI["root_doc"]."/front/ticket.form.php?id=".$_POST["tickets_id"]);

} else if (isset($_POST["delete_followup"])) {
   checkRight("comment_all_ticket","1");
   $fup->delete($_POST);
   Event::log($_POST["tickets_id"], "tracking", 4, "tracking",
              $_SESSION["glpiname"]." ".$LANG['log'][22]." ".$_POST["id"].".");
   glpi_header($CFG_GLPI["root_doc"]."/front/ticket.form.php?id=".$_POST["tickets_id"]);
}

$track->check($_GET["id"],'r');

$track->showTabs($_GET["id"],'',getActiveTab('Ticket'));
echo "<div id='tabcontent'></div>";

echo "<script type='text/javascript'>loadDefaultTab();</script>";

commonFooter();

?>
