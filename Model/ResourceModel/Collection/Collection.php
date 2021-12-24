<?php


namespace Magentohub\CityDropdown\Model\ResourceModel\Collection;

use Magentohub\CityDropdown\Model\CityDropdown as CityDropdownModel;
use Magentohub\CityDropdown\Model\ResourceModel\CityDropdown  as CityDropdownResourceModel;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    //@codingStandardsIgnoreLine
    protected $_idCity = 'entity_id';

    /**
     * Init resource model
     * @return void
     */
    public function _construct()
    {

        $this->_init(
            CityDropdownModel::class,
            CityDropdownResourceModel::class
        );
        $this->_map['citydropdown']['entity_id'] = 'main_table.entity_id';
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        return $this;
    }
}
