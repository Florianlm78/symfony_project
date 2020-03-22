<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Form\PanierType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $panier = new Panier();
        $form = $this->createForm(PanierType::class, $panier);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($panier);
            $em->flush();

            $this->addFlash('success', 'Panier ajoutée');
        }

        $paniers = $em->getRepository(Panier::class)->findAll();
        
        return $this->render('panier/index.html.twig', [
            'panier' => $paniers,
            'ajout_panier' => $form->createView()
        ]);
    }



     /**
     * @Route("/panier/delete/{id}", name="panier_delete")
     */
    public function PanierDelete(Panier $panier=null){
        if($panier != null){
            $em = $this->getDoctrine()->getManager();
            $em->remove($panier);
            $em->flush();

            $this->addFlash('success', 'Cet élément a été supprimé');
        }

        return $this->redirectToRoute('home');
    }
}
