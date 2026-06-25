<?php
require 'includes/db_connect.php';

class BSTNode {
    public $datetimeID;
    public $datetimeStr;
    public $movieID;
    public $left;
    public $right;

    public function __construct($datetimeID, $datetimeStr, $movieID) {
        $this->datetimeID = $datetimeID;
        $this->datetimeStr = $datetimeStr;
        $this->movieID = $movieID;
        $this->left = null;
        $this->right = null;
    }
}

class ShowtimeBST {
    private $root;

    public function __construct() {
        $this->root = null;
    }

    public function insert($datetimeID, $datetimeStr, $movieID) {
        $this->root = $this->insertRec($this->root, $datetimeID, $datetimeStr, $movieID);
    }

    private function insertRec($node, $datetimeID, $datetimeStr, $movieID) {
        if ($node === null) {
            return new BSTNode($datetimeID, $datetimeStr, $movieID);
        }

        if ($datetimeStr < $node->datetimeStr) {
            $node->left = $this->insertRec($node->left, $datetimeID, $datetimeStr, $movieID);
        } elseif ($datetimeStr > $node->datetimeStr) {
            $node->right = $this->insertRec($node->right, $datetimeID, $datetimeStr, $movieID);
        }

        return $node;
    }

    public function inOrder() {
        $result = [];
        $this->inOrderRec($this->root, $result);
        return $result;
    }

    private function inOrderRec($node, &$result) {
        if ($node !== null) {
            $this->inOrderRec($node->left, $result);
            $result[] = [
                'datetimeID' => $node->datetimeID,
                'datetimeStr' => $node->datetimeStr,
                'movieID' => $node->movieID
            ];
            $this->inOrderRec($node->right, $result);
        }
    }
}

$movieStmt = $pdo->prepare("SELECT movieID, movieName, price FROM movie");
$movieStmt->execute();
$movies = $movieStmt->fetchAll(PDO::FETCH_ASSOC);

