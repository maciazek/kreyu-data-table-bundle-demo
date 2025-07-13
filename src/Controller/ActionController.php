<?php

namespace App\Controller;

use App\DataTable\Type\Action\ActionCompactDataTableType;
use App\DataTable\Type\Action\ActionStandardDataTableType;
use App\Enum\DataTableIconTheme;
use App\Enum\DataTableTheme;
use App\Enum\EmployeeStatus;
use App\Repository\EmployeeRepository;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/action')]
final class ActionController extends AbstractController
{
    use DataTableFactoryAwareTrait;

    #[Route('/standard', name: 'app_action_standard')]
    public function standard(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee');

        $dataTable = $this->createDataTable(ActionStandardDataTableType::class, $queryBuilder, options: [
            'themes' => [
                $request->getSession()->get('_data_table_theme')->getPath(),
                $request->getSession()->get('_data_table_icon_theme')->getPath(),
            ],
        ]);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }

        return $this->render('action/standard.html.twig', [
            'employees' => $dataTable->createView(),
            'source_code_classes' => [
                ActionStandardDataTableType::class,
                EmployeeStatus::class,
            ],
        ]);
    }

    #[Route('/compact', name: 'app_action_compact')]
    public function compact(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee');

        $dataTable = $this->createDataTable(ActionCompactDataTableType::class, $queryBuilder, options: [
            'themes' => [
                $request->getSession()->get('_data_table_theme')->getPath(),
                $request->getSession()->get('_data_table_icon_theme')->getPath(),
            ],
        ]);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }

        return $this->render('action/compact.html.twig', [
            'employees' => $dataTable->createView(),
            'source_code_classes' => [
                ActionCompactDataTableType::class,
                EmployeeStatus::class,
            ],
        ]);
    }
}
