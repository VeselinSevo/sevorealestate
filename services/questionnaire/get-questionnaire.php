<?php
require 'config/conn.php';
session_start();
// Assuming you have a database connection established already


    $conn = null;

    try {
        // Connect to your database
        $conn = $createConnection();
        // Fetch question data
        $queryQuestions = "SELECT id, text, type FROM question";
        $stmtQuestions = $conn->prepare($queryQuestions);
        $stmtQuestions->execute();
        $questions = $stmtQuestions->fetchAll(PDO::FETCH_ASSOC);

        // Fetch answers data with image names
        $answers = [];
        foreach ($questions as $question) {
            $queryAnswers = "SELECT id, text, image_name FROM answer WHERE question_id = :question_id";
            $stmtAnswers = $conn->prepare($queryAnswers);
            $stmtAnswers->bindParam(':question_id', $question['id']);
            $stmtAnswers->execute();
            $answers[$question['id']] = $stmtAnswers->fetchAll(PDO::FETCH_ASSOC);
        }

        // Now $questions contains all questions, and $answers is an associative array where keys are question IDs
        // and values are arrays of answers associated with each question, including image names if available.

    } catch (PDOException $e) {
        // Handle database errors
        echo "Error: " . $e->getMessage();
    }

?>