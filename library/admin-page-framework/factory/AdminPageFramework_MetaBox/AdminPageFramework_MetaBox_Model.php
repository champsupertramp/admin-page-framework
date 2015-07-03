<?php
/**
 Admin Page Framework v3.5.10b04 by Michael Uno
 Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
 <http://en.michaeluno.jp/admin-page-framework>
 Copyright (c) 2013-2015, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT>
 */
abstract class AdminPageFramework_MetaBox_Model extends AdminPageFramework_MetaBox_Router {
    private $_bIsNewPost = false;
    protected function _setUpValidationHooks($oScreen) {
        if ('attachment' === $oScreen->post_type && in_array('attachment', $this->oProp->aPostTypes)) {
            add_filter('wp_insert_attachment_data', array($this, '_replyToFilterSavingData'), 10, 2);
        } else {
            add_filter('wp_insert_post_data', array($this, '_replyToFilterSavingData'), 10, 2);
        }
    }
    public function _replyToAddMetaBox() {
        foreach ($this->oProp->aPostTypes as $sPostType) {
            add_meta_box($this->oProp->sMetaBoxID, $this->oProp->sTitle, array($this, '_replyToPrintMetaBoxContents'), $sPostType, $this->oProp->sContext, $this->oProp->sPriority, null);
        }
    }
    protected function _registerFormElements($oScreen) {
        if (!$this->oUtil->isPostDefinitionPage($this->oProp->aPostTypes)) {
            return;
        }
        $this->_loadFieldTypeDefinitions();
        $this->oForm->format();
        $this->oForm->applyConditions();
        $this->oForm->applyFiltersToFields($this, $this->oProp->sClassName);
        $this->_setOptionArray($this->_getPostID(), $this->oUtil->getAsArray($this->oForm->aConditionedFields));
        $this->oForm->setDynamicElements($this->oProp->aOptions);
        $this->_registerFields($this->oForm->aConditionedFields);
    }
    private function _getPostID() {
        if (isset($GLOBALS['post']->ID)) {
            return $GLOBALS['post']->ID;
        }
        if (isset($_GET['post'])) {
            return $_GET['post'];
        }
        if (isset($_POST['post_ID'])) {
            return $_POST['post_ID'];
        }
        return null;
    }
    protected function _getSavedMetaArray($iPostID, $aInputStructure) {
        $_aSavedMeta = array();
        foreach ($aInputStructure as $_sSectionORFieldID => $_v) {
            $_aSavedMeta[$_sSectionORFieldID] = get_post_meta($iPostID, $_sSectionORFieldID, true);
        }
        return $_aSavedMeta;
    }
    protected function _setOptionArray($iPostID, array $aFields) {
        if (!$this->oUtil->isNumericInteger($iPostID)) {
            return;
        }
        $this->oProp->aOptions = $this->oUtil->getAsArray($this->oProp->aOptions);
        $this->_fillOptionsArrayFromPostMeta($this->oProp->aOptions, $iPostID, $aFields);
        $this->oProp->aOptions = $this->oUtil->addAndApplyFilter($this, 'options_' . $this->oProp->sClassName, $this->oProp->aOptions);
        $_aLastInput = isset($_GET['field_errors']) && $_GET['field_errors'] ? $this->oProp->aLastInput : array();
        $this->oProp->aOptions = $_aLastInput + $this->oUtil->getAsArray($this->oProp->aOptions);
    }
    private function _fillOptionsArrayFromPostMeta(array & $aOptions, $iPostID, array $aFields) {
        $_aMetaKeys = $this->oUtil->getAsArray(get_post_custom_keys($iPostID));
        foreach ($aFields as $_sSectionID => $_aFields) {
            if ('_default' == $_sSectionID) {
                foreach ($_aFields as $_aField) {
                    if (!in_array($_aField['field_id'], $_aMetaKeys)) {
                        continue;
                    }
                    $aOptions[$_aField['field_id']] = get_post_meta($iPostID, $_aField['field_id'], true);
                }
            }
            if (!in_array($_sSectionID, $_aMetaKeys)) {
                continue;
            }
            $aOptions[$_sSectionID] = get_post_meta($iPostID, $_sSectionID, true);
        }
    }
    public function _replyToGetSectionHeaderOutput($sSectionDescription, $aSection) {
        return $this->oUtil->addAndApplyFilters($this, array('section_head_' . $this->oProp->sClassName . '_' . $aSection['section_id']), $sSectionDescription);
    }
    public function _replyToFilterSavingData($aPostData, $aUnmodified) {
        if ('auto-draft' === $aUnmodified['post_status']) {
            return $aPostData;
        }
        if (!$this->_validateCall()) {
            return $aPostData;
        }
        if (!in_array($aUnmodified['post_type'], $this->oProp->aPostTypes)) {
            return $aPostData;
        }
        $_iPostID = $aUnmodified['ID'];
        if (!current_user_can($this->oProp->sCapability, $_iPostID)) {
            return $aPostData;
        }
        $_aInput = $this->oForm->getUserSubmitDataFromPOST($this->oForm->aConditionedFields, $this->oForm->aConditionedSections);
        $_aInputRaw = $_aInput;
        $_aSavedMeta = $_iPostID ? $this->oUtil->getSavedMetaArray($_iPostID, array_keys($_aInput)) : array();
        $_aInput = $this->oUtil->addAndApplyFilters($this, "validation_{$this->oProp->sClassName}", call_user_func_array(array($this, 'validate'), array($_aInput, $_aSavedMeta, $this)), $_aSavedMeta, $this);
        if ($this->hasFieldError()) {
            $this->_setLastInput($_aInputRaw);
            $aPostData['post_status'] = 'pending';
            add_filter('redirect_post_location', array($this, '_replyToModifyRedirectPostLocation'));
        }
        $this->oForm->updateMetaDataByType($_iPostID, $_aInput, $this->oForm->dropRepeatableElements($_aSavedMeta), $this->oForm->sFieldsType);
        return $aPostData;
    }
    public function _replyToModifyRedirectPostLocation($sLocation) {
        remove_filter('redirect_post_location', array($this, __FUNCTION__));
        return add_query_arg(array('message' => 'apf_field_error', 'field_errors' => true), $sLocation);
    }
    private function _validateCall() {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return false;
        }
        if (!isset($_POST[$this->oProp->sMetaBoxID]) || !wp_verify_nonce($_POST[$this->oProp->sMetaBoxID], $this->oProp->sMetaBoxID)) {
            return false;
        }
        return true;
    }
}