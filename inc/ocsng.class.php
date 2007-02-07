<?php


/*
 * @version $Id$
 -------------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2006 by the INDEPNET Development Team.

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

if (!defined('GLPI_ROOT')) {
	die("Sorry. You can't access directly to this file");
}

class DBocs extends DBmysql {

	var $ocs_server_id = -1;
	function DBocs($ID) {
		global $CFG_GLPI;
			$this->ocs_server_id = $ID;
			
			if ($CFG_GLPI["ocs_mode"]) {
			$data = getOcsConf($ID);
			$this->dbhost = $data["ocs_db_host"];
			$this->dbuser = $data["ocs_db_user"];
			$this->dbpassword = urldecode($data["ocs_db_passwd"]);
			$this->dbdefault = $data["ocs_db_name"];
			$this->dbh = @ mysql_connect($this->dbhost, $this->dbuser, $this->dbpassword) or $this->error = 1;
			@ mysql_select_db($this->dbdefault) or $this->error = 1;
			}
	}
	
	function getServerID()
	{
		return $this->ocs_server_id;
	}
}

class Ocsng extends CommonDBTM {
	var $fields = array ();

	function Ocsng() {
		global $CFG_GLPI;

		$this->table = "glpi_ocs_config";
		$this->type = OCSNG_TYPE;
	}

function titleOCSNG() {
	// Un titre pour la gestion des sources externes

	global $LANG, $CFG_GLPI;

	displayTitle($CFG_GLPI["root_doc"] . "/pics/logoOcs2.png", $LANG["ocsng"][0], $LANG["ocsng"][0]);

}
	function ocsFormConfig($target, $ID,$withtemplate='') {

		global $DB, $LANG, $CFG_GLPI;

		if (!haveRight("ocsng", "w"))
			return false;

		if (empty ($ID)) {
			if ($this->getEmpty())
				$spotted = true;
		} else {
			if ($this->getfromDB($ID))
				$spotted = true;
		}


		echo "<form name='formconfig' action=\"$target\" method=\"post\">";
		echo "<input type='hidden' name='ID' value='" . $ID . "'>";
		if (empty ($ID))
			echo "<input type='hidden' name='add_ocs_server' value='1'>";
		else
			echo "<input type='hidden' name='update_ocs_server' value='1'>";


		echo "<div align='center'><table class='tab_cadre'>";
		echo "<tr><th colspan='2'>" . $LANG["ocsconfig"][5] . "</th></tr>";
		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["ocsconfig"][17] . " </td><td> <input type=\"text\" size='30' name=\"tag_limit\" value=\"" . $this->fields["tag_limit"] . "\"></td></tr>";

		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["ocsconfig"][16] . " </td><td>";
		dropdownValue("glpi_dropdown_state", "default_state", $this->fields["default_state"]);
		echo "</td></tr>";

		$periph = $this->fields["import_periph"];
		$monitor = $this->fields["import_monitor"];
		$printer = $this->fields["import_printer"];
		$software = $this->fields["import_software"];
		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["ocsconfig"][8] . " </td><td>";
		echo "<select name='import_periph'>";
		echo "<option value='0' " . ($periph == 0 ? " selected " : "") . ">" . $LANG["ocsconfig"][11] . "</option>";
		echo "<option value='1' " . ($periph == 1 ? " selected " : "") . ">" . $LANG["ocsconfig"][10] . "</option>";
		echo "<option value='2' " . ($periph == 2 ? " selected " : "") . ">" . $LANG["ocsconfig"][12] . "</option>";
		echo "</select>";
		echo "</td></tr>";
		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["ocsconfig"][7] . " </td><td>";
		echo "<select name='import_monitor'>";
		echo "<option value='0' " . ($monitor == 0 ? " selected " : "") . ">" . $LANG["ocsconfig"][11] . "</option>";
		echo "<option value='1' " . ($monitor == 1 ? " selected " : "") . ">" . $LANG["ocsconfig"][10] . "</option>";
		echo "<option value='2' " . ($monitor == 2 ? " selected " : "") . ">" . $LANG["ocsconfig"][12] . "</option>";
		echo "</select>";
		echo "</td></tr>";
		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["ocsconfig"][9] . " </td><td>";

		echo "<select name='import_printer'>";
		echo "<option value='0' " . ($printer == 0 ? " selected " : "") . ">" . $LANG["ocsconfig"][11] . "</option>";
		echo "<option value='1' " . ($printer == 1 ? " selected " : "") . ">" . $LANG["ocsconfig"][10] . "</option>";
		echo "<option value='2' " . ($printer == 2 ? " selected " : "") . ">" . $LANG["ocsconfig"][12] . "</option>";
		echo "</select>";
		echo "</td></tr>";
		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["ocsconfig"][6] . " </td><td>";
		echo "<select name='import_software'>";
		echo "<option value='0' " . ($software == 0 ? " selected " : "") . ">" . $LANG["ocsconfig"][11] . "</option>";
		echo "<option value='1' " . ($software == 1 ? " selected " : "") . ">" . $LANG["ocsconfig"][12] . "</option>";
		echo "</select>";

		echo "</td></tr>";
		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["ocsconfig"][38] . " </td><td>";
		dropdownYesNoInt("use_soft_dict", $this->fields["use_soft_dict"]);
		echo "</td></tr>";
		echo "</td></tr>";
		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["ocsconfig"][40] . " </td><td>";
		dropdownInteger('cron_sync_number', $this->fields["cron_sync_number"], 0, 100);
		echo "</td></tr>";

		echo "</table></div>";

		echo "<div align='center'>" . $LANG["ocsconfig"][15] . "</div>";
		echo "<div align='center'>" . $LANG["ocsconfig"][14] . "</div>";
		echo "<div align='center'>" . $LANG["ocsconfig"][13] . "</div>";

		echo "<br />";

		echo "<div align='center'><table class='tab_cadre'>";
		echo "<tr><th>" . $LANG["ocsconfig"][27] . "</th><th>" . $LANG["ocsconfig"][28] . "</th></tr>";
		echo "<tr><td class='tab_bg_2' valign='top'><table width='100%' cellpadding='1' cellspacing='0' border='0'>";

		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["ocsconfig"][39] . " </td><td>";
		echo "<select name='import_tag_field'>";
		echo "<option value=''>" . $LANG["ocsconfig"][11] . "</option>";
		echo "<option value='otherserial' " . ($this->fields["import_tag_field"] == "otherserial" ? "selected" : "") . ">" . $LANG["common"][20] . "</option>";
		echo "<option value='contact_num' " . ($this->fields["import_tag_field"] == "contact_num" ? "selected" : "") . ">" . $LANG["common"][21] . "</option>";
		echo "<option value='location' " . ($this->fields["import_tag_field"] == "location" ? "selected" : "") . ">" . $LANG["common"][15] . "</option>";
		echo "<option value='network' " . ($this->fields["import_tag_field"] == "network" ? "selected" : "") . ">" . $LANG["setup"][88] . "</option>";
		echo "</select>";
		echo "</td></tr>";

		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["common"][16] . " </td><td>";
		dropdownYesNoInt("import_general_name", $this->fields["import_general_name"]);
		echo "</td></tr>";

		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["computers"][9] . " </td><td>";
		dropdownYesNoInt("import_general_os", $this->fields["import_general_os"]);
		echo "</td></tr>";

		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["common"][19] . " </td><td>";
		dropdownYesNoInt("import_general_serial", $this->fields["import_general_serial"]);
		echo "</td></tr>";
		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["common"][22] . " </td><td>";
		dropdownYesNoInt("import_general_model", $this->fields["import_general_model"]);
		echo "</td></tr>";
		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["common"][5] . " </td><td>";
		dropdownYesNoInt("import_general_enterprise", $this->fields["import_general_enterprise"]);
		echo "</td></tr>";
		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["common"][17] . " </td><td>";
		dropdownYesNoInt("import_general_type", $this->fields["import_general_type"]);
		echo "</td></tr>";
		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["setup"][89] . " </td><td>";
		dropdownYesNoInt("import_general_domain", $this->fields["import_general_domain"]);
		echo "</td></tr>";
		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["common"][18] . " </td><td>";
		dropdownYesNoInt("import_general_contact", $this->fields["import_general_contact"]);
		echo "</td></tr>";
		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["common"][25] . " </td><td>";
		dropdownYesNoInt("import_general_comments", $this->fields["import_general_comments"]);
		echo "</td></tr>";

		echo "<tr class='tab_bg_2'><td colspan='2'>&nbsp;";
		echo "</td></tr>";
		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["networking"][14] . " </td><td>";
		dropdownYesNoInt("import_ip", $this->fields["import_ip"]);
		echo "</td></tr>";

		echo "</table></td>";
		echo "<td class='tab_bg_2' valign='top'><table width='100%' cellpadding='1' cellspacing='0' border='0'>";

		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["devices"][4] . " </td><td>";
		dropdownYesNoInt("import_device_processor", $this->fields["import_device_processor"]);
		echo "</td></tr>";

		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["devices"][6] . " </td><td>";
		dropdownYesNoInt("import_device_memory", $this->fields["import_device_memory"]);
		echo "</td></tr>";

		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["devices"][1] . " </td><td>";
		dropdownYesNoInt("import_device_hdd", $this->fields["import_device_hdd"]);
		echo "</td></tr>";

		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["devices"][3] . " </td><td>";
		dropdownYesNoInt("import_device_iface", $this->fields["import_device_iface"]);
		echo "</td></tr>";

		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["devices"][2] . " </td><td>";
		dropdownYesNoInt("import_device_gfxcard", $this->fields["import_device_gfxcard"]);
		echo "</td></tr>";

		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["devices"][7] . " </td><td>";
		dropdownYesNoInt("import_device_sound", $this->fields["import_device_sound"]);
		echo "</td></tr>";

		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["devices"][19] . " </td><td>";
		dropdownYesNoInt("import_device_drives", $this->fields["import_device_drives"]);
		echo "</td></tr>";

		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["ocsconfig"][36] . " </td><td>";
		dropdownYesNoInt("import_device_modems", $this->fields["import_device_modems"]);
		echo "</td></tr>";

		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["ocsconfig"][37] . " </td><td>";
		dropdownYesNoInt("import_device_ports", $this->fields["import_device_ports"]);
		echo "</td></tr>";

		echo "</table></td></tr>";
		echo "</table></div>";
		echo "<p class=\"submit\"><input type=\"submit\" name=\"update_ocs_server\" class=\"submit\" value=\"" . $LANG["buttons"][2] . "\" ></p>";
		echo "</form>";

	}

	function showForm($target, $ID) {

		global $DB, $LANG, $CFG_GLPI;

		if (!haveRight("ocsng", "w"))
			return false;

		if (empty ($ID)) {
			if ($this->getEmpty())
				$spotted = true;
		} else {
			if ($this->getfromDB($ID))
				$spotted = true;
		}

		echo "<form name='formdbconfig' action=\"$target\" method=\"post\">";
		if (!empty ($ID))
			echo "<input type='hidden' name='ID' value='" . $_GET["ID"] . "'>";

		echo "<div align='center'><table class='tab_cadre'>";
		echo "<tr><th colspan='2'>" . $LANG["ocsconfig"][0] . "</th></tr>";
		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["common"][16] . " </td><td> <input type=\"text\" name=\"name\" value=\"" . $this->fields["name"] . "\"></td></tr>";
		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["ocsconfig"][2] . " </td><td> <input type=\"text\" name=\"ocs_db_host\" value=\"" . $this->fields["ocs_db_host"] . "\"></td></tr>";
		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["ocsconfig"][4] . " </td><td> <input type=\"text\" name=\"ocs_db_name\" value=\"" . $this->fields["ocs_db_name"] . "\"></td></tr>";
		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["ocsconfig"][1] . " </td><td> <input type=\"text\" name=\"ocs_db_user\" value=\"" . $this->fields["ocs_db_user"] . "\"></td></tr>";
		echo "<tr class='tab_bg_2'><td align='center'>" . $LANG["ocsconfig"][3] . " </td><td> <input type=\"password\" name=\"ocs_db_passwd\"></td></tr>";
		echo "</table>";

		echo "<br><table border='0'>";
		
		if (empty ($ID))
			echo "<tr class='tab_bg_2'><td align='center' colspan=2><input type=\"submit\" name=\"add_ocs_server\" class=\"submit\" value=\"" . $LANG["buttons"][2] . "\" ></td></tr>";
		else
		{
			echo "<tr class='tab_bg_2'><td align='center'><input type=\"submit\" name=\"update_ocs_server\" class=\"submit\" value=\"" . $LANG["buttons"][2] . "\" ></td>";
			echo "<td align='center'><input type=\"submit\" name=\"delete_ocs_server\" class=\"submit\" value=\"" . $LANG["buttons"][6] . "\" ></td></tr>";
			
		}
		echo "</table></div>";
		echo "</form>";


		echo "<div align='center'>";

		if (!empty ($ID)) {
			
			$DBocs = getDBocs($ID);	
			if (!$DBocs->error) {
				echo $LANG["ocsng"][18] . "<br>";
				$result = $DBocs->query("SELECT TVALUE FROM config WHERE NAME='GUI_VERSION'");
				if ($DBocs->numrows($result) == 1 && $DBocs->result($result, 0, 0) >= 4020) {
					$query = "UPDATE config SET IVALUE='1' WHERE NAME='TRACE_DELETED'";
					$DBocs->query($query);

					echo $LANG["ocsng"][19] . "</div>";
					$this->ocsFormConfig($target, $ID);
				} else
					echo $LANG["ocsng"][20] . "</div>";
			} else
				echo $LANG["ocsng"][21] . "</div>";

		}
	}
	
	function prepareInputForUpdate($input)
	{
		$input["date_mod"]=$_SESSION["glpi_currenttime"];
		
				if (isset($input["ocs_db_passwd"])&&!empty($input["ocs_db_passwd"])){
			$input["ocs_db_passwd"]=urlencode(stripslashes($input["ocs_db_passwd"]));
		} else {
			unset($input["ocs_db_passwd"]);
		}

		if (isset($input["import_ip"])){
			$input["checksum"]=0;

			if ($input["import_ip"]) $input["checksum"]|= pow(2,NETWORKS_FL);
			if ($input["import_device_ports"]) $input["checksum"]|= pow(2,PORTS_FL);
			if ($input["import_device_modems"]) $input["checksum"]|= pow(2,MODEMS_FL);
			if ($input["import_device_drives"]) $input["checksum"]|= pow(2,STORAGES_FL);
			if ($input["import_device_sound"]) $input["checksum"]|= pow(2,SOUNDS_FL);
			if ($input["import_device_gfxcard"]) $input["checksum"]|= pow(2,VIDEOS_FL);
			if ($input["import_device_iface"]) $input["checksum"]|= pow(2,NETWORKS_FL);
			if ($input["import_device_hdd"]) $input["checksum"]|= pow(2,STORAGES_FL);
			if ($input["import_device_memory"]) $input["checksum"]|= pow(2,MEMORIES_FL);
			if (	$input["import_device_processor"]
					||$input["import_general_contact"]
					||$input["import_general_comments"]
					||$input["import_general_domain"]
					||$input["import_general_os"]
					||$input["import_general_name"]) $input["checksum"]|= pow(2,HARDWARE_FL);
			if (	$input["import_general_enterprise"]
					||$input["import_general_type"]
					||$input["import_general_model"]
					||$input["import_general_serial"]) $input["checksum"]|= pow(2,BIOS_FL);
			if ($input["import_printer"]) $input["checksum"]|= pow(2,PRINTERS_FL);
			if ($input["import_software"]) $input["checksum"]|= pow(2,SOFTWARES_FL);
			if ($input["import_monitor"]) $input["checksum"]|= pow(2,MONITORS_FL);
			if ($input["import_periph"]) $input["checksum"]|= pow(2,INPUTS_FL);
		}
		
		return $input;
	}
	
	function prepareInputForAdd($input)
	{
		$input["date_mod"]=$_SESSION["glpi_currenttime"];
		
				if (isset($input["ocs_db_passwd"])&&!empty($input["ocs_db_passwd"])){
			$input["ocs_db_passwd"]=urlencode(stripslashes($input["ocs_db_passwd"]));
		} else {
			unset($input["ocs_db_passwd"]);
		}

		if (isset($input["import_ip"])){
			$input["checksum"]=0;

			if ($input["import_ip"]) $input["checksum"]|= pow(2,NETWORKS_FL);
			if ($input["import_device_ports"]) $input["checksum"]|= pow(2,PORTS_FL);
			if ($input["import_device_modems"]) $input["checksum"]|= pow(2,MODEMS_FL);
			if ($input["import_device_drives"]) $input["checksum"]|= pow(2,STORAGES_FL);
			if ($input["import_device_sound"]) $input["checksum"]|= pow(2,SOUNDS_FL);
			if ($input["import_device_gfxcard"]) $input["checksum"]|= pow(2,VIDEOS_FL);
			if ($input["import_device_iface"]) $input["checksum"]|= pow(2,NETWORKS_FL);
			if ($input["import_device_hdd"]) $input["checksum"]|= pow(2,STORAGES_FL);
			if ($input["import_device_memory"]) $input["checksum"]|= pow(2,MEMORIES_FL);
			if (	$input["import_device_processor"]
					||$input["import_general_contact"]
					||$input["import_general_comments"]
					||$input["import_general_domain"]
					||$input["import_general_os"]
					||$input["import_general_name"]) $input["checksum"]|= pow(2,HARDWARE_FL);
			if (	$input["import_general_enterprise"]
					||$input["import_general_type"]
					||$input["import_general_model"]
					||$input["import_general_serial"]) $input["checksum"]|= pow(2,BIOS_FL);
			if ($input["import_printer"]) $input["checksum"]|= pow(2,PRINTERS_FL);
			if ($input["import_software"]) $input["checksum"]|= pow(2,SOFTWARES_FL);
			if ($input["import_monitor"]) $input["checksum"]|= pow(2,MONITORS_FL);
			if ($input["import_periph"]) $input["checksum"]|= pow(2,INPUTS_FL);
		}
		
		return $input;
	}
	
}
?>
