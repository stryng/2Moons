<?php

/**
 *  2Moons
 *  Copyright (C) 2011  Slaver
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package 2Moons
 * @author Slaver <slaver7@gmail.com>
 * @copyright 2009 Lucky <lucky@xgproyect.net> (XGProyecto)
 * @copyright 2011 Slaver <slaver7@gmail.com> (Fork/2Moons)
 * @license http://www.gnu.org/licenses/gpl.html GNU GPLv3 License
 * @version 1.5 (2011-07-31)
 * @info $Id$
 * @link http://code.google.com/p/2moons/
 */

class ShowOptionsPage
{
	private function CheckVMode()
	{
		global $db, $USER, $PLANET;

		if(!empty($USER['b_tech']) || !empty($PLANET['b_building']) || !empty($PLANET['b_hangar']))
			return false;

		$fleets = $db->countquery("SELECT COUNT(*) FROM ".FLEETS." WHERE `fleet_owner` = '".$USER['id']."' OR `fleet_target_owner` = '".$USER['id']."';");
		if($fleets != 0)
			return false;
					
		$query = $db->query("SELECT * FROM ".PLANETS." WHERE id_owner = '".$USER['id']."' AND id != '".$PLANET['id']."' AND `destruyed` = 0;");
		
		
		while($CPLANET = $db->fetch_array($query))
		{
			$PlanetRess = new ResourceUpdate();
		
			list($USER, $CPLANET)	= $PlanetRess->CalcResource($USER, $CPLANET, true);
		
			if(!empty($CPLANET['b_building']) || !empty($CPLANET['b_hangar']))
				return false;
			
			unset($CPLANET);
		}

		$db->free_result($query);
		
		return true;
	}
	
