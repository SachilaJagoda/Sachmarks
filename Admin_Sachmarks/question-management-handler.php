<?php
require_once 'db.php';

// Ensure no output before headers
ob_start();

// Set error handling for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON content type header
header('Content-Type: application/json');

function sendJsonResponse($success, $message, $data = null) {
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response);
    exit;
}

// Check for POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, 'Invalid request method');
}

try {
    // Validate input exists
    if (!isset($_POST['subject_id']) || !isset($_POST['lesson_id'])) {
        throw new Exception('Subject and lesson are required');
    }

    $subject_id = filter_var($_POST['subject_id'], FILTER_VALIDATE_INT);
    $lesson_id = filter_var($_POST['lesson_id'], FILTER_VALIDATE_INT);
    
    if ($subject_id === false || $lesson_id === false) {
        throw new Exception('Invalid subject or lesson ID');
    }

    // Establish database connection first
    Database::setUpConnection();
    
    // Start transaction
    if (!Database::$connection->begin_transaction()) {
        throw new Exception('Failed to start transaction');
    }
    
    $questionsAdded = 0;
    
    // Handle MCQ questions
    if (isset($_POST['mcq_questions']) && !empty($_POST['mcq_questions'])) {
        $mcq_questions = json_decode($_POST['mcq_questions'], true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid MCQ questions format: ' . json_last_error_msg());
        }
        
        foreach ($mcq_questions as $mcq) {
            // Validate question data
            if (empty($mcq['question']) || !isset($mcq['answers']) || !is_array($mcq['answers'])) {
                throw new Exception('Invalid MCQ question format');
            }
            
            // Insert question using prepared statement
            $stmt = Database::$connection->prepare("INSERT INTO poll_questions (subject_id, lesson_id, question) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $subject_id, $lesson_id, $mcq['question']);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to insert MCQ question: ' . $stmt->error);
            }
            
            $question_id = Database::$connection->insert_id;
            $stmt->close();
            
            // Insert answers using prepared statement
            $stmt = Database::$connection->prepare("INSERT INTO poll_answers (question_id, answer, is_correct) VALUES (?, ?, ?)");
            
            foreach ($mcq['answers'] as $answer) {
                if (!isset($answer['text']) || !isset($answer['isCorrect'])) {
                    throw new Exception('Invalid answer format');
                }
                
                $stmt->bind_param("isi", $question_id, $answer['text'], $answer['isCorrect']);
                
                if (!$stmt->execute()) {
                    throw new Exception('Failed to insert answer: ' . $stmt->error);
                }
            }
            
            $stmt->close();
            $questionsAdded++;
        }
    }
    
    // Handle typing questions
    if (isset($_POST['typing_questions']) && !empty($_POST['typing_questions'])) {
        $typing_questions = json_decode($_POST['typing_questions'], true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid typing questions format: ' . json_last_error_msg());
        }
        
        $stmt = Database::$connection->prepare("INSERT INTO typing_questions (subject_id, lesson_id, question, correct_answer, keywords) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($typing_questions as $typing) {
            if (empty($typing['question']) || empty($typing['correct_answer'])) {
                throw new Exception('Invalid typing question format');
            }
            
            $keywords = isset($typing['keywords']) ? $typing['keywords'] : '';
            $stmt->bind_param("iisss", $subject_id, $lesson_id, $typing['question'], $typing['correct_answer'], $keywords);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to insert typing question: ' . $stmt->error);
            }
            
            $questionsAdded++;
        }
        
        $stmt->close();
    }
    
    // Commit transaction
    if (!Database::$connection->commit()) {
        throw new Exception('Failed to commit transaction');
    }
    
    // Clear output buffer and close connection
    ob_end_clean();
    Database::closeConnection();
    
    sendJsonResponse(true, "Successfully saved $questionsAdded questions!");
    
} catch (Exception $e) {
    // Rollback transaction on error
    if (isset(Database::$connection)) {
        Database::$connection->rollback();
        Database::closeConnection();
    }
    
    // Clear output buffer
    ob_end_clean();
    
    sendJsonResponse(false, $e->getMessage());
}
?>