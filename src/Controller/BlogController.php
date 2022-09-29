<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{    
    #[Route('/', name: 'home')] 

    public function home()
    {
      return $this->render('blog/home.html.twig', [
        'slogan' => "La démo d'un blog",
        'age' => 31
      ]); 

    }

    #[Route('/blog', name: 'app_blog')]
    
    public function index(ArticleRepository $repo): Response
    {

      $articles =$repo->findAll();

        return $this->render('blog/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    
    #[Route('/blog/show/{id}', name: 'blog_show')]
    
    public function show($id, ArticleRepository $repo)
    {       $article = $repo->find($id);
        return $this->render('blog/show.html.twig', ['item' =>$article]);
        
    }

    #[Route('/blog/new', name: 'blog_create')]
    #[Route('/blog/edit/{id}', name: 'blog_edit')]   

    public function form(Request $globals, EntityManagerInterface $manager, Article $article =null)
    {  if($article == null){
      $article = new Article;
      $article->setCreatedAt(new \DateTime);
    }
      

      $form = $this->createForm(ArticleType::class, $article);

      $form->handleRequest($globals);

     // dump($globals);

     //dump($article);

         if($form->isSubmitted() && $form->isValid()){

          
          $manager->persist($article);
          $manager->flush();

          return $this->redirectToRoute('blog_show', [
            'id' => $article->getId()
          ]);
        
         }

        return $this->renderForm("blog/form.html.twig",[
          'formArticle' => $form,
          'editMode' => $article->getId() !==null
        ]);
    }

    #[Route('/blog/delete/{id}', name: 'blog_delete')]
    
    public function delete($id, EntityManagerInterface $manager, ArticleRepository $repo)
    {
      $article = $repo->find($id);

      $manager->remove($article);
      $manager->flush();

      return $this->redirectToRoute('app_blog');
    
    
     
    }
}

