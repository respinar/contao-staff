<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'Respinar\Staff',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'Respinar\Staff\Staff'              => 'system/modules/staff/classes/Staff.php',

	// Modules
	'Respinar\Staff\ModuleStaff'        => 'system/modules/staff/modules/ModuleStaff.php',
	'Respinar\Staff\ModuleStaffList'    => 'system/modules/staff/modules/ModuleStaffList.php',
	'Respinar\Staff\ModuleStaffDetail'  => 'system/modules/staff/modules/ModuleStaffDetail.php',

	// Models
	'Respinar\Staff\StaffCategoryModel' => 'system/modules/staff/models/StaffCategoryModel.php',
	'Respinar\Staff\StaffMemberModel'   => 'system/modules/staff/models/StaffMemberModel.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_staff_detail'   => 'system/modules/staff/templates/modules',
	'mod_staff_list'     => 'system/modules/staff/templates/modules',
	'staff_member_full'  => 'system/modules/staff/templates/staff_member',
	'staff_member_short' => 'system/modules/staff/templates/staff_member',
));
