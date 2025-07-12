<?php

namespace App\Controller;

use App\Entity\Contract;
use App\Entity\Employee;
use App\Form\ContractType;
use App\Repository\TargetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/contract')]
final class ContractController extends AbstractController
{
    // #[Route(name: 'app_contract_index', methods: ['GET'])]
    // public function index(ContractRepository $contractRepository): Response
    // {
    //     return $this->render('contract/index.html.twig', [
    //         'contracts' => $contractRepository->findAll(),
    //     ]);
    // }

    #[Route('/new/{employeeId}', name: 'app_contract_new', methods: ['GET', 'POST'])]
    public function new(Request $request, #[MapEntity(id: 'employeeId')] Employee $employee, EntityManagerInterface $entityManager): Response
    {
        $contract = new Contract();
        $contract->setEmployee($employee);
        $form = $this->createForm(ContractType::class, $contract);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contract);
            $entityManager->flush();

            return $this->redirectToRoute('app_employee_show', ['id' => $employee->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contract/new.html.twig', [
            'contract' => $contract,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_contract_show', methods: ['GET'])]
    public function show(Contract $contract, TargetRepository $targetRepository): Response
    {
        return $this->render('contract/show.html.twig', [
            'contract' => $contract,
            'targets' => $targetRepository->findByContract($contract, ['month' => 'asc']),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_contract_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contract $contract, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ContractType::class, $contract);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_employee_show', ['id' => $contract->getEmployee()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contract/edit.html.twig', [
            'contract' => $contract,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_contract_delete', methods: ['POST'])]
    public function delete(Request $request, Contract $contract, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contract->getId(), $request->getPayload()->getString('_token'))) {
            if ($contract->getId() === $contract->getEmployee()->getCurrentContract()?->getId()) {
                $contract->getEmployee()->setCurrentContract(null);
            }
            $contract->setCurrentTarget(null);
            $entityManager->remove($contract);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_employee_show', ['id' => $contract->getEmployee()->getId()], Response::HTTP_SEE_OTHER);
    }
}
