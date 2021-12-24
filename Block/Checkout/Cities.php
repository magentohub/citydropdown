<?php

namespace Magentohub\CityDropdown\Block\Checkout;

use Magentohub\CityDropdown\Model\CityDropdownRepository;
use Magentohub\CityDropdown\Model\CityDropdown;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;

class Cities extends Template
{
    /** @var CityDropdownRepository  */
    private $cityDropdownRepository;

    /** @var SearchCriteriaBuilder  */
    private $searchCriteria;

    /** @var SerializerInterface  */
    private $serializer;

    public function __construct(
        Template\Context $context,
        CityDropdownRepository $cityDropdownRepository,
        SearchCriteriaBuilder $searchCriteria,
        SerializerInterface $serializer,
        array $data = []
    )
    {
        $this->searchCriteria = $searchCriteria;
        $this->cityDropdownRepository = $cityDropdownRepository;
        $this->serializer = $serializer;
        parent::__construct($context, $data);
    }

    public function citiesJson()
    {

        $searchCriteriaBuilder = $this->searchCriteria;
        $searchCriteria = $searchCriteriaBuilder->create();

        $citiesList = $this->cityDropdownRepository->getList($searchCriteria);
        $items = $citiesList->getItems();

        $return = [];

        /** @var CityDropdown $item */
        foreach ($items as $item) {
            $return[] = ['region_id' => $item->getRegionId(), 'city_name' => $item->getCityName()];
        }

        return $this->serializer->serialize($return);
    }
}
