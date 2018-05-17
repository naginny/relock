<?php
/**
 * Created by PhpStorm.
 * User: Stella
 * Date: 5/7/2018
 * Time: 2:30 PM
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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Validator\Constraints;

class NewOrdersController extends Controller
{
    /**
     * @Route("/admin/new-orders", name="admin_new_orders")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $orders = $em->getRepository('AppBundle:CustomerOrder')->findBy(
            [
                'status' => $em->getRepository('AppBundle:CustomerOrderStatus')->findOneBy(['code' => 'new'])
            ]
        );
        return $this->render('admin/new-orders/list.html.twig', [
            'title' => 'New orders',
            'current' => 'newOrders',
            'orders' => $orders
        ]);
    }


    /**
     * @Route("/admin/reject/{orderId}", name="admin_reject")
     * @ParamConverter("order", class="AppBundle\Entity\CustomerOrder", options={"id" = "orderId"})
     * @param CustomerOrder $order
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function rejectAction(CustomerOrder $order, Request $request)
    {
        /** @var FormInterface $form */
        $form = $this->createFormBuilder()
            ->add('rejectReason', TextareaType::class, [
                'attr' => array('style' => "height: 200px;", 'placeholder' => 'Enter the reason to reject this order.'),
                'required' => true,
                'constraints' => [
                    new Constraints\Length(['min' => 20])
                ]
            ])
            ->add('submit', SubmitType::class)
            ->getForm()
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $em = $this->get('doctrine.orm.entity_manager');
            $order->setStatus(
                $em->getRepository('AppBundle:CustomerOrderStatus')->findOneBy(['code' => 'rejected'])
            );
            $order->setRejectedAt(new \DateTime());
            $order->setRejectedReason($data['rejectReason']);

            $em->persist($order);
            $em->flush();

            $this->get('process')->log(
                $order->getCustomer(),
                'order rejected',
                'order id: ' . $order->getId(),
                $this->getUser()
            );

            $this->addFlash('notice', 'Order successfully rejected');

            return $this->redirectToRoute('admin_new_orders');
        }

        // replace this example code with whatever you need
        return $this->render('admin/form.html.twig', array(
            'form' => $form->createView(),
            'title' => 'Rejection form'
        ));
    }

    /**
     * @Route("/admin/proceed/{orderId}", name="admin_new_order_proceed")
     * @ParamConverter("order", class="AppBundle\Entity\CustomerOrder", options={"id" = "orderId"})
     * @param CustomerOrder $order
     * @param Request $request
     */
    public function proceedAction(CustomerOrder $order, Request $request)
    {
        if ($order->getProducts()->count()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $order->setStatus($em->getRepository('AppBundle:CustomerOrderStatus')->findOneBy(['code' => 'awaitingMeasurements']));
            $em->persist($order);
            $em->flush();
            $this->addFlash('notice', 'Order placed into Awaiting Measurements');

            $this->get('process')->log(
                $order->getCustomer(),
                'order changed status to awaitingMeasurements',
                'order id: ' . $order->getId(),
                $this->getUser()
            );
        } else {
            $this->addFlash('warning', 'There should be at least one product to proceed');
        }

        return $this->redirectToRoute('admin_new_orders');
    }

    /**
     * @Route("/admin/products/{orderId}", name="admin_new_order_products")
     * @ParamConverter("order", class="AppBundle\Entity\CustomerOrder", options={"id" = "orderId"})
     * @param CustomerOrder $order
     * @param Request $request
     * @return Response
     */
    public function productsAction(CustomerOrder $order, Request $request)
    {
        /** @var Product $products */
        $products = $order->getProducts();
        return $this->render('admin/products/list.html.twig', [
            'title' => 'Products',
            'current' => 'newOrders',
            'products' => $products,
            'order' => $order
        ]);
    }

    /**
     * @Route("/admin/products/add/{orderId}", name="admin_new_order_products_add")
     * @ParamConverter("order", class="AppBundle\Entity\CustomerOrder", options={"id" = "orderId"})
     * @param CustomerOrder $order
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function productsAddAction(CustomerOrder $order, Request $request)
    {
        return $this->productProcessForm(
            new Product(), $order, $request,
            'Product successfully added!', 'Create new product'
        );
    }

    /**
     * @Route("/admin/products/edit/{orderId}/{productId}", name="admin_new_order_products_edit")
     * @ParamConverter("order", class="AppBundle\Entity\CustomerOrder", options={"id" = "orderId"})
     * @ParamConverter("product", class="AppBundle\Entity\Product", options={"id" = "productId", "orderId" = "orderId"})
     * @param CustomerOrder $order
     * @param Product $product
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function productsEditAction(CustomerOrder $order, Product $product, Request $request)
    {
        return $this->productProcessForm(
            $product, $order, $request,
            'Product successfully edited!', 'Change the product'
        );
    }

    /**
     * @param Product $product
     * @param CustomerOrder $order
     * @param Request $request
     * @param string $success
     * @param string $title
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    private function productProcessForm(Product $product, CustomerOrder $order, Request $request, string $success, string $title)
    {
        /** @var FormInterface $form */
        $form = $this->createFormBuilder($product)
            ->add('type', EntityType::class, array(
                // looks for choices from this entity
                'class' => ProductType::class,

                // uses the User.username property as the visible option string
                'choice_label' => 'title',
            ))
            ->add('address', EntityType::class, array(
                // looks for choices from this entity
                'class' => CustomerAddress::class,

                // uses the User.username property as the visible option string
                'choices' => $order->getCustomer()->getAddresses(),
            ))
            ->add('measurementAt', DateType::class, array(
                'data' => (new \DateTime())->add(new \DateInterval('P1W')),
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
            /** @var Product $product */
            $product = $form->getData();
            $product->setOrder($order);
            $em->persist($product);
            $em->flush();

            $this->get('process')->log(
                $order->getCustomer(),
                'order product changed',
                'order id: ' . $order->getId() . ', product id: ' . $product->getId(),
                $this->getUser()
            );

            $this->addFlash('notice', $success);

            return $this->redirectToRoute('admin_new_order_products', ['orderId' => $order->getId()]);
        }

        // replace this example code with whatever you need
        return $this->render('admin/form.html.twig', array(
            'form' => $form->createView(),
            'title' => $title
        ));
    }


    /**
     * @Route("/admin/addresses/{orderId}", name="admin_new_order_addresses")
     * @ParamConverter("order", class="AppBundle\Entity\CustomerOrder", options={"id" = "orderId"})
     * @param CustomerOrder $order
     * @param Request $request
     * @return Response
     */
    public function addressesAction(CustomerOrder $order, Request $request)
    {
        /** @var CustomerAddress[] $addresses */
        $addresses = $order->getCustomer()->getAddresses();
        return $this->render('admin/addresses/list.html.twig', [
            'title' => 'Addresses',
            'current' => 'newOrders',
            'addresses' => $addresses,
            'order' => $order
        ]);
    }

    /**
     * @Route("/admin/addresses/add/{orderId}", name="admin_new_order_addresses_add")
     * @ParamConverter("order", class="AppBundle\Entity\CustomerOrder", options={"id" = "orderId"})
     * @param CustomerOrder $order
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function addressesAddAction(CustomerOrder $order, Request $request)
    {
        return $this->addressProcessForm(
            new CustomerAddress(), $order, $request,
            'Address successfully added!', 'Create new address'
        );
    }

    /**
     * @Route("/admin/addresses/edit/{orderId}/{customerId}/{addressId}", name="admin_new_order_addresses_edit")
     * @ParamConverter("order", class="AppBundle\Entity\CustomerOrder", options={"id" = "orderId"})
     * @ParamConverter("address", class="AppBundle\Entity\CustomerAddress", options={"id" = "addressId", "customerId" = "customerId"})
     * @param CustomerOrder $order
     * @param CustomerAddress $address
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function addressesEditAction(CustomerOrder $order, CustomerAddress $address, Request $request)
    {
        return $this->addressProcessForm(
            $address, $order, $request,
            'Address successfully edited!', 'Change new address'
        );
    }


    /**
     * @param CustomerAddress $address
     * @param CustomerOrder $order
     * @param Request $request
     * @param string $success
     * @param string $title
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    private function addressProcessForm(CustomerAddress $address, CustomerOrder $order, Request $request, string $success, string $title)
    {
        $address->setCustomer($order->getCustomer());

        /** @var FormInterface $form */
        $form = $this->createFormBuilder($address)
            ->add('city', ChoiceType::class, array(
                'choices'  => array(
                    'Rīga' => 'Rīga',
					'Ulbroka' => 'Ulbroka',
					'Mārupe' => 'Mārupe',
					'Baloži' => 'Baloži',
                    'Rēzekne' => 'Rēzekne',
                    'Ogre' => 'Ogre',
                    'Daugavpils' => 'Daugavpils',
                    'Cits' => 'Cits'
                ),
            ))
            ->add('streetAddress', TextType::class, [
                'constraints' => [
                    new Constraints\Length(['max' => 255])
                ]
            ])
            ->add('floor', IntegerType::class, [
                'required' => false,
                'constraints' => [
                    new Constraints\LessThanOrEqual(50)
                ]
            ])
            ->add('notes', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length(['max' => 5000])
                ]
            ])
            ->add('submit', SubmitType::class)
            ->getForm()
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            /** @var CustomerAddress $address */
            $address = $form->getData();
            $address->setCustomer($order->getCustomer());
            $em->persist($address);
            $em->flush();

            $this->addFlash('notice', $success);

            $this->get('process')->log(
                $order->getCustomer(),
                'customer address changed',
                'order id: ' . $order->getId() . ', product id: ' . $address->getId(),
                $this->getUser()
            );

            return $this->redirectToRoute('admin_new_order_addresses', ['orderId' => $order->getId()]);
        }

        // replace this example code with whatever you need
        return $this->render('admin/form.html.twig', array(
            'form' => $form->createView(),
            'title' => $title
        ));
    }
}