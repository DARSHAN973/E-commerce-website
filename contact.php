<?php
$activePage = 'contact';
include 'includes/navbar.php';
include_once 'includes/db.php';

// Handle form submission
$success = false;
$error = '';

if ($_POST && isset($_POST['submit_contact'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    // Validation
    if (empty($name) || strlen($name) < 2) {
        $error = 'Name must be at least 2 characters long.';
    } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (empty($phone) || strlen($phone) < 10) {
        $error = 'Please enter a valid phone number.';
    } elseif (empty($subject) || strlen($subject) < 5) {
        $error = 'Subject must be at least 5 characters long.';
    } elseif (empty($message) || strlen($message) < 10) {
        $error = 'Message must be at least 10 characters long.';
    } else {
        // Generate UUID
        $id = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
        
        // Insert into database
        try {
            $stmt = $conn->prepare(
                "INSERT INTO contact_submissions (id, name, email, phone, subject, message, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'new', NOW())"
            );
            if ($stmt->execute([$id, $name, $email, $phone, $subject, $message])) {
                $success = true;
                $_POST = array();
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        } catch (PDOException $e) {
            $error = 'Something went wrong. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Stylique</title>
    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assests/css/contact.css">
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap');
        
        /* Root Variables for Stylique Theme */
        :root {
            --stylique-primary: #c2185b;
            --stylique-primary-hover: #a91650;
            --stylique-dark: #1a1a1a;
            --stylique-light: #f8f9fa;
            --stylique-gradient: linear-gradient(135deg, #c2185b, #e91e63);
            --stylique-shadow: 0 10px 30px rgba(194, 24, 91, 0.1);
            --stylique-border-radius: 12px;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: var(--stylique-light);
        }

        /* Stylique Heading */
        .stylique-heading {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            color: var(--stylique-dark);
            position: relative;
            display: inline-block;
        }

        .stylique-heading::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: var(--stylique-gradient);
            border-radius: 2px;
        }

        /* Stylique Card */
        .stylique-card {
            background: white;
            border-radius: var(--stylique-border-radius);
            box-shadow: var(--stylique-shadow);
            border: none;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .stylique-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(194, 24, 91, 0.15);
        }

        /* Form Styles */
        .stylique-input {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 16px;
            transition: all 0.3s ease;
            background-color: #fafafa;
        }

        .stylique-input:focus {
            border-color: var(--stylique-primary);
            box-shadow: 0 0 0 0.2rem rgba(194, 24, 91, 0.25);
            background-color: white;
            outline: none;
        }

        .stylique-input.is-invalid {
            border-color: #dc3545;
        }

        /* Button Styles */
        .stylique-btn {
            background: var(--stylique-gradient);
            border: none;
            color: white;
            font-weight: 600;
            font-size: 16px;
            padding: 12px 30px;
            border-radius: 50px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stylique-btn:hover {
            background: linear-gradient(135deg, var(--stylique-primary-hover), #c2185b);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(194, 24, 91, 0.3);
            color: white;
        }

        /* Contact Info Cards */
        .contact-info-card {
            background: white;
            border-radius: var(--stylique-border-radius);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
        }

        .contact-info-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .contact-info-card i {
            color: var(--stylique-primary);
        }

        /* Form Labels */
        .form-label {
            color: var(--stylique-dark);
            font-weight: 600;
            margin-bottom: 8px;
        }

        /* Alert Styles */
        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            border: 1px solid #b8dabc;
            color: #155724;
            border-radius: var(--stylique-border-radius);
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f1aeb5);
            border: 1px solid #f1aeb5;
            color: #721c24;
            border-radius: var(--stylique-border-radius);
        }

        /* Character Counter */
        .char-counter {
            font-size: 14px;
            color: #6c757d;
            text-align: right;
            margin-top: 4px;
        }
    </style>
</head>

<body>
    <!-- Main Content -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Header Section -->
                <div class="text-center mb-5">
                    <h2 class="stylique-heading mb-3">Get in Touch</h2>
                    <p class="text-muted fs-5">
                        Have questions about our fashion collections? We'd love to hear from you.
                    </p>
                </div>

                <!-- Success/Error Messages -->
                <?php if ($success): ?>
                    <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                        <i class="fas fa-check-circle me-3 fs-4"></i>
                        <div>
                            <strong>Thank you!</strong> Your message has been sent successfully. 
                            We'll get back to you within 24 hours.
                        </div>
                    </div>
                <?php elseif (!empty($error)): ?>
                    <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                        <i class="fas fa-exclamation-circle me-3 fs-4"></i>
                        <div><?php echo htmlspecialchars($error); ?></div>
                    </div>
                <?php endif; ?>

                <!-- Contact Form -->
                <div class="stylique-card">
                    <div class="card-body p-5">
                        <form method="POST" action="" id="contactForm">
                            <div class="row g-4">
                                <!-- Name Field -->
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold">
                                        Full Name <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control stylique-input"
                                        id="name"
                                        name="name"
                                        value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                                        placeholder="Enter your full name"
                                        maxlength="100"
                                        required
                                    />
                                </div>

                                <!-- Email Field -->
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold">
                                        Email Address <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="email"
                                        class="form-control stylique-input"
                                        id="email"
                                        name="email"
                                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                        placeholder="your.email@example.com"
                                        maxlength="100"
                                        required
                                    />
                                </div>

                                <!-- Phone Field -->
                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-semibold">
                                        Phone Number <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="tel"
                                        class="form-control stylique-input"
                                        id="phone"
                                        name="phone"
                                        value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                                        placeholder="+91 00000 00000"
                                        maxlength="15"
                                        required
                                    />
                                </div>

                                <!-- Subject Field -->
                                <div class="col-md-6">
                                    <label for="subject" class="form-label fw-semibold">
                                        Subject <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control stylique-input"
                                        id="subject"
                                        name="subject"
                                        value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>"
                                        placeholder="What's this about?"
                                        maxlength="200"
                                        required
                                    />
                                </div>

                                <!-- Message Field -->
                                <div class="col-12">
                                    <label for="message" class="form-label fw-semibold">
                                        Message <span class="text-danger">*</span>
                                    </label>
                                    <textarea
                                        class="form-control stylique-input"
                                        id="message"
                                        name="message"
                                        rows="5"
                                        placeholder="Tell us more about your inquiry..."
                                        maxlength="1000"
                                        required
                                    ><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                                    <div class="char-counter">
                                        <span id="charCount">0</span>/1000 characters
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-12 text-center">
                                    <button
                                        type="submit"
                                        name="submit_contact"
                                        class="btn stylique-btn px-5 py-3"
                                    >
                                        <i class="fas fa-paper-plane me-2"></i>
                                        Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="row g-4 mt-5">
                    <div class="col-md-4 text-center">
                        <div class="contact-info-card p-4">
                            <i class="fas fa-envelope fa-2x mb-3"></i>
                            <h6 class="fw-semibold">Email Us</h6>
                            <p class="text-muted mb-0">support@stylique.com</p>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="contact-info-card p-4">
                            <i class="fas fa-phone fa-2x mb-3"></i>
                            <h6 class="fw-semibold">Call Us</h6>
                            <p class="text-muted mb-0">+1 (555) 123-4567</p>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="contact-info-card p-4">
                            <i class="fas fa-clock fa-2x mb-3"></i>
                            <h6 class="fw-semibold">Business Hours</h6>
                            <p class="text-muted mb-0">Mon-Fri: 9AM-6PM</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Character counter for message field
        document.getElementById('message').addEventListener('input', function() {
            const charCount = this.value.length;
            document.getElementById('charCount').textContent = charCount;
        });

        // Update character count on page load
        document.addEventListener('DOMContentLoaded', function() {
            const messageField = document.getElementById('message');
            const charCount = messageField.value.length;
            document.getElementById('charCount').textContent = charCount;
        });

        // Auto-hide success message after 5 seconds
        <?php if ($success): ?>
        setTimeout(function() {
            const alert = document.querySelector('.alert-success');
            if (alert) {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s';
                setTimeout(() => alert.remove(), 500);
            }
        }, 5000);
        <?php endif; ?>
    </script>
</body>
</html>

<?php include 'includes/footer.php'; ?>