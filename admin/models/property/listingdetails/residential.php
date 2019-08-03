<?php
/*----------------------------------------------------------------------------------|  www.vdm.io  |----/
				Most Wanted Web Services, Inc.
/-------------------------------------------------------------------------------------------------------/

	@version		2.0.0
	@build			27th November, 2017
	@created		1st May, 2016
	@package		Real Estate NOW!
	@subpackage		residential.php
	@author			Most Wanted Web Services, Inc. <http://mostwantedrealestatesites.com>
	@copyright		Copyright (C) 2015-2017. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
	
	Real Estate NOW! Component
	
/------------------------------------------------------------------------------------------------------*/

//namespace Realestatenow\Models\Property\Listingdetails;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Model\AdminModel as JModelAdmin;
use Joomla\Registry\Registry;


/**
 * Realestatenow Residential Model
 */
class RealestatenowModelResidential extends JModelAdmin
{
    /**
     * @var        string    The prefix to use with controller messages.
     * @since   1.6
     */
    protected $text_prefix = 'COM_REALESTATENOW';
    
    /**
     * The type alias for this content type.
     *
     * @var      string
     * @since    3.2
     */
    public $typeAlias = 'com_realestatenow.residential';
    
    /**
     * Returns a Table object, always creating it
     *
     * @param   type    $type    The table type to instantiate
     * @param   string  $prefix  A prefix for the table class name. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  JTable  A database object
     *
     * @since   1.6
     */
    public function getTable($type = 'residential', $prefix = 'RealestatenowTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }
    
    /**
     * Method to get a single record.
     *
     * @param   integer  $pk  The id of the primary key.
     *
     * @return  mixed  Object on success, false on failure.
     *
     * @since   1.6
     */
    public function getItem($pk = null)
    {
        if ($item = parent::getItem($pk))
        {
            if (!empty($item->params))
            {
                // Convert the params field to an array.
                $registry = new Registry;
                $registry->loadString($item->params);
                $item->params = $registry->toArray();
            }
            
            if (!empty($item->metadata))
            {
                // Convert the metadata field to an array.
                $registry = new Registry;
                $registry->loadString($item->metadata);
                $item->metadata = $registry->toArray();
            }
            
            if (!empty($item->addcustom))
            {
                // [Interpretation 3758] Convert the addcustom field to an array.
                $addcustom = new Registry;
                $addcustom->loadString($item->addcustom);
                $item->addcustom = $addcustom->toArray();
            }
            
            if (!empty($item->id))
            {
                $item->tags = new JHelperTags;
                $item->tags->getTagIds($item->id, 'com_realestatenow.residential');
            }
        }
        
        return $item;
    }
    
    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  mixed  A JForm object on success, false on failure
     *
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // [Interpretation 10087] Get the form.
        $form = $this->loadForm('com_realestatenow.residential', 'residential', array('control' => 'jform', 'load_data' => $loadData));
        
        if (empty($form))
        {
            return false;
        }
        
        $jinput = JFactory::getApplication()->input;
        
        // [Interpretation 10178] The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
        if ($jinput->get('a_id'))
        {
            $id = $jinput->get('a_id', 0, 'INT');
        }
        // [Interpretation 10183] The back end uses id so we use that the rest of the time and set it to 0 by default.
        else
        {
            $id = $jinput->get('id', 0, 'INT');
        }
        
        $user = JFactory::getUser();
        
        // [Interpretation 10189] Check for existing item.
        // [Interpretation 10190] Modify the form based on Edit State access controls.
        if ($id != 0 && (!$user->authorise('core.edit.state', 'com_realestatenow.residential.' . (int) $id))
            || ($id == 0 && !$user->authorise('core.edit.state', 'com_realestatenow')))
        {
            // [Interpretation 10203] Disable fields for display.
            $form->setFieldAttribute('ordering', 'disabled', 'true');
            $form->setFieldAttribute('published', 'disabled', 'true');
            // [Interpretation 10206] Disable fields while saving.
            $form->setFieldAttribute('ordering', 'filter', 'unset');
            $form->setFieldAttribute('published', 'filter', 'unset');
        }
        // [Interpretation 10211] If this is a new item insure the greated by is set.
        if (0 == $id)
        {
            // [Interpretation 10214] Set the created_by to this user
            $form->setValue('created_by', null, $user->id);
        }
        // [Interpretation 10217] Modify the form based on Edit Creaded By access controls.
        if ($id != 0 && (!$user->authorise('core.edit.created_by', 'com_realestatenow.residential.' . (int) $id))
            || ($id == 0 && !$user->authorise('core.edit.created_by', 'com_realestatenow')))
        {
            // [Interpretation 10229] Disable fields for display.
            $form->setFieldAttribute('created_by', 'disabled', 'true');
            // [Interpretation 10231] Disable fields for display.
            $form->setFieldAttribute('created_by', 'readonly', 'true');
            // [Interpretation 10233] Disable fields while saving.
            $form->setFieldAttribute('created_by', 'filter', 'unset');
        }
        // [Interpretation 10236] Modify the form based on Edit Creaded Date access controls.
        if ($id != 0 && (!$user->authorise('core.edit.created', 'com_realestatenow.residential.' . (int) $id))
            || ($id == 0 && !$user->authorise('core.edit.created', 'com_realestatenow')))
        {
            // [Interpretation 10248] Disable fields for display.
            $form->setFieldAttribute('created', 'disabled', 'true');
            // [Interpretation 10250] Disable fields while saving.
            $form->setFieldAttribute('created', 'filter', 'unset');
        }
        // [Interpretation 10283] Only load these values if no id is found
        if (0 == $id)
        {
            // [Interpretation 10286] Set redirected field name
            $redirectedField = $jinput->get('ref', null, 'STRING');
            // [Interpretation 10288] Set redirected field value
            $redirectedValue = $jinput->get('refid', 0, 'INT');
            if (0 != $redirectedValue && $redirectedField)
            {
                // [Interpretation 10292] Now set the local-redirected field default value
                $form->setValue($redirectedField, null, $redirectedValue);
            }
        }
        
        return $form;
    }
    
