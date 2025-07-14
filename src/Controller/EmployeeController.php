<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Enum\EmployeeRole;
use App\Enum\EmployeeStatus;
use App\Form\EmployeeType;
use App\Repository\ContractRepository;
use App\Repository\EmployeeRepository;
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

    #[Route('/{id}/show', name: 'app_employee_show', methods: ['GET'])]
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

    #[Route('/new', name: 'app_employee_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $employee = new Employee();
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($employee);
            $entityManager->flush();

            return $request->getSession()->get('_redirect_to')
                ? $this->redirect($request->getSession()->get('_redirect_to'))
                : $this->redirectToRoute('app_employee_show', ['id' => $employee->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('employee/new.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_employee_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Employee $employee, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $request->getSession()->get('_redirect_to')
                ? $this->redirect($request->getSession()->get('_redirect_to'))
                : $this->redirectToRoute('app_employee_show', ['id' => $employee->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('employee/edit.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/activate', name: 'app_employee_activate', methods: ['POST'])]
    public function activate(Request $request, Employee $employee, EntityManagerInterface $entityManager): Response
    {
        if ($employee->getStatus() !== EmployeeStatus::ACTIVE) {
            $employee->setStatus(EmployeeStatus::ACTIVE);

            $entityManager->flush();
        }

        return $request->getSession()->get('_redirect_to')
            ? $this->redirect($request->getSession()->get('_redirect_to'))
            : $this->redirectToRoute('app_homepage_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/batch_activate', name: 'app_employee_batch_activate', methods: ['POST'])]
    public function batchActivate(Request $request, EmployeeRepository $employeeRepository, EntityManagerInterface $entityManager): Response
    {
        $employees = $employeeRepository->findBy([
            'id' => array_map(fn ($id) => intval($id), $request->request->all('id')),
        ]);
        foreach ($employees as $employee) {
            if ($employee->getStatus() !== EmployeeStatus::ACTIVE) {
                $employee->setStatus(EmployeeStatus::ACTIVE);
            }
        }
        $entityManager->flush();

        return $request->getSession()->get('_redirect_to')
            ? $this->redirect($request->getSession()->get('_redirect_to'))
            : $this->redirectToRoute('app_homepage_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/deactivate', name: 'app_employee_deactivate', methods: ['POST'])]
    public function deactivate(Request $request, Employee $employee, EntityManagerInterface $entityManager): Response
    {
        if ($employee->getStatus() === EmployeeStatus::ACTIVE) {
            $employee->setCurrentContract(null);
            $employee->setStatus(EmployeeStatus::INACTIVE);

            $entityManager->flush();
        }

        return $request->getSession()->get('_redirect_to')
            ? $this->redirect($request->getSession()->get('_redirect_to'))
            : $this->redirectToRoute('app_homepage_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/batch_deactivate', name: 'app_employee_batch_deactivate', methods: ['POST'])]
    public function batchDeactivate(Request $request, EmployeeRepository $employeeRepository, EntityManagerInterface $entityManager): Response
    {
        $employees = $employeeRepository->findBy([
            'id' => array_map(fn ($id) => intval($id), $request->request->all('id')),
        ]);
        foreach ($employees as $employee) {
            if ($employee->getStatus() !== EmployeeStatus::INACTIVE) {
                $employee->setCurrentContract(null);
                $employee->setStatus(EmployeeStatus::INACTIVE);
            }
        }
        $entityManager->flush();

        return $request->getSession()->get('_redirect_to')
            ? $this->redirect($request->getSession()->get('_redirect_to'))
            : $this->redirectToRoute('app_homepage_index', [], Response::HTTP_SEE_OTHER);
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

        return $request->getSession()->get('_redirect_to')
            ? $this->redirect($request->getSession()->get('_redirect_to'))
            : $this->redirectToRoute('app_homepage_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/batch_delete', name: 'app_employee_batch_delete', methods: ['GET', 'POST'])]
    public function batchDelete(Request $request, EmployeeRepository $employeeRepository, EntityManagerInterface $entityManager): Response
    {
        $employees = $employeeRepository->findBy([
            'id' => array_map(fn ($id) => intval($id), $request->query->all('id')),
        ]);

        if ($request->getMethod() === 'GET') {
            return $this->render('employee/_batch_delete_modal_content.html.twig', [
                'employees' => $employees,
            ]);
        }

        if ($this->isCsrfTokenValid('delete'.implode('_', array_map(fn ($employee) => $employee->getId(), $employees)), $request->getPayload()->getString('_token'))) {
            foreach ($employees as $employee) {
                foreach ($employee->getContracts() as $contract) {
                    $contract->setCurrentTarget(null);
                }
                $employee->setCurrentContract(null);
                $entityManager->flush();

                $entityManager->remove($employee);
            }
            $entityManager->flush();
        }

        return $request->getSession()->get('_redirect_to')
            ? $this->redirect($request->getSession()->get('_redirect_to'))
            : $this->redirectToRoute('app_homepage_index', [], Response::HTTP_SEE_OTHER);
    }
}
