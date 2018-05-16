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

class PricingAdminController extends Controller
{
    /**
     * @Route("/admin/awaiting-pricing", name="admin_awaiting_pricing")
     * @param Request $request
     * @return Response
     */
    public function awaitingMeasurementsAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $orders = $em->getRepository('AppBundle:CustomerOrder')->findBy(
            [
                'status' => $em->getRepository('AppBundle:CustomerOrderStatus')->findOneBy(['code' => 'awaitingPricing'])
            ]
        );
        return $this->render('admin/awaiting-pricing/list.html.twig', [
            'title' => 'Awaiting pricing',
            'current' => 'awaitingPricing',
            'orders' => $orders
        ]);
    }

    /**
     * @Route("/admin/pricing-products/{orderId}", name="admin_awaiting_pricing_products")
     * @ParamConverter("order", class="AppBundle\Entity\CustomerOrder", options={"id" = "orderId"})
     * @param CustomerOrder $order
     * @param Request $request
     * @return Response
     */
    public function productsAction(CustomerOrder $order, Request $request)
    {
        /** @var Product[] $products */
        $products = $order->getProducts();
        return $this->render('admin/pricing-products/list.html.twig', [
            'title' => 'Products',
            'current' => 'awaitingPricing',
            'products' => $products,
            'order' => $order
        ]);
    }

    /**
     * @Route("/admin/pricing-products/edit/{orderId}/{productId}", name="admin_awaiting_pricing_products_edit")
     * @ParamConverter("order", class="AppBundle\Entity\CustomerOrder", options={"id" = "orderId"})
     * @ParamConverter("product", class="AppBundle\Entity\Product", options={"id" = "productId", "orderId" = "orderId"})
     * @param CustomerOrder $order
     * @param Product $product
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
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
            ->add('price')
            ->add('markup')
            ->add('installationPrice')
            ->add('deliveryPrice')
            ->add('additionalMarkup')
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

            $this->addFlash('notice', $success);
            $this->get('process')->log(
                $order->getCustomer(),
                'order product pricing changed',
                'order id: ' . $order->getId() . ', product id: ' . $product->getId(),
                $this->getUser()
            );

            return $this->redirectToRoute('admin_awaiting_pricing_products', ['orderId' => $order->getId()]);
        }

        // replace this example code with whatever you need
        return $this->render('admin/form.html.twig', array(
            'form' => $form->createView(),
            'title' => $title,
            'current' => 'awaitingPricing'
        ));
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }

    /**
     * @Route("/admin/pricing-products/proceed/{orderId}", name="admin_awaiting_pricing_proceed")
     * @ParamConverter("order", class="AppBundle\Entity\CustomerOrder", options={"id" = "orderId"})
     * @param CustomerOrder $order
     * @param Request $request
     */
    public function proceedAction(CustomerOrder $order, Request $request)
    {
        if ($order->isInvoiceSent() === true) {
            $em = $this->get('doctrine.orm.entity_manager');
            $order->setStatus($em->getRepository('AppBundle:CustomerOrderStatus')->findOneBy(['code' => 'awaitingPrepayment']));
            $em->persist($order);
            $em->flush();
            $this->addFlash('notice', 'Order placed into Awaiting Prepayment');
            $this->get('process')->log(
                $order->getCustomer(),
                'order changed status to awaitingPrepayment',
                'order id: ' . $order->getId(),
                $this->getUser()
            );
        } else {
            $this->addFlash('warning', 'The invoice should be sent');
        }

        return $this->redirectToRoute('admin_awaiting_pricing');
    }


    /**
     * @Route("/admin/pricing-products/invoice-send/{orderId}", name="admin_awaiting_pricing_invoice_send")
     * @ParamConverter("order", class="AppBundle\Entity\CustomerOrder", options={"id" = "orderId"})
     * @param CustomerOrder $order
     * @param Request $request
     */
    public function invoiceSentAction(CustomerOrder $order, Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $order->setInvoiceSent(true);
        $em->persist($order);
        $em->flush();
        $this->addFlash('notice', 'Invoice sent');
        $this->get('process')->log(
            $order->getCustomer(),
            'order invoice sent',
            'order id: ' . $order->getId(),
            $this->getUser()
        );

        return $this->redirectToRoute('admin_awaiting_pricing');
    }

    /**
     * @Route("/invoice/{invoiceId}", name="admin_awaiting_pricing_invoice_view")
     * @param Request $request
     * @return Response
     */
    public function invoiceAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        /** @var CustomerOrder $order */
        $order = $em->getRepository('AppBundle:CustomerOrder')->findOneBy(['invoiceId' => $request->get('invoiceId')]);
        /** @var Product[] $products */
        $products = $order->getProducts();
        $this->get('process')->log(
            $order->getCustomer(),
            'order invoice viewed',
            'order id: ' . $order->getId(),
            $this->getUser() ?: null
        );

        return $this->render('admin/invoice.html.twig', array(
            'title' => 'Invoice # ' . $order->getInvoiceId(),
            'order' => $order,
            'products' => $products
        ));
    }
}