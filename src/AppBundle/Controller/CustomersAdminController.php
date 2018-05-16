<?php
/**
 * Created by PhpStorm.
 * User: Stella
 * Date: 5/7/2018
 * Time: 1:14 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Customer;
use AppBundle\Entity\CustomerAddress;
use AppBundle\Entity\CustomerOrder;
use AppBundle\Entity\CustomerOrderStatus;
use AppBundle\Entity\ProcessLog;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductType;
use AppBundle\Repository\CustomerOrderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Validator\Constraints;

class CustomersAdminController extends Controller
{
    /**
     * @Route("/admin/customers", name="admin_customers")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function indexAction(Request $request)
    {
        // making a list of clients created at a specific period
        $em = $this->get('doctrine.orm.entity_manager');
        /** @var Customer[] $customers */
        $customers = $em->getRepository('AppBundle:Customer')->createQueryBuilder('c')
            ->where('c.createdAt >= :from')
            ->andWhere('c.createdAt <= :to')
            ->setParameter('from', $request->get('from', date('Y-m-d')))
            ->setParameter('to', $request->get('to', date('Y-m-d')))
            ->getQuery()
            ->getResult();

        return $this->render('admin/customers/list.html.twig', array(
            'title' => 'Customers',
            'current' => 'customers',
            'customers' => $customers,
            'from' => $request->get('from', date('Y-m-d')),
            'to' => $request->get('to', date('Y-m-d')),
        ));
    }

    /**
     * @Route("/admin/customers/view/{customerId}", name="admin_customers_view")
     * @ParamConverter("customer", class="AppBundle\Entity\Customer", options={"id" = "customerId"})
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function viewAction(Customer $customer)
    {
        return $this->render('admin/customers/view.html.twig', [
            'title' => 'Customer ' . $customer->getName() . ' ' . $customer->getSurname(),
            'current' => 'customers',
            'customer' => $customer,
        ]);
    }

    /**
     * @Route("/admin/customers/download/{customerId}", name="admin_customers_download")
     * @ParamConverter("customer", class="AppBundle\Entity\Customer", options={"id" = "customerId"})
     * @param Customer $customer
     */
    public function downloadAction(Customer $customer)
    {
        $filename = tempnam('/tmp', 'customer_download');
        $csv = fopen($filename, 'w+');
        fputcsv($csv, ['Customer information']);
        fputcsv($csv, [
            'id',
            'Created at',
            'Name',
            'Surname',
            'Email',
            'Phone',
            'Permission To Use PersonalInfo',
            'Permission Received At',
            'Permission Denied At',
        ]);

        fputcsv($csv, [
            $customer->getId(),
            $customer->getCreatedAt()->format('Y-m-d H:i:s'),
            $customer->getName(),
            $customer->getSurname(),
            $customer->getEmail(),
            $customer->getPhone(),
            $customer->getPermissionToUsePersonalInfo() ? 'yes' : 'no',
            $customer->getPermissionReceivedAt()->format('Y-m-d H:i:s'),
            $customer->getPermissionDeniedAt() ? $customer->getPermissionDeniedAt()->format('Y-m-d H:i:s') : '',
        ]);

        fputcsv($csv, ['Customer addresses']);
        fputcsv($csv, [
            'id',
            'City',
            'Street',
            'Floor',
            'Notes',
        ]);

        /** @var CustomerAddress $address */
        foreach($customer->getAddresses() as $address) {
            fputcsv($csv, [
                $address->getId(),
                $address->getCity(),
                $address->getStreetAddress(),
                $address->getFloor(),
                $address->getNotes()
            ]);
        }

        fputcsv($csv, ['Customer orders']);
        fputcsv($csv, [
            'Id',
            'Created At',
            'Closed At',
            'Requested Measurement At',
            'Requested Installation At',
            'Requested Door Amount',
            'Requested Window Amount',
            'Requested Jalousie Amount',
            'Requested Gate Amount',
            'Status',
            'ProductsAmount',
            'Address',
            'Rejected At',
            'RejectedReason',
            'Notes',
            'Measurements Set',
            'Prepayment Amount',
            'Total Price',
            'InvoiceId',
            'Delivery At',
            'Installation At',
        ]);

        /** @var CustomerOrder $order */
        foreach($customer->getOrders() as $order) {
            fputcsv($csv, [
                $order->getId(),
                $order->getCreatedAt()? $order->getCreatedAt()->format('Y-m-d H:i:s') : '---',
                $order->getClosedAt()? $order->getClosedAt()->format('Y-m-d H:i:s') : '---',
                $order->getRequestedMeasurementAt()? $order->getRequestedMeasurementAt()->format('Y-m-d H:i:s') : '---',
                $order->getRequestedInstallationAt()? $order->getRequestedInstallationAt()->format('Y-m-d H:i:s') : '---',
                $order->getRequestedDoorAmount(),
                $order->getRequestedWindowAmount(),
                $order->getRequestedJalousieAmount(),
                $order->getRequestedGateAmount(),
                $order->getStatus()->getTitle(),
                $order->getProductsAmount(),
                $order->getAddress(),
                $order->getRejectedAt() ? $order->getRejectedAt()->format('Y-m-d H:i:s') : '---',
                $order->getRejectedReason(),
                $order->getNotes(),
                $order->getMeasurementsSet()? 'yes' : 'no',
                $order->getPrepaymentAmount(),
                $order->getTotalPrice(),
                $order->getInvoiceId(),
                $order->getDeliveryAt()? $order->getDeliveryAt()->format('Y-m-d H:i:s') : '---',
                $order->getInstallationAt()? $order->getInstallationAt()->format('Y-m-d H:i:s') : '---',
            ]);
        }

        fputcsv($csv, ['Customer products']);
        fputcsv($csv, [
            'Id',
            'Measurement',
            'Installation',
            'Material',
            'Manufacturer',
            'Price',
            'VendorCode',
            'Notes',
            'Sketch',
            'Type',
            'Address',
            'Width',
            'Height',
            'MeasurementsSet',
            'TotalPrice',
            'PrepaymentAmount',
        ]);

        /** @var Product $product */
        foreach($customer->getProducts() as $product) {
            fputcsv($csv, [
                $product->getId(),
                $product->getMeasurementAt() ? $product->getMeasurementAt()->format('Y-m-d H:i:s') : '---',
                $product->getInstallationAt() ? $product->getInstallationAt()->format('Y-m-d H:i:s') : '---',
                $product->getMaterial(),
                $product->getManufacturer(),
                $product->getPrice(),
                $product->getVendorCode(),
                $product->getNotes(),
                $product->getSketch(),
                $product->getType(),
                $product->getAddress(),
                $product->getWidth(),
                $product->getHeight(),
                $product->getMeasurementsSet()? 'yes' : 'no',
                $product->getTotalPrice(),
                $product->getPrepaymentAmount(),
            ]);
        }

        fclose($csv);

        $response = new Response();
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-Type', 'application/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="customer-information.csv";');
        $response->headers->set('Content-length', strlen($response->getContent()));
        $response->setContent(file_get_contents($filename));

        // clear up
        unlink($filename);

        // return response
        return $response;
    }

    /**
     * @Route("/admin/customers/revoke/{customerId}", name="admin_customers_revoke")
     * @ParamConverter("customer", class="AppBundle\Entity\Customer", options={"id" = "customerId"})
     * @param Customer $customer
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function revokeAction(Customer $customer, Request $request)
    {
        $customer->setPermissionToUsePersonalInfo(false);
        $customer->setPermissionDeniedAt(new \DateTime());
        $em = $this->get('doctrine.orm.entity_manager');
        $em->persist($customer);
        $em->flush();
        $this->get('process')->log($customer, 'customer revoked permissions', $this->getUser());

        $this->addFlash('notice', 'revoke successful');

        return $this->redirect(
            $request
                ->headers
                ->get('referer')
        );
    }

    /**
     * @Route("/admin/customers/unrevoke/{customerId}", name="admin_customers_unrevoke")
     * @ParamConverter("customer", class="AppBundle\Entity\Customer", options={"id" = "customerId"})
     * @param Customer $customer
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function unrevokeAction(Customer $customer, Request $request)
    {
        $customer->setPermissionToUsePersonalInfo(true);
        $customer->setPermissionReceivedAt(new \DateTime());
        $em = $this->get('doctrine.orm.entity_manager');
        $em->persist($customer);
        $em->flush();
        $this->get('process')->log($customer, 'customer un-revoked permissions', $this->getUser());

        $this->addFlash('notice', 'un-revoke successful');

        return $this->redirect(
            $request
                ->headers
                ->get('referer')
        );
    }

    /**
     * @Route("/admin/customers/anonymize/{customerId}", name="admin_customers_anonymize")
     * @ParamConverter("customer", class="AppBundle\Entity\Customer", options={"id" = "customerId"})
     * @param Customer $customer
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function anonymizeAction(Customer $customer, Request $request)
    {
        $repo = $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:Customer');
        $repo->anonymize($customer);

        $this->get('process')->log($customer, 'customer anonymized data', '', $this->getUser());

        $this->addFlash('notice', 'anonymize successful');

        return $this->redirect(
            $request
                ->headers
                ->get('referer')
        );
    }

}