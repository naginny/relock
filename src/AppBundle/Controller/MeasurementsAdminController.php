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

class MeasurementsAdminController extends Controller
{
    /**
     * @Route("/admin/awaiting-measurements", name="admin_awaiting_measurements")
     * @param Request $request
     * @return Response
     */
    public function awaitingMeasurementsAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $orders = $em->getRepository('AppBundle:CustomerOrder')->findBy(
            [
                'status' => $em->getRepository('AppBundle:CustomerOrderStatus')->findOneBy(['code' => 'awaitingMeasurements'])
            ]
        );
        return $this->render('admin/awaiting-measurements/list.html.twig', [
            'title' => 'Awaiting measurements',
            'current' => 'awaitingMeasurements',
            'orders' => $orders
        ]);
    }

    /**
     * @Route("/admin/measurement-products/{orderId}", name="admin_awaiting_measurements_products")
     * @ParamConverter("order", class="AppBundle\Entity\CustomerOrder", options={"id" = "orderId"})
     * @param CustomerOrder $order
     * @param Request $request
     * @return Response
     */
    public function productsAction(CustomerOrder $order, Request $request)
    {
        /** @var Product[] $products */
        $products = $order->getProducts();
        return $this->render('admin/measurement-products/list.html.twig', [
            'title' => 'Products',
            'current' => 'awaitingMeasurements',
            'products' => $products,
            'order' => $order
        ]);
    }

    /**
     * @Route("/admin/measurement-products/edit/{orderId}/{productId}", name="admin_awaiting_measurements_products_edit")
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
        $originalSketch = null;
        if ($product->getId() && $product->getSketch()) {
            $originalSketch = $product->getSketch();
            $product->setSketch(
                new File($this->getParameter('sketch_directory').'/'.$product->getSketch())
            );
        }
        /** @var FormInterface $form */
        $form = $this->createFormBuilder($product)
            ->add('width')
            ->add('height')
            ->add('material')
            ->add('manufacturer')
            ->add('vendor_code')
            ->add('sketch', FileType::class, [
                'label' => 'Sketch (PDF)',
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('notes', TextareaType::class, [
                'attr' => array('style' => "height: 200px;", 'placeholder' => 'Any details you might find necessary'),
                'required' => false,
                'constraints' => [
                    new Constraints\Length(['min' => 20])
                ]
            ])
            ->add('submit', SubmitType::class)
            ->getForm()
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            /** @var Product $product */
            $product = $form->getData();
            $product->setOrder($order);

            if ($product->getSketch() === null && $originalSketch !== null) {
                $product->setSketch($originalSketch);
            } elseif ($product->getSketch() !== '') {

                if ($originalSketch !== null) {
                    unlink($this->getParameter('sketch_directory') . '/' . $originalSketch);
                }

                /** @var File $file */
                $file = new File($product->getSketch());
                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

                // moves the file to the directory where brochures are stored
                $file->move(
                    $this->getParameter('sketch_directory'),
                    $fileName
                );

                // updates the 'brochure' property to store the PDF file name
                // instead of its contents
                $product->setSketch($fileName);
            }

            $em->persist($product);
            $em->flush();

            $this->addFlash('notice', $success);

            $this->get('process')->log(
                $order->getCustomer(),
                'order product measurement changed',
                'order id: ' . $order->getId() . ', product id: ' . $product->getId(),
                $this->getUser()
            );

            return $this->redirectToRoute('admin_awaiting_measurements_products', ['orderId' => $order->getId()]);
        }

        // replace this example code with whatever you need
        return $this->render('admin/form.html.twig', array(
            'form' => $form->createView(),
            'title' => $title
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
     * @Route("/admin/measurement-products/proceed/{orderId}", name="admin_awaiting_measurements_proceed")
     * @ParamConverter("order", class="AppBundle\Entity\CustomerOrder", options={"id" = "orderId"})
     * @param CustomerOrder $order
     * @param Request $request
     */
    public function proceedAction(CustomerOrder $order, Request $request)
    {
        if ($order->getMeasurementsSet() === false) {
            $em = $this->get('doctrine.orm.entity_manager');
            $order->setStatus($em->getRepository('AppBundle:CustomerOrderStatus')->findOneBy(['code' => 'awaitingPricing']));
            $em->persist($order);
            $em->flush();
            $this->addFlash('notice', 'Order placed into Awaiting Pricing');

            $this->get('process')->log(
                $order->getCustomer(),
                'order changed status to awaitingPricing',
                'order id: ' . $order->getId(),
                $this->getUser()
            );
        } else {
            $this->addFlash('warning', 'There are some products without measurements');
        }

        return $this->redirectToRoute('admin_awaiting_measurements');
    }




}