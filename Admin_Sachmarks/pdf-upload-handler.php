<?php
require_once 'db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];

    try {
        // Validate inputs
        if (!isset($_POST['subject']) || $_POST['subject'] === 'Select Subject') {
            throw new Exception('Please select a subject');
        }
        if (!isset($_POST['lesson']) || $_POST['lesson'] === 'Select Lesson') {
            throw new Exception('Please select a lesson');
        }
        if (!isset($_FILES['pdfFile']) || $_FILES['pdfFile']['error'] !== 0) {
            throw new Exception('Please select a PDF file');
        }

        // Validate file type
        $fileType = strtolower(pathinfo($_FILES['pdfFile']['name'], PATHINFO_EXTENSION));
        if ($fileType !== 'pdf') {
            throw new Exception('Only PDF files are allowed');
        }

        // Get subject and lesson details
        $subject_id = intval($_POST['subject']);
        $lesson_id = intval($_POST['lesson']);

        // Get lesson details for folder naming
        $lessonQuery = "SELECT l.lesson_name, g.grade_name, s.subject_name 
                       FROM lessons l 
                       JOIN grade g ON l.grade_id = g.grade_id 
                       JOIN subjects s ON l.subject_id = s.subject_id 
                       WHERE l.lesson_id = ?";

        $result = Database::prepareAndExecute($lessonQuery, "i", $lesson_id);
        if ($row = $result->fetch_assoc()) {
            $gradeName = str_replace(' ', ' ', $row['grade_name']);
            $subjectName = $row['subject_name'];
            $lessonName = $row['lesson_name'];

            // Create directory structure
            $baseDir = "../Resources/Study Content";
            $gradeDir = "$baseDir/$gradeName";
            $subjectDir = "$gradeDir/$subjectName";
            $lessonDir = "$subjectDir/$lesson_id Lesson - $lessonName";

            // Create directories if they don't exist
            if (!file_exists($gradeDir))
                mkdir($gradeDir, 0777, true);
            if (!file_exists($subjectDir))
                mkdir($subjectDir, 0777, true);
            if (!file_exists($lessonDir))
                mkdir($lessonDir, 0777, true);

            // Generate file name and path
            $fileName = sprintf("%02d %s.pdf", $lesson_id, $lessonName);
            $filePath = "$lessonDir/$fileName";

            // Save file
            if (move_uploaded_file($_FILES['pdfFile']['tmp_name'], $filePath)) {
                // Store in database
                $relativePath = str_replace('', '../', $filePath);
                $insertQuery = "INSERT INTO pdfs (subject_id, lesson_id, pdf_path) VALUES (?, ?, ?)";
                Database::prepareAndExecute($insertQuery, "iis", $subject_id, $lesson_id, $relativePath);

                $response['success'] = true;
                $response['message'] = 'PDF uploaded successfully!';
            } else {
                throw new Exception('Failed to upload file');
            }
        } else {
            throw new Exception('Lesson not found');
        }
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Get subjects
if (isset($_GET['getSubjects'])) {
    $query = "SELECT subject_id, subject_name FROM subjects WHERE medium_id = 1 ORDER BY subject_name";
    $result = Database::search($query);
    $subjects = [];
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($subjects);
    exit;
}

// Get lessons for a subject
if (isset($_GET['getLessons']) && isset($_GET['subject_id'])) {
    $subject_id = intval($_GET['subject_id']);
    $query = "SELECT lesson_id, lesson_name FROM lessons WHERE subject_id = ? ORDER BY lesson_id";
    $result = Database::prepareAndExecute($query, "i", $subject_id);
    $lessons = [];
    while ($row = $result->fetch_assoc()) {
        $lessons[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($lessons);
    exit;
}
?>