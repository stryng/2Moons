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
 * @version 1.6.1 (2011-11-19)
 * @info $Id$
 * @link http://code.google.com/p/2moons/
 */

function ShowTechTreePage()
{
	global $resource, $requeriments, $LNG, $reslist, $USER, $PLANET;
	
	$PlanetRess = new ResourceUpdate();
	$PlanetRess->CalcResource();
	$PlanetRess->SavePlanetToDB();
	
	$template	= new template();
	$RequeriList = array();
	foreach($LNG['tech'] as $Element => $ElementName)
	{
		if(in_array($Element, $reslist['dmfunc'])) continue;
		
		if(!isset($resource[$Element]))
			$TechTreeList[]	= $ElementName;
		else
		{
			$RequeriList	= array();
			if(isset($requeriments[$Element]))
			{
				foreach($requeriments[$Element] as $RegID => $RedCount)
				{
					$RequeriList[$Element][]	= array('id' => $RegID, 'count' => $RedCount, 'own' => (isset($PLANET[$resource[$RegID]])) ? $PLANET[$resource[$RegID]] : $USER[$resource[$RegID]]);
				}
			}
			$TechTreeList[]	= array(
				'id' 	=> $Element,
				'name'	=> $ElementName,
				'need'	=> $RequeriList,
			);
		}
	}
	
	$template->assign_vars(array(
		'TechTreeList'		=> $TechTreeList,
		'tt_requirements'	=> $LNG['tt_requirements'],
		'LNG'				=> $LNG['tech'],
		'tt_lvl'			=> $LNG['tt_lvl'],
	));

	$template->show("techtree_overview.tpl");
}
?>