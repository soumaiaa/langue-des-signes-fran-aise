<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Score;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\QuizRepository;
use App\Repository\ReponseRepository;
use App\Repository\ScoreRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuizController extends AbstractController
{
    #[Route('/quiz/{userId}/{categoryId}', name: 'app_quiz')]
    public function quiz(int $userId, int $categoryId, UserRepository $userRepository, CategoryRepository $categoryRepository, QuizRepository $quizRepository): Response
    {
        $user = $userRepository->find($userId);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $category = $categoryRepository->find($categoryId);
        if (!$category) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }

        $quiz = $quizRepository->findOneBy(['titre' => $category]);

        if (!$quiz) {
            throw $this->createNotFoundException('Quiz non trouvé pour cette catégorie');
        }

        return $this->render('quiz/quiz.html.twig', [
            'quiz' => $quiz,
            'user' => $user,
            'category' => $category,
        ]);
    }

    #[Route('/quiz/check-answer', name: 'app_quiz_check_answer', methods: ['POST'])]
    public function checkAnswer(Request $request, ReponseRepository $reponseRepository): JsonResponse
    {
        $reponseId = $request->request->get('reponseId');
        $score = (int) $request->request->get('score');  // Cast score to int

        $reponse = $reponseRepository->find($reponseId);

        if (!$reponse) {
            return new JsonResponse(['correct' => false, 'score' => $score]);
        }

        $correct = $reponse->isIsCorrect();

        if ($correct) {
            $score++;
        }

        return new JsonResponse(['correct' => $correct, 'score' => $score]);
    }

    #[Route('/quiz/save-score', name: 'app_quiz_save_score', methods: ['POST'])]
    public function saveScore(Request $request, UserRepository $userRepository, QuizRepository $quizRepository, ScoreRepository $scoreRepository): JsonResponse
    {
        $userId = $request->request->get('userId');
        $quizId = $request->request->get('quizId');
        $scoreValue = $request->request->get('score');

        $user = $userRepository->find($userId);
        $quiz = $quizRepository->find($quizId);

        if (!$user || !$quiz) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid user or quiz'], 400);
        }

        $score = new Score();
        $score->setUser($user);
        $score->setQuiz($quiz);
        $score->setScore($scoreValue);
        $score->setTakenAt(new \DateTimeImmutable());

        $scoreRepository->save($score, true);

        return new JsonResponse(['status' => 'success']);
    }

  
   
    #[Route('/quiz/{userId}/{categoryId}/results', name: 'app_final_results')]
    public function results(int $userId, int $categoryId, UserRepository $userRepository,CategoryRepository $categoryRepository, ScoreRepository $scoreRepository): Response
    {
        $user = $userRepository->find($userId);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $lastScore = $scoreRepository->findOneBy(
            ['user' => $user],
            ['takenAt' => 'DESC']
        );

        if (!$lastScore) {
            throw $this->createNotFoundException('Aucun score trouvé pour cet utilisateur');
        }
    // Logique pour obtenir la catégorie suivante
    $currentCategory = $categoryRepository->find($categoryId);
    $nextCategory = $categoryRepository->findOneBy(['id' => $categoryId + 1]); // Par exemple, trouver la prochaine catégorie par ID
        // Récupérer tous les scores triés par score décroissant
        $usersScores = $scoreRepository->findBy([], ['score' => 'DESC'], 10);

    return $this->render('quiz/result.html.twig', [
        'user' => $user,
        'score' => $lastScore->getScore(),
        'categoryId' => $categoryId, // Pass categoryId to the template
        'nextCategory' => $nextCategory,
        'usersScores' => $usersScores,
    ]);
    }

    #[Route('/quiz/{userId}/{categoryId}/resultsFinal', name: 'app_results_final')]
    public function usersScores(UserRepository $userRepository, ScoreRepository $scoreRepository): Response
    {
        // Récupérer tous les utilisateurs
        $users = $userRepository->findAll();

        // Tableau pour stocker les scores totaux par utilisateur
        $usersScores = [];

        // Récupérer le score le plus élevé pour chaque quiz pour chaque utilisateur
        foreach ($users as $user) {
            $highestScores = $scoreRepository->findHighestScoresByUser($user);

            // Calculer le score total
            $totalScore = array_sum(array_column($highestScores, 'highestScore'));

            // Ajouter l'utilisateur avec son score total
            $usersScores[] = [
                'user' => $user,
                'totalScore' => $totalScore,
            ];
        }

        // Trier les utilisateurs par score total décroissant
        usort($usersScores, fn($a, $b) => $b['totalScore'] <=> $a['totalScore']);

        return $this->render('quiz/resultatFinal.html.twig', [
            'usersScores' => $usersScores,
        ]);
    }
}
