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
 * Add palettes to tl_module
 */

$GLOBALS['TL_DCA']['tl_module']['palettes']['staff_list'] = '{title_legend},name,headline,type;{staff_legend},staff_categories;{config_legend},staff_detailModule,numberOfItems,perPage,skipFirst;{template_legend},staff_member_template,customTpl;{staff_employee_legend},staff_member_class,staff_member_perRow;{image_legend},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['staff_detail'] = '{title_legend},name,headline,type;{staff_legend},staff_categories;{template_legend:hide},staff_member_template,customTpl;{image_legend},imgSize,fullsize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';


/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['staff_categories'] = array
(
	'label'                => &$GLOBALS['TL_LANG']['tl_module']['staff_categories'],
	'exclude'              => true,
	'inputType'            => 'checkbox',
	'options_callback'     => array('tl_module_staff', 'getStaffCategories'),
	'eval'                 => array('multiple'=>true, 'mandatory'=>true),
    'sql'                  => "blob NULL"
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
$GLOBALS['TL_DCA']['tl_module']['fields']['staff_member_template'] = array
(
	'label'                => &$GLOBALS['TL_LANG']['tl_module']['staff_member_template'],
	'exclude'              => true,
	'inputType'            => 'select',
	'options_callback'     => array('tl_module_staff', 'getMemberTemplates'),
	'eval'                 => array('tl_class'=>'w50'),
    'sql'                  => "varchar(64) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['staff_member_class'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['staff_member_class'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>128, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['staff_member_perRow'] = array
(
    'label'                => &$GLOBALS['TL_LANG']['tl_module']['staff_member_perRow'],
    'default'              => '4',
    'exclude'              => true,
    'inputType'            => 'select',
    'options'              => array('1','2','3','4','6','12'),
    'eval'                 => array('tl_class'=>'w50'),
    'sql'                  => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['fullsize']['eval']['tl_class'] = 'w50 m12';


/**
 * Class tl_module_staff
 */
class tl_module_staff extends Backend
{

    /**
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }


    /**
     * Get all staff categories and return them as array
     *
     * @return array
     */
    public function getStaffCategories()
    {
        if (!$this->User->isAdmin && !is_array($this->User->staffs))
        {
            return array();
        }

        $arrCategories = array();
        $objCategories = $this->Database->execute("SELECT id, title FROM tl_staff_category ORDER BY title");

        while ($objCategories->next())
        {
            if ($this->User->hasAccess($objCategories->id, 'news'))
            {
                $arrCategories[$objCategories->id] = $objCategories->title;
            }
        }

        return $arrCategories;
    }

	/**
	 * Return all prices templates as array
	 *
	 * @return array
	 */
	public function getMemberTemplates()
	{
		return $this->getTemplateGroup('staff_member_');
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
