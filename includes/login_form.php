<!-- Login/Register Modal -->
<div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <!-- Enhanced Header -->
            <div class="modal-header border-0 bg-gradient text-white" style="background: linear-gradient(135deg, #c2185b, #e91e63); padding: 2rem 2rem 1rem;">
                <div class="w-100 text-center">
                    <h4 class="modal-title fw-bold mb-2" id="authModalLabel" style="font-family: 'Playfair Display', serif;">Welcome to Stylique</h4>
                    <p class="mb-0 opacity-75" style="font-size: 14px;">Your fashion journey starts here</p>
                </div>
                <button type="button" class="btn-close btn-close-white position-absolute" style="top: 1rem; right: 1rem;" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4">
                <!-- Login Form -->
                <div id="loginForm">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="fas fa-user-circle" style="font-size: 3rem; color: #c2185b;"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Sign in to your account</h5>
                        <p class="text-muted small">Enter your credentials to continue</p>
                    </div>
                    
                    <form id="loginFormSubmit" method="POST" action="includes/auth.php">
                        <input type="hidden" name="action" value="login">
                        <div class="mb-3">
                            <label for="loginEmail" class="form-label fw-semibold text-dark">
                                <i class="fas fa-envelope me-2 text-muted"></i>Email Address
                            </label>
                            <input type="email" class="form-control form-control-lg border-2" id="loginEmail" name="email" 
                                   style="border-radius: 12px; border-color: #e9ecef; transition: all 0.3s ease;" 
                                   placeholder="Enter your email" required>
                        </div>
                        <div class="mb-4">
                            <label for="loginPassword" class="form-label fw-semibold text-dark">
                                <i class="fas fa-lock me-2 text-muted"></i>Password
                            </label>
                            <input type="password" class="form-control form-control-lg border-2" id="loginPassword" name="password" 
                                   style="border-radius: 12px; border-color: #e9ecef; transition: all 0.3s ease;" 
                                   placeholder="Enter your password" required>
                        </div>
                        <button type="submit" class="btn btn-lg w-100 mb-4 text-white fw-semibold" 
                                style="background: linear-gradient(135deg, #c2185b, #e91e63); border-radius: 12px; border: none; padding: 12px; transition: all 0.3s ease;">
                            <i class="fas fa-sign-in-alt me-2"></i>Sign In
                        </button>
                    </form>
                    
                    <div class="text-center">
                        <p class="text-muted mb-0">
                            Don't have an account? 
                            <a href="#" onclick="showRegisterForm()" class="text-decoration-none fw-semibold" 
                               style="color: #c2185b; transition: all 0.3s ease;">Create Account</a>
                        </p>
                    </div>
                </div>

                <!-- Register Form -->
                <div id="registerForm" style="display: none;">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="fas fa-user-plus" style="font-size: 3rem; color: #c2185b;"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Create your account</h5>
                        <p class="text-muted small">Join our fashion community</p>
                    </div>
                    
                    <form id="registerFormSubmit" method="POST" action="includes/auth.php">
                        <input type="hidden" name="action" value="register">
                        <div class="mb-3">
                            <label for="registerName" class="form-label fw-semibold text-dark">
                                <i class="fas fa-user me-2 text-muted"></i>Full Name
                            </label>
                            <input type="text" class="form-control border-2" id="registerName" name="name" 
                                   style="border-radius: 12px; border-color: #e9ecef; transition: all 0.3s ease;" 
                                   placeholder="Enter your full name" required>
                        </div>
                        <div class="mb-3">
                            <label for="registerEmail" class="form-label fw-semibold text-dark">
                                <i class="fas fa-envelope me-2 text-muted"></i>Email Address
                            </label>
                            <input type="email" class="form-control border-2" id="registerEmail" name="email" 
                                   style="border-radius: 12px; border-color: #e9ecef; transition: all 0.3s ease;" 
                                   placeholder="Enter your email" required>
                        </div>
                        <div class="mb-3">
                            <label for="registerPhone" class="form-label fw-semibold text-dark">
                                <i class="fas fa-phone me-2 text-muted"></i>Phone Number
                            </label>
                            <input type="tel" class="form-control border-2" id="registerPhone" name="phone" 
                                   style="border-radius: 12px; border-color: #e9ecef; transition: all 0.3s ease;" 
                                   placeholder="Enter your phone number" required>
                        </div>
                        <div class="mb-3">
                            <label for="registerAddress" class="form-label fw-semibold text-dark">
                                <i class="fas fa-map-marker-alt me-2 text-muted"></i>Address
                            </label>
                            <textarea class="form-control border-2" id="registerAddress" name="address" rows="3" 
                                      style="border-radius: 12px; border-color: #e9ecef; transition: all 0.3s ease;" 
                                      placeholder="Enter your address" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="registerPassword" class="form-label fw-semibold text-dark">
                                <i class="fas fa-lock me-2 text-muted"></i>Password
                            </label>
                            <input type="password" class="form-control border-2" id="registerPassword" name="password" 
                                   style="border-radius: 12px; border-color: #e9ecef; transition: all 0.3s ease;" 
                                   placeholder="Create a password" required minlength="6">
                        </div>
                        <div class="mb-4">
                            <label for="confirmPassword" class="form-label fw-semibold text-dark">
                                <i class="fas fa-lock me-2 text-muted"></i>Confirm Password
                            </label>
                            <input type="password" class="form-control border-2" id="confirmPassword" name="confirm_password" 
                                   style="border-radius: 12px; border-color: #e9ecef; transition: all 0.3s ease;" 
                                   placeholder="Confirm your password" required>
                        </div>
                        <button type="submit" class="btn btn-lg w-100 mb-4 text-white fw-semibold" 
                                style="background: linear-gradient(135deg, #c2185b, #e91e63); border-radius: 12px; border: none; padding: 12px; transition: all 0.3s ease;">
                            <i class="fas fa-user-plus me-2"></i>Create Account
                        </button>
                    </form>
                    
                    <div class="text-center">
                        <p class="text-muted mb-0">
                            Already have an account? 
                            <a href="#" onclick="showLoginForm()" class="text-decoration-none fw-semibold" 
                               style="color: #c2185b; transition: all 0.3s ease;">Sign In</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Success/Error Alert -->
