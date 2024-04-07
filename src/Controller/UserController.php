<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class UserController extends AbstractController
{
    private $manager;
    private $jwtManager;
    public function __construct(EntityManagerInterface $manager , JWTTokenManagerInterface $jwtManager)
    {
        $this->manager=$manager;
        $this->jwtManager = $jwtManager;
    }


   
    
    #[Route('/showUsers', name : 'show_users' ,methods:'GET')]
    public function show_users():Response
    {
        $users = $this->manager->getRepository(User:: class)->findBy(["isdeleted" => null]);
        $results = [
            "status" => 200,
            "users"  => $users
        ] ;
        return $this->json($results,200);
    }

    #[Route('/showUser/{id}', name : 'show_user' ,methods:'GET')]

    public function show_user(int $id):Response
    { 
        $user = $this->manager->getRepository(User:: class)->findOneBy(["id" => $id , "isdeleted" => null]);
        if($user){
            $code = 200;
            $msg = "user found";
        }else{ 
            $code = 404 ;
            $msg = "user not found";
        }
        $results = [
            "status" => $code, 
            "user" => $user,
            "msg" => $msg
             
            //"user"  => $user ? $user : "user not found"
        ] ;
        return $this->json($results);
    }

    #[Route('/delete/{id}', name : 'delete_user' ,methods:'GET')]  
    public function delete(int $id):Response
    {
        $user = $this->manager->getRepository(User:: class)->findOneBy(["id" => $id , "isdeleted" => null]);
        if($user){
            $currentDateTime = new \DateTime();
            $user->setIsdeleted($currentDateTime);
            $this->manager->persist($user);
            $this->manager->flush();
            $code = 200 ;
            $msg = "user deleted" ;
        }else{
            $code = 404 ;
            $msg = "user not found" ;
        }
        $results = [
            'response'=>$msg,
            'status'=> $code
        ] ;
        return $this->json($results);
        
       
    }


    #[Route('/api/login', name : 'app_login' ,methods:'POST')]
    public function login(Request $request): Response
    {
        $data=json_decode($request->getContent(), true);
        $email = $data['email'];
        $password = $data['password'];
        $user = $this->manager->getRepository(User::class)->findOneBy(["email" => $email]);
        if($user){
            if($user->getPassword() == $password){
                
                $code = 200; 
                $msg = "user found" ;
            }else{
                $code = 500; 
                $msg = "password incorrect !" ;
            }

        }else{
            $code = 404; 
            $msg = "email not found" ;
        }

    
        $results = [
            'response'=>$msg,
            'status'=> $code,
            "user" => $user
        ] ;
        return $this->json($results,200);
    }


    #[Route('/update/{id}', name : 'update' ,methods:'POST')]

    public function update_user(Request $request, int $id):Response
    {  $data=json_decode($request->getContent(), true);
       $user = $this->manager->getRepository(User:: class)->findOneBy(["id" => $id , "isdeleted" => null]);
        if ($user){
            $user->setFirstname($data['firstname']); 
            $user->setPhonenumber($data['phonenumber']);
            $this->manager->persist($user);
            $this->manager->flush();
            $code = 200; 
            $msg = "user updated" ;
        }
        else{ 
            $code = 404; 
            $msg = "user not found" ;
        }
        $results = [
            'response'=>$msg,
            'status'=> $code,
            "user" => $user
        ] ;
        return $this->json($results,200);
    }
    

    #[Route('/api/register', name : 'register' ,methods:'POST')]

    public function register(Request $request): JsonResponse
    {  
        $data=json_decode($request->getContent(), true);
        $email=$data['email'];
        $is_exist = $this->manager->getRepository(User::class)->findOneBy(["email" => $email]);
        if($is_exist){
            $code = 500;
            $msg = "email already exists";
        }else{
            $firstname=$data['firstname'];
            $lastname=$data['lastname'];
            $phonenumber=$data['phonenumber'];
            $role=$data['type'];
            $password=$data['password'];
            $fullname=$firstname.$lastname; ////fullname supprimer lespace 
            $user =new User();
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setEmail($email);
            $user->setPhonenumber($phonenumber);
            $user->setRole($role);
            $user->setSlug($fullname);
            $user->setPassword($password);
            $this->manager->persist($user);
            $this->manager->flush();
            $code = 200;
            $msg = "register successfully";
        }
        return new JsonResponse(
            [
                'response'=>$msg,
                'status'=> $code
            ]
            );
    }
}
