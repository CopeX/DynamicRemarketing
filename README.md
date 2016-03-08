CopeX_DynamicRemarketing
=====================
Adds the google dynamic remarketing tags to the magento body

Original version by Adwise and adopted by CopeX.io

Facts
-----
- version: 1.0.0
- extension key: CopeX_DynamicRemarketing
- Composer name: copex/dynamicremarketing

Description
-----------
This module adds the google dynamic remarketing tags to the html body


Main Features:
- Enable / Disable extension
- Enable advanced tagging (Adds following values: ecomm_pvalue, ecomm_category, ecomm_quantity, hasaccount)
- Adds specific tags to:
  - homepage
  - searchresults
  - category view
  - product detail view
  - cart view
  - checkout success view

Requirements & Compatibility
------------
- PHP >= 5.3.0
- Magento >= 1.6.0.0

Configuration
-
System > Configuration > Sales > Google API > Google Dynamic Remarketing

Installation Instructions
-------------------------
1. Install the extension via submodule/modman, composer or a similar method.
2. Clear the cache, logout from the admin panel and then login again.
3. Configure the backend settings

Uninstallation
--------------
1. Remove all extension files from your Magento installation



Lead Developer
---------
Andreas Pointner
https://copex.io/
andreas.pointner@copex.io