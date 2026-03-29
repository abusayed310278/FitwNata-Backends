<!DOCTYPE html>
<html lang="en">
<head>
    <title>Fitwnata - Support</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- FAVICON -->
    <link rel="icon" type="image/png" href="favicon.png">

    <!-- BOOTSTRAP 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
:root {
    --primary: #11c6a5; /* Fitwnata brand color */
    --bg-light: #f5f7fa;
}

body {
    font-family: "Inter", sans-serif;
    background: var(--bg-light);
    margin: 0;
}

/* HEADER */
.header {
    /* padding: 60px 20px;
    color: white;
    font-size: 2.6em;
    font-weight: 700;
    text-align: center;
    border-bottom-left-radius: 20px;
    border-bottom-right-radius: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08); */
    background: var(--primary);
	padding: 20px;
	text-align: center;
	color: white;
	font-size: 2.5em;
	margin-bottom: 20px;
}

/* SECTION TITLE */
.section-title {
    border-left: 6px solid var(--primary);
    padding-left: 14px;
    font-weight: 700;
    color: #222;
}

/* CARDS */
.custom-card {
    background: #fff;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.06);
}

/* CARD HEADINGS */
.custom-title {
    font-size: 1.4em;
    font-weight: 600;
    color: var(--primary);
    margin-bottom: 15px;
}

/* FORM INPUT */
.form-control:focus,
.form-select:focus {
    border-color: var(--primary) !important;
    box-shadow: 0 0 8px rgba(17,198,165,0.3) !important;
}

/* BUTTON */
.btn-primary {
    background: var(--primary);
    border-color: var(--primary);
    padding: 12px;
    font-size: 1.1em;
    border-radius: 12px;
}

.btn-primary:hover {
    opacity: 0.9;
}

/* FOOTER */
.footer {
    margin-top: 60px;
    padding: 25px;
    text-align: center;
    color: #777;
    background: white;
    border-top: 1px solid #eee;
}
</style>
</head>

<body>

    <!-- HEADER -->
    <div class="header">Contact & Support</div>

    <div class="container py-5">

        <h2 class="section-title mb-3">We’re Here to Help</h2>
        <p>If you have questions, feedback, or need support, feel free to contact us. Our team will respond as soon as possible.</p>

        <div class="row g-4 mt-4">

            <!-- LEFT COLUMN -->
            <div class="col-md-6">
                <div class="custom-card">
                    <h3 class="custom-title">Contact Details</h3>

                    <p><strong>Email:</strong><br> med.hayballa@gmail.com</p>
                    <p><strong>Phone:</strong><br> +22 236 827565</p>
                    <!-- <p><strong>Address:</strong><br>
                        Fitwnata HQ, Wellness Street,<br>
                        Silicon City, CA 94000
                    </p> -->
                </div>
            </div>

            <!-- RIGHT COLUMN -->
            <div class="col-md-6">
                <div class="custom-card">
                    <h3 class="custom-title">Send Us a Message</h3>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @elseif(session('error'))
                        <div class="alert alert-error">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('contact.send') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Your Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Your Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea name="message" class="form-control" rows="4" required></textarea>
                        </div>

                        <button type="submit" id="submitBtn" class="btn btn-primary w-100">
                            <span id="btnText">Send Message</span>
                            <span id="btnLoader" class="spinner-border spinner-border-sm ms-2" style="display:none;"></span>
                        </button>
                    </form>

                </div>
            </div>

        </div>

    </div>

    <div class="footer">© 2025 Fitwnata. All rights reserved.</div>

    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.querySelector("form").addEventListener("submit", function() {
            const btn = document.getElementById("submitBtn");
            const text = document.getElementById("btnText");
            const loader = document.getElementById("btnLoader");

            // Disable button
            btn.disabled = true;

            // Show loader
            loader.style.display = "inline-block";

            // Optional: Change button text
            text.textContent = "Sending...";
        });
    </script>

</body>
</html>
