<?php
    /*----------------------------------------------------------------------------------|  www.vdm.io  |----/
                    Most Wanted Web Services, Inc.
    /-------------------------------------------------------------------------------------------------------/
    
        @version		3.1.15
        @build			8th September, 2018
        @created		1st May, 2016
        @package		Real Estate NOW!
        @subpackage		view.json.php
        @author			Most Wanted Web Services, Inc. <https://mostwantedrealestatesites.com>
        @copyright		Copyright (C) 2015-2018. All Rights Reserved
        @license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
        
        Real Estate NOW! Component
        
    /------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\View\HtmlView;

// import Joomla view library
    jimport('joomla.application.component.view');
    
    /**
     * Realestatenow View class for the Properties
     */
class RealestatenowViewProperties extends HtmlView
{
    protected $response;
    protected $totalproperties;
    protected $active_state_vars;
    protected $markers = [];
    protected $infohtml = [];
    protected $items = [];
		
    // Overwriting JView display method
    function display($tpl = null)
    {
        
        $this->app = JFactory::getApplication();
    
            // get combined params of both component and menu
        $this->params = $this->app->getParams();
        $this->menu = $this->app->getMenu()->getActive();
            
            // get the user object
        $this->user = JFactory::getUser();

            //Initialise variables.
        $this->items = $this->get('Items');
        $this->totalproperties = $this->get('Total');

        $this->setActiveFilterListVars();

        $this->response = new stdClass();
        
        $this->response->pagination = new stdClass();
        $this->response->pagination->totalItems = $this->totalproperties;
        $this->response->pagination->totalPages = intval( ceil($this->totalproperties / $this->active_state_vars['list']['limit'] ) );
        $this->response->pagination->currentPage = intval( $this->active_state_vars['list']['page'] );
        $this->response->pagination->limit =  intval( $this->active_state_vars['list']['limit'] );
        
        $this->response->mapOptions = new stdClass();
        $this->response->mapOptions->zoom = intval ( $this->active_state_vars['componentparams']['zoom'] );
        $this->response->mapOptions->center = new stdClass();
        $this->response->mapOptions->center->lat = (float)$this->active_state_vars['componentparams']['latitude'];
        $this->response->mapOptions->center->lng = (float)$this->active_state_vars['componentparams']['longitude'];
				
			if(is_array($this->items) && ( count($this->items) > 0 ) )
        {
            if($this->params->get('properties_display') == 2)
            {
                foreach($this->items as &$item)
                {
                    $item->offset = ( ( $this->active_state_vars['list']['page'] - 1 ) * $this->active_state_vars['list']['limit'] ) + $key;
                    $this->response->listhtml[] = JLayoutHelper::render('allpropertypanellistings',$item);
                    if( !empty($item->latitude) && !empty($item->longitude) ) {
                        $this->response->mapOptions->infoWindowContent[] = [JLayoutHelper::render ('allpropertyinfobox', $item)];
                        $this->response->mapOptions->markers[] = array($item->name, $item->latitude, $item->longitude);
                    }
                }
            }
			elseif($this->params->get('properties_display') == 3)
            {
                foreach($this->items as $key => &$item)
                {
                    $item->offset = ( ( $this->active_state_vars['list']['page'] - 1 ) * $this->active_state_vars['list']['limit'] ) + $key;
                    $this->response->listhtml[] = JLayoutHelper::render('allpropertylandingpage',$item);
                    if( !empty($item->latitude) && !empty($item->longitude) ) {
                        $this->response->mapOptions->infoWindowContent[] = [JLayoutHelper::render ('allpropertyinfobox', $item)];
                        $this->response->mapOptions->markers[] = array($item->name, $item->latitude, $item->longitude);
                    }
                }
            }
            else
            {
                foreach($this->items as $key => &$item)
                {
                    $item->offset = ( ( $this->active_state_vars['list']['page'] - 1 ) * $this->active_state_vars['list']['limit'] ) + $key;
                    $this->response->listhtml[] = JLayoutHelper::render('allpropertylistings',$item);
                    if( !empty($item->latitude) && !empty($item->longitude) ) {
                        $this->response->mapOptions->infoWindowContent[] = [JLayoutHelper::render ('allpropertyinfobox', $item)];
                        $this->response->mapOptions->markers[] = array($item->name, $item->latitude, $item->longitude);
                    }
                }
            }
        }
        else
        {
                $this->response->listhtml = [];
                $this->response->mapOptions->infoWindowContent = [];
                $this->response->mapOptions->markers = [];
        }


        if (count($errors = $this->get('Errors')))
            throw new Exception(implode("\n", $errors), 500);
        
        return $this->response;
    }

    public function setActiveFilterListVars()
    {
        $this->active_state_vars = ['list'=>[],'filter'=>[]];
        $state = $this->getModel('Properties')->getState();
        $this->active_state_vars['list'] = $state->get('list');
        $this->active_state_vars['filter'] = $state->get('filter');
        $this->active_state_vars['componentparams'] = $state->get('componentparams');
    }
}
