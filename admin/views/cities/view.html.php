<?php
/*----------------------------------------------------------------------------------|  www.vdm.io  |----/
				Most Wanted Web Services, Inc. 
/-------------------------------------------------------------------------------------------------------/

	@version		3.1.18
	@build			29th October, 2018
	@created		1st May, 2016
	@package		Real Estate NOW!
	@subpackage		view.html.php
	@author			Most Wanted Web Services, Inc. <https://mostwantedrealestatesites.com>	
	@copyright		Copyright (C) 2015-2018. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
	
	Real Estate NOW! Component
	
/------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Realestatenow View class for the Cities
 */
class RealestatenowViewCities extends JViewLegacy
{
	/**
	 * Cities view display method
	 * @return void
	 */
	function display($tpl = null)
	{
		if ($this->getLayout() !== 'modal')
		{
			// Include helper submenu
			RealestatenowHelper::addSubmenu('cities');
		}

		// Assign data to the view
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->user = JFactory::getUser();
		$this->listOrder = $this->escape($this->state->get('list.ordering'));
		$this->listDirn = $this->escape($this->state->get('list.direction'));
		$this->saveOrder = $this->listOrder == 'ordering';
		// get global action permissions
		$this->canDo = RealestatenowHelper::getActions('city');
		$this->canEdit = $this->canDo->get('city.edit');
		$this->canState = $this->canDo->get('city.edit.state');
		$this->canCreate = $this->canDo->get('city.create');
		$this->canDelete = $this->canDo->get('city.delete');
		$this->canBatch = $this->canDo->get('core.batch');

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
			$this->sidebar = JHtmlSidebar::render();
			// load the batch html
			if ($this->canCreate && $this->canEdit && $this->canState)
			{
				$this->batchDisplay = JHtmlBatch_::render();
			}
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		JToolBarHelper::title(JText::_('COM_REALESTATENOW_CITIES'), 'joomla');
		JHtmlSidebar::setAction('index.php?option=com_realestatenow&view=cities');
		JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

		if ($this->canCreate)
		{
			JToolBarHelper::addNew('city.add');
		}

		// Only load if there are items
		if (RealestatenowHelper::checkArray($this->items))
		{
			if ($this->canEdit)
			{
				JToolBarHelper::editList('city.edit');
			}

			if ($this->canState)
			{
				JToolBarHelper::publishList('cities.publish');
				JToolBarHelper::unpublishList('cities.unpublish');
				JToolBarHelper::archiveList('cities.archive');

				if ($this->canDo->get('core.admin'))
				{
					JToolBarHelper::checkin('cities.checkin');
				}
			}

			// Add a batch button
			if ($this->canBatch && $this->canCreate && $this->canEdit && $this->canState)
			{
				// Get the toolbar object instance
				$bar = JToolBar::getInstance('toolbar');
				// set the batch button name
				$title = JText::_('JTOOLBAR_BATCH');
				// Instantiate a new JLayoutFile instance and render the batch button
				$layout = new JLayoutFile('joomla.toolbar.batch');
				// add the button to the page
				$dhtml = $layout->render(array('title' => $title));
				$bar->appendButton('Custom', $dhtml, 'batch');
			}

			if ($this->state->get('filter.published') == -2 && ($this->canState && $this->canDelete))
			{
				JToolbarHelper::deleteList('', 'cities.delete', 'JTOOLBAR_EMPTY_TRASH');
			}
			elseif ($this->canState && $this->canDelete)
			{
				JToolbarHelper::trash('cities.trash');
			}

			if ($this->canDo->get('core.export') && $this->canDo->get('city.export'))
			{
				JToolBarHelper::custom('cities.exportData', 'download', '', 'COM_REALESTATENOW_EXPORT_DATA', true);
			}
		}

		if ($this->canDo->get('core.import') && $this->canDo->get('city.import'))
		{
			JToolBarHelper::custom('cities.importData', 'upload', '', 'COM_REALESTATENOW_IMPORT_DATA', false);
		}

		// set help url for this view if found
		$help_url = RealestatenowHelper::getHelpUrl('cities');
		if (RealestatenowHelper::checkString($help_url))
		{
				JToolbarHelper::help('COM_REALESTATENOW_HELP_MANAGER', false, $help_url);
		}

		// add the options comp button
		if ($this->canDo->get('core.admin') || $this->canDo->get('core.options'))
		{
			JToolBarHelper::preferences('com_realestatenow');
		}

		if ($this->canState)
		{
			JHtmlSidebar::addFilter(
				JText::_('JOPTION_SELECT_PUBLISHED'),
				'filter_published',
				JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
			);
			// only load if batch allowed
			if ($this->canBatch)
			{
				JHtmlBatch_::addListSelection(
					JText::_('COM_REALESTATENOW_KEEP_ORIGINAL_STATE'),
					'batch[published]',
					JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('all' => false)), 'value', 'text', '', true)
				);
			}
		}

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_ACCESS'),
			'filter_access',
			JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'))
		);

		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			JHtmlBatch_::addListSelection(
				JText::_('COM_REALESTATENOW_KEEP_ORIGINAL_ACCESS'),
				'batch[access]',
				JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text')
			);
		}

		// [Interpretation 10638] Set Stateid Name Selection
		$this->stateidNameOptions = JFormHelper::loadFieldType('States')->options;
		if ($this->stateidNameOptions)
		{
			// [Interpretation 10642] Stateid Name Filter
			JHtmlSidebar::addFilter(
				'- Select '.JText::_('COM_REALESTATENOW_CITY_STATEID_LABEL').' -',
				'filter_stateid',
				JHtml::_('select.options', $this->stateidNameOptions, 'value', 'text', $this->state->get('filter.stateid'))
			);

			if ($this->canBatch && $this->canCreate && $this->canEdit)
			{
				// [Interpretation 10651] Stateid Name Batch Selection
				JHtmlBatch_::addListSelection(
					'- Keep Original '.JText::_('COM_REALESTATENOW_CITY_STATEID_LABEL').' -',
					'batch[stateid]',
					JHtml::_('select.options', $this->stateidNameOptions, 'value', 'text')
				);
			}
		}
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		if (!isset($this->document))
		{
			$this->document = JFactory::getDocument();
		}
		$this->document->setTitle(JText::_('COM_REALESTATENOW_CITIES'));
		$this->document->addStyleSheet(JURI::root() . "administrator/components/com_realestatenow/assets/css/cities.css", (RealestatenowHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/css');
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * @param   mixed  $var  The output to escape.
	 *
	 * @return  mixed  The escaped value.
	 */
	public function escape($var)
	{
		if(strlen($var) > 50)
		{
			// use the helper htmlEscape method instead and shorten the string
			return RealestatenowHelper::htmlEscape($var, $this->_charset, true);
		}
		// use the helper htmlEscape method instead.
		return RealestatenowHelper::htmlEscape($var, $this->_charset);
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 */
	protected function getSortFields()
	{
		return array(
			'a.sorting' => JText::_('JGRID_HEADING_ORDERING'),
			'a.published' => JText::_('JSTATUS'),
			'a.name' => JText::_('COM_REALESTATENOW_CITY_NAME_LABEL'),
			'g.name' => JText::_('COM_REALESTATENOW_CITY_STATEID_LABEL'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
