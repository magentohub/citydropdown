<?php

declare(strict_types=1);

namespace Magentohub\CityDropdown\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Serialize\SerializerInterface;

class Cities implements ConfigProviderInterface
{
    /** @var CityDropdownRepository  */
    private $cityDropdownRepository;

    /** @var SearchCriteriaBuilder  */
    private $searchCriteria;

    /** @var SerializerInterface  */
    private $serializer;

    public function __construct(
        CityDropdownRepository $cityDropdownRepository,
        SearchCriteriaBuilder $searchCriteria,
        SerializerInterface $serializer
    ) {
        $this->cityDropdownRepository = $cityDropdownRepository;
        $this->searchCriteria = $searchCriteria;
        $this->serializer = $serializer;
    }

    public function getConfig(): array
    {
        return [
            'cities' => $this->getCities()
        ];
    }

    private function getCities(): string
    {
        $searchCriteriaBuilder = $this->searchCriteria;
        $searchCriteria = $searchCriteriaBuilder->create();

        $citiesList = $this->cityDropdownRepository->getList($searchCriteria);
        $items = $citiesList->getItems();

        $return = [];

        /** @var CityDropdown $item */
        foreach ($items as $item) {
            $return[$item->getRegionId()][] = $item->getCityName();
        }

        return $this->serializer->serialize($return);
    }
}
