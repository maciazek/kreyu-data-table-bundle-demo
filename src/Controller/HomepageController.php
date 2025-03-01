<?php

namespace App\Controller;

use App\DataTable\Type\HomepageDataTableType;
use App\Repository\EmployeeRepository;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomepageController extends AbstractController
{
    use DataTableFactoryAwareTrait;

    #[Route('/', name: 'app_homepage_index')]
    public function index(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee')
            ->leftJoin('employee.currentContract', 'currentContract')
            ->addSelect('currentContract')
        ;

        $dataTable = $this->createDataTable(HomepageDataTableType::class, $queryBuilder);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }

        $sourceCodeFiles = array_map(function($item) {
            $item = new \ReflectionClass($item);

            return [
                'id' => $item->getShortName(),
                'name' => $item->getShortName().'.php',
                'content' => highlight_file($item->getFileName(), true),
            ];
        }, [
            \App\DataTable\Type\HomepageDataTableType::class,
            \App\DataTable\Filter\Formatter\DateRangeActiveFilterFormatter::class,
        ]);

        return $this->render('homepage/index.html.twig', [
            'source_code_files' => $sourceCodeFiles,
            'employees' => $dataTable->createView(),
        ]);
    }
}
