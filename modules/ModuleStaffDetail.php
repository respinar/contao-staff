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
 * Namespace
 */
namespace staff;


/**
 * Class ModuleStaffDetail
 *
 * @copyright  respinar 2014
 * @author     Hamid Abbaszadeh
 * @package    Devtools
 */
class ModuleStaffDetail extends \ModuleStaff
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_staff_detail';

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['staff_detail'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		// Set the item from the auto_item parameter
		if (!isset($_GET['items']) && $GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item']))
        {
			\Input::setGet('items', \Input::get('auto_item'));
        }

        $this->staff_categories = $this->sortOutProtected(deserialize($this->staff_categories));

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{

		global $objPage;

		$this->Template->employee = '';
		$this->Template->referer = 'javascript:history.go(-1)';
		$this->Template->back = $GLOBALS['TL_LANG']['MSC']['goBack'];

		$objEmployee = \StaffEmployeeModel::findPublishedByParentAndIdOrAlias(\Input::get('items'),$this->staff_categories);

		// Overwrite the page title
		if ($objEmployee->title != '')
		{
			$objPage->pageTitle = strip_tags(strip_insert_tags($objEmployee->firstname . ' ' .$objEmployee->lastname));
		}

		// Overwrite the page description
		if ($objEmployee->description != '')
		{
			$objPage->description = $this->prepareMetaDescription($objEmployee->description);
		}

		$arrEmployee = $this->parseEmployee($objEmployee);

		$this->Template->employee = $arrEmployee;

	}
}
