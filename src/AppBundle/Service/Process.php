<?php
/**
 * Created by PhpStorm.
 * User: Stella
 * Date: 5/7/2018
 * Time: 7:16 PM
 */

namespace AppBundle\Service;


use AppBundle\Entity\Customer;
use AppBundle\Entity\Employee;
use AppBundle\Entity\ProcessLog;
use Doctrine\ORM\EntityManager;

class Process
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Customer $customer
     * @param string $actionName
     * @param string $message
     * @param Employee|null $employee
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function log(Customer $customer, string $actionName, string $message = '', Employee $employee = null)
    {
        $log = new ProcessLog();
        $log->setCustomer($customer);
        $log->setAction($actionName);
        $message !== null && $log->setMessage($message);
        $employee !== null && $log->setEmployee($employee);
        $log->setCreatedAt(new \DateTime());
        $customer->getLogs()->add($log);
        $this->em->persist($log);
        $this->em->persist($customer);
        $this->em->flush();
    }
}