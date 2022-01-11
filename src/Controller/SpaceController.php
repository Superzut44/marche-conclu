<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Slot;
use App\Entity\Space;
use App\Entity\User;
use App\Form\SlotType;
use App\Form\SpaceType;
use App\Repository\SpaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/space', name: 'space_')]
class SpaceController extends AbstractController
{
    public const CATEGORIES = [
        'BUREAU PRIVEE',
        'CO-WORKING',
        'SALLE DE REUNION',
        'OPEN SPACE',
        'ESPACE DE STOCKAGE',
    ];

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(SpaceRepository $spaceRepository): Response
    {
        return $this->render('space/index.html.twig', [
            'spaces' => $spaceRepository->findAll(),
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     */
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {

        $space = new Space();
        $form = $this->createForm(SpaceType::class, $space);
        $form->handleRequest($request);
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $space->setOwner((USER)($user));
            $entityManager->persist($space);
            $entityManager->flush();

            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('space/new.html.twig', [
            'space' => $space,
            'form' => $form,
        ]);
    }

    #[Route('/search', name: 'search', methods: ['GET'])]
    public function search(Request $request, SpaceRepository $spaceRepository): Response
    {
        $options = $request->query->all();
        foreach ($options as $key => $option) {
            if ($option === "") {
                unset($options[$key]);
            }
            if ($option === "on") {
                $options['category'] = $key;
                unset($options[$key]);
            }
        }
        $spaces = $options ? $spaceRepository->findByCriterias($options) : $spaceRepository->findAll();

        return $this->renderForm('space/search.html.twig', [
            'location' => $options['location'] ?? null,
            'spaces' => $spaces, 'categories' => self::CATEGORIES,
            'api' => $_ENV['API_KEY']
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Request $request, EntityManagerInterface $entityManager, Space $space): Response
    {
        $slot = new Slot();
        $form = $this->createForm(SlotType::class, $slot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($slot);
            $entityManager->flush();

            return $this->redirectToRoute('space_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('space/show.html.twig', [
            'space' => $space,
            'slot' => $slot,
            'form' => $form

        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     */
    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Space $space, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SpaceType::class, $space);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('space_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('space/edit.html.twig', [
            'space' => $space,
            'form' => $form,
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     */
    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Space $space, EntityManagerInterface $entityManager): Response
    {

        if ($this->isCsrfTokenValid('delete' . $space->getId(), strval($request->request->get('_token')))) {
            $entityManager->remove($space);
            $entityManager->flush();
        }

        return $this->redirectToRoute('space_index', [], Response::HTTP_SEE_OTHER);
    }
}
