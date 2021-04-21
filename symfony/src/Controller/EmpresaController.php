<?php

namespace App\Controller;

use App\Entity\Empresa;
use App\Form\EmpresaType;
use App\Form\EmpresaFilterType;
use App\Repository\EmpresaRepository;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\View\TwitterBootstrap4View;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdaterInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controlador utilizado para administrar las empresas
 * @author Juan Manuel Lazzarini <juan.manuel.lazzarini@gmail.com>
 * 
 * @Route("/empresa") 
 */
class EmpresaController extends AbstractController
{
    /**
     * @Route("/", name="empresa_index", methods={"GET"})
     */
    public function index(EmpresaRepository $empresaRepository, Request $request, FilterBuilderUpdaterInterface $query_builder_updater): Response
    {

        $queryBuilder = $empresaRepository->queryAllEmpresas();
       
        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request, $query_builder_updater);
        list($empresas, $pagerHtml) = $this->paginator($queryBuilder, $request);
        $totalOfRecordsString = $this->getTotalOfRecordsString($queryBuilder, $request); 

        return $this->render('empresa/index.html.twig', [
            'empresas' => $empresas,
            'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView(),
            'totalOfRecordsString' => $totalOfRecordsString,
        ]);
    }


    
    /*
     * Calcular el total de entradas
     */
    protected function getTotalOfRecordsString($queryBuilder, $request)
    {
      
        $totalOfRecords =count($queryBuilder->getQuery()->getResult());
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
        // $sortCol = $queryBuilder->getRootAlias() . '.' . $request->get('pcg_sort_col', 'id');
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
     * Crear y procesar filtro
     */
    protected function filter($queryBuilder, Request $request, $query_builder_updater)
    {
        $session = $request->getSession();
        $filterForm = $this->createForm(EmpresaFilterType::class);

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('EmpresaControllerFilter');
        }

        // Filter action
        if ($request->get('filter_action') == 'filter') {
            // Bind values from the request
            $filterForm->handleRequest($request);

          
                // Build the query from the given form object
                $query_builder_updater->addFilterConditions($filterForm, $queryBuilder);
                // Save filter to session
                $filterData = $filterForm->getData();
                $session->set('EmpresaControllerFilter', $filterData);
            
        } else {
            // Get filter from session
            if ($session->has('EmpresaControllerFilter')) {
                $filterData = $session->get('EmpresaControllerFilter');

                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
                    if (is_object($filter)) {
                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
                    }
                }

                $filterForm = $this->createForm(EmpresaFilterType::class, $filterData);
                $query_builder_updater->addFilterConditions($filterForm, $queryBuilder);

            }
        }

        return array($filterForm, $queryBuilder);
    }


    /**
     * @Route("/new", name="empresa_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $empresa = new Empresa();
        $form = $this->createForm(EmpresaType::class, $empresa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($empresa);
                $entityManager->flush();
                $this->addFlash('success', Empresa::EXITO_CREACION);
            }catch (\Exception $e) {
                $this->addFlash('danger', Empresa::ERROR_CREACION);
                }
            return $this->redirectToRoute('empresa_index');
        }

        return $this->render('empresa/new.html.twig', [
            'empresa' => $empresa,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="empresa_show", methods={"GET"})
     */
    public function show(Empresa $empresa): Response
    {
        return $this->render('empresa/show.html.twig', [
            'empresa' => $empresa,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="empresa_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Empresa $empresa): Response
    {
        $form = $this->createForm(EmpresaType::class, $empresa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', Empresa::EXITO_EDICION);
            }catch (\Exception $e) {
                $this->addFlash('danger', Empresa::ERROR_EDICION);
            }
            return $this->redirectToRoute('empresa_index');
        }

        return $this->render('empresa/edit.html.twig', [
            'empresa' => $empresa,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="empresa_delete", methods={"POST"})
     */
    public function delete(Request $request, Empresa $empresa): Response
    {
        if ($this->isCsrfTokenValid('delete'.$empresa->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($empresa);
            $entityManager->flush();
            $this->addFlash('success', Empresa::EXITO_ELININACION );
        } else {
            $this->addFlash('danger', Empresa::ERROR_ELININACION);
        }


        return $this->redirectToRoute('empresa_index');
    }
}
