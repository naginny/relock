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

class InstallationAdminController extends Controller
{
    /**
     * @Route("/admin/awaiting-installation", name="admin_awaiting_installation")
     * @param Request $request
     * @return Response
     */
    public function awaitingAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $orders = $em->getRepository('AppBundle:CustomerOrder')->findBy(
            [
                'status' => $em->getRepository('AppBundle:CustomerOrderStatus')->findOneBy(['code' => 'awaitingInstallation'])
            ]
        );
        return $this->render('admin/awaiting-installation/list.html.twig', [
            'title' => 'Awaiting installation',
            'current' => 'awaitingInstallation',
            'orders' => $orders
        ]);
    }
    /**
     * @Route("/admin/awaiting-installation/edit/{orderId}", name="admin_awaiting_installation_edit")
     * @ParamConverter("order", class="AppBundle\Entity\CustomerOrder", options={"id" = "orderId"})
     * @param CustomerOrder $order
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function installationEditAction(CustomerOrder $order,Request $request)
    {
        return $this->installationProcessForm(
            $order, $request,
            'Order successfully edited!', 'Set installation date'
        );
    }

    /**
     * @param CustomerOrder $order
     * @param Request $request
     * @param string $success
     * @param string $title
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    private function installationProcessForm(CustomerOrder $order, Request $request, string $success, string $title)
    {
        /** @var FormInterface $form */
        $form = $this->createFormBuilder($order)
            ->add('installationAt', DateType::class, array(
                'data' => (new \DateTime()),
                'attr' => array('class' => 'js-datepicker'),
                'widget' => 'single_text',
                'html5' => false,
            ))
            ->add('submit', SubmitType::class)
            ->getForm()
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            /** @var CustomerOrder $order */
            $em->persist($form->getData());
            $em->flush();

            $this->addFlash('notice', $success);
            $this->get('process')->log(
                $order->getCustomer(),
                'order product installation changed',
                'order id: ' . $order->getId(),
                $this->getUser()
            );

            return $this->redirectToRoute('admin_awaiting_installation');
        }

        // replace this example code with whatever you need
        return $this->render('admin/form.html.twig', array(
            'form' => $form->createView(),
            'title' => $title
        ));
    }

    /**
     * @Route("/admin/awaiting-installation/proceed/{orderId}", name="admin_awaiting_installation_proceed")
     * @ParamConverter("order", class="AppBundle\Entity\CustomerOrder", options={"id" = "orderId"})
     * @param CustomerOrder $order
     * @param Request $request
     */
    public function proceedAction(CustomerOrder $order, Request $request)
    {
        if ($order->getInstallationAt() !== null) {
            $em = $this->get('doctrine.orm.entity_manager');
            $order->setStatus($em->getRepository('AppBundle:CustomerOrderStatus')->findOneBy(['code' => 'closed']));
            $order->setInstalled(true);
            $order->setClosedAt(new \DateTime());
            $em->persist($order);
            $em->flush();
            $this->addFlash('notice', 'Order placed into Closed');
            $this->get('process')->log(
                $order->getCustomer(),
                'order changed status to closed',
                'order id: ' . $order->getId(),
                $this->getUser()
            );
        } else {
            $this->addFlash('warning', 'There should be installation date set');
        }

        return $this->redirectToRoute('admin_awaiting_installation');
    }
}