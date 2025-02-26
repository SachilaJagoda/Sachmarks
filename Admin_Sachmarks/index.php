<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Panel | Sachmarks</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="bootstrap.css" />
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" href="Sachmarks-Icon.png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        .content-section {
            display: none;
        }

        .active-tab {
            font-weight: bold;
            color: #007bff;
        }

        .question-block {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #dee2e6;
        }

        .answer-option {
            margin-bottom: 1rem;
        }

        .add-question-btn {
            cursor: pointer;
            color: #007bff;
            padding: 0.5rem;
            border-radius: 0.25rem;
            transition: background-color 0.2s;
        }

        .add-question-btn:hover {
            background-color: #f8f9fa;
        }

        .keywords-tags {
            display: inline-block;
            background-color: #e9ecef;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            margin: 0.25rem;
            font-size: 0.875rem;
        }

        .remove-question {
            color: #dc3545;
            cursor: pointer;
            float: right;
        }
    </style>
</head>

<body class="bg-light">
    <!-- Sidebar -->
    <div class="sidebar p-3">
        <div class="d-flex align-items-center mb-4 px-2">
            &nbsp;&nbsp;
            <img src="Sachmarks-Icon.png" alt="" style="width: 50px; height: 50px;">
            &nbsp;&nbsp;&nbsp;
            <h4 class="mb-0">Sachmarks<br />Admin</h4>
        </div>

        <nav class="nav flex-column gap-2">
            <a class="nav-link active-tab" href="#" data-target="dashboard"><i
                    class="fas fa-home me-2"></i>Dashboard</a>
            <a class="nav-link" href="#" data-target="messages"><i class="fas fa-envelope me-2"></i>Messages</a>
            <a class="nav-link" href="#" data-target="students"><i class="fas fa-users me-2"></i>Students</a>
            <a class="nav-link" href="#" data-target="study-content"><i class="fas fa-book me-2"></i>Study Content</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content p-4">
        <div class="content-section" id="dashboard">
            <h2>Dashboard Overview</h2>
        </div>
        <div class="content-section" id="messages">
            <h2>Messages</h2>
        </div>
        <div class="content-section" id="students">
            <h2>Students</h2>
        </div>

        <div class="content-section" id="study-content">
            <div class="container">
                <!-- Header Section -->
                <div class="row mb-4">
                    <div class="col">
                        <h2 class="mb-4">Study Content Management</h2>

                        <!-- Subject and Lesson Selection -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Select Subject and Lesson</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <select class="form-select" id="subjectSelect"
                                            onchange="loadLessons(this.value)">
                                            <option selected disabled>Select Subject</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <select class="form-select" id="lessonSelect">
                                            <option selected disabled>Select Lesson</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Content Upload Section -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Upload Study Material</h5>
                                <form id="pdfUploadForm">
                                    <div class="mb-3">
                                        <div class="input-group">
                                            <input type="file" class="form-control" id="pdfFile" accept=".pdf">
                                            <button type="button" class="btn btn-primary" id="uploadBtn">
                                                <i class="fas fa-upload me-2"></i>Upload PDF
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- MCQ Questions Section -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="card-title mb-0">Multiple Choice Questions</h5>
                                    <button type="button" class="btn btn-primary btn-sm mcq-add-btn">
                                        <i class="fas fa-plus me-2"></i>Add MCQ Question
                                    </button>
                                </div>
                                <div class="mcq-questions">
                                    <!-- MCQ questions will be added here dynamically -->
                                </div>
                            </div>
                        </div>

                        <!-- Typing Questions Section -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="card-title mb-0">Typing Questions</h5>
                                    <button type="button" class="btn btn-primary btn-sm typing-add-btn">
                                        <i class="fas fa-plus me-2"></i>Add Typing Question
                                    </button>
                                </div>
                                <div class="typing-questions">
                                    <!-- Typing questions will be added here dynamically -->
                                </div>
                            </div>
                        </div>

                        <!-- Save Questions Button -->
                        <div class="text-end">
                            <button type="button" class="btn btn-primary" id="saveQuestions">
                                <i class="fas fa-save me-2"></i>Save All Questions
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>