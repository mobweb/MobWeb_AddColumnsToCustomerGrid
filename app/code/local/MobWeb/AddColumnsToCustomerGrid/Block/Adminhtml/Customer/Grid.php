<?php
class MobWeb_AddColumnsToCustomerGrid_Block_Adminhtml_Customer_Grid extends Mage_Adminhtml_Block_Customer_Grid{
    protected function _prepareCollection(){
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            // Copy one of the following lines to add a column based on
            // an attribute of the customer themself
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('group_id')
            // Or copy one of the following lines to add a column based on
            // an attribute of the customer's main billing address
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left')
            // Add a "Company" column
            ->joinAttribute('company', 'customer_address/company', 'default_billing', null, 'left');

        $this->setCollection($collection);
        if ($this->getCollection()) {
            $this->_preparePage();

            $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
            $dir      = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
            $filter   = $this->getParam($this->getVarNameFilter(), null);

            if (is_null($filter)) {
                $filter = $this->_defaultFilter;
            }

            if (is_string($filter)) {
                $data = $this->helper('adminhtml')->prepareFilterString($filter);
                $this->_setFilterValues($data);
            }
            else if ($filter && is_array($filter)) {
                $this->_setFilterValues($filter);
            }
            else if(0 !== sizeof($this->_defaultFilter)) {
                $this->_setFilterValues($this->_defaultFilter);
            }

            if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {
                $dir = (strtolower($dir)=='desc') ? 'desc' : 'asc';
                $this->_columns[$columnId]->setDir($dir);
                $this->_setCollectionOrder($this->_columns[$columnId]);
            }

            if (!$this->_isExport) {
                $this->getCollection()->load();
                $this->_afterLoadCollection();
            }
        }

        return $this;
    }

    protected function _prepareColumns(){
        $this->addColumnAfter('company', array(
            'header'    => Mage::helper('customer')->__('Company'),
            'index'     => 'company'
        ),'email');
        return parent::_prepareColumns();
    }
}