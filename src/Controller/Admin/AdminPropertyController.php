<?php

namespace App\Controller\Admin;

use App\Entity\Option;
use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request as BrowserKitRequest;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class AdminPropertyController extends AbstractController
{

    /**
     * @var PropertyRepository
     */
    private $repository;

    /**
     * @var ObjectManager
     */
    private $em;

    public function __construct(PropertyRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }


    /**
     * @Route("/admin", name="admin.property.index")
     * @return \Symfony\Component\HttpFoundation\Response 
     */
    public function index()
    {
        $properties = $this->repository->findAll();
        return $this->render('admin/property/index.html.twig', compact('properties'));
    }

    /**
     * @Route("/admin/property/create", name="admin.property.new")
     */
    public function new(Request $request)
    {
        $property = new Property();
        $form = $this->createForm(PropertyType::class, $property);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Permet de persist dans la bdd 
            $this->em->persist($property);

            // insérer l'objet dans la BDD
            $this->em->flush();

            $this->addFlash('info', 'Bien créer avec succès');


            return $this->redirectToRoute('admin/property/index');
        }

        return $this->render('admin/property/new.html.twig', [
            'property' => $property,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/property/{id}", name="admin.property.edit", methods="GET|POST")
     * @param Property $property
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Property $property,  Request $request)
    {

        // Pour rajouter les options 
        // $option = new Option();
        // $property->addOption($option);

        $form = $this->createForm(PropertyType::class, $property);
        // handleRequest vérifie si qq chose à changé dans les champs avec les setter de $property
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // insérer l'objet dans la BDD
            $this->em->flush();

            // Message flash pour confirmer modification annonce
            $this->addFlash('info', 'Annonce modifier avec succès');

            return $this->redirectToRoute('admin.property.index');
        }

        return $this->render('admin/property/edit.html.twig', [
            'property' => $property,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("admin/property/{id}", name="admin.property.delete", methods="DELETE")
     * @param Property $property
     * @return \Symfony\Component\HttpFoundation\RedirectResponse

     */
    public function delete(Property $property, Request $request)
    {
        // Ce if Permet de valider le token du delete 
        if ($this->isCsrfTokenValid('delete' . $property->getId(), $request->get('_token'))) {
            $this->em->remove($property);
            $this->em->flush();
            $this->addFlash('info', 'Annonce supprimée avec succès');


        }


        return $this->redirectToRoute('admin.property.index');
    }
}
