const token = localStorage.getItem("token");
let favouriteCareerIds = new Set();
let allCareers = [];
let activeCategory = "all";
let currentPage = 1;
const careersPerPage = 6;

if (!token) {
    window.location.href = "login.html";
}

document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("welcome").textContent = `Welcome, ${localStorage.getItem("userName") || "User"}!`;

    refreshDashboard();

    document.getElementById("logoutBtn").addEventListener("click", function() {
        localStorage.removeItem("token");
        localStorage.removeItem("userName");
        window.location.href = "login.html";
    });
});

async function refreshDashboard() {
    await loadLatestQuizResult();
    await loadFavourites();
    await loadCareers();
}

async function loadLatestQuizResult() {
    const resultContainer = document.getElementById("latest-quiz-result");

    try {
        const response = await fetch("http://localhost/Life-Discovery-Backend/api/quiz/latest.php", {
            method: "GET",
            headers: {
                "Authorization": `Bearer ${token}`
            }
        });
        const data = await response.json();

        if (!data.success || !data.result) {
            resultContainer.innerHTML = `
                <p>You have not taken the quiz yet.</p>
                <a href="quiz.html" class="quiz-summary-btn">Take Quiz</a>
            `;
            return;
        }

        resultContainer.innerHTML = `
            <span class="career-category">${formatCategoryLabel(data.result.top_category)}</span>
            <p class="quiz-summary-text">
                Your latest result suggests <strong>${formatCategoryLabel(data.result.top_category)}</strong> careers may be a strong fit.
            </p>
            ${renderTopCategoryMatches(data.result.scores)}
            <p class="quiz-summary-text">
                Suggested careers: ${data.result.recommended_careers.map(career => career.name).join(", ") || "More careers coming soon."}
            </p>
            <a href="quiz.html" class="quiz-summary-btn">Retake Quiz</a>
        `;
    } catch (error) {
        console.error("Error loading latest quiz result:", error);
        resultContainer.innerHTML = `
            <p>Failed to load your quiz result.</p>
            <a href="quiz.html" class="quiz-summary-btn">Retake Quiz</a>
        `;
    }
}

async function loadCareers() {
    try {
        const response = await fetch("http://localhost/Life-Discovery-Backend/api/careers.php");
        allCareers = await response.json();

        renderCategoryDropdown();
        renderCareers();
    } catch (error) {
        console.error("Error loading careers:", error);
        document.getElementById("careers-list").innerHTML = "<p>Failed to load careers.</p>";
    }
}

function renderCategoryDropdown() {
    const categoryFilter = document.getElementById("category-filter");
    const categories = ["all", ...new Set(allCareers.map(career => career.category).filter(Boolean))];

    categoryFilter.innerHTML = categories.map(category => `
        <option value="${category}" ${category === activeCategory ? "selected" : ""}>
            ${formatCategoryLabel(category)}
        </option>
    `).join("");

    categoryFilter.onchange = function() {
        activeCategory = this.value;
        currentPage = 1;
        renderCareers();
    };
}

function renderCareers() {
    const careersList = document.getElementById("careers-list");
    const filteredCareers = activeCategory === "all"
        ? allCareers
        : allCareers.filter(career => career.category === activeCategory);
    const totalPages = Math.max(1, Math.ceil(filteredCareers.length / careersPerPage));

    if (currentPage > totalPages) {
        currentPage = totalPages;
    }

    const startIndex = (currentPage - 1) * careersPerPage;
    const visibleCareers = filteredCareers.slice(startIndex, startIndex + careersPerPage);

    careersList.innerHTML = "";
    renderPagination(filteredCareers.length, totalPages);

    if (visibleCareers.length === 0) {
        careersList.innerHTML = "<p>No careers found in this category yet.</p>";
        return;
    }

    visibleCareers.forEach(career => {
        const isFavourite = favouriteCareerIds.has(String(career.id));
        const careerCard = document.createElement("div");
        careerCard.className = "career-card";
        careerCard.innerHTML = `
            <span class="career-category">${formatCategoryLabel(career.category)}</span>
            <h4>${career.name}</h4>
            <p>${career.short_description || "No description available."}</p>
            <p class="career-meta"><strong>Education:</strong> ${career.education_required || "Flexible"}</p>
            <p class="career-meta"><strong>Time to start:</strong> ${career.time_to_start || "Varies"}</p>
            <button class="fav-btn ${isFavourite ? "favourited" : ""}" data-career-id="${career.id}" data-is-favourite="${isFavourite}">
                ${isFavourite ? "Saved" : "Save to Favourites"}
            </button>
        `;
        careersList.appendChild(careerCard);
    });

    careersList.querySelectorAll(".fav-btn").forEach(btn => {
        btn.addEventListener("click", async function() {
            const careerId = this.getAttribute("data-career-id");
            const isFavourite = this.getAttribute("data-is-favourite") === "true";

            if (isFavourite) {
                await removeFromFavourites(careerId);
            } else {
                await addToFavourites(careerId);
            }
        });
    });
}

