<?php

namespace App\Controller;

use App\Entity\Contract;
use App\Entity\Target;
use App\Form\TargetType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/target')]
final class TargetController extends AbstractController
{
    // #[Route(name: 'app_target_index', methods: ['GET'])]
    // public function index(TargetRepository $targetRepository): Response
    // {
    //     return $this->render('target/index.html.twig', [
    //         'targets' => $targetRepository->findAll(),
    //     ]);
    // }

    #[Route('/new/{contractId}', name: 'app_target_new', methods: ['GET', 'POST'])]
    public function new(Request $request, #[MapEntity(id: 'contractId')] Contract $contract, EntityManagerInterface $entityManager): Response
    {
        $target = new Target();
        $target->setContract($contract);
        $form = $this->createForm(TargetType::class, $target);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($target);
            $entityManager->flush();

            return $this->redirectToRoute('app_contract_show', ['id' => $contract->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('target/new.html.twig', [
            'target' => $target,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_target_show', methods: ['GET'])]
    public function show(Target $target): Response
    {
        return $this->render('target/show.html.twig', [
            'target' => $target,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_target_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Target $target, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TargetType::class, $target);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_contract_show', ['id' => $target->getContract()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('target/edit.html.twig', [
            'target' => $target,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_target_delete', methods: ['POST'])]
    public function delete(Request $request, Target $target, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$target->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($target);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_contract_show', ['id' => $target->getContract()->getId()], Response::HTTP_SEE_OTHER);
    }
}
