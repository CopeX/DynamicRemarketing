<?php
/**
 * ==============================================================================
 * Adwise Internetmarketing & CopeX.io
 * ==============================================================================
 *
 * @package 	CopeX_DynamicRemarketing
 * @author		Adwise Internetmarketing & copex.io
 * @copyright  	Copyright (c) 2013 Adwise Internetmarketing under GPL and CopeX.io
 * @version 	1.0.1
 *
 */
Class CopeX_DynamicRemarketing_Helper_Data extends Mage_Core_Helper_Abstract{
	
	/**
	 * Checks if the module is well configured and enabled
	 * @return boolean
	 */
	public function isAvailable(){
		if($this->getGoogleConversionId() && (boolean)Mage::getStoreConfig('google/dynamicremarketing/enable',Mage::app()->getStore()) === true){
			return true;
		}
		return false;
	}
	
	/**
	 * Returns the store config value for javascript tag google_conversion_id
	 * @return string $conversionId
	 */
	public function getGoogleConversionId(){
		$conversionId = Mage::getStoreConfig('google/dynamicremarketing/conversion_id',Mage::app()->getStore());
		if($conversionId != ''){
			return $conversionId;
		}
		return false;
	}
	
	/**
	 * Returns the store config value for javascript tag google_remarketing_only
	 * @return boolean;
	 */
	public function getGoogleRemarketingOnly(){
		$boolean = (boolean)Mage::getStoreConfig('google/dynamicremarketing/js_remarketing_only',Mage::app()->getStore());
		return $boolean === true ? "true": "false";
	}
	
	/**
	 * Returns a javascript object with unquoted keys
	 * Thanks to Saggy for this neat solution
	 * @param array $data
	 */
	public function getJsObjectString(array $data){
		return preg_replace('/"([^"]+)"s*:s*/', '$1:', json_encode($data));
	}
	 
}
