<?php
/*----------------------------------------------------------------------------------|  www.vdm.io  |----/
				Most Wanted Web Services, Inc. 
/-------------------------------------------------------------------------------------------------------/

	@version		3.1.18
	@build			29th October, 2018
	@created		1st May, 2016
	@package		Real Estate NOW!
	@subpackage		cities.php
	@author			Most Wanted Web Services, Inc. <https://mostwantedrealestatesites.com>	
	@copyright		Copyright (C) 2015-2018. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
	
	Real Estate NOW! Component
	
/------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Cities Form Field class for the Realestatenow component
 */
class JFormFieldCities extends JFormFieldList
{
	/**
	 * The cities field type.
	 *
	 * @var		string
	 */
	public $type = 'cities';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array    An array of JHtml options.
	 */
	protected function getOptions()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('a.id','a.name'),array('id','city_request_id_name')));
		$query->from($db->quoteName('#__realestatenow_city', 'a'));
		$query->where($db->quoteName('a.published') . ' = 1');
		$query->order('a.name ASC');
		$db->setQuery((string)$query);
		$items = $db->loadObjectList();
		$options = array();
		if ($items)
		{
			$options[] = JHtml::_('select.option', '', 'Select a city');
			foreach($items as $item)
			{
				$options[] = JHtml::_('select.option', $item->id, $item->city_request_id_name);
			}
		}
		return $options;
	}
}
