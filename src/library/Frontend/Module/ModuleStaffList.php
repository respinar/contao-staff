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
 * Namespace
 */
namespace Respinar\Staff\Frontend\Module;

use Respinar\Staff\Model\StaffMemberModel;
use Respinar\Staff\Frontend\Module\ModuleStaff;


/**
 * Class ModuleStaffList
 */
class ModuleStaffList extends ModuleStaff
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_staff_list';

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['staff_list'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		$this->staff_categories = $this->sortOutProtected(deserialize($this->staff_categories));

		// No staff categories available
		if (!is_array($this->staff_categories) || empty($this->staff_categories))
		{
			return '';
		}

		// Show the staff detail if an item has been selected
		if ($this->staff_detailModule > 0 && (isset($_GET['items']) || ($GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item']))))
		{
			return $this->getFrontendModule($this->staff_detailModule, $this->strColumn);
		}

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{

		$offset = intval($this->skipFirst);
		$limit = null;

		// Maximum number of items
		if ($this->numberOfItems > 0)
		{
			$limit = $this->numberOfItems;
		}

		$this->Template->persons = array();
		$this->Template->empty = $GLOBALS['TL_LANG']['MSC']['emptyStaff'];

		$intTotal = StaffMemberModel::countPublishedByPids($this->staff_categories);

		if ($intTotal < 1)
		{
			return;
		}

		$total = $intTotal - $offset;


		// Split the results
		if ($this->perPage > 0 && (!isset($limit) || $this->numberOfItems > $this->perPage))
		{
			// Adjust the overall limit
			if (isset($limit))
			{
				$total = min($limit, $total);
			}

			// Get the current page
			$id = 'page_n' . $this->id;
			$page = \Input::get($id) ?: 1;

			// Do not index or cache the page if the page number is outside the range
			if ($page < 1 || $page > max(ceil($total/$this->perPage), 1))
			{
				global $objPage;
				$objPage->noSearch = 1;
				$objPage->cache = 0;

				// Send a 404 header
				header('HTTP/1.1 404 Not Found');
				return;
			}

			// Set limit and offset
			$limit = $this->perPage;
			$offset += (max($page, 1) - 1) * $this->perPage;
			$skip = intval($this->skipFirst);

			// Overall limit
			if ($offset + $limit > $total + $skip)
			{
				$limit = $total + $skip - $offset;
			}

			// Add the pagination menu
			$objPagination = new \Pagination($total, $this->perPage, \Config::get('maxPaginationLinks'), $id);
			$this->Template->pagination = $objPagination->generate("\n  ");
		}

		// Get the items
		if (isset($limit))
		{
			$objMembers = StaffMemberModel::findPublishedByPids($this->staff_categories, null, $limit, $offset);
		}
		else
		{
			$objMembers = StaffMemberModel::findPublishedByPids($this->staff_categories, null, 0, $offset);
		}


		// Add the members
		if ($objMembers !== null)
		{
			$this->Template->members = $this->parseMembers($objMembers);
		}

	}
}
