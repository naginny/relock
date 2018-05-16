<?php

namespace AppBundle\Command;

use AppBundle\Entity\Customer;
use AppBundle\Entity\CustomerOrder;
use AppBundle\Repository\CustomerOrderRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearOldClientsPersonalInfoCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:clear_old_clients_personal_info_command')
            ->setDescription('clears old clients personal information');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $stmt = $em->getConnection()->prepare("
            SELECT c.id
            FROM customer AS c
            WHERE
            (
              c.anonymized IS NULL 
              OR c.anonymized = 0
            )
            AND
            (
              SELECT 
                COUNT(co.id)
              FROM customer_order as co
              WHERE co.customer_id = c.id 
              AND co.status_id NOT IN (SELECT status_id FROM customer_order_status WHERE code IN ( 'closed', 'rejected' ))
            ) = 0
            AND
            (SELECT
              IFNULL(co2.closed_at, co2.rejected_at)
              FROM customer_order as co2
              WHERE co2.customer_id = c.id
            ) < :threshold
        ");
        $stmt->execute([
            'threshold' => (new \DateTime())->sub(new \DateInterval('P2Y'))->format('Y-m-d H:i:s')
        ]);
        $customers = $stmt->fetchAll() ?: [];
        $customerIds = array_column($customers, 'id');

        $repo = $em->getRepository('AppBundle:Customer');
        $process = $this->getContainer()->get('process');

        foreach($customerIds as $customerId) {
            $customer = $repo->find($customerId);
            $repo->anonymize($customer);
            $process->log($customer, 'customer anonymized data automatically');
        }

        $output->writeln('customers processed: ' . count($customers));
    }
}
