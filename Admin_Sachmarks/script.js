document.addEventListener('DOMContentLoaded', function() {
  // Navigation between tabs
  const navLinks = document.querySelectorAll('.nav-link');
  const contentSections = document.querySelectorAll('.content-section');
  
  navLinks.forEach(link => {
      link.addEventListener('click', function(e) {
          e.preventDefault();
          
          // Remove active class from all links
          navLinks.forEach(item => item.classList.remove('active-tab'));
          
          // Add active class to clicked link
          this.classList.add('active-tab');
          
          // Hide all content sections
          contentSections.forEach(section => section.style.display = 'none');
          
          // Show the target section
          const targetId = this.getAttribute('data-target');
          document.getElementById(targetId).style.display = 'block';
      });
  });
  
  // Show dashboard by default
  document.getElementById('dashboard').style.display = 'block';
  
});

document.addEventListener('DOMContentLoaded', function() {
  // Navigation between tabs
  const navLinks = document.querySelectorAll('.nav-link');
  const contentSections = document.querySelectorAll('.content-section');
  
  navLinks.forEach(link => {
      link.addEventListener('click', function(e) {
          e.preventDefault();
          
          // Remove active class from all links
          navLinks.forEach(item => item.classList.remove('active-tab'));
          
          // Add active class to clicked link
          this.classList.add('active-tab');
          
          // Hide all content sections
          contentSections.forEach(section => section.style.display = 'none');
          
          // Show the target section
          const targetId = this.getAttribute('data-target');
          document.getElementById(targetId).style.display = 'block';
      });
  });
  
  // Show dashboard by default
  document.getElementById('dashboard').style.display = 'block';
  
});

// Load subjects when the study content tab is shown
document.querySelector('[data-target="study-content"]').addEventListener('click', loadSubjects);

function loadSubjects() {
    fetch('pdf-upload-handler.php?getSubjects')
        .then(response => response.json())
        .then(subjects => {
            const select = document.querySelector('#subjectSelect');
            if (!select) return;
            
            select.innerHTML = '<option selected disabled>Select Subject</option>';
            subjects.forEach(subject => {
                const option = document.createElement('option');
                option.value = subject.subject_id;
                option.textContent = subject.subject_name;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error loading subjects:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load subjects'
            });
        });
}

// Load lessons when a subject is selected
function loadLessons(subjectId) {
    fetch(`pdf-upload-handler.php?getLessons&subject_id=${subjectId}`)
        .then(response => response.json())
        .then(lessons => {
            const select = document.querySelector('#lessonSelect');
            if (!select) return;
            
            select.innerHTML = '<option selected disabled>Select Lesson</option>';
            lessons.forEach(lesson => {
                const option = document.createElement('option');
                option.value = lesson.lesson_id;
                option.textContent = lesson.lesson_name;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error loading lessons:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load lessons'
            });
        });
}

// Handle PDF upload
document.addEventListener('DOMContentLoaded', function() {
    const uploadBtn = document.querySelector('#uploadBtn');
    if (uploadBtn) {
        uploadBtn.addEventListener('click', handlePdfUpload);
    }
});

function handlePdfUpload() {
    // Show loading state
    const uploadBtn = document.querySelector('#uploadBtn');
    const originalText = uploadBtn.innerHTML;
    uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Uploading...';
    uploadBtn.disabled = true;

    // Get form data
    const formData = new FormData();
    const subject = document.querySelector('#subjectSelect').value;
    const lesson = document.querySelector('#lessonSelect').value;
    const pdfFile = document.querySelector('#pdfFile').files[0];

    // Validate inputs
    if (!subject || subject === 'Select Subject') {
        showError('Please select a subject');
        resetButton();
        return;
    }
    if (!lesson || lesson === 'Select Lesson') {
        showError('Please select a lesson');
        resetButton();
        return;
    }
    if (!pdfFile) {
        showError('Please select a PDF file');
        resetButton();
        return;
    }
    if (!pdfFile.type.includes('pdf')) {
        showError('Please select a valid PDF file');
        resetButton();
        return;
    }

    // Append data to form
    formData.append('subject', subject);
    formData.append('lesson', lesson);
    formData.append('pdfFile', pdfFile);

    // Send request
    fetch('pdf-upload-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: data.message
            });
            // Clear form
            document.querySelector('#pdfUploadForm').reset();
            document.querySelector('#lessonSelect').innerHTML = '<option selected disabled>Select Lesson</option>';
        } else {
            showError(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('An unexpected error occurred');
    })
    .finally(() => {
        resetButton();
    });

    function showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message
        });
    }

    function resetButton() {
        uploadBtn.innerHTML = originalText;
        uploadBtn.disabled = false;
    }
}





