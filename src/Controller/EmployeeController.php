<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Enum\EmployeeRole;
use App\Form\EmployeeType;
use App\Repository\ContractRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/employee')]
final class EmployeeController extends AbstractController
{
    // #[Route(name: 'app_employee_index', methods: ['GET'])]
    // public function index(EmployeeRepository $employeeRepository): Response
    // {
    //     return $this->render('employee/index.html.twig', [
    //         'employees' => $employeeRepository->findAll(),
    //     ]);
    // }

    #[Route('/new', name: 'app_employee_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $employee = new Employee();
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($employee);
            $entityManager->flush();

            return $this->redirectToRoute('app_homepage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('employee/new.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_employee_show', methods: ['GET'])]
    public function show(Employee $employee, ContractRepository $contractRepository, TranslatorInterface $translator): Response
    {
        return $this->render('employee/show.html.twig', [
            'employee' => $employee,
            'contracts' => $contractRepository->findByEmployee($employee, ['beginDate' => 'asc']),
            'roles' => array_map(function (EmployeeRole $role) use ($translator): string {
                return $role->trans($translator);
            }, $employee->getRoles() ?? []),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_employee_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Employee $employee, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_homepage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('employee/edit.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_employee_delete', methods: ['POST'])]
    public function delete(Request $request, Employee $employee, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$employee->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($employee);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_homepage_index', [], Response::HTTP_SEE_OTHER);
    }
}
