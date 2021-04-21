<?php

namespace App\Controller;

use App\Entity\Sector;
use App\Form\SectorType;
use App\Repository\SectorRepository;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\View\TwitterBootstrap4View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;

/**
 * Controlador utilizado para administrar los sectores
 * @author Juan Manuel Lazzarini <juan.manuel.lazzarini@gmail.com>
 * 
 * @Route("/sector")
 */
class SectorController extends AbstractController
{
    /**
     * @Route("/", name="sector_index", methods={"GET"})
     */
    public function index(SectorRepository $sectorRepository, Request $request): Response
    {    
        $query = $sectorRepository->queryAllSectores();
       
        $totalOfRecordsString = $this->getTotalOfRecordsString($query, $request); 
        list($sectores, $pagerHtml) = $this->paginator($query, $request);

        return $this->render('sector/index.html.twig', [
            'sectors' => $sectores,
            'pagerHtml' => $pagerHtml,
            'totalOfRecordsString' => $totalOfRecordsString,
        ]);
    }

  
    /*
     * Calcular el total de entradas
     */
    protected function getTotalOfRecordsString($queryBuilder, $request)
    {
      
        $totalOfRecords =count($queryBuilder->getResult());
        $show = $request->get('pcg_show', 10);
        $page = $request->get('pcg_page', 1);

        $startRecord = ($show * ($page - 1)) + 1;
        $endRecord = $show * $page;

        if ($endRecord > $totalOfRecords) {
            $endRecord = $totalOfRecords;
        }
        return "Mostrando $startRecord - $endRecord de $totalOfRecords Registros.";
    }


    /**
     * Paginacion, armar y obtener el request
     */
    protected function paginator($queryBuilder, Request $request)
    {
        //sorting
        //  $sortCol = $queryBuilder->getRootAlias() . '.' . $request->get('pcg_sort_col', 'id');
        // $queryBuilder->orderBy($sortCol, $request->get('pcg_sort_order', 'desc'));
        
         // Paginator
        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($request->get('pcg_show', 10));

        try {
            $pagerfanta->setCurrentPage($request->get('pcg_page', 1));
        } catch (\Pagerfanta\Exception\OutOfRangeCurrentPageException $ex) {
            $pagerfanta->setCurrentPage(1);
        }

        $entities = $pagerfanta->getCurrentPageResults();

        // Paginator - route generator
        $me = $this;
        $routeGenerator = function ($page) use ($me, $request) {
            $requestParams = $request->query->all();
            $requestParams['pcg_page'] = $page;
            return $me->generateUrl('empresa_index', $requestParams);
        };

        // Paginator - view
        $view = new TwitterBootstrap4View();
        $pagerHtml = $view->render($pagerfanta, $routeGenerator, array(
            'proximity' => 3,
            'prev_message' => 'anterior',
            'next_message' => 'siguiente',
        ));

        return array($entities, $pagerHtml);
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
