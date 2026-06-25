<?php
require 'includes/db_connect.php';
require 'includes/header.php';

class MovieNode {
    public $movieName;
    public $comingStatus;
    public $next;

    public function __construct($movieName, $comingStatus) {
        $this->movieName = $movieName;
        $this->comingStatus = $comingStatus;
        $this->next = null;
    }
}

class UpcomingMovieQueue {
    private $head;
    private $tail;

    public function __construct() {
        $this->head = null;
        $this->tail = null;
    }

    public function enqueue($movieName, $comingStatus) {
        $newNode = new MovieNode($movieName, $comingStatus);
        if ($this->head === null) {
            $this->head = $newNode;
            $this->tail = $newNode;
        } else {
            $this->tail->next = $newNode;
            $this->tail = $newNode;
        }
    }

    public function getMovies() {
        $movies = [];
        $current = $this->head;
        while ($current !== null) {
            $movies[] = [
                'movieName' => $current->movieName,
                'comingStatus' => $current->comingStatus
            ];
            $current = $current->next;
        }
        return $movies;
    }
}

$selectedCategories = isset($_GET['categories']) ? array_map('intval', $_GET['categories']) : [];
$selectedLanguages = isset($_GET['languages']) ? array_map('intval', $_GET['languages']) : [];
$selectedTime = isset($_GET['time']) ? $_GET['time'] : '';

$sql = "SELECT DISTINCT m.*, c.categoryName 
        FROM movie m 
        JOIN moviecategory c ON m.categoryID = c.categoryID";
$params = [];

if (!empty($selectedLanguages)) {
    $sql .= " JOIN moviexlanguage ml ON m.movieID = ml.movieID";
}
if (!empty($selectedTime)) {
    $sql .= " JOIN moviexcinema mc ON m.movieID = mc.movieID";
}

$whereClauses = [];
if (!empty($selectedCategories)) {
    $placeholders = implode(',', array_fill(0, count($selectedCategories), '?'));
    $whereClauses[] = "m.categoryID IN ($placeholders)";
    $params = array_merge($params, $selectedCategories);
}
if (!empty($selectedLanguages)) {
    $placeholders = implode(',', array_fill(0, count($selectedLanguages), '?'));
    $whereClauses[] = "ml.languageID IN ($placeholders)";
    $params = array_merge($params, $selectedLanguages);
}
if ($selectedTime === 'morning') {
    $whereClauses[] = "HOUR(mc.datetime) < 12";
} elseif ($selectedTime === 'night') {
    $whereClauses[] = "HOUR(mc.datetime) >= 18";
}

