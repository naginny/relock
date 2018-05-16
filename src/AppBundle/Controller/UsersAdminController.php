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
use AppBundle\Entity\Employee;
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

class UsersAdminController extends Controller
{
    /**
     * @Route("/admin/users", name="admin_users")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function indexAction(Request $request)
    {
        return $this->render('admin/users/list.html.twig', [
            'title' => 'Users',
            'current' => 'users',
            'users' => $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:Employee')->findAll()
        ]);
    }

    /**
     * @Route("/admin/users/add", name="admin_users_add")
     */
    public function productsAddAction(Request $request)
    {
        return $this->productProcessForm(
            null, $request,
            'User successfully added!', 'Create new user'
        );
    }

    /**
     * @Route("/admin/users/edit/{employeeId}", name="admin_users_edit")
     * @ParamConverter("user", class="AppBundle\Entity\Employee", options={"id" = "employeeId"})
     */
    public function productsEditAction(Employee $user, Request $request)
    {
        return $this->productProcessForm(
            $user, $request,
            'User successfully edited!', 'Change the user'
        );
    }

    /**
     */
    private function productProcessForm(Employee $user = null, Request $request, string $success, string $title)
    {
        $userManager = $this->get('fos_user.user_manager');

        $roles = array_keys($this->getParameter('security.role_hierarchy.roles'));

        /** @var FormInterface $form */
        $form = $this->createFormBuilder($user ?: $userManager->createUser())
            ->add('enabled')
            ->add('username')
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'choices' => array_combine($roles, $roles),
                'multiple' => true,
                'expanded' => true
            ])
            ->add('submit', SubmitType::class)
            ->getForm()
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            /** @var Employee $user */
            $user = $form->getData();
            if (!$user->getId()) {
                $user->setPlainPassword('default');
            }
            $em->persist($user);
            $em->flush();

            $this->addFlash('notice', $success);

            return $this->redirectToRoute('admin_users');
        }

        // replace this example code with whatever you need
        return $this->render('admin/form.html.twig', array(
            'form' => $form->createView(),
            'title' => $title
        ));
    }

}