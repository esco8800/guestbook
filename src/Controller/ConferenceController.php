<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Twig\Environment;
use App\Repository\CommentRepository;
use App\Entity\Conference;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ConferenceRepository;
use App\Entity\Comment;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\CommentFormType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ConferenceController extends AbstractController
{
    /**
     * @var Environment
     */
     private $twig;

    /**
     * @var
     */
     private $entityManager;


     public function __construct(Environment $twig, EntityManagerInterface $entityManager)
     {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
     }

    /**
     * @Route("/conference", name="homepage")
     */
    public function index(ConferenceRepository $conferenceRepository)
    {
        return new Response($this->twig->render('conference/index.html.twig'));
    }

    /**
    * @Route("/conference/{slug}", name="conference")
    */
    public function show(Request $request, ConferenceRepository $conferenceRepository,
                         Conference $conference, CommentRepository $commentRepository, string $photoDir)
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getCommentPaginator($conference, $offset);
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setConference($conference);
            if ($photo = $form['photo']->getData()) {
                $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
                try {
                    $photo->move($photoDir, $filename);
                } catch (FileException $e) {
                    // Отлов ошибки
                }
                $comment->setPhotoFilename($filename);
            }
            $this->entityManager->persist($comment);
            $this->entityManager->flush();
            return $this->redirectToRoute('conference', ['slug' => $conference->getSlug()]);
        }

        return new Response($this->twig->render('conference/show.html.twig', [
            'comment_form' => $form->createView(),
            'conference' => $conference,
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
        ]));

    }
}
