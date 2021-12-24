<?php


namespace Magentohub\CityDropdown\Model;

use Magentohub\CityDropdown\Api\Data\CityDropdownInterface;
use Magentohub\CityDropdown\Model\ResourceModel\CityDropdown as CityDropdownResourceModel;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource as AbstractResourceModel;
use Magento\Framework\Registry;

class CityDropdown extends AbstractModel implements CityDropdownInterface
{
    public function __construct(
        Context $context,
        Registry $registry,
        AbstractResourceModel $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    public function _construct()
    {
        $this->_init(CityDropdownResourceModel::class);
    }

    public function getEntityId()
    {
        return $this->getData(CityDropdownInterface::ENTITY_ID);
    }

    public function getRegionId()
    {
        return $this->getData(CityDropdownInterface::REGION_ID);
    }

    public function getCityName()
    {
        return $this->getData(CityDropdownInterface::CITY_NAME);
    }


    public function setEntityId($entityId)
    {
        $this->setData(CityDropdownInterface::ENTITY_ID, $entityId);
    }

    public function setRegionId($regionId)
    {
        $this->setData(CityDropdownInterface::REGION_ID, $regionId);
    }

    public function setCityName($cityName)
    {
        $this->setData(CityDropdownInterface::CITY_NAME, $cityName);
    }
}
