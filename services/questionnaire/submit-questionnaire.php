<?php
require '../../config/conn.php';
require '../../util/is-post.php';
session_start();

if (is_post()) {
    $conn = null;

    try {
        // Connect to your database
        $conn = $createConnection();
        $userId = $_SESSION['user']['id'];

        // Extract the answer IDs from the POST data
        $answer1Id = $_POST['question1-answers'] ?? null;
        $answer2Id = $_POST['question2-answers'] ?? null;
        $optionIds = $_POST['question3-answers'] ?? [];

        // Prepare SQL statement to insert user answers into the database
        $sql = "INSERT INTO user_answer (user_id, answer_id) VALUES ";

        // Add values for the first question (radio button)
        if ($answer1Id) {
            $sql .= "($userId, $answer1Id), ";
        }

        // Add values for the second question (radio button)
        if ($answer2Id) {
            $sql .= "($userId, $answer2Id), ";
        }

        // Add values for the third question (checkbox)
        foreach ($optionIds as $optionId) {
            $sql .= "($userId, $optionId), ";
        }

        // Remove the trailing comma and space
        $sql = rtrim($sql, ", ");

        // Execute the SQL statement
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // Add submission date to the user table
        $submissionDate = date("Y-m-d H:i:s"); // Current date and time
        $updateDateSql = "UPDATE user SET questionnaire_submitted_at = :submission_date WHERE id = :user_id";
        $updateDateStmt = $conn->prepare($updateDateSql);
        $updateDateStmt->bindParam(':submission_date', $submissionDate);
        $updateDateStmt->bindParam(':user_id', $userId);
        $updateDateStmt->execute();


        $_SESSION['user']['questionnaire_submitted_at'] = $submissionDate;

        header("Location: ../../index.php");
        exit();

    } catch (PDOException $e) {
        // Handle database errors
        echo "Error: " . $e->getMessage();
    }
}
?>
