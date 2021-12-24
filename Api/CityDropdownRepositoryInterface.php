<?php


namespace Magentohub\CityDropdown\Api;

use Magentohub\CityDropdown\Api\Data\CityDropdownInterface;

interface CityDropdownRepositoryInterface
{
    /**
     * @param CityDropdownInterface $templates
     * @return mixed
     */
    public function save(CityDropdownInterface $templates);

    /**
     * @param $value
     * @return mixed
     */
    public function getById($value);

    /**
     * @param CityDropdownInterface $templates
     * @return mixed
     */
    public function delete(CityDropdownInterface $templates);

    /**
     * @param $value
     * @return mixed
     */
    public function deleteById($value);
}
