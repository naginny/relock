<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 */
class Product
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
     * @ORM\Column(name="measurement_at", type="datetime", nullable=true)
     */
    private $measurementAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="installation_at", type="datetime", nullable=true)
     */
    private $installationAt;

    /**
     * @var string
     *
     * @ORM\Column(name="material", type="string", length=255, nullable=true)
     */
    private $material;

    /**
     * @var string
     *
     * @ORM\Column(name="manufacturer", type="string", length=255, nullable=true)
     */
    private $manufacturer;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer", nullable=true)
     */
    private $price;

    /**
     * @var int
     *
     * @ORM\Column(name="markup", type="integer", nullable=true)
     */
    private $markup;

    /**
     * @var int
     *
     * @ORM\Column(name="installation_price", type="integer", nullable=true)
     */
    private $installationPrice;

    /**
     * @var int
     *
     * @ORM\Column(name="delivery_price", type="integer", nullable=true)
     */
    private $deliveryPrice;

    /**
     * @var int
     *
     * @ORM\Column(name="additional_markup", type="integer", nullable=true)
     */
    private $additionalMarkup;

    /**
     * @var int
     *
     * @ORM\Column(name="width", type="integer", nullable=true)
     */
    private $width;

    /**
     * @var int
     *
     * @ORM\Column(name="height", type="integer", nullable=true)
     */
    private $height;

    /**
     * @var string
     *
     * @ORM\Column(name="vendor_code", type="string", length=255, nullable=true)
     */
    private $vendorCode;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes;

    /**
     * @var string
     *
     * @ORM\Column(name="sketch", type="string", nullable=true)
     */
    private $sketch;

    /**
     * @var CustomerOrder
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CustomerOrder", inversedBy="products")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @var ProductType
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ProductType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    private $type;

    /**
     * @var CustomerAddress
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CustomerAddress")
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     */
    private $address;


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
     * Set measurementAt
     *
     * @param \DateTime $measurementAt
     *
     * @return Product
     */
    public function setMeasurementAt($measurementAt)
    {
        $this->measurementAt = $measurementAt;

        return $this;
    }

    /**
     * Get measurementAt
     *
     * @return \DateTime
     */
    public function getMeasurementAt()
    {
        return $this->measurementAt;
    }

    /**
     * Set installationAt
     *
     * @param \DateTime $installationAt
     *
     * @return Product
     */
    public function setInstallationAt($installationAt)
    {
        $this->installationAt = $installationAt;

        return $this;
    }

    /**
     * Get installationAt
     *
     * @return \DateTime
     */
    public function getInstallationAt()
    {
        return $this->installationAt;
    }

    /**
     * Set material
     *
     * @param string $material
     *
     * @return Product
     */
    public function setMaterial($material)
    {
        $this->material = $material;

        return $this;
    }

    /**
     * Get material
     *
     * @return string
     */
    public function getMaterial()
    {
        return $this->material;
    }

    /**
     * Set manufacturer
     *
     * @param string $manufacturer
     *
     * @return Product
     */
    public function setManufacturer($manufacturer)
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    /**
     * Get manufacturer
     *
     * @return string
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * Set price
     *
     * @param integer $price
     *
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set vendorCode
     *
     * @param string $vendorCode
     *
     * @return Product
     */
    public function setVendorCode($vendorCode)
    {
        $this->vendorCode = $vendorCode;

        return $this;
    }

    /**
     * Get vendorCode
     *
     * @return string
     */
    public function getVendorCode()
    {
        return $this->vendorCode;
    }

    /**
     * Set notes
     *
     * @param string $notes
     *
     * @return Product
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @return string
     */
    public function getSketch()
    {
        return $this->sketch;
    }

    /**
     * @param string $sketch
     * @return Product
     */
    public function setSketch($sketch)
    {
        $this->sketch = $sketch;

        return $this;
    }

    /**
     * @return CustomerOrder
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param CustomerOrder $order
     * @return Product
     */
    public function setOrder(CustomerOrder $order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return ProductType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param ProductType $type
     * @return Product
     */
    public function setType(ProductType $type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return CustomerAddress
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param CustomerAddress $address
     * @return Product
     */
    public function setAddress(CustomerAddress $address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth(int $width)
    {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight(int $height)
    {
        $this->height = $height;
    }

    /**
     * @return bool
     */
    public function getMeasurementsSet()
    {
        return $this->getWidth() && $this->getHeight();
    }

    /**
     * @return int
     */
    public function getMarkup()
    {
        return $this->markup;
    }

    /**
     * @param int $markup
     * @return Product
     */
    public function setMarkup(int $markup): Product
    {
        $this->markup = $markup;
        return $this;
    }

    /**
     * @return int
     */
    public function getInstallationPrice()
    {
        return $this->installationPrice;
    }

    /**
     * @param int $installationPrice
     * @return Product
     */
    public function setInstallationPrice(int $installationPrice): Product
    {
        $this->installationPrice = $installationPrice;
        return $this;
    }

    /**
     * @return int
     */
    public function getDeliveryPrice()
    {
        return $this->deliveryPrice;
    }

    /**
     * @param int $deliveryPrice
     * @return Product
     */
    public function setDeliveryPrice(int $deliveryPrice): Product
    {
        $this->deliveryPrice = $deliveryPrice;
        return $this;
    }

    /**
     * @return int
     */
    public function getAdditionalMarkup()
    {
        return $this->additionalMarkup;
    }

    /**
     * @param int $additionalMarkup
     * @return Product
     */
    public function setAdditionalMarkup(int $additionalMarkup): Product
    {
        $this->additionalMarkup = $additionalMarkup;
        return $this;
    }

    /**
     * @return float
     */
    public function getTax()
    {
        return .21;
    }

    /**
     * @return float|int
     */
    public function getTotalPrice()
    {
        return round(
            $this->getTotalNetPrice() * (1 + $this->getTax()),
            2
        )
        ;
    }

    /**
     * @return float|int
     */
    public function getTotalNetPrice()
    {
        return round(
            array_sum(
                [
                    $this->getPrice(),
                    $this->getDeliveryPrice(),
                    $this->getInstallationPrice(),
                    $this->getAdditionalMarkup(),
                    $this->getMarkup()
                ]
            ),
            2
        )
        ;
    }

    /**
     * @return float|int
     */
    public function getPrepaymentAmount()
    {
        return round(
            $this->getTotalPrice() / 2,
            2
        );
    }
}

