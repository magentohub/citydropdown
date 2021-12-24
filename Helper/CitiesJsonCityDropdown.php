<?php

namespace Magentohub\CityDropdown\Helper;

use Magentohub\CityDropdown\Model\CityDropdownRepository;
use Magentohub\CityDropdown\Model\CityDropdown;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Directory\Model\Region;
use Magento\Directory\Model\ResourceModel\Country\Collection;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Directory\Helper\Data;
use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Json\Helper\Data as DataHelper;
use Magento\Store\Model\StoreManagerInterface;

class CitiesJsonCityDropdown extends Data
{
    private $cityDropdownRepository;

    private $searchCriteria;

    public function __construct(
        Context $context,
        Config $configCacheType,
        Collection $countryCollection,
        CollectionFactory $regCollectionFactory,
        DataHelper $jsonHelper,
        StoreManagerInterface $storeManager,
        CurrencyFactory $currencyFactory,
        CityDropdownRepository $cityDropdownRepository,
        SearchCriteriaBuilder $searchCriteria
    ) {
        $this->searchCriteria = $searchCriteria;
        $this->cityDropdownRepository = $cityDropdownRepository;
        parent::__construct(
            $context,
            $configCacheType,
            $countryCollection,
            $regCollectionFactory,
            $jsonHelper,
            $storeManager,
            $currencyFactory
        );
    }
    /**
     * Retrieve regions data
     *
     * @return array
     */
    public function getRegionData()
    {
        $countryIds = [];
        foreach ($this->getCountryCollection() as $country) {
            $countryIds[] = $country->getCountryId();
        }
        $collection = $this->_regCollectionFactory->create();
        $collection->addCountryFilter($countryIds)->load();
        $regions = [
            'config' => [
                'show_all_regions' => $this->isShowNonRequiredState(),
                'regions_required' => $this->getCountriesWithStatesRequired(),
            ],
        ];

        $searchCriteriaBuilder = $this->searchCriteria;
        $searchCriteria = $searchCriteriaBuilder->create();

        $citiesList = $this->cityDropdownRepository->getList($searchCriteria);
        $items = $citiesList->getItems();

        /** @var CityDropdown $item */
        $citiesData = [];
        foreach ($items as $item) {
            $citiesData[$item->getEntityId()] = $item;
        }

        foreach ($collection as $region) {
            /** @var $region Region */
            if (!$region->getRegionId()) {
                continue;
            }

            $cities = [];
            foreach ($citiesData as $cityId => $cityData) {
                $entityId = $cityData->getRegionId();
                $regionId = $region->getId();
                if ($entityId == $regionId) {
                    $id       = $cityData->getId();
                    $cityName = $cityData->getCityName();
                    $cities[$id] = [
                        'name' => $cityName,
                        'id' => $cityId
                    ];
                }
            }

            $regions[$region->getCountryId()][$region->getRegionId()] = [
                'code' => $region->getCode(),
                'name' => (string)__($region->getName()),
                'cities' => $cities
            ];
        }
        return $regions;
    }

}