if (!empty($whereClauses)) {
    $sql .= " WHERE " . implode(' AND ', $whereClauses);
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM upcomingmovie");
$upcomingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
$upcomingQueue = new UpcomingMovieQueue();
foreach ($upcomingData as $upcoming) {
    $upcomingQueue->enqueue($upcoming['movieName'], $upcoming['comingStatus']);
}
$upcomingMovies = $upcomingQueue->getMovies();

$stmt = $pdo->query("SELECT * FROM moviecategory");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM language");
$languages = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
Big-O Analysis:
- Data Structures:
  - Array (movies, categories, languages, selectedCategories):
    - Access: O(1)
    - Insertion/Deletion: O(n)
    - Space: O(n)
  - Linked List (upcoming movies queue):
    - Enqueue (insert at tail): O(1) with tail pointer
    - Traversal (getMovies): O(n) to visit all nodes
    - Space: O(n) for n upcoming movies
- Algorithms:
  - Searching (searchMovies function): O(n) linear search over movie titles
  - Sorting: Not explicitly used in this file (categories could be sorted in database or PHP, O(n log n) if applied)
- Linked List Advantage: Efficient O(1) enqueue for adding upcoming movies, suitable for FIFO queue behavior.
*/

?>

<section class="main-container">
    <div class="sidebar">
        <form action="index.php" method="GET">
            <div class="sidebar-groups">
                <h3 class="sg-title">Categories</h3>
                <?php foreach ($categories as $category): ?>
                    <input type="checkbox" 
                           id="cat_<?php echo $category['categoryID']; ?>" 
                           name="categories[]" 
                           value="<?php echo $category['categoryID']; ?>" 
                           <?php echo in_array($category['categoryID'], $selectedCategories) ? 'checked' : ''; ?>>
                    <label for="cat_<?php echo $category['categoryID']; ?>">
                        <?php echo htmlspecialchars($category['categoryName']); ?>
                    </label><br>
                <?php endforeach; ?>
            </div>
            <div class="sidebar-groups">
                <h3 class="sg-title">Language</h3>
                <?php foreach ($languages as $language): ?>
                    <input type="checkbox" 
                           id="lang_<?php echo $language['languageID']; ?>" 
                           name="languages[]" 
                           value="<?php echo $language['languageID']; ?>" 
                           <?php echo in_array($language['languageID'], $selectedLanguages) ? 'checked' : ''; ?>>
                    <label for="lang_<?php echo $language['languageID']; ?>">
                        <?php echo htmlspecialchars($language['languageName']); ?>
                    </label><br>
                <?php endforeach; ?>
            </div>
            <div class="sidebar-groups">
                <h3 class="sg-title">Time</h3>
                <!-- <input type="radio" 
                       id="morning" 
                       name="time" 
                       value="morning" 
                       <?php echo $selectedTime === 'morning' ? 'checked' : ''; ?>>
                <label for="morning">Morning</label><br>
                <input type="radio" 
                       id="night" 
                       name="time" 
                       value="night" 
                       <?php echo $selectedTime === 'night' ? 'checked' : ''; ?>>
                <label for="night">Night</label><br> -->
                <input type="radio" 
                       id="all_times" 
                       name="time" 
                       value="" 
                       <?php echo empty($selectedTime) ? 'checked' : ''; ?>>
                <label for="all_times">All Times</label><br>
            </div>
            <div class="sidebar-groups">
                <button type="submit" class="btn-l btn">Apply Filters</button>
            </div>
        </form>
    </div>

    <div class="movies-container">
        <div class="upcoming-img-box">
            <img src="assets/image/upcoming.webp" alt="">
            <p class="upcoming-title"><a href="#howitworks">Upcoming Movie</a></p>
            <div class="buttons">
                <a href="#reservationForm" class="btn">Book Now</a>
                <a href="#" class="btn-alt btn">Play Trailer</a>
            </div>
        </div>

        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search for a movie...">
            <button class="btn" onclick="searchMovies()">Search</button>
        </div>

        <div class="current-movies">
            <?php if (empty($movies)): ?>
                <p>No movies match your filters.</p>
            <?php else: ?>
                <?php foreach ($movies as $movie): ?>
                    <div class="current-movie">
                        <div class="cm-img-box">
                            <img src="assets/image/<?php echo htmlspecialchars($movie['image']); ?>" alt="">
                        </div>
                        <h3 class="movie-title"><?php echo htmlspecialchars($movie['movieName']); ?></h3>
                        <p class="screen-name">Screen: <?php echo htmlspecialchars($movie['screen']); ?></p>
                        <div class="movie-info">
                            ⭐ <?php echo htmlspecialchars($movie['rating']); ?> | ⏱ <?php echo htmlspecialchars($movie['duration']); ?>
                            <p><?php echo htmlspecialchars($movie['sub_title']); ?></p>
                        </div>
                        <div class="booking">
                            <h2 class="price"><?php echo htmlspecialchars($movie['price']); ?>฿</h2>
                            <a href="#reservationForm" class="btn">Buy Tickets</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="how-it-works" id="howitworks">
    <section class="upcoming-movies">
        <h2>Coming Soon</h2>
        <div class="movie-scroll">
            <?php foreach ($upcomingMovies as $upcoming): ?>
                <div class="upcoming-card">
                    <p><?php echo htmlspecialchars($upcoming['movieName']); ?></p>
                    <span><?php echo htmlspecialchars($upcoming['comingStatus']); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</section>

<section class="how-it-works">
    <h2>How It Works</h2>
    <div class="steps">
        <div class="step">
            <span>🎬</span>
            <h3>Select Movie</h3>
            <p>Browse from the latest blockbusters to timeless classics.</p>
        </div>
        <div class="step">
            <span>🪑</span>
            <h3>Choose Seats</h3>
            <p>Pick your perfect spot from the seat map.</p>
        </div>
        <div class="step">
            <span>💳</span>
            <h3>Pay Securely</h3>
            <p>Use card, PayPal or mobile wallet — 100% secure.</p>
        </div>
        <div class="step">
            <span>📱</span>
            <h3>Get e-Ticket</h3>
            <p>Check your email or app to access your ticket instantly.</p>
        </div>
    </div>
</section>

<?php 
require 'BookingPage.php';
?>

<section class="contact-form">
    <h2>Contact Us</h2>
    <form id="contactForm" method="POST" action="contact.php">
        <label for="contact_name">Your Name:</label>
        <input type="text" id="contact_name" name="name" required>

        <label for="contact_email">Your Email:</label>
        <input type="email" id="contact_email" name="email" required>

        <label for="message">Your Message:</label>
        <textarea id="message" name="message" required></textarea>

        <button type="submit" class="btn">Send Message</button>
    </form>
</section>

<div id="payment-modal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2 class="payment-caption">Payment</h2>
        <form action="#">
            <label for="payment-method">Select Payment Method:</label>
            <select id="payment-method" name="payment-method">
                <option value="credit-card">Credit Card</option>
                <option value="paypal">PayPal</option>
            </select>
            <br><br>
            <input type="submit" value="Pay Now" class="btn">
        </form>
    </div>
</div>

<script>
function searchMovies() {
    const input = document.getElementById("searchInput").value.toLowerCase();
    const movies = document.querySelectorAll(".current-movie");

    movies.forEach(movie => {
        const title = movie.querySelector(".movie-title").textContent.toLowerCase();
        movie.style.display = title.includes(input) ? "block" : "none";
    });
}

const upcomingButton = document.querySelector('a[href="#howitworks"]');
if (upcomingButton) {
    upcomingButton.addEventListener('click', (event) => {
        event.preventDefault();
        const upcomingSection = document.getElementById('howitworks');
        upcomingSection.scrollIntoView({ behavior: 'smooth' });
    });
}

// Reservation Form Functionality
/*document.addEventListener('DOMContentLoaded', () => {
    const seats = document.querySelectorAll('.seat.available');
    const ticketInput = document.getElementById('tickets');
    const ticketPriceSpan = document.getElementById('ticket-price');
    const movieSelect = document.getElementById('movie');
    let pricePerTicket = movieSelect ? movieSelect.options[movieSelect.selectedIndex].dataset.price : 0;

    // Update price per ticket based on selected movie
    if (movieSelect) {
        movieSelect.addEventListener('change', () => {
            pricePerTicket = movieSelect.options[movieSelect.selectedIndex].dataset.price;
            const selectedSeats = document.querySelectorAll('.seat.selected');
            const totalTickets = selectedSeats.length;
            ticketInput.value = totalTickets;
            ticketPriceSpan.textContent = (totalTickets * pricePerTicket) + ' ฿';
        });
    }*/

    // Handle Seat Selection
 /*   seats.forEach(seat => {
        seat.addEventListener('click', () => {
            if (!seat.classList.contains('booked')) {
                seat.classList.toggle('selected');
                const checkbox = seat.querySelector('input[type="checkbox"]');
                checkbox.checked = seat.classList.contains('selected');

                const selectedSeats = document.querySelectorAll('.seat.selected');
                const totalTickets = selectedSeats.length;
                ticketInput.value = totalTickets;
                ticketPriceSpan.textContent = (totalTickets * pricePerTicket) + ' ฿';
            }
        });
    });*/

    // Smooth Scroll for "Book Now" Button
   /* const bookNowButtons = document.querySelectorAll('.btn[href="#reservationForm"]');
    bookNowButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            const reservationSection = document.getElementById('reservationForm');
            reservationSection.scrollIntoView({ behavior: 'smooth' });
        });
    });
});*/
</script>

<?php
require 'includes/footer.php';
?>