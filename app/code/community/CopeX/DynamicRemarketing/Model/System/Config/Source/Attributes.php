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
Class CopeX_DynamicRemarketing_Model_System_Config_Source_Attributes{
	
	/**
	 * Method returns an array of attribute options and values
	 * @return unknown
	 */
	public function toOptionArray(){
		$options 	= array(array('value'=> '', 'label'=> Mage::helper('adminhtml')->__('-- Please Select --')));
		$options = array_merge($options,$this->initAttributes());
		return $options;
	}
	
	/**
	 * Collects and returns a option list of user defined product attributes
	 * that are available for creating configurable products
	 * @return multitype:multitype:NULL
	 */
	private function initAttributes(){
		$attributes = array();
		$attributes[] = array('value'=>'entity_id','label'=>'Entity ID');
		$collection = Mage::getResourceModel('eav/entity_attribute_collection')->setEntityTypeFilter(Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId());
		if($collection->getSize()){
			foreach($collection as $attribute){
				$attributes[] = array('value'=>$attribute->getAttributeCode(),'label'=>$attribute->getName());
			}
		}
		return $attributes;
	}
	
}
