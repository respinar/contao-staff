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
 * Add palettes to tl_module
 */

$GLOBALS['TL_DCA']['tl_module']['palettes']['department_list'] = '{title_legend},name,headline,type;
                                                                  {department_legend},departments;
                                                                  {config_legend},department_detailModule,numberOfItems,perPage,skipFirst;
                                                                  {template_legend},person_template,customTpl;
                                                                  {person_legend},person_class,imgSize;
                                                                  {protected_legend:hide},protected;
                                                                  {expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['department_detail'] = '{title_legend},name,headline,type;
                                                                  {departmets_legend},departments;
                                                                  {image_legend},imgSize,fullsize;
                                                                  {template_legend:hide},person_template,customTpl;
                                                                  {protected_legend:hide},protected;
                                                                  {expert_legend:hide},guests,cssID,space';


/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['departments'] = array
(
	'label'                => &$GLOBALS['TL_LANG']['tl_module']['departments'],
	'exclude'              => true,
	'inputType'            => 'checkbox',
	'options_callback'     => array('tl_module_department', 'getDepartments'),
	'eval'                 => array('multiple'=>true, 'mandatory'=>true),
    'sql'                  => "blob NULL"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['person_template'] = array
(
	'label'                => &$GLOBALS['TL_LANG']['tl_module']['person_template'],
	'exclude'              => true,
	'inputType'            => 'select',
	'options_callback'     => array('tl_module_department', 'getPersonTemplates'),
	'eval'                 => array('tl_class'=>'w50'),
    'sql'                  => "varchar(64) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['department_detailModule'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['department_detailModule'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_module_department', 'getDetailModules'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_module'],
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['person_class'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['person_class'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>128, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['fullsize']['eval']['tl_class'] = 'w50 m12';

/**
 * Class tl_module_catalog
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Hamid Abbaszadeh 2014
 * @author     Hamid Abbaszadeh <http://respinar.com>
 * @package    Catalog
 */
class tl_module_department extends Backend
{

	/**
	 * Get all news archives and return them as array
	 * @return array
	 */
	public function getDepartments()
	{
		//if (!$this->User->isAdmin && !is_array($this->User->news))
		//{
		//	return array();
		//}

		$arrDepartments = array();
		$objDepartments = $this->Database->execute("SELECT id, title FROM tl_department ORDER BY title");

		while ($objDepartments->next())
		{
			//if ($this->User->hasAccess($objArchives->id, 'news'))
			//{
				$arrDepartments[$objDepartments->id] = $objDepartments->title;
			//}
		}

		return $arrDepartments;
	}

	/**
	 * Return all prices templates as array
	 * @param object
	 * @return array
	 */
	public function getPersonTemplates(DataContainer $dc)
	{
		return $this->getTemplateGroup('person_', $dc->activeRecord->pid);
	}

	/**
	 * Get all product detail modules and return them as array
	 * @return array
	 */
	public function getDetailModules()
	{
		$arrModules = array();
		$objModules = $this->Database->execute("SELECT m.id, m.name, t.name AS theme FROM tl_module m LEFT JOIN tl_theme t ON m.pid=t.id WHERE m.type='department_detail' ORDER BY t.name, m.name");

		while ($objModules->next())
		{
			$arrModules[$objModules->theme][$objModules->id] = $objModules->name . ' (ID ' . $objModules->id . ')';
		}

		return $arrModules;
	}
}


