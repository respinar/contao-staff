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
	'staff',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Modules
	'staff\ModuleStaff'       => 'system/modules/staff/modules/ModuleStaff.php',
	'staff\ModuleStaffList'   => 'system/modules/staff/modules/ModuleStaffList.php',
	'staff\ModuleStaffDetail' => 'system/modules/staff/modules/ModuleStaffDetail.php',

	// Models
	'staff\StaffMemberModel'  => 'system/modules/staff/models/StaffMemberModel.php',
	'staff\StaffModel'        => 'system/modules/staff/models/StaffModel.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_staff_detail' => 'system/modules/staff/templates/modules',
	'mod_staff_list'   => 'system/modules/staff/templates/modules',
	'member_full'      => 'system/modules/staff/templates/member',
	'member_short'     => 'system/modules/staff/templates/member',
));
