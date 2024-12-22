<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }

    #[Route('/article/cree', name: 'app_article_create')]

    public function create(EntityManagerInterface $entityManager,Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $article = new Article();
        $form=$this->createForm(ArticleType::class,$article);

        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid()){
            $article = $form ->getData();
            $imageFile = $form->get('image')->getData();

    

            if ($imageFile) {
                // Donnez un nom unique au fichier
                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                // Déplacez le fichier vers le répertoire configuré
                $imageFile->move(
                    $this->getParameter('images_directory'), // Répertoire défini dans les paramètres
                    $newFilename
                );

                // Enregistrez le nom du fichier dans l'entité
                $article->setImage($newFilename);
            }


            $entityManager->persist($article);
            $entityManager->flush();
            return $this->redirectToRoute('article_show');

        }

        return $this->render('article/creer.html.twig', [
            'controller_name' => 'ArticleController',
            'titre' => 'Article',
            'article' => $article,
            'form'=>$form
        ]);
    }

    #[Route('/article/update/{id}', name: 'app_article_update')]

    public function update(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $article = $entityManager->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException(
                'No article found for id '.$id
            );
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                // Supprimer l'ancienne image si elle existe
                if ($article->getImage()) {
                    $oldImagePath = $this->getParameter('images_directory') . '/' . $article->getImage();
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                // Télécharger et enregistrer la nouvelle image
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
                $article->setImage($newFilename);
            }

            $entityManager->flush(); // Sauvegarde des modifications
    
            return $this->redirectToRoute('article_show');
        }

        return $this->render('article/modifier.html.twig', [
            'form' => $form->createView(),
            'article' => $article
        ]);
    }

    #[Route('/article/liste', name: 'article_show')]
    public function show(EntityManagerInterface $entityManager): Response
    {
        $articles = $entityManager->getRepository(Article::class)->findAll();

        return $this->render('article/liste.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/article/delete/{id}', name: 'article_delete')]


    public function delete(EntityManagerInterface $entityManager, int $id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $article = $entityManager->getRepository(Article::class)->find($id);
        $entityManager->remove($article); 
        $entityManager->flush();
        return $this->redirectToRoute('article_show');
        
    }
}