    /**
     * Method to get the script that have to be included on the form
     *
     * @return string	script files
     */
    public function getScript()
    {
        return 'administrator/components/com_realestatenow/models/forms/residential.js';
    }
    
    /**
     * Method to test whether a record can be deleted.
     *
     * @param   object  $record  A record object.
     *
     * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
     *
     * @since   1.6
     */
    protected function canDelete($record)
    {
        if (!empty($record->id))
        {
            if ($record->published != -2)
            {
                return;
            }
            
            $user = JFactory::getUser();
            // [Interpretation 10408] The record has been set. Check the record permissions.
            return $user->authorise('core.delete', 'com_realestatenow.residential.' . (int) $record->id);
        }
        return false;
    }
    
    /**
     * Method to test whether a record can have its state edited.
     *
     * @param   object  $record  A record object.
     *
     * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
     *
     * @since   1.6
     */
    protected function canEditState($record)
    {
        $user = JFactory::getUser();
        $recordId	= (!empty($record->id)) ? $record->id : 0;
        
        if ($recordId)
        {
            // [Interpretation 10496] The record has been set. Check the record permissions.
            $permission = $user->authorise('core.edit.state', 'com_realestatenow.residential.' . (int) $recordId);
            if (!$permission && !is_null($permission))
            {
                return false;
            }
        }
        // [Interpretation 10518] In the absense of better information, revert to the component permissions.
        return parent::canEditState($record);
    }
    
    /**
     * Method override to check if you can edit an existing record.
     *
     * @param	array	$data	An array of input data.
     * @param	string	$key	The name of the key for the primary key.
     *
     * @return	boolean
     * @since	2.5
     */
    protected function allowEdit($data = array(), $key = 'id')
    {
        // [Interpretation 10319] Check specific edit permission then general edit permission.
        $user = JFactory::getUser();
        
        return $user->authorise('core.edit', 'com_realestatenow.residential.'. ((int) isset($data[$key]) ? $data[$key] : 0)) or $user->authorise('core.edit',  'com_realestatenow');
    }
    
