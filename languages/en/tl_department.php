<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package   catalog
 * @author    Hamid Abbaszadeh
 * @license   GNU/LGPL
 * @copyright 2014
 */


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_department']['title']     = array('Department Title', 'Please enter the department title.');
$GLOBALS['TL_LANG']['tl_department']['jumpTo']    = array('Redirect page', 'Please choose the list page to which visitors will be redirected when clicking a menu.');
$GLOBALS['TL_LANG']['tl_department']['protected'] = array('Protect department', 'Show department items to certain member groups only.');
$GLOBALS['TL_LANG']['tl_department']['groups']    = array('Allowed member groups', 'These groups will be able to see the menu items in this catalog.');
$GLOBALS['TL_LANG']['tl_department']['master']    = array('Master department', 'Please define the master department to allow language switching.');
$GLOBALS['TL_LANG']['tl_department']['language']  = array('Language', 'Please enter the language according to the RFC3066 format (e.g. en, en-us or en-cockney).');


/**
 * References
 */
$GLOBALS['TL_LANG']['tl_department']['isMaster']  = 'This is a master department';
$GLOBALS['TL_LANG']['tl_department']['isSlave']   = 'Master department is "%s"';


/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_department']['title_legend']     = 'Department Title';
$GLOBALS['TL_LANG']['tl_department']['redirect_legend']  = 'Redirect';
$GLOBALS['TL_LANG']['tl_department']['protected_legend'] = 'Access protection';


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_department']['new']    = array('New department', 'Create a new department');
$GLOBALS['TL_LANG']['tl_department']['show']   = array('Department details', 'Show the details of department ID %s');
$GLOBALS['TL_LANG']['tl_department']['edit']   = array('Edit department', 'Edit department ID %s');
$GLOBALS['TL_LANG']['tl_department']['cut']    = array('Move department', 'Move department ID %s');
$GLOBALS['TL_LANG']['tl_department']['copy']   = array('Duplicate department', 'Duplicate department ID %s');
$GLOBALS['TL_LANG']['tl_department']['delete'] = array('Delete department', 'Delete department ID %s');
