<?php

namespace App\Controller;

use App\DataTable\Type\ActionDataTableType;
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

    #[Route('/', name: 'app_action_index')]
    public function index(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee');

        $dataTable = $this->createDataTable(ActionDataTableType::class, $queryBuilder, options: [
            'themes' => [
                DataTableTheme::from($request->getSession()->get('_data_table_theme'))->getPath(),
                DataTableIconTheme::from($request->getSession()->get('_data_table_icon_theme'))->getPath(),
            ],
        ]);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }

        return $this->render('action/index.html.twig', [
            'employees' => $dataTable->createView(),
            'source_code_classes' => [
                ActionDataTableType::class,
                EmployeeStatus::class,
            ],
        ]);
    }
}
