<?php


namespace Magentohub\CityDropdown\Model\ResourceModel;

use Magentohub\CityDropdown\Api\Data\CityDropdownInterface;
use Magentohub\CityDropdown\Setup\InstallSchema;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Class CityDropdown
 * @package Magentohub\CityDropdown\Model\ResourceModel
 */
class CityDropdown extends AbstractDb
{
    /**
     * CityDropdown constructor.
     * @param Context $context
     * @param string|null $connectionName
     */
    public function __construct(
        Context $context,
        string $connectionName = null
    ) {
        parent::__construct(
            $context,
            $connectionName
        );
    }

    public function _construct()
    {
        $this->_init('magentohub_citydropdown', CityDropdownInterface::ENTITY_ID);
    }
}
