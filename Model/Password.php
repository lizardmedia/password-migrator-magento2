<?php
declare(strict_types=1);

/**
 * File:Password.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use LizardMedia\PasswordMigrator\Api\Data\PasswordInterface;
use LizardMedia\PasswordMigrator\Api\Data\PasswordInterfaceFactory;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Model\Context;
use LizardMedia\PasswordMigrator\Model\ResourceModel\Password as PasswordResource;

/**
 * Class Password
 * @package LizardMedia\PasswordMigrator\Model
 */
class Password extends AbstractModel
{
    const ID = 'id';
    const CUSTOMER_ID = 'customer_id';
    const PASSWORD = 'password';
    const SALT = 'salt';
    const CREATED_AT = 'created_at';

    /**
     * @var PasswordInterfaceFactory
     */
    private $passwordFactory;

    /**
     * Password constructor.
     * @param PasswordInterfaceFactory $passwordFactory
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        PasswordInterfaceFactory $passwordFactory,
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->passwordFactory = $passwordFactory;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(PasswordResource::class);
    }

    /**
     * @return PasswordInterface
     */
    public function getDataModel(): PasswordInterface
    {
        /** @var PasswordInterface $passwordDTO */
        $passwordDTO = $this->passwordFactory->create(
            [
                'id' => $this->getData(self::ID),
                'customerId' => $this->getData(self::CUSTOMER_ID)
            ]
        );
        $passwordDTO->setPassword($this->getData(self::PASSWORD));
        $passwordDTO->setSalt($this->getData(self::SALT));
        $passwordDTO->setCreatedAt($this->getData(self::CREATED_AT));
        return $passwordDTO;
    }
}
