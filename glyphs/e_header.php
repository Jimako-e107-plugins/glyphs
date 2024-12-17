<?php
/*
 * e107 website system
 *
 * Copyright (C) 2008-2014 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * Related configuration module - News
 *
 *
*/

if (!defined('e107_INIT')) { exit; }


if (deftrue('USER_AREA')) // prevents inclusion of JS/CSS/meta in the admin area.
{
	/* supported packs only */
	$curVal = e107::pref('glyphs', 'packs');
	$packs = e107::unserialize($curVal);
	$filteredPacks = array_filter($packs, function ($value)
	{
		return $value == 1; // Retain only keys with a value of 1
	});
	 
	//shortcut, css file name with the same name
	foreach ($filteredPacks as $pack => $value)
	{

		e107::css("glyphs", "{$pack}/css/{$pack}.min.css");
	}

}
