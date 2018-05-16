<?php
/**
 * Created by PhpStorm.
 * User: Stella
 * Date: 5/7/2018
 * Time: 1:14 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\CustomerAddress;
use AppBundle\Entity\CustomerOrder;
use AppBundle\Entity\CustomerOrderStatus;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductType;
use AppBundle\Repository\CustomerOrderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Validator\Constraints;

class PrepaymentAdminController extends Controller
{
    /**
     * @Route("/admin/awaiting-prepayment", name="admin_awaiting_prepayment")
     * @param Request $request
     * @return Response
     */
    public function awaitingAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $orders = $em->getRepository('AppBundle:CustomerOrder')->findBy(
            [
                'status' => $em->getRepository('AppBundle:CustomerOrderStatus')->findOneBy(['code' => 'awaitingPrepayment'])
            ]
        );
        return $this->render('admin/awaiting-prepayment/list.html.twig', [
            'title' => 'Awaiting prepayment',
            'current' => 'awaitingPrepayment',
            'orders' => $orders
        ]);
    }

    /**
     * @Route("/admin/awaiting-prepayment/proceed/{orderId}", name="admin_awaiting_prepayment_proceed")
     * @ParamConverter("order", class="AppBundle\Entity\CustomerOrder", options={"id" = "orderId"})
     * @param CustomerOrder $order
     * @param Request $request
     */
    public function proceedAction(CustomerOrder $order, Request $request)
    {
        if ($order->isInvoiceSent() === true) {
            $em = $this->get('doctrine.orm.entity_manager');
            $order->setStatus($em->getRepository('AppBundle:CustomerOrderStatus')->findOneBy(['code' => 'awaitingDelivery']));
            $order->setPrepaymentReceived(true);
            $em->persist($order);
            $em->flush();
            $this->addFlash('notice', 'Order placed into Awaiting Delivery');
            $this->get('process')->log(
                $order->getCustomer(),
                'order changed status to awaitingDelivery',
                'order id: ' . $order->getId(),
                $this->getUser()
            );
        } else {
            $this->addFlash('warning', 'The invoice should be sent');
        }

        return $this->redirectToRoute('admin_awaiting_prepayment');
    }
}