    /**
     * Prepare and sanitise the table data prior to saving.
     *
     * @param   JTable  $table  A JTable object.
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function prepareTable($table)
    {
        $date = JFactory::getDate();
        $user = JFactory::getUser();
        
        if (isset($table->name))
        {
            $table->name = htmlspecialchars_decode($table->name, ENT_QUOTES);
        }
        
        if (isset($table->alias) && empty($table->alias))
        {
            $table->generateAlias();
        }
        
        if (empty($table->id))
        {
            $table->created = $date->toSql();
            // set the user
            if ($table->created_by == 0 || empty($table->created_by))
            {
                $table->created_by = $user->id;
            }
            // Set ordering to the last item if not set
            if (empty($table->ordering))
            {
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                    ->select('MAX(ordering)')
                    ->from($db->quoteName('#__realestatenow_residential'));
                $db->setQuery($query);
                $max = $db->loadResult();
                
                $table->ordering = $max + 1;
            }
        }
        else
        {
            $table->modified = $date->toSql();
            $table->modified_by = $user->id;
        }
        
        if (!empty($table->id))
        {
            // Increment the items version number.
            $table->version++;
        }
    }
    
    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     *
     * @since   1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_realestatenow.edit.residential.data', array());
        
        if (empty($data))
        {
            $data = $this->getItem();
        }
        
        return $data;
    }
    
    /**
     * Method to get the unique fields of this table.
     *
     * @return  mixed  An array of field names, boolean false if none is set.
     *
     * @since   3.0
     */
    protected function getUniqeFields()
    {
        return false;
    }
    
    /**
     * Method to delete one or more records.
     *
     * @param   array  &$pks  An array of record primary keys.
     *
     * @return  boolean  True if successful, false if an error occurs.
     *
     * @since   12.2
     */
    public function delete(&$pks)
    {
        if (!parent::delete($pks))
        {
            return false;
        }
        
        return true;
    }
    
    /**
     * Method to change the published state of one or more records.
     *
     * @param   array    &$pks   A list of the primary keys to change.
     * @param   integer  $value  The value of the published state.
     *
     * @return  boolean  True on success.
     *
     * @since   12.2
     */
    public function publish(&$pks, $value = 1)
    {
        if (!parent::publish($pks, $value))
        {
            return false;
        }
        
        return true;
    }
    
    /**
     * Method to perform batch operations on an item or a set of items.
     *
     * @param   array  $commands  An array of commands to perform.
     * @param   array  $pks       An array of item ids.
     * @param   array  $contexts  An array of item contexts.
     *
     * @return  boolean  Returns true on success, false on failure.
     *
     * @since   12.2
     */
    public function batch($commands, $pks, $contexts)
    {
        // Sanitize ids.
        $pks = array_unique($pks);
        JArrayHelper::toInteger($pks);
        
        // Remove any values of zero.
        if (array_search(0, $pks, true))
        {
            unset($pks[array_search(0, $pks, true)]);
        }
        
        if (empty($pks))
        {
            $this->setError(JText::_('JGLOBAL_NO_ITEM_SELECTED'));
            return false;
        }
        
        $done = false;
        
        // Set some needed variables.
        $this->user			= JFactory::getUser();
        $this->table			= $this->getTable();
        $this->tableClassName		= get_class($this->table);
        $this->contentType		= new JUcmType;
        $this->type			= $this->contentType->getTypeByTable($this->tableClassName);
        $this->canDo			= RealestatenowHelper::getActions('residential');
        $this->batchSet			= true;
        
        if (!$this->canDo->get('core.batch'))
        {
            $this->setError(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));
            return false;
        }
        
        if ($this->type == false)
        {
            $type = new JUcmType;
            $this->type = $type->getTypeByAlias($this->typeAlias);
        }
        
        $this->tagsObserver = $this->table->getObserverOfClass('JTableObserverTags');
        
        if (!empty($commands['move_copy']))
        {
            $cmd = JArrayHelper::getValue($commands, 'move_copy', 'c');
            
            if ($cmd == 'c')
            {
                $result = $this->batchCopy($commands, $pks, $contexts);
                
                if (is_array($result))
                {
                    foreach ($result as $old => $new)
                    {
                        $contexts[$new] = $contexts[$old];
                    }
                    $pks = array_values($result);
                }
                else
                {
                    return false;
                }
            }
            elseif ($cmd == 'm' && !$this->batchMove($commands, $pks, $contexts))
            {
                return false;
            }
            
            $done = true;
        }
        
        if (!$done)
        {
            $this->setError(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));
            
