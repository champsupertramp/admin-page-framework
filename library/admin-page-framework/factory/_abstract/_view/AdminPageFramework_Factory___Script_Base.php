<?php
abstract class AdminPageFramework_Factory___Script_Base extends AdminPageFramework_WPUtility {
    static public $_aEnqueued = array();
    public $oMsg;
    public function __construct($oMsg = null) {
        $_sClassName = get_class($this);
        if (in_array($_sClassName, self::$_aEnqueued)) {
            return;
        }
        self::$_aEnqueued[$_sClassName] = $_sClassName;
        $this->oMsg = $oMsg ? $oMsg : AdminPageFramework_Message::getInstance();
        $this->registerAction('customize_controls_print_footer_scripts', array($this, '_replyToPrintScript'));
        $this->registerAction('admin_print_footer_scripts', array($this, '_replyToPrintScript'));
        $this->construct();
        $this->registerAction('wp_enqueue_scripts', array($this, 'load'));
    }
    public function construct() {
    }
    public function load() {
    }
    public function _replyToPrintScript() {
        $_sScript = $this->getScript($this->oMsg);
        if (!$_sScript) {
            return;
        }
        echo "<script type='text/javascript' class='" . strtolower(get_class($this)) . "'>" . '/* <![CDATA[ */' . $_sScript . '/* ]]> */' . "</script>";
    }
    static public function getScript() {
        $_aParams = func_get_args() + array(null);
        $_oMsg = $_aParams[0];
        return "";
    }
}