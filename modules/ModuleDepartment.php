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
 * Namespace
 */
namespace department;


/**
 * Class ModuleDepartment
 *
 * @copyright  respinar 2014
 * @author     Hamid Abbaszadeh
 * @package    Devtools
 */
abstract class ModuleDepartment extends \Module
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
	protected function sortOutProtected($arrDepartments)
	{
		if (BE_USER_LOGGED_IN || !is_array($arrDepartments) || empty($arrDepartments))
		{
			return $arrDepartments;
		}

		$this->import('FrontendUser', 'User');
		$objDepartment = \DepartmentModel::findMultipleByIds($arrDepartments);
		$arrDepartments = array();

		if ($objDepartment !== null)
		{
			while ($objDepartment->next())
			{
				if ($objDepartment->protected)
				{
					if (!FE_USER_LOGGED_IN)
					{
						continue;
					}

					$groups = deserialize($objDepartment->groups);

					if (!is_array($groups) || empty($groups) || !count(array_intersect($groups, $this->User->groups)))
					{
						continue;
					}
				}

				$arrDepartments[] = $objDepartment->id;
			}
		}

		return $arrDepartments;
	}


	/**
	 * Parse an item and return it as string
	 * @param object
	 * @param boolean
	 * @param string
	 * @param integer
	 * @return string
	 */
	protected function parsePerson($objPerson, $blnAddDepartment=false, $strClass='', $intCount=0)
	{
		global $objPage;

		$objTemplate = new \FrontendTemplate($this->person_template);
		$objTemplate->setData($objPerson->row());

		$objTemplate->class = (($this->person_class != '') ? ' ' . $this->person_class : '') . $strClass;


		$objTemplate->title = $objPerson->firstname . ' ' . $objPerson->lastname;

		if (!empty($objPerson->education))
		{
			$objTemplate->education    = deserialize($objPerson->education);
		}

		$objTemplate->link        = $this->generateSetUrl($objPerson, $blnAddDepartment);
		$objTemplate->more        = $this->generateLink($GLOBALS['TL_LANG']['MSC']['moredetail'], $objPerson, $blnAddDepartment, true);

		$objTemplate->department  = $objPerson->getRelated('pid');

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
	protected function parsePersons($objPersons, $blnAddDepartment=false)
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
			$arrPersons[] = $this->parsePerson($objPersons, $blnAddDepartment, ((++$count == 1) ? ' first' : '') . (($count == $limit) ? ' last' : '') . ((($count % 2) == 0) ? ' odd' : ' even'), $count);
		}

		return $arrPersons;
	}

	/**
	 * Generate a URL and return it as string
	 * @param object
	 * @param boolean
	 * @return string
	 */
	protected function generateSetUrl($objItem, $blnAddDepartment=false)
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
	protected function generateLink($strLink, $objPerson, $blnAddDepartment=false, $blnIsReadMore=false)
	{

		return sprintf('<a href="%s" title="%s">%s%s</a>',
						$this->generateSetUrl($objPerson, $blnAddDepartment),
						specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $objPerson->title), true),
						$strLink,
						($blnIsReadMore ? ' <span class="invisible">'.$objPerson->title.'</span>' : ''));

	}

}
