<?php

namespace App\Controller;

use App\DataTable\Type\Experimental\ExperimentalBatchModalActionDataTableType;
use App\DataTable\Type\Experimental\ExperimentalDefaultFiltersDataTableType;
use App\Enum\EmployeeStatus;
use App\Repository\EmployeeRepository;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/experimental')]
final class ExperimentalController extends AbstractController
{
    use DataTableFactoryAwareTrait;

    #[Route('/batch_modal_action', name: 'app_experimental_batch_modal_action')]
    public function batchModalAction(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee');

        $dataTable = $this->createDataTable(ExperimentalBatchModalActionDataTableType::class, $queryBuilder, options: [
            'themes' => [
                $request->getSession()->get('_data_table_theme')->getPath(),
                $request->getSession()->get('_data_table_icon_theme')->getPath(),
            ],
        ]);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }

        return $this->render('experimental/batch_modal_action.html.twig', [
            'employees' => $dataTable->createView(),
            'source_code_classes' => [
                ExperimentalBatchModalActionDataTableType::class,
                EmployeeStatus::class,
            ],
        ]);
    }
}
