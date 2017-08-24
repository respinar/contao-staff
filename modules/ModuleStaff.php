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
namespace Respinar\Staff;


/**
 * Class ModuleStaff
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
		$objCategory = StaffCategoryModel::findMultipleByIds($arrCategories);
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
	protected function parseMember($objMember, $blnAddStaff=false, $strClass='', $intCount=0)
	{
		global $objPage;

		$objTemplate = new \FrontendTemplate($this->staff_member_template);
		$objTemplate->setData($objMember->row());

		$objTemplate->class = (($this->staff_member_class != '') ? ' ' . $this->staff_member_class : '') . $strClass;

		if (!empty($objMember->education))
		{
			$objTemplate->education    = deserialize($objMember->education);
		}

		$objTemplate->link   = $this->generateMemberUrl($objMember, $blnAddStaff);

		$objTemplate->staff  = $objMember->getRelated('pid');


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
		if ($objMember->singleSRC != '')
		{
			$objModel = \FilesModel::findByUuid($objMember->singleSRC);

			if ($objModel === null)
			{
				if (!\Validator::isUuid($objMember->singleSRC))
				{
					$objTemplate->text = '<p class="error">'.$GLOBALS['TL_LANG']['ERR']['version2format'].'</p>';
				}
			}
			elseif (is_file(TL_ROOT . '/' . $objModel->path))
			{
				// Do not override the field now that we have a model registry (see #6303)
				$arrMember = $objMember->row();

				// Override the default image size
				if ($this->imgSize != '')
				{
					$size = deserialize($this->imgSize);

					if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2]))
					{
						$arrMember['size'] = $this->imgSize;
					}
				}

				$arrMember['singleSRC'] = $objModel->path;
				$strLightboxId = 'lightbox[lb' . $this->id . ']';
				$arrMember['fullsize'] = $this->fullsize;
				$this->addImageToTemplate($objTemplate, $arrMember,null, $strLightboxId);
			}
		}

		$objTemplate->enclosure = array();

		// Add enclosures
		if ($objMember->addEnclosure)
		{
			$this->addEnclosuresToTemplate($objTemplate, $objMember->row());
		}

		return $objTemplate->parse();
	}


	/**
	 * Parse one or more items and return them as array
	 * @param object
	 * @param boolean
	 * @return array
	 */
	protected function parseMembers($objMembers, $blnAddStaff=false)
	{
		$limit = $objMembers->count();

		if ($limit < 1)
		{
			return array();
		}

		$count = 0;
		$arrMembers = array();

		while ($objMembers->next())
		{
			$arrMembers[] = $this->parseMember($objMembers, $blnAddStaff, ((++$count == 1) ? ' first' : '') . (($count == $limit) ? ' last' : '') . ((($count % $this->staff_member_perRow) == 0) ? ' last_col' : '') . ((($count % $this->staff_member_perRow) == 1) ? ' first_col' : ''), $count);
		}

		return $arrMembers;
	}

	/**
	 * Generate a URL and return it as string
	 * @param object
	 * @param boolean
	 * @return string
	 */
	protected function generateMemberUrl($objItem, $blnAddStaff=false)
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
	protected function generateLink($strLink, $objMember, $blnAddStaff=false, $blnIsReadMore=false)
	{

		return sprintf('<a href="%s" title="%s">%s%s</a>',
						$this->generateMemberUrl($objMember, $blnAddStaff),
						specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $objMember->firstname . ' ' . $objMember->lastname), true),
						$strLink,
						($blnIsReadMore ? ' <span class="invisible">'.$objMember->firstname . ' ' . $objMember->lastname.'</span>' : ''));

	}

}
