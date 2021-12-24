<?php


namespace Magentohub\CityDropdown\Controller\Adminhtml\Index;

use Magentohub\CityDropdown\Helper\Data;
use Magentohub\CityDropdown\Model\CityDropdownRepository;
use Magentohub\CityDropdown\Model\CityDropdownFactory;
use Magentohub\CityDropdown\Model\ResourceModel\Collection\Collection;
use Magentohub\CityDropdown\Model\ResourceModel\Collection\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;
use Magento\Framework\File\Csv;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\App\Filesystem\DirectoryList;

class Upload extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magentohub_CityDropdown::citydropdown';

    protected $resultPageFactory;

    private $csvProccesor;

    private $moduleReader;

    private $directoryList;

    private $dataHelper;

    private $collectionFactory;

    private $cityDropdownFactory;

    private $resultRedirect;

    private $cityDropdownRepository;

    public function __construct(
        Context $context,
        Csv $csvProccesor,
        Reader $moduleReader,
        PageFactory $resultPageFactory,
        DirectoryList $directoryList,
        CollectionFactory $collectionFactory,
        ResultFactory $resultRedirect,
        CityDropdownFactory $cityDropdownFactory,
        CityDropdownRepository $cityDropdownRepository,
        Data $dataHelper
    ) {
        $this->cityDropdownRepository = $cityDropdownRepository;
        $this->resultRedirect = $resultRedirect;
        $this->cityDropdownFactory = $cityDropdownFactory;
        $this->collectionFactory = $collectionFactory;
        $this->dataHelper = $dataHelper;
        $this->directoryList = $directoryList;
        $this->moduleReader = $moduleReader;
        $this->csvProccesor = $csvProccesor;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Index action list city.
     * @return $resultRedirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $url = $this->_redirect->getRefererUrl();
        $resultRedirect->setUrl($url);

        $this->readCsv();

        return $resultRedirect;
    }

    public function readCsv()
    {
        $pubMediaDir = $this->directoryList->getPath(DirectoryList::MEDIA);
        $fieName = $this->dataHelper->getConfigFileName();
        $ds = DIRECTORY_SEPARATOR;
        $dirTest = '/test';

        $file = $pubMediaDir . $dirTest . $ds . $fieName;

        if (!empty($file)) {
            $csvData = $this->csvProccesor->getData($file);

            $csvDataProcessed = [];
            unset($csvData[0]);
            list($collection, $csvDataProcessed) = $this->csvProcessValues($csvData, $csvDataProcessed);

            foreach ($csvDataProcessed as $dataRow) {
                $regionId = $dataRow['region_id'];
                $cityName = $dataRow['city'];
                $entityId = $dataRow['entity_id'];

                $cityDropdownRepository = $this->cityDropdownFactory->create();
                if (isset($entityId) && is_numeric($entityId)) {
                    $cityDropdownRepository = $this->cityDropdownRepository->getById($entityId);
                    $cityDropdownRepository->setCityName($cityName);
                    $this->cityDropdownRepository->save($cityDropdownRepository);
                    continue;
                }

                $cityDropdownRepository->setRegionId($regionId);
                $cityDropdownRepository->setCityName($cityName);

                $collection->addItem($cityDropdownRepository);
            }
        }
        $collection->walk('save');
    }

    /**
     * @return bool
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }

    /**
     * @param $csvData
     * @param $csvDataProcessed
     * @return array
     */
    private function csvProcessValues($csvData, $csvDataProcessed)
    {
        /** @var  Collection $collection */
        $collection = $this->collectionFactory->create();

        foreach ($csvData as $csvValue) {
            $csvValueProcessed = [];
            foreach ($csvValue as $key => $value) {
                if ($key == 0) {
                    $csvValueProcessed['entity_id'] = $value;
                }

                if ($key == 1) {
                    $csvValueProcessed['region_id'] = $value;
                }

                if ($key == 2) {
                    $csvValueProcessed['city'] = $value;
                }
            }
            $csvDataProcessed[] = $csvValueProcessed;
        }
        return [$collection, $csvDataProcessed];
    }
}


