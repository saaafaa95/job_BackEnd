<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;


class PostController extends AbstractController
{    
    CONST Etat = array(
        'pending' => 'Pending',
        'rejected' => 'Rejected',
        'approved' => 'Approved'
    );

    private $manager;
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager=$manager;
    }
     
    #[Route('/show_my_jobs/{id_recruteur}', name : 'show_my_jobs' ,methods:'GET')]
    public function show_my_jobs(int $id_recruteur):Response
    {
        $jobs = $this->manager->getRepository(Post::class)->findBy(["user" => $id_recruteur ,"isdeleted" => null]);
        
        if($jobs){
            $code = 200;
        }else{
            $code = 404;
        }
        $results = [
            "status" => $code,
            "jobs"  => $jobs
        ] ;
        
        return $this->json($results);
    }

    #[Route('/show_post/{id}', name : 'show_post' ,methods:'GET')]
    public function show_post(int $id):Response
    {  
        $post = $this->manager->getRepository(Post::class)->findOneBy(["id" => $id , "isdeleted" => null]);
        if($post){
            $code = 200;
            $msg = " Postfound";
        }else{ 
            $code = 404 ;
            $msg = "Post not found";
        }
        $results = [
            "status" => $code, 
            "Post" => $post,
            "msg" => $msg
        ] ;
        return $this->json($results);
    }       


    #[Route('/deletePost/{id}', name : 'delete_post' ,methods:'GET')]  
    public function delete_job_post(int $id):Response
    {
        $Post = $this->manager->getRepository(Post:: class)->findOneBy(["id" => $id , "isdeleted" => null]);
        if($Post){
        
            $currentDateTime = new \DateTime(); 
            $Post->setIsdeleted($currentDateTime);
            $this->manager->persist($Post);
            $this->manager->flush();
            $code = 200 ;
            $msg = "Post deleted" ;
        }else{
            $code = 404 ;
            $msg = "Post not found" ;
        }
        $results = [
            'response'=>$msg,
            'status'=> $code
        ] ;
        return $this->json($results);
    }


    #[Route('/update_post/{id}', name : 'update_post' ,methods:'POST')]
    public function update_post(Request $request, int $id):Response
    {    $data=json_decode($request->getContent(), true);
         $post = $this->manager->getRepository(post:: class)->findOneBy(["id" => $id , "isdeleted" => null]);
        if ($post){
            $post->setTitle($data['title']);
            $post->setDescription($data['description']);
            $post->settype($data['type']);
            $post->setcategory($data['category']);
            $post->setlocation($data['location']);
            $this->manager->persist($post);
            $this->manager->flush();
            $code = 200; 
            $msg = "post updated" ;
        }
        else{ 
            $code = 404; 
            $msg = "post not found" ;
        }
        $results = [
            'response'=>$msg,
            'status'=> $code,
            "Post" => $post,
        ] ;
        return $this->json($results);
    }
    
    

    #[Route('/api/AddPost', name : 'add_Post' ,methods:'POST')]
    public function add_post(Request $request): JsonResponse     
    {   
        $data=json_decode($request->getContent(), true);
        $id_user=$data['id_user'];
        $user = $this->manager->getRepository(User::class)->find($id_user);
        $post = new Post();
        $post->setTitle($data['title']);
        $post->setDescription($data['description']);
        $post->setPublishDate(new \DateTime());
        $post->setExpirationDate(new \DateTime($data['expiration_date']));
        $post->setType($data['type']);
        $post->setSalary($data['salary']);
        $post->setJobHours($data['job_hours']);
        $post->setCategory($data['category']);
        $post->setLocation($data['location']);
        $post->setCompanyName($data['company_name']);
        $post->setCompanyLogo($data['company_logo']);
        $post->setEtat(self::Etat['pending']);
        $post->setUser($user);
        //Enregistrer le nouveau poste dans la base de données
        $this->manager->persist($post);
        $this->manager->flush();
        $code = 200;
        $msg = "post added successfully";
     
     return new JsonResponse(
         [
             'response'=>$msg,
             'status'=> $code
         ]
         );
        }
       
}

      
