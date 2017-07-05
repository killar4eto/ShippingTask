<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Shipping;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extention\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
		$getData = $this->getDoctrine()->getRepository('AppBundle:Shipping')->findAll();
		return $this->render('default/index.html.twig', array('getData' => $getData));

    }
	
    /**
     * @Route("/create", name="create")
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
     */
	public function createAction(Request $request){
		$newData = new Shipping;
		
		$form = $this->createFormBuilder($newData)
		->add('First_name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 15px', 'required')))
		->add('Last_name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 15px', 'required')))
		->add('Email', EmailType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 15px', 'required')))
		->add('Password', PasswordType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 15px', 'required')))
		->add('Default_Address', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 15px', 'required')))
		->add('Create', SubmitType::class, array('attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom: 15px')))
		->getForm();
		
		$form->handleRequest($request);
		
		if($form->isSubmitted() && $form->isValid()){
			
			//Get data
			$firstname = $form['First_name']->getData();
			$lastname = $form['Last_name']->getData();
			$email = $form['Email']->getData();
			$password = $form['Password']->getData();
			$defAddress = $form['Default_Address']->getData();
			
			
			$now = new\DateTime('now');
			
			$newData->setFirstname($firstname);
			$newData->setLastname($lastname);
			$newData->setEmail($email);
			$newData->setPassword($password);
			$newData->setdefaultAddress($defAddress);
			$newData->setmodified($now);
			
			$em = $this->getDoctrine()->getManager();
			
			$em->persist($newData);
			$em->flush();
			
			$this->addFlash(
				'notice',
				'Client added!'
			);
			
			return new RedirectResponse('./');
		}
		
		return $this->render("default/create.html.twig", array(
			'form' => $form->createView()
		));
	}
	
    /**
     * @Route("/view/{id}", name="view")
     */
	public function viewAction($id){
		$getData = $this->getDoctrine()->getRepository('AppBundle:Shipping')->find($id);
		
		return $this->render("default/view.html.twig", array(
			'result' => $getData
		));
	}	
	
    /**
     * @Route("/edit/{id}", name="edit")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
	public function editAction($id, Request $request){
		$getData = $this->getDoctrine()->getRepository('AppBundle:Shipping')->find($id);

		$getData->setFirstname($getData->getfirstname());
		$getData->setLastname($getData->getlastname());
		$getData->setEmail($getData->getemail());
		$getData->setPassword($getData->getpassword());
		$getData->setdefaultAddress($getData->getdefaultAddress());		

		$form = $this->createFormBuilder($getData)
		->add('First_name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 15px', 'required')))
		->add('Last_name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 15px', 'required')))
		->add('Email', EmailType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 15px', 'required')))
		->add('Password', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 15px', 'required')))
		->add('Default_Address', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 15px', 'required')))
		->add('Address_2', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 15px', 'required' => false)))
		->add('Address_3', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 15px', 'required' => false)))
		->add('Update', SubmitType::class, array('attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom: 15px')))
		->getForm();
		
		$form->handleRequest($request);
		
		if($form->isSubmitted() && $form->isValid()){
			
			//Get data
			$firstname = $form['First_name']->getData();
			$lastname = $form['Last_name']->getData();
			$email = $form['Email']->getData();
			$Password = $form['Password']->getData();
			$defAddress = $form['Default_Address']->getData();
			
			$em = $this->getDoctrine()->getManager();
			$getData = $em->getRepository('AppBundle:Shipping')->find($id);
			
			$now = new\DateTime('now');
			
			$em->flush();
			
			$this->addFlash(
				'notice',
				'Client updated!'
			);
			
			//return $this->redirect('../');
			return new RedirectResponse('../');
		}		
		
		return $this->render("default/edit.html.twig", array(
			'result' => $getData,
			'form' => $form->createView()
		));
	}

    /**
     * @Route("/remove/{id}", name="remove")
     */
	public function removeAction($id){
		$em = $this->getDoctrine()->getManager();
		
		$out = $em->getRepository('AppBundle:Shipping')->find($id);
		
		$em->remove($out);
		$em->flush();
		
		$this->addFlash(
			'notice',
			'Client removed'
		);
		
		return new RedirectResponse('../');
	}	
	
}