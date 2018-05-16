<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomerAddress
 *
 * @ORM\Table(name="customer_address")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CustomerAddressRepository")
 */
class CustomerAddress
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
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="street_address", type="string", length=255, nullable=true)
     */
    private $streetAddress;

    /**
     * @var int
     *
     * @ORM\Column(name="floor", type="integer", nullable=true)
     */
    private $floor;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes;

    /**
     * @var Customer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer", inversedBy="addresses")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    private $customer;

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
     * Set city
     *
     * @param string $city
     *
     * @return CustomerAddress
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        if ($this->getCustomer()->getPermissionToUsePersonalInfo() === false) {
            return '***';
        }
        return $this->city;
    }

    /**
     * @return string
     */
    public function getStreetAddress()
    {
        if ($this->getCustomer()->getPermissionToUsePersonalInfo() === false) {
            return '***';
        }
        return $this->streetAddress;
    }

    /**
     * @param string $streetAddress
     * @return CustomerAddress
     */
    public function setStreetAddress($streetAddress)
    {
        $this->streetAddress = $streetAddress;
        return $this;
    }

    /**
     * Set floor
     *
     * @param integer $floor
     *
     * @return CustomerAddress
     */
    public function setFloor($floor)
    {
        $this->floor = $floor;

        return $this;
    }

    /**
     * Get floor
     *
     * @return int
     */
    public function getFloor()
    {
        if ($this->getCustomer()->getPermissionToUsePersonalInfo() === false) {
            return '***';
        }
        return $this->floor;
    }

    /**
     * Set notes
     *
     * @param string $notes
     *
     * @return CustomerAddress
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
        if ($this->getCustomer()->getPermissionToUsePersonalInfo() === false) {
            return '***';
        }
        return $this->notes;
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
     * @return CustomerAddress
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return
            $this->getCity() . ',' .
            $this->getStreetAddress() . ',' .
            ($this->getFloor() ? ' floor ' . $this->getFloor() : '')
        ;
    }
}

