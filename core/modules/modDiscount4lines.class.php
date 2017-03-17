<?php
/* Copyright (C) 2003      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2016 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2017      Jean-François Ferry	<jfefe@aternatik.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 	\defgroup   discount4lines     Module Discount4lines
*  \brief      Example of a module descriptor.
*				Such a file must be copied into htdocs/discount4lines/core/modules directory.
*  \file       htdocs/discount4lines/core/modules/modDiscount4lines.class.php
*  \ingroup    discount4lines
*  \brief      Description and activation file for module Discount4lines
*/
include_once DOL_DOCUMENT_ROOT .'/core/modules/DolibarrModules.class.php';


/**
 *  Description and activation class for module Discount4lines
 */
class modDiscount4lines extends DolibarrModules
{
	/**
	 * Constructor. Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		global $langs,$conf;

		$this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 104973;		// TODO Go on page http://wiki.dolibarr.org/index.php/List_of_modules_id to reserve id number for your module
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'discount4lines';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','interface','other'
		// It is used to group modules by family in module setup page
		$this->family = "crm";
		// Module position in the family
		$this->module_position = 500;
		// Gives the possibility to the module, to provide his own family info and position of this family (Overwrite $this->family and $this->module_position. Avoid this)
		//$this->familyinfo = array('myownfamily' => array('position' => '001', 'label' => $langs->trans("MyOwnFamily")));

		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
		$this->description = "Description of module Discount4lines";
		$this->descriptionlong = "A very lon description. Can be a full HTML content";
		$this->editor_name = 'aternatik';
		$this->editor_url = 'http://www.aternatik.fr';

		// Possible values for version are: 'development', 'experimental', 'dolibarr', 'dolibarr_deprecated' or a version string like 'x.y.z'
		$this->version = '1.0';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='generic';

		$this->module_parts = array(
			'hooks' => array('invoicecard','propalcard')
		);

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/discount4lines/temp");
		$this->dirs = array();

		// Config pages. Put here list of php page, stored into discount4lines/admin directory, to use to setup module.
		//$this->config_page_url = array("");

		// Dependencies
		$this->hidden = false;			// A condition to hide module
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->conflictwith = array();	// List of modules id this module is in conflict with
		$this->phpmin = array(5,0);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(4,0);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("discount4lines@discount4lines");

		// Constants
		$this->const = array();

		$this->tabs = array();

		if (! isset($conf->discount4lines) || ! isset($conf->discount4lines->enabled))
		{
			$conf->discount4lines=new stdClass();
			$conf->discount4lines->enabled=0;
		}

		// Dictionaries
		$this->dictionaries=array();

		// Cronjobs
		$this->cronjobs = array();			// List of cron jobs entries to add
		// Example: $this->cronjobs=array(0=>array('label'=>'My label', 'jobtype'=>'method', 'class'=>'/dir/class/file.class.php', 'objectname'=>'MyClass', 'method'=>'myMethod', 'parameters'=>'', 'comment'=>'Comment', 'frequency'=>2, 'unitfrequency'=>3600, 'test'=>true),
		//                                1=>array('label'=>'My label', 'jobtype'=>'command', 'command'=>'', 'parameters'=>'', 'comment'=>'Comment', 'frequency'=>1, 'unitfrequency'=>3600*24, 'test'=>true)
		// );

		// Permissions
		$this->rights = array();		// Permission array used by this module
		$r=0;


		// Main menu entries
		$this->menu = array();			// List of menus to add
		$r=0;


		// Exports
		$r=1;

	}

	/**
	 *		Function called when module is enabled.
	 *		The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *		It also creates data directories
	 *
	 *      @param      string	$options    Options when enabling module ('', 'noboxes')
	 *      @return     int             	1 if OK, 0 if KO
	 */
	public function init($options='')
	{
		$sql = array();

		//$this->_load_tables('/discount4lines/sql/');

		return $this->_init($sql, $options);
	}

	/**
	 * Function called when module is disabled.
	 * Remove from database constants, boxes and permissions from Dolibarr database.
	 * Data directories are not deleted
	 *
	 * @param      string	$options    Options when enabling module ('', 'noboxes')
	 * @return     int             	1 if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		$sql = array();

		return $this->_remove($sql, $options);
	}

}

