<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package   department
 * @author    Hamid Abbaszadeh
 * @license   GNU/LGPL3
 * @copyright respinar 2014
 */


/**
 * Back end modules
 */
array_insert($GLOBALS['BE_MOD']['content'], 1, array
(
	'department' => array
	(
		'tables'     => array('tl_department','tl_department_person'),
		'icon'       => 'system/modules/department/assets/icon.png',
	)
));

/**
 * Front end modules
 */

array_insert($GLOBALS['FE_MOD'], 2, array
(
	'department' => array
	(
		'department_list'    => 'ModuleDepartmentList',
		'department_detail'  => 'ModuleDepartmentDetail'
	)
));
