const token = localStorage.getItem("token");
let quizQuestions = [];
let currentQuestionIndex = 0;
let quizAnswers = {};

if (!token) {
    window.location.href = "login.html";
}

document.addEventListener("DOMContentLoaded", function() {
    loadQuizQuestions();

    document.getElementById("logoutBtn").addEventListener("click", function() {
        localStorage.removeItem("token");
        localStorage.removeItem("userName");
        window.location.href = "login.html";
    });
});

async function loadQuizQuestions() {
    const quizForm = document.getElementById("quiz-form");

    try {
        const response = await fetch("http://localhost/Life-Discovery-Backend/api/quiz/questions.php");
        const data = await response.json();

        if (!data.success) {
            quizForm.innerHTML = "<p>Failed to load quiz questions.</p>";
            return;
        }

        quizQuestions = data.questions || [];
        currentQuestionIndex = 0;
        quizAnswers = {};

        if (quizQuestions.length === 0) {
            quizForm.innerHTML = "<p>No quiz questions are available right now.</p>";
            return;
        }

        renderCurrentQuestion();
    } catch (error) {
        console.error("Error loading quiz:", error);
        quizForm.innerHTML = "<p>Failed to load quiz questions.</p>";
    }
}

function renderCurrentQuestion() {
    const quizForm = document.getElementById("quiz-form");
    const question = quizQuestions[currentQuestionIndex];
    const selectedOptionId = quizAnswers[question.id] || null;
    const progressPercent = Math.round(((currentQuestionIndex + 1) / quizQuestions.length) * 100);
    const isLastQuestion = currentQuestionIndex === quizQuestions.length - 1;

    quizForm.innerHTML = `
        <div class="quiz-progress-wrap">
            <div class="quiz-progress-top">
                <span>Question ${currentQuestionIndex + 1} of ${quizQuestions.length}</span>
                <strong>${progressPercent}%</strong>
            </div>
            <div class="quiz-progress-bar">
                <div class="quiz-progress-fill" style="width: ${progressPercent}%"></div>
            </div>
        </div>

        <div class="quiz-question-card">
            <h3>${currentQuestionIndex + 1}. ${question.question_text}</h3>
            <div class="quiz-options">
                ${question.options.map(option => `
                    <label class="quiz-option ${selectedOptionId === option.id ? "selected" : ""}">
                        <input
                            type="radio"
                            name="question_${question.id}"
                            value="${option.id}"
                            data-question-id="${question.id}"
                            ${selectedOptionId === option.id ? "checked" : ""}
                        />
                        <span>${option.option_text}</span>
                    </label>
                `).join("")}
            </div>
        </div>

        <div class="quiz-nav-actions">
            <button type="button" id="quiz-prev-btn" ${currentQuestionIndex === 0 ? "disabled" : ""}>Previous</button>
            <button type="button" id="quiz-next-btn" ${!selectedOptionId ? "disabled" : ""}>
                ${isLastQuestion ? "See My Results" : "Next"}
            </button>
        </div>
    `;

    quizForm.querySelectorAll('input[type="radio"]').forEach(input => {
        input.addEventListener("change", function() {
            quizAnswers[question.id] = Number(this.value);
            renderCurrentQuestion();
        });
    });

    document.getElementById("quiz-prev-btn").addEventListener("click", function() {
        if (currentQuestionIndex > 0) {
            currentQuestionIndex -= 1;
            renderCurrentQuestion();
        }
    });

    document.getElementById("quiz-next-btn").addEventListener("click", function() {
        if (!quizAnswers[question.id]) {
            return;
        }

        if (isLastQuestion) {
            submitQuiz();
            return;
        }

        currentQuestionIndex += 1;
        renderCurrentQuestion();
    });
}

async function submitQuiz() {
    const answers = Object.entries(quizAnswers).map(([questionId, optionId]) => ({
        question_id: Number(questionId),
        option_id: Number(optionId)
    }));

    if (answers.length !== quizQuestions.length) {
        alert("Please answer every question before submitting.");
        return;
    }

    try {
        const response = await fetch("http://localhost/Life-Discovery-Backend/api/quiz/submit.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": `Bearer ${token}`
            },
            body: JSON.stringify({ answers })
        });

        const data = await response.json();

        if (!data.success) {
            alert(data.message || "Failed to submit quiz.");
            return;
        }

        renderQuizResult(data);
    } catch (error) {
        console.error("Error submitting quiz:", error);
        alert("Failed to submit quiz.");
    }
}

function renderQuizResult(data) {
    const resultSection = document.getElementById("quiz-result");
    const quizForm = document.getElementById("quiz-form");
    const formattedCategory = formatCategoryLabel(data.top_category);

    quizForm.innerHTML = "";
    resultSection.classList.remove("hidden");
    resultSection.innerHTML = `
        <h3>Your strongest match: ${formattedCategory}</h3>
        ${renderTopCategoryMatches(data.scores)}
        <p>Based on your answers, these careers may fit you well:</p>
        <div class="careers-grid">
            ${data.recommended_careers.map(career => `
                <div class="career-card">
                    <span class="career-category">${formatCategoryLabel(career.category)}</span>
                    <h4>${career.name}</h4>
                    <p>${career.short_description || "No description available."}</p>
                    <p class="career-meta"><strong>Education:</strong> ${career.education_required || "Flexible"}</p>
                    <p class="career-meta"><strong>Time to start:</strong> ${career.time_to_start || "Varies"}</p>
                </div>
            `).join("")}
        </div>
        <a href="dashboard.html" class="quiz-dashboard-link">Back to Dashboard</a>
    `;

    resultSection.scrollIntoView({ behavior: "smooth" });
}

function formatCategoryLabel(category) {
    if (!category) {
        return "General";
    }

    return category
        .split("-")
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(" ");
}

function renderTopCategoryMatches(scores) {
    const scoreEntries = Object.entries(scores || {});
    const totalScore = scoreEntries.reduce((sum, [, score]) => sum + Number(score), 0);

    if (totalScore === 0) {
        return "";
    }

    return `
        <div class="match-breakdown">
            ${scoreEntries
                .sort((a, b) => Number(b[1]) - Number(a[1]))
                .slice(0, 3)
                .map(([category, score]) => {
                    const percentage = Math.round((Number(score) / totalScore) * 100);

                    return `
                        <div class="match-row">
                            <div class="match-row-top">
                                <span>${formatCategoryLabel(category)}</span>
                                <strong>${percentage}%</strong>
                            </div>
                            <div class="match-bar">
                                <div class="match-bar-fill" style="width: ${percentage}%"></div>
                            </div>
                        </div>
                    `;
                })
                .join("")}
        </div>
    `;
}