document.addEventListener('DOMContentLoaded', function () {
    let mcqCount = 0;
    let typingCount = 0;
    const MAX_MCQ = 20;
    const MAX_TYPING = 10;

    // Add MCQ question
    document.querySelector('.mcq-add-btn').addEventListener('click', function () {
        if (mcqCount >= MAX_MCQ) {
            showAlert('warning', 'Limit Reached', 'Maximum 20 MCQ questions allowed');
            return;
        }

        const mcqTemplate = `
            <div class="question-block mb-4" data-mcq-id="${mcqCount}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label fw-bold">Question ${mcqCount + 1}</label>
                    <button type="button" class="btn btn-danger btn-sm remove-mcq">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control mcq-question" placeholder="Enter your question here" required>
                </div>
                <div class="answer-options">
                    ${['A', 'B', 'C', 'D'].map(option => `
                        <div class="answer-option d-flex align-items-center mb-2">
                            <input type="radio" name="correct-${mcqCount}" class="form-check-input me-2" required>
                            <input type="text" class="form-control" placeholder="Option ${option}" required>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;

        document.querySelector('.mcq-questions').insertAdjacentHTML('beforeend', mcqTemplate);
        mcqCount++;
    });

    // Add typing question
    document.querySelector('.typing-add-btn').addEventListener('click', function () {
        if (typingCount >= MAX_TYPING) {
            showAlert('warning', 'Limit Reached', 'Maximum 10 typing questions allowed');
            return;
        }

        const typingTemplate = `
            <div class="question-block mb-4" data-typing-id="${typingCount}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label fw-bold">Question ${typingCount + 1}</label>
                    <button type="button" class="btn btn-danger btn-sm remove-typing">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control typing-question" placeholder="Enter your question here" required>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control typing-answer" placeholder="Correct answer" required>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control typing-keywords" placeholder="Keywords (comma separated)" required>
                </div>
            </div>
        `;

        document.querySelector('.typing-questions').insertAdjacentHTML('beforeend', typingTemplate);
        typingCount++;
    });

    // Remove question handlers
    document.addEventListener('click', function (e) {
        if (e.target.closest('.remove-mcq')) {
            e.target.closest('.question-block').remove();
            mcqCount--;
            updateQuestionNumbers('.mcq-questions');
        }
        if (e.target.closest('.remove-typing')) {
            e.target.closest('.question-block').remove();
            typingCount--;
            updateQuestionNumbers('.typing-questions');
        }
    });

    // Update question numbers after removal
    function updateQuestionNumbers(containerSelector) {
        const questions = document.querySelectorAll(`${containerSelector} .question-block`);
        questions.forEach((q, idx) => {
            q.querySelector('.form-label').textContent = `Question ${idx + 1}`;
        });
    }

    // Save questions with improved error handling
    document.querySelector('#saveQuestions').addEventListener('click', async function () {
        try {
            const subject = document.querySelector('#subjectSelect').value;
            const lesson = document.querySelector('#lessonSelect').value;

            if (!subject || !lesson) {
                throw new Error('Please select subject and lesson first');
            }

            // Validate and collect MCQ questions
            const mcqQuestions = validateAndCollectMCQs();
            // Validate and collect typing questions
            const typingQuestions = validateAndCollectTypingQuestions();

            // Prepare form data
            const formData = new FormData();
            formData.append('subject_id', subject);
            formData.append('lesson_id', lesson);
            formData.append('mcq_questions', JSON.stringify(mcqQuestions));
            formData.append('typing_questions', JSON.stringify(typingQuestions));

            // Show loading state
            showLoading('Saving Questions', 'Please wait...');

            // Send request
            const response = await fetch('question-management-handler.php', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (!data) {
                throw new Error('Empty response received from server');
            }

            if (data.success) {
                await showAlert('success', 'Success', data.message || 'Questions saved successfully!');
                resetForm();
            } else {
                throw new Error(data.message || 'Failed to save questions');
            }

        } catch (error) {
            console.error('Error:', error);
            await showAlert('error', 'Error', error.message || 'An unexpected error occurred');
        }
    });

    // Helper functions
    function validateAndCollectMCQs() {
        const mcqQuestions = [];
        const mcqBlocks = document.querySelectorAll('.mcq-questions .question-block');

        mcqBlocks.forEach((block, index) => {
            const question = block.querySelector('.mcq-question').value.trim();
            if (!question) {
                throw new Error(`Please fill in MCQ question ${index + 1}`);
            }

            const answers = [];
            const options = block.querySelectorAll('.answer-option');
            let hasCorrectAnswer = false;

            options.forEach(option => {
                const text = option.querySelector('input[type="text"]').value.trim();
                const isCorrect = option.querySelector('input[type="radio"]').checked;

                if (!text) {
                    throw new Error(`Please fill in all answer options for MCQ question ${index + 1}`);
                }

                if (isCorrect) {
                    hasCorrectAnswer = true;
                }

                answers.push({ text, isCorrect: isCorrect ? 1 : 0 });
            });

            if (!hasCorrectAnswer) {
                throw new Error(`Please select a correct answer for MCQ question ${index + 1}`);
            }

            mcqQuestions.push({ question, answers });
        });

        return mcqQuestions;
    }

    function validateAndCollectTypingQuestions() {
        const typingQuestions = [];
        const typingBlocks = document.querySelectorAll('.typing-questions .question-block');

        typingBlocks.forEach((block, index) => {
            const question = block.querySelector('.typing-question').value.trim();
            const answer = block.querySelector('.typing-answer').value.trim();
            const keywords = block.querySelector('.typing-keywords').value.trim();

            if (!question || !answer) {
                throw new Error(`Please fill in all fields for typing question ${index + 1}`);
            }

            typingQuestions.push({ question, correct_answer: answer, keywords });
        });

        return typingQuestions;
    }

    function showAlert(icon, title, text) {
        return Swal.fire({ icon, title, text });
    }

    function showLoading(title, text) {
        Swal.fire({
            title,
            text,
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => Swal.showLoading()
        });
    }

    function resetForm() {
        document.querySelector('.mcq-questions').innerHTML = '';
        document.querySelector('.typing-questions').innerHTML = '';
        mcqCount = 0;
        typingCount = 0;
    }
});