function renderPagination(totalCareers, totalPages) {
    const paginationInfo = document.getElementById("pagination-info");
    const paginationContainer = document.getElementById("careers-pagination");

    if (totalCareers === 0) {
        paginationInfo.textContent = "";
        paginationContainer.innerHTML = "";
        return;
    }

    paginationInfo.textContent = `Showing page ${currentPage} of ${totalPages} (${totalCareers} careers)`;
    paginationContainer.innerHTML = `
        <button type="button" id="prev-page" ${currentPage === 1 ? "disabled" : ""}>Previous</button>
        <span class="page-count">Page ${currentPage} of ${totalPages}</span>
        <button type="button" id="next-page" ${currentPage === totalPages ? "disabled" : ""}>Next</button>
    `;

    document.getElementById("prev-page").addEventListener("click", function() {
        if (currentPage > 1) {
            currentPage -= 1;
            renderCareers();
        }
    });

    document.getElementById("next-page").addEventListener("click", function() {
        if (currentPage < totalPages) {
            currentPage += 1;
            renderCareers();
        }
    });
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

async function loadFavourites() {
    try {
        const response = await fetch("http://localhost/Life-Discovery-Backend/api/favourites.php", {
            method: "GET",
            headers: {
                "Authorization": `Bearer ${token}`
            }
        });
        const data = await response.json();

        if (data.success) {
            favouriteCareerIds = new Set(data.favourites.map(career => String(career.id)));

            const favouritesList = document.getElementById("favourites-list");
            favouritesList.innerHTML = "";

            if (data.favourites.length === 0) {
                favouritesList.innerHTML = "<p>No favourite careers yet.</p>";
                return;
            }

            data.favourites.forEach(career => {
                const careerCard = document.createElement("div");
                careerCard.className = "career-card";
                careerCard.innerHTML = `
                    <span class="career-category">${formatCategoryLabel(career.category)}</span>
                    <h4>${career.name}</h4>
                    <p>${career.short_description || "No description available."}</p>
                    <p class="career-meta"><strong>Education:</strong> ${career.education_required || "Flexible"}</p>
                    <p class="career-meta"><strong>Time to start:</strong> ${career.time_to_start || "Varies"}</p>
                    <button class="fav-btn favourited" data-career-id="${career.id}">Unsave</button>
                `;
                favouritesList.appendChild(careerCard);
            });

            favouritesList.querySelectorAll(".fav-btn").forEach(btn => {
                btn.addEventListener("click", async function() {
                    const careerId = this.getAttribute("data-career-id");
                    await removeFromFavourites(careerId);
                });
            });
        } else {
            favouriteCareerIds = new Set();
            document.getElementById("favourites-list").innerHTML = "<p>Failed to load favourites.</p>";
        }
    } catch (error) {
        favouriteCareerIds = new Set();
        console.error("Error loading favourites:", error);
        document.getElementById("favourites-list").innerHTML = "<p>Failed to load favourites.</p>";
    }
}

async function addToFavourites(careerId) {
    try {
        const response = await fetch("http://localhost/Life-Discovery-Backend/api/favourites.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": `Bearer ${token}`
            },
            body: JSON.stringify({ career_id: careerId })
        });
        const data = await response.json();

        if (data.success) {
            await refreshDashboard();
        } else {
            alert("Failed to add to favourites: " + (data.error || "Unknown error"));
        }
    } catch (error) {
        console.error("Error adding to favourites:", error);
        alert("Failed to add to favourites.");
    }
}

async function removeFromFavourites(careerId) {
    try {
        const response = await fetch("http://localhost/Life-Discovery-Backend/api/favourites.php", {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json",
                "Authorization": `Bearer ${token}`
            },
            body: JSON.stringify({ career_id: careerId })
        });
        const data = await response.json();

        if (data.success) {
            await refreshDashboard();
        } else {
            alert("Failed to remove from favourites: " + (data.error || "Unknown error"));
        }
    } catch (error) {
        console.error("Error removing from favourites:", error);
        alert("Failed to remove from favourites.");
    }
}
