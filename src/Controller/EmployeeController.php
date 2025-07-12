<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Enum\EmployeeRole;
use App\Enum\EmployeeStatus;
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

            return $this->redirectToRoute('app_employee_show', ['id' => $employee->getId()], Response::HTTP_SEE_OTHER);
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

            return $this->redirectToRoute('app_employee_show', ['id' => $employee->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('employee/edit.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_employee_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, Employee $employee, EntityManagerInterface $entityManager): Response
    {
        if ($request->getMethod() === 'GET') {
            return $this->render('employee/_delete_modal_content.html.twig', [
                'employee' => $employee,
            ]);
        }

        if ($this->isCsrfTokenValid('delete'.$employee->getId(), $request->getPayload()->getString('_token'))) {
            foreach ($employee->getContracts() as $contract) {
                $contract->setCurrentTarget(null);
            }
            $employee->setCurrentContract(null);
            $entityManager->flush();

            $entityManager->remove($employee);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_action_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/activate', name: 'app_employee_activate', methods: ['POST'])]
    public function activate(Employee $employee, EntityManagerInterface $entityManager): Response
    {
        if ($employee->getStatus() !== EmployeeStatus::ACT) {
            $employee->setStatus(EmployeeStatus::ACT);

            $entityManager->flush();
        }

        return $this->redirectToRoute('app_action_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/deactivate', name: 'app_employee_deactivate', methods: ['POST'])]
    public function deactivate(Employee $employee, EntityManagerInterface $entityManager): Response
    {
        if ($employee->getStatus() === EmployeeStatus::ACT) {
            $employee->setCurrentContract(null);
            $employee->setStatus(EmployeeStatus::INA);

            $entityManager->flush();
        }

        return $this->redirectToRoute('app_action_index', [], Response::HTTP_SEE_OTHER);
    }
}
