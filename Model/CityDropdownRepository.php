<?php


namespace Magentohub\CityDropdown\Model;

use Magentohub\CityDropdown\Api\Data\CityDropdownInterface;
use Magentohub\CityDropdown\Model\ResourceModel\CityDropdown as CityDropdownResourceModel;
use Magentohub\CityDropdown\Api\CityDropdownRepositoryInterface;
use Magento\Framework\Exception\LocalizedException as Exception;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magentohub\CityDropdown\Model\ResourceModel\Collection\Collection;
use Magentohub\CityDropdown\Model\ResourceModel\Collection\CollectionFactory;
use Magentohub\CityDropdown\Api\Data\CitySearchResultInterfaceFactory;

class CityDropdownRepository implements CityDropdownRepositoryInterface
{
    /**
     * @var array
     */
    private $instances = [];

    /**
     * @var CityDropdownResourceModel
     */
    private $cityDropdownResourceModel;

    /**
     * @var CityDropdownInterface
     */
    private $cityDropdownInterface;

    /**
     * @var CityDropdownFactory
     */
    private $cityDropdownFactory;

    private $citySearchResultInterfaceFactory;

    private $collectionFactory;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * CityDropdownRepository constructor.
     * @param CityDropdownResourceModel $cityDropdownResourceModel
     * @param CityDropdownInterface $cityDropdownInterface
     * @param CityDropdownFactory $cityDropdownFactory
     * @param CollectionFactory $collectionFactory
     * @param CitySearchResultInterfaceFactory $citySearchResultInterfaceFactory
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        CityDropdownResourceModel $cityDropdownResourceModel,
        CityDropdownInterface $cityDropdownInterface,
        CityDropdownFactory $cityDropdownFactory,
        ManagerInterface $messageManager,
        CollectionFactory $collectionFactory,
        CitySearchResultInterfaceFactory $citySearchResultInterfaceFactory
    ) {
        $this->citySearchResultInterfaceFactory = $citySearchResultInterfaceFactory;
        $this->cityDropdownResourceModel = $cityDropdownResourceModel;
        $this->collectionFactory = $collectionFactory;
        $this->cityDropdownInterface = $cityDropdownInterface;
        $this->cityDropdownFactory = $cityDropdownFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * @param CityDropdownInterface $cityDropdownInterface
     * @return CityDropdownInterface
     * @throws \Exception
     */
    public function save(CityDropdownInterface $cityDropdownInterface)
    {
        try {
            $this->cityDropdownResourceModel->save($cityDropdownInterface);
        } catch (Exception $e) {
            $this->messageManager
                ->addExceptionMessage(
                    $e,
                    'There was a error while saving the city ' . $e->getMessage()
                );
        }

        return $cityDropdownInterface;
    }

    /**
     * @param $$cityDropdownId
     * @return array
     */
    public function getById($cityDropdownId)
    {
        if (!isset($this->instances[$cityDropdownId])) {
            $cityDropdown = $this->cityDropdownFactory->create();
            $this->cityDropdownResourceModel->load($cityDropdown, $cityDropdownId);
            $this->instances[$cityDropdownId] = $cityDropdown;
        }
        return $this->instances[$cityDropdownId];
    }

    /**
     * @param CityDropdownInterface $cityDropdownInterface
     * @return bool
     * @throws \Exception
     */
    public function delete(CityDropdownInterface $cityDropdownInterface)
    {
        $id = $cityDropdownInterface->getId();
        try {
            unset($this->instances[$id]);
            $this->cityDropdownResourceModel->delete($cityDropdownInterface);
        } catch (Exception $e) {
            $this->messageManager
                ->addExceptionMessage($e, 'There was a error while deleting the city');
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * @param $cityDropdownId
     * @return bool
     * @throws \Exception
     */
    public function deleteById($cityDropdownId)
    {
        $cityDropdown = $this->getById($cityDropdownId);
        return $this->delete($cityDropdown);
    }

    /**
     * @param CityDropdownInterface $cityDropdownInterface
     * @return bool
     * @throws \Exception
     */
    public function saveAndDelete(CityDropdownInterface $cityDropdownInterface)
    {
        $cityDropdownInterface->setData(Data::ACTION, Data::DELETE);
        $this->save($cityDropdownInterface);
        return true;
    }

    /**
     * @param $cityDropdownId
     * @return bool
     * @throws \Exception
     */
    public function saveAndDeleteById($cityDropdownId)
    {
        $cityDropdown = $this->getById($cityDropdownId);
        return $this->saveAndDelete($cityDropdown);
    }


    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();
        $this->addFiltersToCollection($searchCriteria, $collection);
        $this->addSortOrdersToCollection($searchCriteria, $collection);
        $this->addPagingToCollection($searchCriteria, $collection);
        $collection->load();
        return $this->buildSearchResult($searchCriteria, $collection);
    }

    private function addFiltersToCollection(SearchCriteriaInterface $searchCriteria, Collection $collection)
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $fields[] = $filter->getField();
                $conditions[] = [$filter->getConditionType() => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
        }
    }

    private function addSortOrdersToCollection(SearchCriteriaInterface $searchCriteria, Collection $collection)
    {
        foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
            $direction = $sortOrder->getDirection() == SortOrder::SORT_ASC ? 'asc' : 'desc';
            $collection->addOrder($sortOrder->getField(), $direction);
        }
    }

    private function addPagingToCollection(SearchCriteriaInterface $searchCriteria, Collection $collection)
    {
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->setCurPage($searchCriteria->getCurrentPage());
    }

    private function buildSearchResult(SearchCriteriaInterface $searchCriteria, Collection $collection)
    {
        $searchResults = $this->citySearchResultInterfaceFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }
}
