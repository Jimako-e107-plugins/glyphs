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

	$path = e_PLUGIN  . "glyphs/glyphs.xml";

	$xml = e107::getXml();

	$vars = $xml->loadXMLfile($path, 'advanced');

	$vars['glyphs'] = array();

	if (!empty($vars['glyphicons']['glyph']))
	{
		foreach ($vars['glyphicons']['glyph'] as $val)
		{
			if (isset($val['@attributes']['name']))  $name =  $val['@attributes']['name'];
			else continue;

			$vars['glyphs'][$name] = array(
				'name'    => $name,
				'pattern' => isset($val['@attributes']['pattern']) ? $val['@attributes']['pattern'] : '',
				'path'    => isset($val['@attributes']['path']) ? e107::getParser()->replaceConstants($val['@attributes']['path'], 'full') : '',
				'class'   => isset($val['@attributes']['class']) ? $val['@attributes']['class'] : '',
				'prefix'  => isset($val['@attributes']['prefix']) ? $val['@attributes']['prefix'] : '',
				'tag'     => isset($val['@attributes']['tag']) ? $val['@attributes']['tag'] : '',
			);
		}

		unset($vars['glyphicons']);
	}

 
	$glyphs =  $vars['glyphs'];

	//shortcut, css file name with the same name
	foreach ($filteredPacks as $pack => $value)
	{ 
		$file = $glyphs[$pack]['path'];
		e107::css("glyphs", $file);
	}

}
