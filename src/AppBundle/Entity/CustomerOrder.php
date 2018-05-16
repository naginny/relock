<?php

namespace AppBundle\Entity;

use AppBundle\Repository\CustomerAddressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * CustomerOrder
 *
 * @ORM\Table(name="customer_order")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CustomerOrderRepository")
 */
class CustomerOrder
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
     * @var \DateTime
     *
     * @ORM\Column(name="closed_at", type="datetime", nullable=true)
     */
    private $closedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="delivery_at", type="datetime", nullable=true)
     */
    private $deliveryAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="delivered", type="boolean", nullable=true)
     */
    private $delivered;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="installation_at", type="datetime", nullable=true)
     */
    private $installationAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="installed", type="boolean", nullable=true)
     */
    private $installed;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="rejected_at", type="datetime", nullable=true)
     */
    private $rejectedAt;


    /**
     * @var string
     *
     * @ORM\Column(name="rejected_reason", type="text", nullable=true)
     */
    private $rejectedReason;


    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes;

    /**
     * @var boolean
     *
     * @ORM\Column(name="invoice_sent", type="boolean", nullable=true)
     */
    private $invoiceSent;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_id", type="string", nullable=true)
     */
    private $invoiceId;


    /**
     * @var boolean
     *
     * @ORM\Column(name="prepayment_received", type="boolean", nullable=true)
     */
    private $prepaymentReceived;


    /**
     * @var boolean
     *
     * @ORM\Column(name="full_payment_received", type="boolean", nullable=true)
     */
    private $fullPaymentReceived;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="requested_measurement_at", type="datetime")
     */
    private $requestedMeasurementAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="requested_installation_at", type="datetime")
     */
    private $requestedInstallationAt;

    /**
     * @var int
     *
     * @ORM\Column(name="requested_door_amount", type="integer")
     */
    private $requestedDoorAmount;

    /**
     * @var int
     *
     * @ORM\Column(name="requested_window_amount", type="integer")
     */
    private $requestedWindowAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="requested_jalousie_amount", type="integer")
     */
    private $requestedJalousieAmount;

    /**
     * @var int
     *
     * @ORM\Column(name="requested_gate_amount", type="integer")
     */
    private $requestedGateAmount;

    /**
     * @var Customer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer", inversedBy="orders")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    private $customer;

    /**
     * @var CustomerOrderStatus
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CustomerOrderStatus")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    private $status;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Product", mappedBy="order")
     */
    private $products;

    /**
     * @var CustomerAddress
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CustomerAddress")
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     */
    private $address;

    /**
     * CustomerOrder constructor.
     */
    public function __construct()
    {
        $this->products = new ArrayCollection();
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return CustomerOrder
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set closedAt
     *
     * @param \DateTime $closedAt
     *
     * @return CustomerOrder
     */
    public function setClosedAt($closedAt)
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    /**
     * Get closedAt
     *
     * @return \DateTime
     */
    public function getClosedAt()
    {
        return $this->closedAt;
    }

    /**
     * @return \DateTime
     */
    public function getRequestedMeasurementAt()
    {
        return $this->requestedMeasurementAt;
    }

    /**
     * @param \DateTime $requestedMeasurementAt
     * @return CustomerOrder
     */
    public function setRequestedMeasurementAt($requestedMeasurementAt)
    {
        $this->requestedMeasurementAt = $requestedMeasurementAt;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRequestedInstallationAt()
    {
        return $this->requestedInstallationAt;
    }

    /**
     * @param \DateTime $requestedInstallationAt
     * @return CustomerOrder
     */
    public function setRequestedInstallationAt($requestedInstallationAt)
    {
        $this->requestedInstallationAt = $requestedInstallationAt;
        return $this;
    }

    /**
     * @return int
     */
    public function getRequestedDoorAmount()
    {
        return $this->requestedDoorAmount;
    }

    /**
     * @param int $requestedDoorAmount
     * @return CustomerOrder
     */
    public function setRequestedDoorAmount($requestedDoorAmount)
    {
        $this->requestedDoorAmount = $requestedDoorAmount;
        return $this;
    }

    /**
     * @return int
     */
    public function getRequestedWindowAmount()
    {
        return $this->requestedWindowAmount;
    }

    /**
     * @param int $requestedWindowAmount
     * @return CustomerOrder
     */
    public function setRequestedWindowAmount($requestedWindowAmount)
    {
        $this->requestedWindowAmount = $requestedWindowAmount;
        return $this;
    }

    /**
     * @return string
     */
    public function getRequestedJalousieAmount()
    {
        return $this->requestedJalousieAmount;
    }

    /**
     * @param string $requestedJalousieAmount
     * @return CustomerOrder
     */
    public function setRequestedJalousieAmount($requestedJalousieAmount)
    {
        $this->requestedJalousieAmount = $requestedJalousieAmount;
        return $this;
    }

    /**
     * @return int
     */
    public function getRequestedGateAmount()
    {
        return $this->requestedGateAmount;
    }

    /**
     * @param int $requestedGateAmount
     * @return CustomerOrder
     */
    public function setRequestedGateAmount($requestedGateAmount)
    {
        $this->requestedGateAmount = $requestedGateAmount;
        return $this;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     * @return CustomerOrder
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @return CustomerOrderStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param CustomerOrderStatus $status
     * @return CustomerOrder
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @return int
     */
    public function getProductsAmount()
    {
        return $this->getProducts()->count();
    }

    /**
     * @param ArrayCollection $products
     * @return CustomerOrder
     */
    public function setProducts(ArrayCollection $products): CustomerOrder
    {
        $this->products = $products;
        return $this;
    }

    /**
     * @param Product $product
     * @return CustomerOrder
     */
    public function addProduct(Product $product)
    {
        $this->getProducts()->add($product);
        return $this;
    }

    /**
     * @return CustomerAddress
     */
    public function getAddress(): CustomerAddress
    {
        return $this->address;
    }

    /**
     * @param CustomerAddress $address
     * @return CustomerOrder
     */
    public function setAddress(CustomerAddress $address): CustomerOrder
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRejectedAt()
    {
        return $this->rejectedAt;
    }

    /**
     * @param \DateTime $rejectedAt
     * @return CustomerOrder
     */
    public function setRejectedAt(\DateTime $rejectedAt): CustomerOrder
    {
        $this->rejectedAt = $rejectedAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getRejectedReason()
    {
        return $this->rejectedReason;
    }

    /**
     * @param string $rejectedReason
     * @return CustomerOrder
     */
    public function setRejectedReason(string $rejectedReason): CustomerOrder
    {
        $this->rejectedReason = $rejectedReason;
        return $this;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     * @return CustomerOrder
     */
    public function setNotes(string $notes): CustomerOrder
    {
        $this->notes = $notes;
        return $this;
    }

    /**
     * @return bool
     */
    public function getMeasurementsSet()
    {
        return $this->getProducts()->filter(function(Product $product) {
            return $product->getMeasurementsSet();
        })->count() === $this->getProducts()->count();
    }

    /**
     * @return float|int
     */
    public function getPrepaymentAmount()
    {
        return array_sum(
            $this->getProducts()->map(function(Product $product){
                return $product->getPrepaymentAmount();
            })->toArray()
        );
    }

    /**
     * @return float|int
     */
    public function getTotalNetPrice()
    {
        return array_sum(
            $this->getProducts()->map(function(Product $product){
                return $product->getTotalNetPrice();
            })->toArray()
        );

    }

    /**
     * @return float|int
     */
    public function getTotalPrice()
    {
        return array_sum(
            $this->getProducts()->map(function(Product $product){
                return $product->getTotalPrice();
            })->toArray()
        );

    }

    /**
     * @return float|int
     */
    public function getTotalMarkup()
    {
        return array_sum(
            $this->getProducts()->map(function(Product $product){
                return $product->getMarkup() + $product->getAdditionalMarkup();
            })->toArray()
        );

    }

    /**
     * @return bool
     */
    public function isInvoiceSent()
    {
        return $this->invoiceSent;
    }

    /**
     * @param bool $invoiceSent
     * @return CustomerOrder
     */
    public function setInvoiceSent(bool $invoiceSent): CustomerOrder
    {
        $this->invoiceSent = $invoiceSent;
        return $this;
    }

    /**
     * @return string
     */
    public function generateInvoiceId()
    {
        return sha1($this->getId() . $this->getCreatedAt()->format('Y-m-d H:i:s'));
    }

    /**
     * @return string
     */
    public function getInvoiceId()
    {
        return $this->invoiceId;
    }

    /**
     * @param string $invoiceId
     * @return CustomerOrder
     */
    public function setInvoiceId(string $invoiceId): CustomerOrder
    {
        $this->invoiceId = $invoiceId;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPrepaymentReceived()
    {
        return $this->prepaymentReceived;
    }

    /**
     * @param bool $prepaymentReceived
     * @return CustomerOrder
     */
    public function setPrepaymentReceived(bool $prepaymentReceived): CustomerOrder
    {
        $this->prepaymentReceived = $prepaymentReceived;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFullPaymentReceived()
    {
        return $this->fullPaymentReceived;
    }

    /**
     * @param bool $fullPaymentReceived
     * @return CustomerOrder
     */
    public function setFullPaymentReceived(bool $fullPaymentReceived): CustomerOrder
    {
        $this->fullPaymentReceived = $fullPaymentReceived;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDeliveryAt()
    {
        return $this->deliveryAt;
    }

    /**
     * @param \DateTime $deliveryAt
     * @return CustomerOrder
     */
    public function setDeliveryAt(\DateTime $deliveryAt): CustomerOrder
    {
        $this->deliveryAt = $deliveryAt;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDelivered()
    {
        return $this->delivered;
    }

    /**
     * @param bool $delivered
     * @return CustomerOrder
     */
    public function setDelivered(bool $delivered): CustomerOrder
    {
        $this->delivered = $delivered;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getInstallationAt()
    {
        return $this->installationAt;
    }

    /**
     * @param \DateTime $installationAt
     * @return CustomerOrder
     */
    public function setInstallationAt(\DateTime $installationAt): CustomerOrder
    {
        $this->installationAt = $installationAt;
        return $this;
    }

    /**
     * @return bool
     */
    public function isInstalled()
    {
        return $this->installed;
    }

    /**
     * @param bool $installed
     * @return CustomerOrder
     */
    public function setInstalled(bool $installed): CustomerOrder
    {
        $this->installed = $installed;
        return $this;
    }
}

