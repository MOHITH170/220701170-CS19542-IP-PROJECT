<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>AGRISEEKS</title>
    <link rel="stylesheet" href="styles2.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <!-- Header Section -->
    <header>
        <h1>AGRISEEKS</h1>
        <nav>
            <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="#features">Features</a></li>
            <li><a href="register.php">Register</a></li>
            <li><a href="logout.php">Login/Logout</a></li>
            </ul>
        </nav>
    </header>

    <!-- Image Slider Section -->
    <section id="image-slider">
        <div class="slider">
            <div class="slides">
                <div class="slide"><img src="farm2.jpg" alt="Slide 1" height="500px"></div>
                <div class="slide"><img src="farm3.jpg" alt="Slide 2" height="500px"></div>
                <div class="slide"><img src="farm1.jpg" alt="Slide 3" height="500px"></div>
            </div>
        </div>
        <button class="prev" onclick="moveSlide(-1)">&#10094;</button>
        <button class="next" onclick="moveSlide(1)">&#10095;</button>
    </section>

    <!-- Intro Section -->
    <section id="intro">
        <h2>Connecting Farmers and Labourers Easily</h2>
        <p>Effortlessly find the right labour force or agricultural opportunities. Whether you're a large-scale farmer or a field labourer, our portal ensures your needs are met quickly and efficiently.</p>
        <a href="register.php" class="button">Get Started</a>
    </section>
<br>
<br>
 

    <!-- Statistics Section -->
    <section id="statistics">
        <h3>Our Impact</h3>
        <div class="stat">
            <h4>500+</h4>
            <p>Jobs Posted</p>
        </div>
        <div class="stat">
            <h4>300+</h4>
            <p>Registered Farmers</p>
        </div>
        <div class="stat">
            <h4>200+</h4>
            <p>Registered Labourers</p>
        </div>
    </section>
    <br>
    <br>
       <!-- Features Section -->
       <section id="features">
        <h4>Features of AGRISEEKS</h4>
        <div class="feature" id="farmerFeature">
            <h4>For Farmers</h4>
            <p>Post your requirements for skilled or general labourers. Browse profiles and select the best fit for your farm's needs.</p>
        </div>
        <div class="feature" id="labourFeature">
            <h4>For Labourers</h4>
            <p>Create a profile and find agricultural work that matches your skill set and preferences, all in one platform.</p>
        </div>
        <div class="feature" id="smartMatchingFeature">
            <h4>Smart Matching</h4>
            <p>Our advanced algorithm ensures that farmers and labourers are matched efficiently based on location, availability, and experience.</p>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq">
        <h3>Frequently Asked Questions</h3>
        <div class="faq-item">
            <h4>How do I post a job?</h4>
            <p>Once you register as a farmer, you can easily post a job through your dashboard.</p>
        </div>
        <div class="faq-item">
            <h4>How do I apply for a job?</h4>
            <p>After registering as a labourer, you can browse job listings and apply directly through the platform.</p>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact">
        <h3>Contact Us</h3>
        <form id="contactForm">
            <label for="contactName">Name:</label>
            <input type="text" id="contactName" required>

            <label for="contactEmail">Email:</label>
            <input type="email" id="contactEmail" required>

            <label for="contactMessage">Message:</label>
            <textarea id="contactMessage" rows="4" required></textarea>

            <button type="submit" class="button">Send Message</button>
        </form>
    </section>

    <!-- Newsletter Signup Section -->
    <section id="newsletter">
        <h3>Stay Updated</h3>
        <p>Sign up for our newsletter to receive the latest updates and job postings.</p>
        <form id="newsletterForm">
            <label for="newsletterEmail">Email:</label>
            <input type="email" id="newsletterEmail" required>
            <button type="submit" class="button">Subscribe</button>
        </form>
    </section>

  

    <script>
        $(document).ready(function() {
            // Form validation on submit
            $('#contactForm').on('submit', function(event) {
                event.preventDefault(); // Prevent form submission
                const name = $('#contactName').val().trim();
                const email = $('#contactEmail').val().trim();
                const message = $('#contactMessage').val().trim();

                if (name === '' || email === '' || message === '') {
                    alert("Please fill in all fields.");
                } else {
                    alert("Message sent successfully!");
                    // Here you would typically handle the form submission (e.g., via AJAX)
                    this.reset(); // Reset form fields
                }
            });

            // Newsletter form submission
            $('#newsletterForm').on('submit', function(event) {
                event.preventDefault(); // Prevent default form submission
                const newsletterEmail = $('#newsletterEmail').val().trim();
                if (newsletterEmail === '') {
                    alert("Please enter your email address.");
                } else {
                    alert("Thank you for subscribing!");
                    // Here you would typically handle the newsletter signup (e.g., via AJAX)
                    this.reset(); // Reset form fields
                }
            });

            // Feature click events
            $('#farmerFeature').on('click', function() {
                window.location.href = 'FAR1.php';
            });

            $('#labourFeature').on('click', function() {
                window.location.href = 'la1.php';
            });

            // Image slider functionality
            let currentSlide = 0;
            const slides = $('.slides');
            const totalSlides = $('.slide').length;

            function showSlide(index) {
                currentSlide = (index + totalSlides) % totalSlides;
                const offset = -currentSlide * 100;
                slides.css('transform', `translateX(${offset}%)`);
            }

            // Move to the next/previous slide
            window.moveSlide = (n) => {
                showSlide(currentSlide + n);
            };

            // Auto-slide every 5 seconds (optional)
            setInterval(() => {
                showSlide(currentSlide + 1);
            }, 5000); // Change slide every 5 seconds
        });
    </script>
      <!-- Footer -->
      <footer id="footer">
        <p>AGRISEEKS | Designed for a better agricultural future</p>
        <p>&copy; 2024 AGRISEEKS.com</p>
    </footer>
</body>
</html>
