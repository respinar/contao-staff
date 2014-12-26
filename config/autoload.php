<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Department
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'department',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'department\Department'             => 'system/modules/department/classes/Department.php',

	// Modules
	'department\ModuleDepartment'       => 'system/modules/department/modules/ModuleDepartment.php',
	'department\ModuleDepartmentList'   => 'system/modules/department/modules/ModuleDepartmentList.php',
	'department\ModuleDepartmentDetail' => 'system/modules/department/modules/ModuleDepartmentDetail.php',

	// Models
	'department\DepartmentModel'        => 'system/modules/department/models/DepartmentModel.php',
	'department\DepartmentPersonModel'  => 'system/modules/department/models/DepartmentPersonModel.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_department_list'   => 'system/modules/department/templates/modules',
	'mod_department_detail' => 'system/modules/department/templates/modules',
	'person_full'           => 'system/modules/department/templates/person',
	'person_short'          => 'system/modules/department/templates/person',
));
