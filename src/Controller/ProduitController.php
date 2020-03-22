<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Panier;
use App\Form\ProduitType;
use App\Form\PanierType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    /**
     * @Route("/produit", name="produit")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $fichier = $form->get('photo')->getData();

            if($fichier){
                $nomPhoto = uniqid() .'.'. $fichier->guessExtension();
            
            try{
                $fichier->move(
                    $this->getParameter('upload_dir'),
                    $nomPhoto
                );
            }

            catch(FileException $e){
                $this->addFlash('danger', 'Le fichier n\'a pas pus être upload ');

                return $this->redirectToRoute('produit');
            }
            $produit->setPhoto($nomPhoto);
        }

            $em->persist($produit);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté');
        }

        $produits = $em->getRepository(Produit::class)->findAll();

        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
            'form_produit' => $form->createView(),
        ]);
    }


    /**
     * @Route("/produit/{id}", name="un_produit")
     */

     public function produit(Request $request, Panier $panier=null, Produit $produit=null){
        if($produit != null){
            $panier = new Panier();
            $form = $this->createForm(PanierType::class, $panier);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($panier);
                $em->flush();

                $this->addFlash('success', 'Le produit est mis à jour');
            }
            return $this->render('produit/produit.html.twig', [
                'panier' => $panier,
                'produit' => $produit, 
                'ajout_panier' => $form->createView(),
            ]);
        }

        else{
            $this->addFlash('danger', 'Produit introuvable');
            return $this->redirectToRoute('produit');
        }
     }

      /**
     * @Route("/produit/delete/{id}", name="delete_produit")
     */
    public function delete(Produit $produit=null){
        if($produit != null){
            $em = $this->getDoctrine()->getManager();
            $em->remove($produit);
            $em->flush();

            $this->addFlash('warning', 'Produit supprimée');
        }

        else{
            $this->addFlash('danger', 'Produit introuvable');
        }

        return $this->redirectToRoute('produit');
    }
}
