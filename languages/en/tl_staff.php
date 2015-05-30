<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @package   staff
 * @author    Hamid Abbaszadeh
 * @license   GNU/LGPL3
 * @copyright 2014-2015
 */


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_staff']['title']     = array('Staff category title', 'Please enter the staff category title.');
$GLOBALS['TL_LANG']['tl_staff']['jumpTo']    = array('Redirect page', 'Please choose the list page to which visitors will be redirected when clicking a menu.');
$GLOBALS['TL_LANG']['tl_staff']['protected'] = array('Protect staff', 'Show staff category to certain member only.');
$GLOBALS['TL_LANG']['tl_staff']['staffs']    = array('Allowed members', 'These staffs will be able to see the menu items in this category.');
$GLOBALS['TL_LANG']['tl_staff']['master']    = array('Master staff category', 'Please define the master staff category to allow language switching.');
$GLOBALS['TL_LANG']['tl_staff']['language']  = array('Language', 'Please enter the language according to the RFC3066 format (e.g. en, en-us or en-cockney).');


/**
 * References
 */
$GLOBALS['TL_LANG']['tl_staff']['isMaster']  = 'This is a master staff';
$GLOBALS['TL_LANG']['tl_staff']['isSlave']   = 'Master staff is "%s"';


/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_staff']['title_legend']     = 'Staff Title';
$GLOBALS['TL_LANG']['tl_staff']['redirect_legend']  = 'Redirect';
$GLOBALS['TL_LANG']['tl_staff']['protected_legend'] = 'Access protection';


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_staff']['new']    = array('New staff category', 'Add a new staff Category');
$GLOBALS['TL_LANG']['tl_staff']['show']   = array('Staff category details', 'Show the details of staff category ID %s');
$GLOBALS['TL_LANG']['tl_staff']['edit']   = array('Edit staff category', 'Edit staff category ID %s');
$GLOBALS['TL_LANG']['tl_staff']['cut']    = array('Move staff category', 'Move staff category ID %s');
$GLOBALS['TL_LANG']['tl_staff']['copy']   = array('Duplicate staff category', 'Duplicate staff category ID %s');
$GLOBALS['TL_LANG']['tl_staff']['delete'] = array('Delete staff category', 'Delete staff category ID %s');
