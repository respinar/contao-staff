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
	 * Sort out protected archives
	 * @param array
	 * @return array
	 */
	protected function sortOutProtected($arrStaffCategories)
	{
		if (BE_USER_LOGGED_IN || !is_array($arrStaffCategories) || empty($arrStaffCategories))
		{
			return $arrStaffCategories;
		}

		$this->import('FrontendUser', 'User');
		$objStaff = \StaffModel::findMultipleByIds($arrStaffCategories);
		$arrStaffCategories = array();

		if ($objStaff !== null)
		{
			while ($objStaff->next())
			{
				if ($objStaff->protected)
				{
					if (!FE_USER_LOGGED_IN)
					{
						continue;
					}

					$staffs = deserialize($objStaff->staffs);

					if (!is_array($staffs) || empty($staffs) || !count(array_intersect($staffs, $this->User->staffs)))
					{
						continue;
					}
				}

				$arrStaffCategories[] = $objStaff->id;
			}
		}

		return $arrStaffCategories;
	}


	/**
	 * Parse an item and return it as string
	 * @param object
	 * @param boolean
	 * @param string
	 * @param integer
	 * @return string
	 */
	protected function parsePerson($objPerson, $blnAddStaff=false, $strClass='', $intCount=0)
	{
		global $objPage;

		$objTemplate = new \FrontendTemplate($this->person_template);
		$objTemplate->setData($objPerson->row());

		$objTemplate->class = (($this->person_class != '') ? ' ' . $this->person_class : '') . $strClass;

		if (!empty($objPerson->education))
		{
			$objTemplate->education    = deserialize($objPerson->education);
		}

		$objTemplate->link        = $this->generatePersonUrl($objPerson, $blnAddStaff);

		$objTemplate->staff  = $objPerson->getRelated('pid');

		$objTemplate->count = $intCount; // see #5708

		$objTemplate->addImage = false;

		// Add an image
		if ($objPerson->singleSRC != '')
		{
			$objModel = \FilesModel::findByUuid($objPerson->singleSRC);

			if ($objModel === null)
			{
				if (!\Validator::isUuid($objPerson->singleSRC))
				{
					$objTemplate->text = '<p class="error">'.$GLOBALS['TL_LANG']['ERR']['version2format'].'</p>';
				}
			}
			elseif (is_file(TL_ROOT . '/' . $objModel->path))
			{
				// Do not override the field now that we have a model registry (see #6303)
				$arrPerson = $objPerson->row();

				// Override the default image size
				if ($this->imgSize != '')
				{
					$size = deserialize($this->imgSize);

					if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2]))
					{
						$arrPerson['size'] = $this->imgSize;
					}
				}

				$arrPerson['singleSRC'] = $objModel->path;
				$strLightboxId = 'lightbox[lb' . $this->id . ']';
				$arrPerson['fullsize'] = $this->fullsize;
				$this->addImageToTemplate($objTemplate, $arrPerson,null, $strLightboxId);
			}
		}

		$objTemplate->enclosure = array();

		// Add enclosures
		if ($objPerson->addEnclosure)
		{
			$this->addEnclosuresToTemplate($objTemplate, $objPerson->row());
		}

		return $objTemplate->parse();
	}


	/**
	 * Parse one or more items and return them as array
	 * @param object
	 * @param boolean
	 * @return array
	 */
	protected function parsePersons($objPersons, $blnAddStaff=false)
	{
		$limit = $objPersons->count();

		if ($limit < 1)
		{
			return array();
		}

		$count = 0;
		$arrPersons = array();

		while ($objPersons->next())
		{
			$arrPersons[] = $this->parsePerson($objPersons, $blnAddStaff, ((++$count == 1) ? ' first' : '') . (($count == $limit) ? ' last' : '') . ((($count % 2) == 0) ? ' odd' : ' even'), $count);
		}

		return $arrPersons;
	}

	/**
	 * Generate a URL and return it as string
	 * @param object
	 * @param boolean
	 * @return string
	 */
	protected function generatePersonUrl($objItem, $blnAddStaff=false)
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
	protected function generateLink($strLink, $objPerson, $blnAddStaff=false, $blnIsReadMore=false)
	{

		return sprintf('<a href="%s" title="%s">%s%s</a>',
						$this->generatePersonUrl($objPerson, $blnAddStaff),
						specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $objPerson->firstname . ' ' . $objPerson->lastname), true),
						$strLink,
						($blnIsReadMore ? ' <span class="invisible">'.$objPerson->title.'</span>' : ''));

	}

}
