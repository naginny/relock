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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Validator\Constraints;

class FinishedAdminController extends Controller
{
    /**
     * @Route("/admin/finished", name="admin_finished")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $orders = $em->getRepository('AppBundle:CustomerOrder')->createQueryBuilder('o')
            ->where('o.status = :status')
            ->andWhere('o.installationAt >= :from')
            ->andWhere('o.installationAt <= :to')
            ->setParameter('status', $em->getRepository('AppBundle:CustomerOrderStatus')->findOneBy(['code' => 'closed']))
            ->setParameter('from', $request->get('from', date('Y-m-d')))
            ->setParameter('to', $request->get('to', date('Y-m-d')))
            ->getQuery()
            ->getResult();
        return $this->render('admin/finished/list.html.twig', [
            'title' => 'Finished',
            'current' => 'finished',
            'orders' => $orders,
            'from' => $request->get('from', date('Y-m-d')),
            'to' => $request->get('to', date('Y-m-d')),
        ]);
    }
    /**
     * @Route("/admin/rejected", name="admin_rejected")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function rejectedAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $orders = $em->getRepository('AppBundle:CustomerOrder')->createQueryBuilder('o')
            ->where('o.status = :status')
            ->andWhere('o.rejectedAt >= :from')
            ->andWhere('o.rejectedAt <= :to')
            ->setParameter('status', $em->getRepository('AppBundle:CustomerOrderStatus')->findOneBy(['code' => 'rejected']))
            ->setParameter('from', $request->get('from', date('Y-m-d')))
            ->setParameter('to', $request->get('to', date('Y-m-d')))
            ->getQuery()
            ->getResult();
        return $this->render('admin/rejected/list.html.twig', [
            'title' => 'Rejected',
            'current' => 'rejected',
            'orders' => $orders,
            'from' => $request->get('from', date('Y-m-d')),
            'to' => $request->get('to', date('Y-m-d')),
        ]);
    }

}