            return false;
        }
        
        // Clear the cache
        $this->cleanCache();
        
        return true;
    }
    
    /**
     * Batch copy items to a new category or current.
     *
     * @param   integer  $values    The new values.
     * @param   array    $pks       An array of row IDs.
     * @param   array    $contexts  An array of item contexts.
     *
     * @return  mixed  An array of new IDs on success, boolean false on failure.
     *
     * @since	12.2
     */
    protected function batchCopy($values, $pks, $contexts)
    {
        if (empty($this->batchSet))
        {
            // [Interpretation 4813] Set some needed variables.
            $this->user 		= JFactory::getUser();
            $this->table 		= $this->getTable();
            $this->tableClassName	= get_class($this->table);
            $this->contentType	= new JUcmType;
            $this->type		= $this->contentType->getTypeByTable($this->tableClassName);
            $this->canDo		= RealestatenowHelper::getActions('residential');
        }
        
        if (!$this->canDo->get('core.create') || !$this->canDo->get('core.batch'))
        {
            return false;
        }
        
        // [Interpretation 4833] get list of uniqe fields
        $uniqeFields = $this->getUniqeFields();
        // [Interpretation 4835] remove move_copy from array
        unset($values['move_copy']);
        
        // [Interpretation 4838] make sure published is set
        if (!isset($values['published']))
        {
            $values['published'] = 0;
        }
        elseif (isset($values['published']) && !$this->canDo->get('core.edit.state'))
        {
            $values['published'] = 0;
        }
        
        $newIds = array();
        
        // [Interpretation 4875] Parent exists so let's proceed
        while (!empty($pks))
        {
            // [Interpretation 4878] Pop the first ID off the stack
            $pk = array_shift($pks);
            
            $this->table->reset();
            
            // [Interpretation 4883] only allow copy if user may edit this item.
            
            if (!$this->user->authorise('core.edit', $contexts[$pk]))
            
            {
                
                // [Interpretation 4893] Not fatal error
                
                $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
                
                continue;
                
            }
            
            // [Interpretation 4898] Check that the row actually exists
            if (!$this->table->load($pk))
            {
                if ($error = $this->table->getError())
                {
                    // [Interpretation 4903] Fatal error
                    $this->setError($error);
                    
                    return false;
                }
                else
                {
                    // [Interpretation 4910] Not fatal error
                    $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
                    continue;
                }
            }
            
            // [Interpretation 4946] insert all set values
            if (RealestatenowHelper::checkArray($values))
            {
                foreach ($values as $key => $value)
                {
                    if (strlen($value) > 0 && isset($this->table->$key))
                    {
                        $this->table->$key = $value;
                    }
                }
            }
            
            // [Interpretation 4958] update all uniqe fields
            if (RealestatenowHelper::checkArray($uniqeFields))
            {
                foreach ($uniqeFields as $uniqeField)
                {
                    $this->table->$uniqeField = $this->generateUniqe($uniqeField,$this->table->$uniqeField);
                }
            }
            
            // [Interpretation 4967] Reset the ID because we are making a copy
            $this->table->id = 0;
            
            // [Interpretation 4970] TODO: Deal with ordering?
            // [Interpretation 4971] $this->table->ordering	= 1;
            
            // [Interpretation 4973] Check the row.
            if (!$this->table->check())
            {
                $this->setError($this->table->getError());
                
                return false;
            }
            
            if (!empty($this->type))
            {
                $this->createTagsHelper($this->tagsObserver, $this->type, $pk, $this->typeAlias, $this->table);
            }
            
            // [Interpretation 4986] Store the row.
            if (!$this->table->store())
            {
                $this->setError($this->table->getError());
                
                return false;
            }
            
            // [Interpretation 4994] Get the new item ID
            $newId = $this->table->get('id');
            
            // [Interpretation 4997] Add the new ID to the array
            $newIds[$pk] = $newId;
        }
        
        // [Interpretation 5001] Clean the cache
        $this->cleanCache();
        
        return $newIds;
    }
    
    /**
     * Batch move items to a new category
     *
     * @param   integer  $value     The new category ID.
     * @param   array    $pks       An array of row IDs.
     * @param   array    $contexts  An array of item contexts.
     *
     * @return  boolean  True if successful, false otherwise and internal error is set.
     *
     * @since	12.2
     */
    protected function batchMove($values, $pks, $contexts)
    {
        if (empty($this->batchSet))
        {
            // [Interpretation 4622] Set some needed variables.
            $this->user		= JFactory::getUser();
            $this->table		= $this->getTable();
            $this->tableClassName	= get_class($this->table);
            $this->contentType	= new JUcmType;
            $this->type		= $this->contentType->getTypeByTable($this->tableClassName);
            $this->canDo		= RealestatenowHelper::getActions('residential');
        }
        
        if (!$this->canDo->get('core.edit') && !$this->canDo->get('core.batch'))
        {
            $this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
            return false;
        }
        
        // [Interpretation 4644] make sure published only updates if user has the permission.
        if (isset($values['published']) && !$this->canDo->get('core.edit.state'))
        {
            unset($values['published']);
        }
        // [Interpretation 4657] remove move_copy from array
        unset($values['move_copy']);
        
        // [Interpretation 4678] Parent exists so we proceed
        foreach ($pks as $pk)
        {
            if (!$this->user->authorise('core.edit', $contexts[$pk]))
            {
                $this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
                
                return false;
            }
            
            // [Interpretation 4695] Check that the row actually exists
            if (!$this->table->load($pk))
            {
                if ($error = $this->table->getError())
                {
                    // [Interpretation 4700] Fatal error
                    $this->setError($error);
                    
                    return false;
                }
                else
                {
                    // [Interpretation 4707] Not fatal error
                    $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
                    continue;
                }
            }
            
            // [Interpretation 4713] insert all set values.
            if (RealestatenowHelper::checkArray($values))
            {
                foreach ($values as $key => $value)
                {
                    // [Interpretation 4718] Do special action for access.
                    if ('access' === $key && strlen($value) > 0)
                    {
                        $this->table->$key = $value;
                    }
                    elseif (strlen($value) > 0 && isset($this->table->$key))
                    {
                        $this->table->$key = $value;
                    }
                }
            }
            
            
            // [Interpretation 4730] Check the row.
            if (!$this->table->check())
            {
                $this->setError($this->table->getError());
                
                return false;
            }
            
            if (!empty($this->type))
            {
                $this->createTagsHelper($this->tagsObserver, $this->type, $pk, $this->typeAlias, $this->table);
            }
            
            // [Interpretation 4743] Store the row.
            if (!$this->table->store())
            {
                $this->setError($this->table->getError());
                
                return false;
            }
        }
        
        // [Interpretation 4752] Clean the cache
        $this->cleanCache();
        
        return true;
    }
    
    /**
     * Method to save the form data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success.
     *
     * @since   1.6
     */
    public function save($data)
    {
        $input	= JFactory::getApplication()->input;
        $filter	= JFilterInput::getInstance();
        
        // set the metadata to the Item Data
        if (isset($data['metadata']) && isset($data['metadata']['author']))
        {
            $data['metadata']['author'] = $filter->clean($data['metadata']['author'], 'TRIM');
            
            $metadata = new JRegistry;
            $metadata->loadArray($data['metadata']);
            $data['metadata'] = (string) $metadata;
        }
        
        // [Interpretation 3869] Set the addcustom items to data.
        if (isset($data['addcustom']) && is_array($data['addcustom']))
        {
            $addcustom = new JRegistry;
            $addcustom->loadArray($data['addcustom']);
            $data['addcustom'] = (string) $addcustom;
        }
        elseif (!isset($data['addcustom']))
        {
            // [Interpretation 3878] Set the empty addcustom to data
            $data['addcustom'] = '';
        }
        
        // Set the Params Items to data
        if (isset($data['params']) && is_array($data['params']))
        {
            $params = new JRegistry;
            $params->loadArray($data['params']);
            $data['params'] = (string) $params;
        }
        
        // [Interpretation 5093] Alter the uniqe field for save as copy
        if ($input->get('task') === 'save2copy')
        {
            // [Interpretation 5096] Automatic handling of other uniqe fields
            $uniqeFields = $this->getUniqeFields();
            if (RealestatenowHelper::checkArray($uniqeFields))
            {
                foreach ($uniqeFields as $uniqeField)
                {
                    $data[$uniqeField] = $this->generateUniqe($uniqeField,$data[$uniqeField]);
                }
            }
        }
        
        if (parent::save($data))
        {
            return true;
        }
        return false;
    }
    
    /**
     * Method to generate a uniqe value.
     *
     * @param   string  $field name.
     * @param   string  $value data.
     *
     * @return  string  New value.
     *
     * @since   3.0
     */
    protected function generateUniqe($field,$value)
    {
        
        // set field value uniqe
        $table = $this->getTable();
        
        while ($table->load(array($field => $value)))
        {
            $value = JString::increment($value);
        }
        
        return $value;
    }
}