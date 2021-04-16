<?php

namespace App\Controller;

use App\Entity\Sector;
use App\Form\SectorType;
use App\Repository\SectorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;

/**
 * @Route("/sector")
 */
class SectorController extends AbstractController
{
    /**
     * @Route("/", name="sector_index", methods={"GET"})
     */
    public function index(SectorRepository $sectorRepository): Response
    {
        return $this->render('sector/index.html.twig', [
            'sectors' => $sectorRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="sector_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $sector = new Sector();
        $form = $this->createForm(SectorType::class, $sector);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($sector);
                $entityManager->flush();
                $this->addFlash('success', Sector::EXITO_CREACION);
            }catch (\Exception $e) {
                $this->addFlash('danger', Sector::ERROR_CREACION);
            }
            return $this->redirectToRoute('sector_index');
        }

        return $this->render('sector/new.html.twig', [
            'sector' => $sector,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="sector_show", methods={"GET"})
     */
    public function show(Sector $sector): Response
    {
        return $this->render('sector/show.html.twig', [
            'sector' => $sector,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sector_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Sector $sector): Response
    {
        $form = $this->createForm(SectorType::class, $sector);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', Sector::EXITO_EDICION);
            }catch (\Exception $e) {
                $this->addFlash('danger', Sector::ERROR_EDICION);
            }
            return $this->redirectToRoute('sector_index');
        }

        return $this->render('sector/edit.html.twig', [
            'sector' => $sector,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="sector_delete", methods={"POST"})
     */
    public function delete(Request $request, Sector $sector): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sector->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            try {
                $entityManager->remove($sector);
                $entityManager->flush();
                $this->addFlash('success', Sector::EXITO_ELININACION);
            }catch (ForeignKeyConstraintViolationException $e) {
                $this->addFlash('danger',   Sector::ERROR_REGISTRO_ASOCIADO );
            }catch (\Exception $e) {
                $this->addFlash('danger', Sector::ERROR_ELININACION);
            }
        }

        return $this->redirectToRoute('sector_index');
    }
}
