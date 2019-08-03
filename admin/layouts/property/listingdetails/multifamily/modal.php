<?php
    /*----------------------------------------------------------------------------------|  www.vdm.io  |----/
                    Most Wanted Web Services, Inc.
    /-------------------------------------------------------------------------------------------------------/
    
        @version		2.0.0
        @build			27th November, 2017
        @created		1st May, 2016
        @package		Real Estate NOW!
        @subpackage		listing_details_right.php
        @author			Most Wanted Web Services, Inc. <http://mostwantedrealestatesites.com>
        @copyright		Copyright (C) 2015-2017. All Rights Reserved
        @license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
        
        Real Estate NOW! Component
        
    /------------------------------------------------------------------------------------------------------*/
    
    // No direct access to this file
    
    defined('_JEXEC') or die('Restricted access');
    
    $modal_params = array();
    $modal_params['closeButton'] = TRUE;
    $modal_params['title'] = 'Multi-Family Details';
    $modal_params['backdrop'] = "false";
    $modal_params['footer'] = '<p><a href="#property-details-multifamily-modal" class="btn btn-primary" data-toggle="modal">'.JText::_('CLOSE', true).'</a></p>';
    $body = JLayoutHelper::render('property.listingdetails.multifamily.canvas', $displayData);
?>


    <a href="#property-details-multifamily-modal" class="btn  btn-primary span12" data-toggle="modal"><?php echo JText::_('Multi-Family Details', true);?></a>
    
<?php echo JHTML::_('bootstrap.renderModal', 'property-details-multifamily-modal', $modal_params, $body); ?>