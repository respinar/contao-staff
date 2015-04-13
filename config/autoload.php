<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Staff
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'staff',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'staff\Staff'             => 'system/modules/staff/classes/Staff.php',

	// Modules
	'staff\ModuleStaff'       => 'system/modules/staff/modules/ModuleStaff.php',
	'staff\ModuleStaffList'   => 'system/modules/staff/modules/ModuleStaffList.php',
	'staff\ModuleStaffDetail' => 'system/modules/staff/modules/ModuleStaffDetail.php',

	// Models
	'staff\StaffModel'        => 'system/modules/staff/models/StaffModel.php',
	'staff\StaffMemberModel'  => 'system/modules/staff/models/StaffMemberModel.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_staff_list'   => 'system/modules/staff/templates/modules',
	'mod_staff_detail' => 'system/modules/staff/templates/modules',
	'member_full'      => 'system/modules/staff/templates/member',
	'member_short'     => 'system/modules/staff/templates/member',
));
