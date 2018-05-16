<?php

namespace AppBundle\Entity;

use AppBundle\Repository\CustomerOrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Customer
 *
 * @ORM\Table(name="customer")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CustomerRepository")
 */
class Customer
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=255, nullable=true)
     */
    private $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, unique=true)
     */
    private $phone;

    /**
     * @var bool
     *
     * @ORM\Column(name="permission_to_use_personal_info", type="boolean")
     */
    private $permissionToUsePersonalInfo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="permission_received_at", type="datetime")
     */
    private $permissionReceivedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="permission_denied_at", type="datetime", nullable=true)
     */
    private $permissionDeniedAt;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CustomerAddress", mappedBy="customer")
     */
    private $addresses;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CustomerOrder", mappedBy="customer")
     */
    private $orders;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ProcessLog", mappedBy="customer")
     */
    private $logs;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="anonymized_at", type="datetime", nullable=true)
     */
    private $anonymizedAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="anonymized", type="boolean", nullable=true)
     */
    private $anonymized;

    /**
     * Customer constructor.
     */
    public function __construct()
    {
        $this->addresses = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->logs = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Customer
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        if ($this->getPermissionToUsePersonalInfo() === false) {
            return '***';
        }
        return $this->name;
    }

    /**
     * Set surname
     *
     * @param string $surname
     *
     * @return Customer
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        if ($this->getPermissionToUsePersonalInfo() === false) {
            return '***';
        }
        return $this->surname;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Customer
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        if ($this->getPermissionToUsePersonalInfo() === false) {
            return '***';
        }
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Customer
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        if ($this->getPermissionToUsePersonalInfo() === false) {
            return '***';
        }
        return $this->phone;
    }

    /**
     * Set permissionToUsePersonalInfo
     *
     * @param boolean $permissionToUsePersonalInfo
     *
     * @return Customer
     */
    public function setPermissionToUsePersonalInfo($permissionToUsePersonalInfo)
    {
        $this->permissionToUsePersonalInfo = $permissionToUsePersonalInfo;

        return $this;
    }

    /**
     * Get permissionToUsePersonalInfo
     *
     * @return bool
     */
    public function getPermissionToUsePersonalInfo()
    {
        return $this->permissionToUsePersonalInfo;
    }

    /**
     * @return \DateTime
     */
    public function getPermissionReceivedAt()
    {
        return $this->permissionReceivedAt;
    }

    /**
     * @param \DateTime $permissionReceivedAt
     * @return Customer
     */
    public function setPermissionReceivedAt($permissionReceivedAt)
    {
        $this->permissionReceivedAt = $permissionReceivedAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getPermissionDeniedAt()
    {
        return $this->permissionDeniedAt;
    }

    /**
     * @param string $permissionDeniedAt
     * @return Customer
     */
    public function setPermissionDeniedAt($permissionDeniedAt)
    {
        $this->permissionDeniedAt = $permissionDeniedAt;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * @param ArrayCollection $addresses
     * @return Customer
     */
    public function setAddresses($addresses)
    {
        $this->addresses = $addresses;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @return ArrayCollection
     */
    public function getProducts()
    {
        $products = new ArrayCollection();

        /** @var CustomerOrder $order */
        foreach($this->getOrders()->toArray() as $order) {
            /** @var Product $product */
            foreach($order->getProducts() as $product) {
                $products->add($product);
            }
        }
        return $products;
    }

    /**
     * @param ArrayCollection $orders
     * @return Customer
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrdersAmount()
    {
        return $this->getOrders()->count();
    }

    /**
     * @return int
     */
    public function getClosedOrdersAmount()
    {
        return $this->getOrders()->filter(function(CustomerOrder $order) {
            dump($order);
            dump($order->getStatus());
//            return $order->getStatus()->getCode() === 'closed';
        })->count();
    }

    /**
     * @return float|int
     */
    public function getTotalPrice()
    {
        return array_sum($this->getOrders()->map(function(CustomerOrder $order) {
            return $order->getTotalPrice();
        })->toArray());
    }

    /**
     * @return float|int
     */
    public function getTotalMarkup()
    {
        return array_sum($this->getOrders()->map(function(CustomerOrder $order) {
            return $order->getTotalMarkup();
        })->toArray());
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return Customer
     */
    public function setCreatedAt(\DateTime $createdAt): Customer
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * @param ArrayCollection $logs
     * @return Customer
     */
    public function setLogs(ArrayCollection $logs): Customer
    {
        $this->logs = $logs;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getAnonymizedAt()
    {
        return $this->anonymizedAt;
    }

    /**
     * @param \DateTime $anonymizedAt
     * @return Customer
     */
    public function setAnonymizedAt(\DateTime $anonymizedAt): Customer
    {
        $this->anonymizedAt = $anonymizedAt;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAnonymized()
    {
        return $this->anonymized;
    }

    /**
     * @param bool $anonymized
     * @return Customer
     */
    public function setAnonymized(bool $anonymized): Customer
    {
        $this->anonymized = $anonymized;
        return $this;
    }
}

