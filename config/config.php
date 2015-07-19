<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package   staff
 * @author    Hamid Abbaszadeh
 * @license   GNU/LGPL3
 * @copyright respinar 2014
 */


/**
 * Back end modules
 */
array_insert($GLOBALS['BE_MOD']['content'], 1, array
(
	'staff' => array
	(
		'tables'     => array('tl_staff','tl_staff_member'),
		'icon'       => 'system/modules/staff/assets/icon.png',
	)
));

/**
 * Front end modules
 */

array_insert($GLOBALS['FE_MOD'], 2, array
(
	'staff' => array
	(
		'staff_list'    => 'ModuleStaffList',
		'staff_detail'  => 'ModuleStaffDetail'
	)
));


/**
 * Add permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'staffs';
$GLOBALS['TL_PERMISSIONS'][] = 'staffp';
