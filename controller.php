<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Question;
use AppBundle\Entity\User;
use AppBundle\Entity\Answer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller {

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request) {

        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("did-you-know", name="did-you-know")
     */
    public function backAction(Request $request) {
        /*if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== FALSE) {
            $id = $_SESSION["question"]->getId();
            $question = $this->getDoctrine()->getRepository(Question::Class)->find($id);
            $validate = $_SESSION["validate"];
            return $this->render('default/info.html.twig', array("validate" => $validate, "question" => $question));
        }*/
        if ($_SESSION['test'] == "question") {
            $id = $_SESSION["question"]->getId() - 1;
            $question = $this->getDoctrine()->getRepository(Question::Class)->find($id);
            $validate = $_SESSION["validate"];
            return $this->render('default/info.html.twig', array("validate" => $validate, "question" => $question));
        } elseif ($_SESSION['test'] == "reponse") {
            return $this->render('default/back.html.twig');
        }
    }

    /**
     * @Route("start", name="start")
     */
    public function startAction() {
        session_abort();
        session_start();
        session_destroy();
        return $this->redirectToRoute('question', array("numQuestion" => "1"));
    }

    /**
     * @Route("question/{numQuestion}", name="question")
     */
    public function questionAction(Request $request, $numQuestion) {
        $em = $this->getDoctrine()->getManager();
        $_SESSION['test'] = "question";
      
        if(isset($_SESSION["Q".$numQuestion])){
//            return $this->redirectToRoute('question',array("numQuestion"=>$numQuestion=$numQuestion+1));
            $numQuestion=$numQuestion+1;
        }
        $question = $this->getDoctrine()->getRepository(Question::Class)->find($numQuestion);

        $_SESSION['question'] = $question;
        return $this->render('default/question.html.twig', array("question" => $question, "answers" => $question->getAnswers()));
    }

    /**
     * @Route("answer/{numAnswer}", name="answer")
     */
    public function answerAction(Request $request, $numAnswer) {
        $em = $this->getDoctrine()->getManager();
        $answer = $this->getDoctrine()->getRepository(Answer::Class)->find($numAnswer);
        $validate = $answer->getValidate();

        $_SESSION["validate"] = $validate;
        $question = $_SESSION["question"];
        if ($validate) {

            $_SESSION["Q" . $question->getId()] = $validate;
        } else if ($validate == false) {

            $_SESSION["Q" . $question->getId()] = "0";
        } else {
            return $this->redirectToRoute('info');
        }


        return $this->redirectToRoute('info');





//        return $this->render('default/question.html.twig',array("question"=>$question,"answers"=>$question->getAnswers()));
    }

    /**
     * @Route("info", name="info")
     */
    public function infoAction() {

        $validate = $_SESSION["validate"];
        $question = $_SESSION["question"];
        $_SESSION['test'] = "reponse";
        return $this->render('default/info.html.twig', array("question" => $question, "validate" => $validate));
    }

    /**
     * @Route("score", name="score")
     */
    public function scoreAction() {
        $q1 = $_SESSION["Q1"];
        $q2 = $_SESSION["Q2"];
        $q3 = $_SESSION["Q3"];
        $q4 = $_SESSION["Q4"];
        $q5 = $_SESSION["Q5"];
        $score = $q1 + $q2 + $q3 + $q4 + $q5;
        return $this->render('default/score.html.twig', array("score" => $score));
    }

    /**
     * "q1" => $q1,"q2" => $q2,"q3" => $q3,"q4" => $q4,"q5" => $q5
     */

    /**
     * @Route("form", name="form")
     */
    public function formAction() {
        
         return $this->render('default/form.html.twig');
    }
    
    /**
     * @Route("traitement", name="traitement")
     */
    public function traitementAction() {
        
        $name = $_POST["name"];
        $surname = $_POST["surname"];
        $email = $_POST["email"];
        $phone = $_POST["phone"];
        $q1 = $_SESSION["Q1"];
        $q2 = $_SESSION["Q2"];
        $q3 = $_SESSION["Q3"];
        $q4 = $_SESSION["Q4"];
        $q5 = $_SESSION["Q5"];
        $score = $q1 + $q2 + $q3 + $q4 + $q5;
        
        $em = $this->getDoctrine()->getManager();

    $user = new User();
    $user->setName($name);
    $user->setSurname($surname);
    $user->setEmail($email);
    $user->setTel($phone);
    $user->setScore($score);
    $user->setQuestion1($q1);
    $user->setQuestion2($q2);
    $user->setQuestion3($q3);
    $user->setQuestion4($q4);
    $user->setQuestion5($q5);
     $_SESSION["name"] = $name;
     $_SESSION["surname"] = $surname;

    // tells Doctrine you want to (eventually) save the Product (no queries yet)
    $em->persist($user);

    // actually executes the queries (i.e. the INSERT query)
    $em->flush();
        
          return $this->redirectToRoute("share");
    }
      
    /**
     * @Route("share", name="share")
     */
    public function shareAction() {
         return $this->render('default/share.html.twig');
    }
    
    /**
     * 
     * @Route("mail", name="mail")
     */
    public function emailAction(){
        $name = $_POST["name"];
        $email = $_POST["email"];
        $senderName = $_SESSION["name"];
        $senderSurname = $_SESSION["surname"];
        $msg = $senderSurname . " " . $senderName . " Test message Acute";
        mail($email,"Acute test partage mail",$msg);
        return $this->redirectToRoute("last");
    }
     /**
     * 
     * @Route("last", name="last")
     */
    public function lastAction(){
        
        return $this->render('default/last.html.twig');
    }

}