$showtimeStmt = $pdo->prepare("
    SELECT m.movieID, d.datetimeID, d.date, d.time
    FROM moviexdatetime mx
    JOIN datetime d ON mx.datetimeID = d.datetimeID
    JOIN movie m ON mx.movieID = m.movieID
");
$showtimeStmt->execute();
$showtimes = $showtimeStmt->fetchAll(PDO::FETCH_ASSOC);

$bst = new ShowtimeBST();
foreach ($showtimes as $showtime) {
    $datetimeStr = $showtime['date'] . ' ' . substr($showtime['time'], 0, 5);
    $bst->insert($showtime['datetimeID'], $datetimeStr, $showtime['movieID']);
}
$bstShowtimes = $bst->inOrder();
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookingPage</title>
    <link rel="stylesheet" href="assets/css/styles.css">

    <style>
        .seat {
            width: 30px;
            height: 30px;
            margin: 5px;
            border: 1px solid #ccc;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .seat.available {
            background-color: #fff;
        }

        .seat.selected {
            background-color: orange;
            transform: scale(1.1);
        }

        .seat.booked {
            background-color: grey;
            cursor: not-allowed;
        }

        .seat input[type="checkbox"] {
            margin: 0;
            cursor: pointer;
        }

        .seat.booked input[type="checkbox"] {
            cursor: not-allowed;
        }

        .seat-grid {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .row {
            display: flex;
        }

        .btn-container {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
<section class="reservation-form">
    <h2>Reserve Your Seat</h2>
    <form id="reservationForm" action="saveReservation.php" method="POST">
        <label for="movie">Select Movie:</label>
        <select id="movie" name="movieID" required>
            <?php foreach ($movies as $movie): ?>
                <option value="<?php echo $movie['movieID']; ?>" data-price="<?php echo $movie['price']; ?>">
                    <?php echo htmlspecialchars($movie['movieName']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="datetime">Select Date and Time : </label>
        <select id="datetime" name="datetimeID" required>
        </select>
        <input type="hidden" id="customerID" name="customerID" value="1">

        <div class="seat-selection">
            <h3>Select Your Seats : </h3>
            <div id="seatMap" class="seat-grid">
                <?php
                $rows = 5;
                $cols = 8;
                for ($i = 0; $i < $rows; $i++): ?>
                    <div class="row">
                        <?php for ($j = 0; $j < $cols; $j++):
                            $seatID = ($i * $cols) + $j + 1;
                        ?>
                            <div class="seat available" data-row="<?php echo $i; ?>" data-col="<?php echo $j; ?>" data-seat-id="<?php echo $seatID; ?>">
                                <input type="checkbox" name="seats[]" value="<?php echo $seatID; ?>">
                            </div>
                        <?php endfor; ?>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <div class="ticket-summary">
            <p>Total Tickets: <span id="ticket-count">0</span></p>
            <p>Total Price: <span id="ticket-price">0 B</span></p>
        </div>

        <div class="btn-container">
            <button type="button" class="btn" id="resetSeats">Reset</button>
            <button type="submit" class="btn">Book Now</button>
        </div>
    </form>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const movieSelect = document.getElementById('movie');
    const datetimeSelect = document.getElementById('datetime');
    const ticketCountSpan = document.getElementById('ticket-count');
    const ticketPriceSpan = document.getElementById('ticket-price');
    let pricePerTicket = parseFloat(movieSelect.options[0].dataset.price);

    const showtimes = <?php echo json_encode($bstShowtimes); ?>;

    movieSelect.addEventListener('change', () => {
        const selectedOption = movieSelect.options[movieSelect.selectedIndex];
        pricePerTicket = parseFloat(selectedOption.dataset.price);
        updateDatetimeOptions();
        updateTotalPrice();
        updateSeatAvailability();
    });

    function updateDatetimeOptions() {
        const selectedMovieID = movieSelect.value;
        datetimeSelect.innerHTML = '';

        const availableDatetimes = showtimes
            .filter(showtime => showtime.movieID == selectedMovieID)
            .map(showtime => ({
                datetimeID: showtime.datetimeID,
                text: showtime.datetimeStr
            }));

        if (availableDatetimes.length === 0) {
            datetimeSelect.innerHTML = '<option value="">No showtimes available</option>';
        } else {
            availableDatetimes.forEach(({ datetimeID, text }) => {
                const option = document.createElement('option');
                option.value = datetimeID;
                option.textContent = text;
                datetimeSelect.appendChild(option);
            });
        }

        updateSeatAvailability();
    }

    datetimeSelect.addEventListener('change', updateSeatAvailability);

    async function updateSeatAvailability() {
        const movieID = movieSelect.value;
        const datetimeID = datetimeSelect.value;

        if (!movieID || !datetimeID) return;

        const response = await fetch('getBookedSeats.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ movieID, datetimeID })
        });
        const bookedSeats = await response.json();

        const seatElements = document.querySelectorAll('.seat');
        seatElements.forEach(seat => {
            const seatID = parseInt(seat.dataset.seatId);
            const checkbox = seat.querySelector('input[type="checkbox"]');
            if (bookedSeats.includes(seatID)) {
                seat.classList.remove('available', 'selected');
                seat.classList.add('booked');
                checkbox.disabled = true;
                checkbox.checked = false;
            } else {
                seat.classList.remove('booked');
                seat.classList.add('available');
                checkbox.disabled = false;
            }
        });

        updateTotalPrice();
    }

    const seatCheckboxes = document.querySelectorAll('.seat input[type="checkbox"]');
    seatCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            const seat = checkbox.parentElement;
            if (checkbox.checked) {
                seat.classList.add('selected');
            } else {
                seat.classList.remove('selected');
            }
            updateTotalPrice();
        });
    });

    function updateTotalPrice() {
        const selectedSeats = document.querySelectorAll('.seat input:checked');
        const totalTickets = selectedSeats.length;
        const totalPrice = totalTickets * pricePerTicket;
        ticketCountSpan.textContent = totalTickets;
        ticketPriceSpan.textContent = totalPrice + ' B';
    }

    const resetButton = document.getElementById('resetSeats');
    resetButton.addEventListener('click', () => {
        const seatElements = document.querySelectorAll('.seat');
        seatElements.forEach(seat => {
            if (!seat.classList.contains('booked')) {
                seat.classList.remove('selected');
                const checkbox = seat.querySelector('input[type="checkbox"]');
                checkbox.checked = false;
            }
        });
        updateTotalPrice();
    });

    const reservationForm = document.getElementById('reservationForm');
    reservationForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const selectedSeats = document.querySelectorAll('.seat input:checked');
        if (selectedSeats.length === 0) {
            alert('Please select at least one seat.');
            return;
        }

        if (!datetimeSelect.value) {
            alert('Please select a valid datetime.');
            return;
        }

        const formData = new FormData(reservationForm);
        const count = parseInt(ticketCountSpan.textContent);
        formData.append('price', pricePerTicket.toFixed(2));
        formData.append('count', count);

        try {
            const response = await fetch('saveReservation.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                const customerID = document.getElementById('customerID').value;
                const seatCount = selectedSeats.length;

                window.location.href = `bookingConfirmation.php?customerID=${customerID}&seatCount=${seatCount}`;

                selectedSeats.forEach(checkbox => {
                    const seat = checkbox.parentElement;
                    seat.classList.remove('selected', 'available');
                    seat.classList.add('booked');
                    checkbox.disabled = true;
                    checkbox.checked = false;
                });
                ticketCountSpan.textContent = '0';
                ticketPriceSpan.textContent = '0 B';
                alert(data.message);
            } else {
                alert(data.message || 'Booking failed. Please try again.');
            }
        } catch (error) {
            alert('Booking failed: ' + error.message);
        }
    });

    updateDatetimeOptions();
});
</script>
</body>
</html>