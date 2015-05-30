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
 * Add palettes to tl_module
 */

$GLOBALS['TL_DCA']['tl_module']['palettes']['staff_list'] = '{title_legend},name,headline,type;
                                                                  {staff_legend},staff_categories;
                                                                  {config_legend},staff_detailModule,numberOfItems,perPage,skipFirst;
                                                                  {template_legend},member_template,customTpl;
                                                                  {member_legend},member_class,imgSize;
                                                                  {protected_legend:hide},protected;
                                                                  {expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['staff_detail'] = '{title_legend},name,headline,type;
                                                                  {staff_legend},staff_categories;
                                                                  {image_legend},imgSize,fullsize;
                                                                  {template_legend:hide},member_template,customTpl;
                                                                  {protected_legend:hide},protected;
                                                                  {expert_legend:hide},guests,cssID,space';


/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['staff_categories'] = array
(
	'label'                => &$GLOBALS['TL_LANG']['tl_module']['staff_categories'],
	'exclude'              => true,
	'inputType'            => 'checkbox',
	'options_callback'     => array('tl_module_staff', 'getStaffs'),
	'eval'                 => array('multiple'=>true, 'mandatory'=>true),
    'sql'                  => "blob NULL"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['member_template'] = array
(
	'label'                => &$GLOBALS['TL_LANG']['tl_module']['member_template'],
	'exclude'              => true,
	'inputType'            => 'select',
	'options_callback'     => array('tl_module_staff', 'getMemberTemplates'),
	'eval'                 => array('tl_class'=>'w50'),
    'sql'                  => "varchar(64) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['staff_detailModule'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['staff_detailModule'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_module_staff', 'getDetailModules'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_module'],
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['member_class'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['member_class'],
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
class tl_module_staff extends Backend
{

	/**
	 * Get all news archives and return them as array
	 * @return array
	 */
	public function getStaffs()
	{
		//if (!$this->User->isAdmin && !is_array($this->User->news))
		//{
		//	return array();
		//}

		$arrStaffs = array();
		$objStaffs = $this->Database->execute("SELECT id, title FROM tl_staff ORDER BY title");

		while ($objStaffs->next())
		{
			//if ($this->User->hasAccess($objArchives->id, 'news'))
			//{
				$arrStaffs[$objStaffs->id] = $objStaffs->title;
			//}
		}

		return $arrStaffs;
	}

	/**
	 * Return all prices templates as array
	 * @param object
	 * @return array
	 */
	public function getMemberTemplates(DataContainer $dc)
	{
		return $this->getTemplateGroup('member_', $dc->activeRecord->pid);
	}

	/**
	 * Get all product detail modules and return them as array
	 * @return array
	 */
	public function getDetailModules()
	{
		$arrModules = array();
		$objModules = $this->Database->execute("SELECT m.id, m.name, t.name AS theme FROM tl_module m LEFT JOIN tl_theme t ON m.pid=t.id WHERE m.type='staff_detail' ORDER BY t.name, m.name");

		while ($objModules->next())
		{
			$arrModules[$objModules->theme][$objModules->id] = $objModules->name . ' (ID ' . $objModules->id . ')';
		}

		return $arrModules;
	}
}


