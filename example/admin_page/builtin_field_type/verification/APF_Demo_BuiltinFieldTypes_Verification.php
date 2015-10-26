<?php
/**
 * Admin Page Framework - Demo
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds a tab in a page.
 * 
 * @package     AdminPageFramework
 * @subpackage  Example
 */
class APF_Demo_BuiltinFieldTypes_Verification {

    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_builtin_field_types';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'verification';
   
    /**
     * Sets up hooks.
     */
    public function __construct() {

        add_action( 
            'load_' . $this->sPageSlug, 
            array( $this, 'replyToAddTab' )
        );
        
    }
    
    /**
     * Triggered when the page is loaded.
     */
    public function replyToAddTab( $oFactory ) {
        
        // Tab
        $oFactory->addInPageTabs(    
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'  => $this->sTabSlug,
                'title'     => __( 'Verification', 'admin-page-framework-loader' ),    
            )      
        );  
        
        add_action( 
            'load_' . $this->sPageSlug . '_' . $this->sTabSlug, 
            array( $this, 'replyToLoadTab' ) 
        );
        
    }
    
    /**
     * Adds form sections.
     * 
     * Triggered when the tab is loaded.
     * 
     * @callback        action      load_{page slug}_{tab slug}
     */
    public function replyToLoadTab( $oFactory ) {
        
        $_aClasses = array(
            'APF_Demo_BuiltinFieldTypes_Verification_Field',
            'APF_Demo_BuiltinFieldTypes_Verification_Section',
        );
        foreach ( $_aClasses as $_sClassName ) {
            if ( ! class_exists( $_sClassName ) ) {
                continue;
            }
            new $_sClassName( $oFactory );
        }   
        
    }
    
}