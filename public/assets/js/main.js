// EduMart Main JavaScript

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});

// Add to cart animation
function addToCart(resourceId) {
    const btn = event.target;
    const originalText = btn.textContent;
    btn.textContent = 'Adding...';
    btn.disabled = true;
    
    setTimeout(() => {
        btn.textContent = '✓ Added';
        setTimeout(() => {
            btn.textContent = originalText;
            btn.disabled = false;
        }, 1000);
    }, 500);
}

// Search functionality
const searchInput = document.querySelector('input[name="search"]');
if (searchInput) {
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.target.closest('form').submit();
        }
    });
}

// Confirm delete actions
document.querySelectorAll('[data-confirm]').forEach(element => {
    element.addEventListener('click', function(e) {
        if (!confirm(this.dataset.confirm)) {
            e.preventDefault();
        }
    });
});

// Mobile menu toggle
const menuToggle = document.querySelector('.menu-toggle');
const navMenu = document.querySelector('.nav-menu');

if (menuToggle) {
    menuToggle.addEventListener('click', () => {
        navMenu.classList.toggle('active');
    });
}

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Form validation
const forms = document.querySelectorAll('form[data-validate]');
forms.forEach(form => {
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.style.borderColor = '#dc3545';
            } else {
                field.style.borderColor = '#ddd';
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });
});

// Price range filter
const minPrice = document.querySelector('input[name="min_price"]');
const maxPrice = document.querySelector('input[name="max_price"]');

if (minPrice && maxPrice) {
    minPrice.addEventListener('change', function() {
        if (maxPrice.value && parseFloat(this.value) > parseFloat(maxPrice.value)) {
            alert('Minimum price cannot be greater than maximum price.');
            this.value = '';
        }
    });
    
    maxPrice.addEventListener('change', function() {
        if (minPrice.value && parseFloat(this.value) < parseFloat(minPrice.value)) {
            alert('Maximum price cannot be less than minimum price.');
            this.value = '';
        }
    });
}

// Star rating display
function displayStars(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 >= 0.5;
    let stars = '⭐'.repeat(fullStars);
    if (hasHalfStar) stars += '⭐';
    return stars;
}

// Image preview for file uploads
const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
imageInputs.forEach(input => {
    input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                let preview = input.parentElement.querySelector('.image-preview');
                if (!preview) {
                    preview = document.createElement('img');
                    preview.className = 'image-preview';
                    preview.style.maxWidth = '200px';
                    preview.style.marginTop = '10px';
                    preview.style.borderRadius = '8px';
                    input.parentElement.appendChild(preview);
                }
                preview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
});

console.log('EduMart initialized successfully!');