	public function __construct()
	{
		global $USER, $PLANET, $CONF, $LNG, $LANG, $UNI, $db, $SESSION, $THEME;

		$mode 			= request_var('mode', '');
		$exit 			= request_var('exit_modus', '');
		$db_deaktjava 	= request_var('db_deaktjava', '');
		
		$PlanetRess = new ResourceUpdate();
		$PlanetRess->CalcResource();
		$PlanetRess->SavePlanetToDB();
	
		$template	= new template();
		$SQLQuery = "";
		switch($mode)
		{
			case "exit":
				if ($exit == 'on' and $USER['urlaubs_until'] <= TIMESTAMP)
					$SQLQuery	.= "UPDATE ".USERS." SET `urlaubs_modus` = '0', `urlaubs_until` = '0' WHERE `id` = '".$USER['id']."' LIMIT 1;UPDATE ".PLANETS." SET `last_update` = '".TIMESTAMP."', `energy_used` = '10', `energy_max` = '10', `metal_mine_porcent` = '10', `crystal_mine_porcent` = '10', `deuterium_sintetizer_porcent` = '10', `solar_plant_porcent` = '10', `fusion_plant_porcent` = '10', `solar_satelit_porcent` = '10' WHERE `id_owner` = '".$USER["id"]."';";

				$SQLQuery .= $db_deaktjava == 'on' ? "UPDATE ".USERS." SET `db_deaktjava` = '".TIMESTAMP."' WHERE `id` = '".$USER['id']."' LIMIT 1;" : "UPDATE ".USERS." SET `db_deaktjava` = '0' WHERE `id` = '".$USER['id']."' LIMIT 1;";
				
				$db->multi_query($SQLQuery);
				$template->message($LNG['op_options_changed'], '?page=options', 1);
			break;
			case "change":
				$design 				= request_var('design', '');
				$noipcheck 				= request_var('noipcheck', '');
				$USERname 				= request_var('db_character', $USER['username'], UTF8_SUPPORT);
				$db_email 				= request_var('db_email', $USER['email']);
				$spio_anz 				= max(request_var('spio_anz', 5), 1);
				$settings_tooltiptime 	= request_var('settings_tooltiptime', 1);
				$settings_fleetactions 	= max(request_var('settings_fleetactions', 1), 1);
				$settings_planetmenu	= request_var('settings_planetmenu', '');
				$settings_esp 			= request_var('settings_esp', '');
				$settings_wri 			= request_var('settings_wri', '');
				$settings_bud 			= request_var('settings_bud', '');
				$settings_mis 			= request_var('settings_mis', '');
				$settings_rep 			= request_var('settings_rep', '');
				$settings_tnstor		= request_var('settings_tnstor', '');
				$urlaubs_modus 			= request_var('urlaubs_modus', '');
				$SetSort  				= request_var('settings_sort' , 0);
				$SetOrder 				= request_var('settings_order', 0);
				$db_password			= request_var('db_password', '');
				$newpass1				= request_var('newpass1', '');
				$newpass2				= request_var('newpass2', '');		
				$hof					= request_var('hof', '');	
				$adm_pl_prot			= request_var('adm_pl_prot', '');	
				$DST					= request_var('dst', 0);	
				$timezone				= request_var('timezone', 0.0);
				$langs					= request_var('langs', $LANG->getUser());	
				$dpath					= request_var('dpath', $THEME->getThemeName());	
				$design 				= ($design == 'on') ? 1 : 0;
				$hof 					= ($hof == 'on') ? 1 : 0;
				$noipcheck 				= ($noipcheck == 'on') ? 1 : 0;
				$settings_esp			= ($settings_esp == 'on') ? 1 : 0;
				$settings_wri			= ($settings_wri == 'on') ? 1 : 0;
				$settings_bud			= ($settings_bud == 'on') ? 1 : 0;
				$settings_mis			= ($settings_mis == 'on') ? 1 : 0;
				$settings_rep 			= ($settings_rep == 'on') ? 1 : 0;
				$settings_tnstor 		= ($settings_tnstor == 'on') ? 1 : 0;
				$settings_planetmenu	= ($settings_planetmenu == 'on') ? 1 : 0;
				$db_deaktjava 			= ($db_deaktjava == 'on') ? TIMESTAMP : 0;
				$langs					= in_array($langs, $LANG->getAllowedLangs()) ? $langs : $LANG->getUser();
				$dpath					= array_key_exists($dpath, Theme::getAvalibleSkins()) ? $dpath : $THEME->getThemeName();
				$authattack				= ($adm_pl_prot == 'on' && $USER['authlevel'] != AUTH_USR) ? $USER['authlevel'] : 0;

				if ($urlaubs_modus == 'on')
				{
					if(!$this->CheckVMode())
					{
						$template->message($LNG['op_cant_activate_vacation_mode'], '?page=options', 3);
						exit;
					}
					
					$SQLQuery	.= "UPDATE ".USERS." SET 
									`urlaubs_modus` = '1',
									`urlaubs_until` = '".(TIMESTAMP + $CONF['vmode_min_time'])."'
									WHERE `id` = '".$USER["id"]."';
									UPDATE ".PLANETS." SET
									`energy_used` = '0',
									`energy_max` = '0',
									`metal_mine_porcent` = '0',
									`crystal_mine_porcent` = '0',
									`deuterium_sintetizer_porcent` = '0',
									`solar_plant_porcent` = '0',
									`fusion_plant_porcent` = '0',
									`solar_satelit_porcent` = '0',
									`metal_perhour` = '0',
                                    `crystal_perhour` = '0',
                                    `deuterium_perhour` =  '0'
                                    WHERE `id_owner` = '".$USER["id"]."';";
				}

				$SQLQuery	.=  "UPDATE ".USERS." SET
								`dpath` = '".$db->sql_escape($dpath)."',
								`design` = '".$design."',
								`noipcheck` = '".$noipcheck."',
								`timezone` = '".$timezone."',
								`dst` = '".$DST."',
								`planet_sort` = '".$SetSort."',
								`planet_sort_order` = '".$SetOrder."',
								`spio_anz` = '".$spio_anz."',
								`settings_tooltiptime` = '".$settings_tooltiptime."',
								`settings_fleetactions` = '".$settings_fleetactions."',
								`settings_planetmenu` = '".$settings_planetmenu."',
								`settings_esp` = '".$settings_esp."',
								`settings_wri` = '".$settings_wri."',
								`settings_bud` = '".$settings_bud."',
								`settings_mis` = '".$settings_mis."',
								`settings_tnstor` = '".$settings_tnstor."',
								`authattack` = '".$authattack."',
								`db_deaktjava` = '".$db_deaktjava."',
								`lang` = '".$langs."',
								`hof` = '".$hof."',
								`settings_rep` = '".$settings_rep."' 
								WHERE `id` = '".$USER["id"]."';";
									
				if (!empty($db_email) && $db_email != $USER['email'])
				{
					if(md5($db_password) != $USER['password']) {
						$template->message($LNG['op_need_pass_mail'], '?page=options', 3);
						exit;
					}
					
					if(!ValidateAddress($db_email)) {
						$template->message($LNG['op_not_vaild_mail'], '?page=options', 3);
						exit;
					}
				
					$query = $db->uniquequery("SELECT id FROM ".USERS." WHERE `id` != '".$USER['id']."' AND `universe` = '".$UNI."' AND (`email` = '".$db->sql_escape($db_email)."' OR `email_2` = '".$db->sql_escape($db_email)."');");

					if (!empty($query)) {
						$template->message(sprintf($LNG['op_change_mail_exist'], $db_email), '?page=options', 3);
						exit;
					}
					
					$SQLQuery	.= "UPDATE ".USERS." SET `email` = '".$db->sql_escape($db_email)."', `setmail` = '".(TIMESTAMP + 604800)."' WHERE `id` = '".$USER['id']."';";
				}				
				
				if (!empty($newpass1) && md5($db_password) == $USER["password"] && $newpass1 == $newpass2)
				{
					$newpass 	 = md5($newpass1);
					$SQLQuery	.= "UPDATE ".USERS." SET `password` = '".$newpass."' WHERE `id` = '".$USER['id']."';";
					$SESSION->DestroySession();
					$template->message($LNG['op_password_changed'],"index.php", 3);
				}
				elseif ($USER['username'] != $USERname)
				{
					if (!CheckName($USERname))
						$template->message($LNG['op_user_name_no_alphanumeric'], '?page=options', 3);
					elseif($USER['uctime'] >= TIMESTAMP - USERNAME_CHANGETIME)
						$template->message($LNG['op_change_name_pro_week'], '?page=options', 3);
					else
					{
						$query = $db->uniquequery("SELECT id FROM ".USERS." WHERE username='".$db->sql_escape($USERname)."';");
						
						if (!empty($query))
							$template->message(sprintf($LNG['op_change_name_exist'], $USERname), '?page=options', 3);
						else 
						{
							$SQLQuery	.= "UPDATE ".USERS." SET `username` = '".$db->sql_escape($USERname)."', `uctime` = '".TIMESTAMP."' WHERE `id`= '".$USER['id']."';";
							$SESSION->DestroySession();
							$template->message($LNG['op_username_changed'], 'index.php', 3);
						}
					}
				}
				else
					$template->message($LNG['op_options_changed'], '?page=options', 3);
					
				$db->multi_query($SQLQuery);
			break;
			default:
				if($USER['urlaubs_modus'] == 1)
				{
					$template->assign_vars(array(	
						'vacation_until'					=> tz_date($USER['urlaubs_until']),
						'opt_delac_data'					=> $USER['db_deaktjava'],
						'is_deak_vacation'					=> $USER['urlaubs_until'] <= TIMESTAMP ? true : false,
					));
					$template->show("options_overview_vmode.tpl");
				}
				else
				{
					$template->assign_vars(array(	
						'opt_usern_data'					=> $USER['username'],
						'opt_mail1_data'					=> $USER['email'],
						'opt_mail2_data'					=> $USER['email_2'],
						'opt_dpath_data'					=> $USER['dpath'],
						'opt_dpath_data_sel'				=> substr($USER['dpath'], 13, -1),
						'opt_probe_data'					=> $USER['spio_anz'],
						'opt_toolt_data'					=> $USER['settings_tooltiptime'],
						'opt_fleet_data'					=> $USER['settings_fleetactions'],
						'opt_sskin_data'					=> $USER['design'],
						'opt_noipc_data'					=> $USER['noipcheck'],
						'opt_allyl_data'					=> $USER['settings_planetmenu'],
						'opt_delac_data'					=> $USER['db_deaktjava'],
						'opt_dst_mode'						=> $USER['dst'],
						'opt_timezone'						=> $USER['timezone'],
						'user_settings_rep' 				=> $USER['settings_rep'],
						'user_settings_esp' 				=> $USER['settings_esp'],
						'user_settings_wri' 				=> $USER['settings_wri'],
						'user_settings_mis' 				=> $USER['settings_mis'],
						'user_settings_bud' 				=> $USER['settings_bud'],
						'opt_hof'							=> $USER['hof'],
						'langs'								=> $USER['lang'],
						'adm_pl_prot_data'					=> $USER['authattack'],					
						'user_authlevel'					=> $USER['authlevel'],					
						'Selectors'							=> array('timezones' => $LNG['timezones'], 'dst' => $LNG['op_dst_mode_sel'], 'Sort' => array(0 => $LNG['op_sort_normal'], 1 => $LNG['op_sort_koords'], 2 => $LNG['op_sort_abc']), 'SortUpDown' => array(0 => $LNG['op_sort_up'], 1 => $LNG['op_sort_down']), 'Skins' => Theme::getAvalibleSkins(), 'lang' => $LANG->getAllowedLangs(false)),
						'planet_sort'						=> $USER['planet_sort'],
						'planet_sort_order'					=> $USER['planet_sort_order'],
						'uctime'							=> (TIMESTAMP - $USER['uctime'] >= (60 * 60 * 24 * 7)) ? true : false,
					));
					
					$template->show("options_overview.tpl");
				}
			break;
		}
	}
}
?>