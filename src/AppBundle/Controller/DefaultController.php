<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Customer;
use AppBundle\Entity\CustomerAddress;
use AppBundle\Entity\CustomerOrder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function indexAction(Request $request)
    {
        $form = $this->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $em = $this->get('doctrine.orm.entity_manager');

            // find customer
            $repo = $em->getRepository('AppBundle:Customer');
            /** @var Customer|null $customer */
            $customer = $repo->createQueryBuilder('c')
                ->where('c.email = :email')
                ->orWhere('c.phone = :phone')
                ->setParameters(array('email' => $data['email'], 'phone' => $data['phone']))
                ->getQuery()
                ->getOneOrNullResult()
            ;

            if (!$customer) {
                $customer = new Customer();
                $customer->setEmail($data['email']);
                $customer->setPhone($data['phone']);
                $customer->setCreatedAt(new \DateTime());
            }

            $customer->setPermissionToUsePersonalInfo($data['agreeToUsePersonalInformation']);
            $customer->setPermissionReceivedAt(new \DateTime());
            $customer->setName($data['name']);
            $customer->setSurname($data['surname']);
            $em->persist($customer);

            $address = new CustomerAddress();
            $address->setCity($data['city']);
            $address->setStreetAddress($data['streetAddress']);
            $address->setFloor($data['floor']);
            $address->setNotes($data['notes']);
            $address->setCustomer($customer);
            $customer->getAddresses()->add($address);
            $em->persist($address);

            $order = new CustomerOrder();
            $order->setCreatedAt(new \DateTime());
            $order->setRequestedDoorAmount($data['amountOfDoors']);
            $order->setRequestedWindowAmount($data['amountOfWindows']);
            $order->setRequestedJalousieAmount($data['amountOfJalousies']);
            $order->setRequestedGateAmount($data['amountOfGates']);
            $order->setRequestedMeasurementAt($data['measurementDate']);
            $order->setRequestedInstallationAt($data['installationDate']);
            $order->setCustomer($customer);
            $order->setStatus(
                $em
                    ->getRepository('AppBundle:CustomerOrderStatus')
                    ->findOneBy(array( 'code' => 'new' ))
            );
            $customer->getOrders()->add($order);
            $order->setAddress($address);
            $em->persist($order);
            $em->flush();

            $order->setInvoiceId($order->generateInvoiceId());
            $em->persist($order);
            $em->flush();

            $this->get('process')->log(
                $customer,
                'order created',
                'order id: ' . $order->getId()
            );

            return $this->redirectToRoute("form-success-page", array(
                'order_id' => $order->getId()
            ));
        }

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/success", name="form-success-page")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function successAction(Request $request)
    {
        if (!$request->get('order_id')) {
            return $this->createNotFoundException('no order id found');
        }

        return $this->render('default/success.html.twig', array(
            'orderId' => $request->get('order_id')
        ));
    }

    /**
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     * @throws \Exception
     */
    private function getForm()
    {
        $amountOf = (function($min, $max) {
            $choices = array();
            for($i = $min; $i < $max + 1; $i++) {
                $choices[] = $i;
            }
            return $choices;
        });
        return $this->createFormBuilder()
            ->add('name', TextType::class, array(
                'required' => true,
                'constraints' => array(
                    new Constraints\Length(array('max' => 255))
                )
            ))
            ->add('surname', TextType::class, array(
                'required' => true,
                'constraints' => array(
                    new Constraints\Length(array('max' => 255))
                )
            ))
            ->add('email', EmailType::class, array(
                'constraints' => array(
                    new Constraints\Length(array('max' => 255))
                )
            ))
            ->add('phone', TextType::class, array(
                'constraints' => array(
                    new Constraints\Length(array('min' => 8,'max' => 12))
                )
            ))
            ->add('city', ChoiceType::class, array(
                'choices'  => array(                        // TODO: remove hardcoded values
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
            ->add('streetAddress', TextType::class, array(
                'constraints' => array(
                    new Constraints\Length(array('max' => 255))
                )
            ))
            ->add('floor', IntegerType::class, array(
                'required' => false,
                'constraints' => array(
                    new Constraints\LessThanOrEqual(50)
                )
            ))
            ->add('notes', TextareaType::class, array(
                'required' => false,
                'constraints' => array(
                    new Constraints\Length(array('max' => 5000))
                )
            ))
            ->add('amountOfDoors', ChoiceType::class, array(
                'choices'  => $amountOf(0,10)
            ))
            ->add('amountOfWindows', ChoiceType::class, array(
                'choices'  => $amountOf(0,20)
            ))
            ->add('amountOfJalousies', ChoiceType::class, array(
                'choices'  => $amountOf(0,20)
            ))
            ->add('amountOfGates', ChoiceType::class, array(
                'choices'  => $amountOf(0,20)
            ))
            ->add('measurementDate', DateType::class, array(
                'data' => (new \DateTime())->add(new \DateInterval('P1W')),
                'widget' => 'single_text',
                'html5' => false,
            ))
            ->add('installationDate', DateType::class, array(
                'data' => (new \DateTime())->add(new \DateInterval('P1M')),
                'widget' => 'single_text',
                'html5' => false,
            ))
            ->add('agreeToUsePersonalInformation', CheckboxType::class, array(
                'label'    => 'Agree to the terms of customer information sharing',
                'required' => true,
            ))
            ->add('placeTheOrder', SubmitType::class)
            ->getForm();
    }
}
