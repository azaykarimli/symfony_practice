<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

//use Doctrine\DBAL\Types\TextType;
// use Doctrine\DBAL\Types\TextareaType;
// use Doctrine\DBAL\Types\SubmitType;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ArticleController extends AbstractController
{

    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }



    /**
     * @Route("/", name="article_list")
     * @Method("GET")
     */
    public function index()
    {

        $repo = $this->em->getRepository(Article::class);
        $articles = $repo->findAll();

        return $this->render(
            'articles/index.html.twig',
            array(
                'name' => 'Slim shady',
                'articles' => $articles
            )
        );

        /*
        $articles = ['Article 1', 'Article 2'];
        //return new Response('<html><body>Hello</body></html>');
        return $this->render(
            'articles/index.html.twig',
            array(
                'name' => 'Slim shady',
                'articles' => $articles
            )
        );
        */
    }

    /**
     * @Route("/article/{id}", name="article_show")
     */

    public function show($id)
    {
        $repo = $this->em->getRepository(Article::class);
        $article = $repo->find($id);
        return $this->render(
            'articles/show.html.twig',
            array(
                'article' => $article
            )
        );
    }

    #[Route('/article/crud/new', name: 'article_new')]
    public function new(Request $request, ManagerRegistry $doctrine)
    {
        //insert
        $article = new Article();
        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('body', TextareaType::class, array(
                'required' => false,
                'attr' => array('class' => 'form-control')
            ))

            ->add('save', SubmitType::class, array(
                'label' => 'Create',
                'attr' => array('class' => 'btn btn-primary mt-3')

            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();

            $entityManager = $doctrine->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_list');
        }



        return $this->render(
            'articles/detail.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }

    #[Route('/article/crud/edit/{id}', name: 'edit')]
    public function edit(Request $request, ManagerRegistry $doctrine, $id)
    {

        $article = new Article();
        $repo = $this->em->getRepository(Article::class);
        $article = $repo->find($id);
        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('body', TextareaType::class, array(
                'required' => false,
                'attr' => array('class' => 'form-control')
            ))

            ->add('save', SubmitType::class, array(
                'label' => 'update',
                'attr' => array('class' => 'btn btn-primary mt-3')

            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();

            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('article_list');
        }



        return $this->render(
            'articles/edit.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }


    #[Route('/article/delete/{id}', name: 'delete')]
    public function delete(Request $request, ManagerRegistry $doctrine, $id)
    {
        $repo = $this->em->getRepository(Article::class);
        $article = $repo->find($id);
        $entityManager = $doctrine->getManager();
        $entityManager->remove($article);
        $entityManager->flush();

        $response = new Response();
        $response->send();
    }

    // /**
    //  * @Route("/article/save")
    //  */
    /*
    public function save(ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();

        $article = new Article();
        $article->setTitle("Article two");
        $article->setBody("Body of the article two");

        $entityManager->persist($article);

        $entityManager->flush();

        return new Response('Saves an article with the id of ' . $article->getId());
    }
    */
}
