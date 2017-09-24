<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @package   staff
 * @author    Hamid Abbaszadeh
 * @license   GNU/LGPL3
 * @copyright respinar 2014-2017
 */


/**
 * Back end modules
 */
array_insert($GLOBALS['BE_MOD']['content'], 1, array
(
	'staff' => array
	(
		'tables'     => array('tl_staff_category','tl_staff_member','tl_content'),
		'icon'       => 'system/modules/staff/assets/icon.png',
	)
));

/**
 * Register models
 */
 $GLOBALS['TL_MODELS']['tl_staff_member']   = 'Respinar\Staff\Model\StaffMemberModel';
 $GLOBALS['TL_MODELS']['tl_staff_category'] = 'Respinar\Staff\Model\StaffCategoryModel';

/**
 * Front end modules
 */
array_insert($GLOBALS['FE_MOD'], 2, array
(
	'staff' => array
	(
		'staff_list'    => 'Respinar\Staff\Frontend\Module\ModuleStaffList',
		'staff_detail'  => 'Respinar\Staff\Frontend\Module\ModuleStaffDetail'
	)
));


/**
 * Add permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'staffs';
$GLOBALS['TL_PERMISSIONS'][] = 'staffp';
