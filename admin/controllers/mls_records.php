<?php
/*----------------------------------------------------------------------------------|  www.vdm.io  |----/
				Most Wanted Web Services, Inc. 
/-------------------------------------------------------------------------------------------------------/

	@version		3.1.18
	@build			29th October, 2018
	@created		1st May, 2016
	@package		Real Estate NOW!
	@subpackage		mls_records.php
	@author			Most Wanted Web Services, Inc. <https://mostwantedrealestatesites.com>	
	@copyright		Copyright (C) 2015-2018. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
	
	Real Estate NOW! Component
	
/------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Mls_records Controller
 */
class RealestatenowControllerMls_records extends JControllerAdmin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_REALESTATENOW_MLS_RECORDS';

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JModelLegacy  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Mls', $prefix = 'RealestatenowModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	public function exportData()
	{
		// [Interpretation 8912] Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// [Interpretation 8914] check if export is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('mls.export', 'com_realestatenow') && $user->authorise('core.export', 'com_realestatenow'))
		{
			// [Interpretation 8918] Get the input
			$input = JFactory::getApplication()->input;
			$pks = $input->post->get('cid', array(), 'array');
			// [Interpretation 8921] Sanitize the input
			JArrayHelper::toInteger($pks);
			// [Interpretation 8923] Get the model
			$model = $this->getModel('Mls_records');
			// [Interpretation 8925] get the data to export
			$data = $model->getExportData($pks);
			if (RealestatenowHelper::checkArray($data))
			{
				// [Interpretation 8929] now set the data to the spreadsheet
				$date = JFactory::getDate();
				RealestatenowHelper::xls($data,'Mls_records_'.$date->format('jS_F_Y'),'Mls records exported ('.$date->format('jS F, Y').')','mls records');
			}
		}
		// [Interpretation 8934] Redirect to the list screen with error.
		$message = JText::_('COM_REALESTATENOW_EXPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_realestatenow&view=mls_records', false), $message, 'error');
		return;
	}


	public function importData()
	{
		// [Interpretation 8943] Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// [Interpretation 8945] check if import is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('mls.import', 'com_realestatenow') && $user->authorise('core.import', 'com_realestatenow'))
		{
			// [Interpretation 8949] Get the import model
			$model = $this->getModel('Mls_records');
			// [Interpretation 8951] get the headers to import
			$headers = $model->getExImPortHeaders();
			if (RealestatenowHelper::checkObject($headers))
			{
				// [Interpretation 8955] Load headers to session.
				$session = JFactory::getSession();
				$headers = json_encode($headers);
				$session->set('mls_VDM_IMPORTHEADERS', $headers);
				$session->set('backto_VDM_IMPORT', 'mls_records');
				$session->set('dataType_VDM_IMPORTINTO', 'mls');
				// [Interpretation 8961] Redirect to import view.
				$message = JText::_('COM_REALESTATENOW_IMPORT_SELECT_FILE_FOR_MLS_RECORDS');
				$this->setRedirect(JRoute::_('index.php?option=com_realestatenow&view=import', false), $message);
				return;
			}
		}
		// [Interpretation 8981] Redirect to the list screen with error.
		$message = JText::_('COM_REALESTATENOW_IMPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_realestatenow&view=mls_records', false), $message, 'error');
		return;
	}
}
