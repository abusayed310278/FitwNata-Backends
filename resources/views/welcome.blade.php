<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitwnata - Coming Soon</title>

    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        /* --- General Styles & Reset --- */
        :root {
            --primary-color: #006A61; /* A deep teal sampled from your designs */
            --accent-color: #26A69A;  /* A lighter teal for highlights */
            --text-color: #ffffff;
            --light-gray: #f0f0f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--primary-color);
            color: var(--text-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            text-align: center;
            padding: 20px;
            overflow: hidden;
            position: relative;
        }

        /* --- Background Shape Decorations --- */
        body::before, body::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            filter: blur(50px);
            z-index: 1;
        }

        body::before {
            top: -100px;
            left: -100px;
        }

        body::after {
            bottom: -150px;
            right: -150px;
        }

        /* --- Main Content Container --- */
        .container {
            max-width: 700px;
            width: 100%;
            z-index: 2;
            animation: fadeIn 1.5s ease-out;
        }

        /* --- Logo --- */
        .logo {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 25px;
            letter-spacing: 1px;
            /* Simple representation of your logo's text */
        }

        /* --- Headings & Text --- */
        h1 {
            font-size: 3rem;
            font-weight: 600;
            margin-bottom: 15px;
            line-height: 1.2;
        }

        p {
            font-size: 1.1rem;
            font-weight: 300;
            max-width: 500px;
            margin: 0 auto 40px auto;
            opacity: 0.9;
        }

        /* --- Countdown Timer --- */
        #countdown {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 40px;
        }

        .countdown-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            min-width: 100px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .countdown-item span {
            display: block;
            font-size: 2.5rem;
            font-weight: 600;
        }

        .countdown-item p {
            font-size: 0.9rem;
            margin: 0;
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.8;
        }

        /* --- Signup Form --- */
        .signup-form {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            max-width: 450px;
            margin: 0 auto;
        }

        .signup-form input {
            flex-grow: 1;
            padding: 15px;
            border: 1px solid var(--accent-color);
            border-radius: 8px;
            font-size: 1rem;
            background: var(--light-gray);
            color: #333;
            font-family: 'Poppins', sans-serif;
        }

        .signup-form input:focus {
            outline: none;
            box-shadow: 0 0 0 3px var(--accent-color);
        }

        .signup-form button {
            padding: 15px 25px;
            border: none;
            border-radius: 8px;
            background: var(--accent-color);
            color: var(--text-color);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .signup-form button:hover {
            background-color: #34c7b9; /* A brighter teal on hover */
            transform: translateY(-2px);
        }

        /* --- Animations --- */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* --- Responsive Design --- */
        @media (max-width: 600px) {
            h1 {
                font-size: 2.2rem;
            }

            p {
                font-size: 1rem;
            }

            #countdown {
                gap: 10px;
            }

            .countdown-item {
                padding: 15px;
                min-width: 75px;
            }

            .countdown-item span {
                font-size: 2rem;
            }

            .signup-form {
                flex-direction: column;
                width: 100%;
            }

            .signup-form input,
            .signup-form button {
                width: 100%;
            }
        }

    </style>
</head>
<body>

    <div class="container">

        <div class="logo">fitwnata</div>

        <h1>A New Era of Fitness is Coming!</h1>

        <p>Our personalized wellness platform is launching soon. Be the first to know and get exclusive early access.</p>

        <div id="countdown">
            <div class="countdown-item">
                <span id="days">00</span>
                <p>Days</p>
            </div>
            <div class="countdown-item">
                <span id="hours">00</span>
                <p>Hours</p>
            </div>
            <div class="countdown-item">
                <span id="minutes">00</span>
                <p>Minutes</p>
            </div>
            <div class="countdown-item">
                <span id="seconds">00</span>
                <p>Seconds</p>
            </div>
        </div>

        <form class="signup-form" onsubmit="alert('Thank you for signing up!'); return false;">
            <input type="email" placeholder="Enter your email address" required>
            <button type="submit">Notify Me</button>
        </form>

    </div>

    <script>
        // --- Countdown Timer Script ---

        // Set the date we're counting down to (30 days from now)
        const countDownDate = new Date();
        countDownDate.setDate(countDownDate.getDate() + 30);

        // Get elements
        const daysEl = document.getElementById('days');
        const hoursEl = document.getElementById('hours');
        const minutesEl = document.getElementById('minutes');
        const secondsEl = document.getElementById('seconds');

        // Update the count down every 1 second
        const x = setInterval(function() {

            // Get today's date and time
            const now = new Date().getTime();

            // Find the distance between now and the count down date
            const distance = countDownDate - now;

            // Time calculations for days, hours, minutes and seconds
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Function to format number with leading zero
            const formatTime = (time) => time < 10 ? `0${time}` : time;

            // Display the result in the elements
            daysEl.innerHTML = formatTime(days);
            hoursEl.innerHTML = formatTime(hours);
            minutesEl.innerHTML = formatTime(minutes);
            secondsEl.innerHTML = formatTime(seconds);

            // If the count down is over, write some text
            if (distance < 0) {
                clearInterval(x);
                document.getElementById("countdown").innerHTML = "<h2>We've Launched!</h2>";
            }
        }, 1000);
    </script>

</body>
</html>
