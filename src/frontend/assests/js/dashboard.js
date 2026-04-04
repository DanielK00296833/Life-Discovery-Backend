const token = localStorage.getItem("token");

if (!token) {
    window.location.href = "login.html";
}

document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("welcome").textContent = `Welcome, ${localStorage.getItem("userName") || "User"}!`;

    loadCareers();
    loadFavourites();

    document.getElementById("logoutBtn").addEventListener("click", function() {
        localStorage.removeItem("token");
        localStorage.removeItem("userName");
        window.location.href = "login.html";
    });
});

async function loadCareers() {
    try {
        const response = await fetch("http://localhost/Life-Discovery-Backend/api/careers.php");
        const careers = await response.json();

        const careersList = document.getElementById("careers-list");
        careersList.innerHTML = "";

        careers.forEach(career => {
            const careerCard = document.createElement("div");
            careerCard.className = "career-card";
            careerCard.innerHTML = `
                <h4>${career.title}</h4>
                <p>${career.description || "No description available."}</p>
                <button class="fav-btn" data-career-id="${career.id}">Save to Favourites</button>
            `;
            careersList.appendChild(careerCard);
        });

        // Add event listeners to fav buttons
        document.querySelectorAll(".fav-btn").forEach(btn => {
            btn.addEventListener("click", async function() {
                const careerId = this.getAttribute("data-career-id");
                await addToFavourites(careerId, this);
            });
        });
    } catch (error) {
        console.error("Error loading careers:", error);
        document.getElementById("careers-list").innerHTML = "<p>Failed to load careers.</p>";
    }
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
                    <h4>${career.title}</h4>
                    <p>${career.description || "No description available."}</p>
                    <button class="fav-btn favourited" disabled>Saved</button>
                `;
                favouritesList.appendChild(careerCard);
            });
        } else {
            document.getElementById("favourites-list").innerHTML = "<p>Failed to load favourites.</p>";
        }
    } catch (error) {
        console.error("Error loading favourites:", error);
        document.getElementById("favourites-list").innerHTML = "<p>Failed to load favourites.</p>";
    }
}

async function addToFavourites(careerId, button) {
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
            button.textContent = "Saved";
            button.classList.add("favourited");
            button.disabled = true;
            // Reload favourites
            loadFavourites();
        } else {
            alert("Failed to add to favourites: " + (data.error || "Unknown error"));
        }
    } catch (error) {
        console.error("Error adding to favourites:", error);
        alert("Failed to add to favourites.");
    }
}
