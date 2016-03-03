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
Class CopeX_DynamicRemarketing_Block_Script extends Mage_Core_Block_Template{
	
	/**
	 * Magento`s current store scope
	 */
	private $_store;
	
	/**
	 * Google Dynamic Remarketing Allowed Pagetypes
	 * @see https://support.google.com/adwords/answer/3103357?hl=en
	 */
	private $_allowedPageTypes 	= array('home','searchresults','category','product','cart','purchase','other');
	
	/**
	 * Google Dynamic Remarketing Allowed JS parameters
	 * key = param name value = required
	 * @see https://support.google.com/adwords/answer/3103357?hl=en
	 */
	private $_allowedJsParams	= array(
		'ecomm_prodid'	=> true,		// Product ID (must match Google Feed ID)
		'ecomm_pagetype' => true,		// Current pagetype (see list of allowed types)
		'ecomm_totalvalue' => true,		// Product price or sum of value in cart/purchase
		'ecomm_rec_prodid' => false,	// Recommanded product ID`s (related products)
		'ecomm_category' => false,		// Product category or array of categories
		'ecomm_pvalue' => false,		// Product value
		'ecomm_quantity' => false,		// Qty of products in cart or purchase
		'a' => false,					// Customers age
		'g' => false,					// Customers gender
		'hasaccount' => false,			// Indicates if a customer has an account
		'cqs' => false,					// Customer Quality Score (1 to 3)
		'rp' => false,					// Customer repeat purchaser (has earlier orders? y/n)
		'ly' => false,					// Customer layalty score (1 to 3)
		'hs' => false					// Customer High Spender Score (1 to 3)
	);

	/**
	 * Indicates to add product data
	 */
	private $_useProductData 	= true;
	
	/**
	 * Indicates the useage of advanced Google Remarketing Tags (can be omitted)
	 */
	private $_useAdvancedTags 	= true;
	
	/**
	 * Default template to use for Google Dynamic Remarketing Script HTML
	 */
	private $_useTemplate		= 'copex/dynamicremarketing/script.phtml';
	
	/**
	 * Default product attribute to use for 
	 */
	private $_productAttribute 	= 'sku';
	
	/**
	 * Default pagetype
	 */
	private $_pagetype			= 'other';
	
	/**
	 * Magento default block constructor
	 */
	public function _construct(){
		parent::_construct();
		$this->_store 				= Mage::app()->getStore();
		$this->_productAttribute 	= Mage::getStoreConfig('google/dynamicremarketing/attribute_key',$this->_store);
	}
	
	/**
	 * Set the needed template
	 */
	public function _prepareLayout(){
		$this->setTemplate($this->_useTemplate);
	}
	
	/**
	 * Set current pagetype 
	 * @param string $pagetype
	 */
	public function setPageType($pagetype){
		if(in_array(strtolower($pagetype),$this->_allowedPageTypes)){
			$this->_pagetype = strtolower($pagetype);
		}
	}
	
	/**
	 * Set product attribute to use for Google Product Key
	 * @param string $attributename
	 */
	public function setProductAttributeName($attributename){
		$this->_productAttribute = strtolower($attributename);
	}
	
	/**
	 * Set useage of advanced javascript parameters
	 * @param boolean $bool
	 */
	public function setUseAdvancedTags($bool){
		$this->_useAdvancedTags = (boolean)$bool;
	}
	
	/**
	 * Set useage of product data
	 * @param boolean $bool
	 */
	public function setUseProductData($bool){
		$this->_useProductData = (boolean)$bool;
	}
	
	/**
	 * Returns a Javascript object string with collected data
	 */
	public function getJsConfigParams(){
		$_params = array('ecomm_pagetype' => $this->_pagetype);
		switch($this->_pagetype){
			default:
			break;
			case 'product':
				$_params = array_merge($_params,$this->collectCurrentProductData());
			break;
			case 'cart':
				$_params = array_merge($_params,$this->collectCurrentQuotationData());
			break;
			case 'purchase':
				$_params = array_merge($_params,$this->collectCurrentOrderData());
			break;
		}
		// Return parameters as an javascript object string
		return Mage::helper('copex_dynamicremarketing')->getJsObjectString($_params);
	}
	
	/**
	 * Collect the data from currently viewed product
	 */
	private function collectCurrentProductData(){
		$_product = Mage::registry('current_product');
		if($_product && $_product instanceof Mage_Catalog_Model_Product){
			
			$_params = array();
			
			$_params['ecomm_prodid'] = $_product->getData($this->_productAttribute);
			$_params['ecomm_totalvalue'] = $this->formatPrice($_product->getFinalPrice());
			
			if($this->_useAdvancedTags){
				
				/**
				 * Optional params
				 * ecomm_rec_prodid	 - Recommanded product ID`s (related products)
				 * ecomm_category - Product category or array of categories
				 * ecomm_pvalue - Product value
				 */
				$_params['ecomm_pvalue'] = $this->formatPrice($_product->getFinalPrice());
				if(Mage::registry('current_category')){
					$_params['ecomm_category'] = Mage::registry('current_category')->getName();
				}
			}
			
			return $_params;
			
		}
		return false;
	}
	
	/**
	 * Collect data from the customers quotation
	 */
	private function collectCurrentQuotationData(){
		$_quotation = Mage::getSingleton('checkout/session')->getQuote();
		if($_quotation && $_quotation instanceof Mage_Sales_Model_Quote){
			
			$qtys		= array();
			$products 	= array();
			
			foreach($_quotation->getAllVisibleItems() as $_product){
				$qtys[] 	= $_product->getQty();
				$products[] = $_product->getData($this->_productAttribute);
			}
			
			$_params = array();
			$_params['ecomm_prodid'] = $products;
			$_params['ecomm_totalvalue'] = $this->formatPrice($_quotation->getGrandTotal());
			
			if($this->_useAdvancedTags){
				$_params['ecomm_quantity'] = $qtys;
			}
			
			return $_params;
		}
		return false;
	}
	
	/**
	 * Collect data from the customers order
	 */
	private function collectCurrentOrderData(){
		$_order = Mage::getSingleton('sales/order')->load(Mage::getSingleton('checkout/session')->getLastOrderId());
		if($_order && $_order instanceof Mage_Sales_Model_Order){
			
			$total = 0.00;
			$qtys = array();
			$products = array();
			$prices = array();
			
			foreach($_order->getAllVisibleItems() as $_product){
				$total += $_product->getPrice();
				$products[] = $_product->getData($this->_productAttribute);
				$qtys[] = number_format($_product->getQtyOrdered(),2);
				$prices[] = number_format($_product->getPrice(),2);
			}
			
			$_params = array();
			$_params['ecomm_prodid'] = $products;
			$_params['ecomm_totalvalue'] = $this->formatPrice($total);
			
			if($this->_useAdvancedTags){
				$_params['ecomm_quantity'] = $qtys;
				$_params['ecomm_pvalue'] =  $prices;
				$_params['hasaccount'] = $_order['customer_is_guest'] == 1 ? 'N' : 'Y';
			}
			
			return $_params;

		}
		
		return false;
	}
	
	/**
	 * Formats a price in store currency settings
	 */
	private function formatPrice($price){
		return Mage::helper('core')->currency($price, false, false);
	}
	
}