<div class="position-fixed top-0 start-50 translate-middle-x" style="z-index: 9999; margin-top: 20px;">
    <div id="authAlert" class="alert alert-dismissible fade shadow-lg" role="alert" style="display: none; border-radius: 15px; border: none; min-width: 300px;">
        <div class="d-flex align-items-center">
            <i id="alertIcon" class="me-3" style="font-size: 1.2rem;"></i>
            <span id="authAlertMessage" class="fw-semibold"></span>
        </div>
        <button type="button" class="btn-close" onclick="hideAlert()"></button>
    </div>
</div>

<style>
/* Enhanced form styling */
.form-control:focus {
    border-color: #c2185b !important;
    box-shadow: 0 0 0 0.2rem rgba(194, 24, 91, 0.25) !important;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(194, 24, 91, 0.3);
}

/* Smooth transitions for form switching */
#loginForm, #registerForm {
    transition: all 0.3s ease;
}

/* Enhanced alert styling */
.alert-success {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.alert-danger {
    background: linear-gradient(135deg, #dc3545, #fd7e14);
    color: white;
}

/* Modal animation */
.modal-content {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Form field animations */
.form-control {
    transition: all 0.3s ease;
}

.form-control:hover {
    border-color: #c2185b;
    transform: translateY(-1px);
}

/* Link hover effects */
a[onclick*="show"]:hover {
    color: #a91650 !important;
    text-decoration: underline !important;
}
</style>

<script>
function showLoginForm() {
    document.getElementById('loginForm').style.display = 'block';
    document.getElementById('registerForm').style.display = 'none';
    document.getElementById('authModalLabel').textContent = 'Welcome Back';
    document.querySelector('.modal-header p').textContent = 'Sign in to continue your journey';
}

function showRegisterForm() {
    document.getElementById('loginForm').style.display = 'none';
    document.getElementById('registerForm').style.display = 'block';
    document.getElementById('authModalLabel').textContent = 'Join Stylique';
    document.querySelector('.modal-header p').textContent = 'Create your account today';
}

function showAlert(message, type) {
    const alert = document.getElementById('authAlert');
    const alertMessage = document.getElementById('authAlertMessage');
    const alertIcon = document.getElementById('alertIcon');
    
    // Set icon based on type
    if (type === 'success') {
        alertIcon.className = 'fas fa-check-circle me-3';
    } else {
        alertIcon.className = 'fas fa-exclamation-circle me-3';
    }
    
    alert.className = `alert alert-${type} alert-dismissible fade show shadow-lg`;
    alertMessage.textContent = message;
    alert.style.display = 'block';
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        hideAlert();
    }, 5000);
}

function hideAlert() {
    const alert = document.getElementById('authAlert');
    alert.style.display = 'none';
}

// Form validation for password confirmation
document.getElementById('registerFormSubmit').addEventListener('submit', function(e) {
    const password = document.getElementById('registerPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        showAlert('Passwords do not match!', 'danger');
        return false;
    }
});

// Handle form submissions with AJAX
document.getElementById('loginFormSubmit').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('includes/auth.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('Something went wrong. Please try again.', 'danger');
    });
});

document.getElementById('registerFormSubmit').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const password = document.getElementById('registerPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (password !== confirmPassword) {
        showAlert('Passwords do not match!', 'danger');
        return false;
    }
    
    const formData = new FormData(this);
    
    fetch('includes/auth.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            setTimeout(() => {
                showLoginForm();
            }, 1500);
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('Something went wrong. Please try again.', 'danger');
    });
});
</script>