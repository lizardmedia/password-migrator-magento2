<?php
declare(strict_types=1);

/**
 * File:PasswordRepository.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Model\Data;

use Exception;
use LizardMedia\PasswordMigrator\Api\Data\PasswordInterface;
use LizardMedia\PasswordMigrator\Api\Data\PasswordRepositoryInterface;
use LizardMedia\PasswordMigrator\Model\Password;
use LizardMedia\PasswordMigrator\Model\ResourceModel\Password\Collection;
use LizardMedia\PasswordMigrator\Model\ResourceModel\Password\CollectionFactory;
use LizardMedia\PasswordMigrator\Model\PasswordFactory;
use LizardMedia\PasswordMigrator\Model\ResourceModel\Password as PasswordResource;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class PasswordRepository
 * @package LizardMedia\PasswordMigrator\Model\Data
 */
class PasswordRepository implements PasswordRepositoryInterface
{
    const EXPIRED_PASSWORDS_PURGE_SIZE = 60000;

    /**
     * @var PasswordFactory
     */
    private $passwordFactory;

    /**
     * @var PasswordResource
     */
    private $passwordResource;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * PasswordRepository constructor.
     * @param PasswordFactory $passwordFactory
     * @param PasswordResource $passwordResource
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        PasswordFactory $passwordFactory,
        PasswordResource $passwordResource,
        CollectionFactory $collectionFactory
    ) {
        $this->passwordFactory = $passwordFactory;
        $this->passwordResource = $passwordResource;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param int $customerId
     * @return PasswordInterface
     * @throws NoSuchEntityException
     */
    public function getByCustomerId(int $customerId): PasswordInterface
    {
        /** @var Password $password */
        $password = $this->passwordFactory->create();
        $this->passwordResource->load($password, $customerId, Password::CUSTOMER_ID);
        if (!$password->getId()) {
            throw new NoSuchEntityException();
        }
        return $password->getDataModel();
    }

    /**
     * @param PasswordInterface $password
     * @return void
     * @throws Exception
     */
    public function save(PasswordInterface $password): void
    {
        /** @var Password $model */
        $model = $this->passwordFactory->create();
        if ($password->getId()) {
            $this->passwordResource->load($model, $password->getId());
        }

        $model->setData(Password::CUSTOMER_ID, $password->getCustomerId());
        $model->setData(Password::PASSWORD, $password->getPassword());
        $model->setData(Password::SALT, $password->getSalt());

        $this->passwordResource->save($model);
    }

    /**
     * @param PasswordInterface $password
     * @return void
     * @throws Exception
     */
    public function delete(PasswordInterface $password): void
    {
        if (!$password->getId()) {
            throw new NoSuchEntityException();
        }

        /** @var Password $model */
        $model = $this->passwordFactory->create();
        $this->passwordResource->load($model, $password->getId());
        $this->passwordResource->delete($model);
    }

    /**
     * @param string $dateTime
     * @return PasswordInterface[]
     */
    public function getOlderThan(string $dateTime): array
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(Password::CREATED_AT, ['lt' => $dateTime])
            ->setPageSize(self::EXPIRED_PASSWORDS_PURGE_SIZE)
            ->setCurPage(1);

        return array_map(function ($password) {
            /** @var Password $password */
            return $password->getDataModel();
        }, $collection->getItems());
    }
}
