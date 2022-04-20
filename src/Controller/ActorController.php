<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Form\ActorFormType;
use App\Repository\ActorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ActorController extends AbstractController
{
    private $em;
    private $actorRepository;
    public function __construct(EntityManagerInterface $em, ActorRepository $actorRepository) 
    {
        $this->em = $em;
        $this->actorRepository = $actorRepository;
    }

    #[Route('/actor', name: 'actor')]
    public function index(): Response
    {
        $actor = $this->actorRepository->findAll();

        return $this->render('actor/index.html.twig', [
            'actor' => $actor
        ]);
    }

    #[Route('/actor/create', name: 'create_actor')]
    public function create(Request $request): Response
    {
        $actor = new ACtor();
        $form = $this->createForm(ActorFormType::class, $actor);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newActor = $form->getData();
            $imagePath = $form->get('imagePath')->getData();
            
            if ($imagePath) {
                $newFileName = uniqid() . '.' . $imagePath->guessExtension();

                try {
                    $imagePath->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }
                $newActor->setUserId($this->getUser()->getId());
                $newActor->setImagePath('/uploads/' . $newFileName);
            }

            $this->em->persist($newActor);
            $this->em->flush();

            return $this->redirectToRoute('actor');
        }

        return $this->render('actor/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/actor/edit/{id}', name: 'edit_actor')]
    public function edit($id, Request $request): Response 
    {
        $this->checkLoggedInUser($id);
        $actor = $this->actorRepository->find($id);

        $form = $this->createForm(ActorFormType::class, $actor);

        $form->handleRequest($request);
        $imagePath = $form->get('imagePath')->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            if ($imagePath) {
                if ($actor->getImagePath() !== null) {
                    if (file_exists(
                        $this->getParameter('kernel.project_dir') . $actor->getImagePath()
                        )) {
                            $this->GetParameter('kernel.project_dir') . $actor->getImagePath();
                    }
                    $newFileName = uniqid() . '.' . $imagePath->guessExtension();

                    try {
                        $imagePath->move(
                            $this->getParameter('kernel.project_dir') . '/public/uploads',
                            $newFileName
                        );
                    } catch (FileException $e) {
                        return new Response($e->getMessage());
                    }

                    $actor->setImagePath('/uploads/' . $newFileName);
                    $this->em->flush();

                    return $this->redirectToRoute('actor');
                }
            } else {
                $actor->setName($form->get('name')->getData());
                $actor->setBirth($form->get('Birth')->getData());
                $actor->setDescription($form->get('Description')->getData());

                $this->em->flush();
                return $this->redirectToRoute('actor');
            }
        }

        return $this->render('actor/edit.html.twig', [
            'actor' => $actor,
            'form' => $form->createView()
        ]);
    }

    #[Route('/actor/delete/{id}', methods: ['GET', 'DELETE'], name: 'delete_actor')]
    public function delete($id): Response
    {
        $this->checkLoggedInUser($id);
        $actor = $this->actorRepository->find($id);
        $this->em->remove($actor);
        $this->em->flush();

        return $this->redirectToRoute('actor');
    }

    #[Route('/actor/{id}', methods: ['GET'], name: 'show_actor')]
    public function show($id): Response
    {
        $actor = $this->actorRepository->find($id);
        
        return $this->render('actor/show.html.twig', [
            'actor' => $actor
        ]);
    }

    private function checkLoggedInUser($actorId) {
        if($this->getUser() == null || $this->getUser()->getId() !== $actorId) {
            return $this->redirectToRoute('actor');
        }
    }
}

