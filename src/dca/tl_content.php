<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @package   product
 * @author    Hamid Abbaszadeh
 * @license   LGPL-3.0+
 * @copyright 2014-2016
 */

 /**
 * Dynamically add the permission check and parent table
 */
if (Input::get('do') == 'staff')
{
	$GLOBALS['TL_DCA']['tl_content']['config']['ptable'] = 'tl_staff_member';
	$GLOBALS['TL_DCA']['tl_content']['list']['sorting']['headerFields'] = array('firstname', 'lastname', 'post', 'published');

}