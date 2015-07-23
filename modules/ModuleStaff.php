<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @package   Staff
 * @author    Hamid Abbaszadeh
 * @license   GNU/LGPL3
 * @copyright 2014-2015
 */


/**
 * Namespace
 */
namespace staff;


/**
 * Class ModuleStaff
 *
 * @copyright  respinar 2014
 * @author     Hamid Abbaszadeh
 * @package    Staff
 */
abstract class ModuleStaff extends \Module
{

	/**
	 * URL cache array
	 * @var array
	 */
	private static $arrUrlCache = array();


	/**
	 * Sort out protected categories
	 *
	 * @param array $arrCategories
	 *
	 * @return array
	 */
	protected function sortOutProtected($arrCategories)
	{
		if (BE_USER_LOGGED_IN || !is_array($arrCategories) || empty($arrCategories))
		{
			return $arrCategories;
		}

		$this->import('FrontendUser', 'User');
		$objCategory = \StaffCategoryModel::findMultipleByIds($arrCategories);
		$arrCategories = array();

		if ($objCategory !== null)
		{
			while ($objCategory->next())
			{
				if ($objCategory->protected)
				{
					if (!FE_USER_LOGGED_IN)
					{
						continue;
					}

					$groups = deserialize($objCategory->groups);

					if (!is_array($groups) || empty($groups) || !count(array_intersect($groups, $this->User->groups)))
					{
						continue;
					}
				}

				$arrCategories[] = $objCategory->id;
			}
		}

		return $arrCategories;
	}


	/**
	 * Parse an item and return it as string
	 * @param object
	 * @param boolean
	 * @param string
	 * @param integer
	 * @return string
	 */
	protected function parseEmployee($objEmployee, $blnAddStaff=false, $strClass='', $intCount=0)
	{
		global $objPage;

		$objTemplate = new \FrontendTemplate($this->staff_employee_template);
		$objTemplate->setData($objEmployee->row());

		$objTemplate->class = (($this->staff_employee_class != '') ? ' ' . $this->staff_employee_class : '') . $strClass;

		if (!empty($objEmployee->education))
		{
			$objTemplate->education    = deserialize($objEmployee->education);
		}

		$objTemplate->link   = $this->generateEmployeeUrl($objEmployee, $blnAddStaff);

		$objTemplate->staff  = $objEmployee->getRelated('pid');


		$objTemplate->txt_educations = $GLOBALS['TL_LANG']['MSC']['educations'];
		$objTemplate->txt_contact    = $GLOBALS['TL_LANG']['MSC']['contact'];
		$objTemplate->txt_room       = $GLOBALS['TL_LANG']['MSC']['room'];
		$objTemplate->txt_phone      = $GLOBALS['TL_LANG']['MSC']['phone'];
		$objTemplate->txt_mobile     = $GLOBALS['TL_LANG']['MSC']['mobile'];
		$objTemplate->txt_fax        = $GLOBALS['TL_LANG']['MSC']['fax'];
		$objTemplate->txt_email      = $GLOBALS['TL_LANG']['MSC']['email'];
		$objTemplate->txt_website    = $GLOBALS['TL_LANG']['MSC']['website'];
		$objTemplate->txt_facebook   = $GLOBALS['TL_LANG']['MSC']['facebook'];
		$objTemplate->txt_googleplus = $GLOBALS['TL_LANG']['MSC']['googleplus'];
		$objTemplate->txt_twitter    = $GLOBALS['TL_LANG']['MSC']['twitter'];
		$objTemplate->txt_linkedin   = $GLOBALS['TL_LANG']['MSC']['linkedin'];

		$objTemplate->count  = $intCount; // see #5708

		$objTemplate->addImage = false;

		// Add an image
		if ($objEmployee->singleSRC != '')
		{
			$objModel = \FilesModel::findByUuid($objEmployee->singleSRC);

			if ($objModel === null)
			{
				if (!\Validator::isUuid($objEmployee->singleSRC))
				{
					$objTemplate->text = '<p class="error">'.$GLOBALS['TL_LANG']['ERR']['version2format'].'</p>';
				}
			}
			elseif (is_file(TL_ROOT . '/' . $objModel->path))
			{
				// Do not override the field now that we have a model registry (see #6303)
				$arrEmployee = $objEmployee->row();

				// Override the default image size
				if ($this->imgSize != '')
				{
					$size = deserialize($this->imgSize);

					if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2]))
					{
						$arrEmployee['size'] = $this->imgSize;
					}
				}

				$arrEmployee['singleSRC'] = $objModel->path;
				$strLightboxId = 'lightbox[lb' . $this->id . ']';
				$arrEmployee['fullsize'] = $this->fullsize;
				$this->addImageToTemplate($objTemplate, $arrEmployee,null, $strLightboxId);
			}
		}

		$objTemplate->enclosure = array();

		// Add enclosures
		if ($objEmployee->addEnclosure)
		{
			$this->addEnclosuresToTemplate($objTemplate, $objEmployee->row());
		}

		return $objTemplate->parse();
	}


	/**
	 * Parse one or more items and return them as array
	 * @param object
	 * @param boolean
	 * @return array
	 */
	protected function parseEmployees($objEmployees, $blnAddStaff=false)
	{
		$limit = $objEmployees->count();

		if ($limit < 1)
		{
			return array();
		}

		$count = 0;
		$arrEmployees = array();

		while ($objEmployees->next())
		{
			$arrEmployees[] = $this->parseEmployee($objEmployees, $blnAddStaff, ((++$count == 1) ? ' first' : '') . (($count == $limit) ? ' last' : '') . ((($count % $this->staff_employee_perRow) == 0) ? ' last_col' : '') . ((($count % $this->staff_employee_perRow) == 1) ? ' first_col' : ''), $count);
		}

		return $arrEmployees;
	}

	/**
	 * Generate a URL and return it as string
	 * @param object
	 * @param boolean
	 * @return string
	 */
	protected function generateEmployeeUrl($objItem, $blnAddStaff=false)
	{
		$strCacheKey = 'id_' . $objItem->id;

		// Load the URL from cache
		if (isset(self::$arrUrlCache[$strCacheKey]))
		{
			return self::$arrUrlCache[$strCacheKey];
		}

		// Initialize the cache
		self::$arrUrlCache[$strCacheKey] = null;

		// Link to the default page
		if (self::$arrUrlCache[$strCacheKey] === null)
		{
			$objPage = \PageModel::findByPk($objItem->getRelated('pid')->jumpTo);

			if ($objPage === null)
			{
				self::$arrUrlCache[$strCacheKey] = ampersand(\Environment::get('request'), true);
			}
			else
			{
				self::$arrUrlCache[$strCacheKey] = ampersand($this->generateFrontendUrl($objPage->row(), ((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ?  '/' : '/items/') . ((!\Config::get('disableAlias') && $objItem->alias != '') ? $objItem->alias : $objItem->id)));
			}

		}

		return self::$arrUrlCache[$strCacheKey];
	}


	/**
	 * Generate a link and return it as string
	 * @param string
	 * @param object
	 * @param boolean
	 * @param boolean
	 * @return string
	 */
	protected function generateLink($strLink, $objEmployee, $blnAddStaff=false, $blnIsReadMore=false)
	{

		return sprintf('<a href="%s" title="%s">%s%s</a>',
						$this->generateEmployeeUrl($objEmployee, $blnAddStaff),
						specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $objEmployee->firstname . ' ' . $objEmployee->lastname), true),
						$strLink,
						($blnIsReadMore ? ' <span class="invisible">'.$objEmployee->firstname . ' ' . $objEmployee->lastname.'</span>' : ''));

	}

}
