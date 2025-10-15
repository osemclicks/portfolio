// Navigation Toggle
const hamburger = document.querySelector('.hamburger');
const navLinks = document.querySelector('.nav-links');
const links = document.querySelectorAll('.nav-links li');

hamburger.addEventListener('click', () => {
    navLinks.classList.toggle('active');
    
    // Animate hamburger
    hamburger.classList.toggle('active');
    const lines = hamburger.querySelectorAll('.line');
    if (hamburger.classList.contains('active')) {
        lines[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
        lines[1].style.opacity = '0';
        lines[2].style.transform = 'rotate(-45deg) translate(7px, -6px)';
    } else {
        lines[0].style.transform = 'none';
        lines[1].style.opacity = '1';
        lines[2].style.transform = 'none';
    }
});

// Close menu when clicking on a link
links.forEach(link => {
    link.addEventListener('click', () => {
        navLinks.classList.remove('active');
        
        // Reset hamburger
        const lines = hamburger.querySelectorAll('.line');
        hamburger.classList.remove('active');
        lines[0].style.transform = 'none';
        lines[1].style.opacity = '1';
        lines[2].style.transform = 'none';
    });
});

// Portfolio Filtering
const filterBtns = document.querySelectorAll('.filter-btn');
const portfolioItems = document.querySelectorAll('.portfolio-item');

filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        // Remove active class from all buttons
        filterBtns.forEach(btn => btn.classList.remove('active'));
        
        // Add active class to clicked button
        btn.classList.add('active');
        
        const filter = btn.getAttribute('data-filter');
        
        portfolioItems.forEach(item => {
            if (filter === 'all' || item.classList.contains(filter)) {
                item.style.display = 'block';
                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'scale(1)';
                }, 10);
            } else {
                item.style.opacity = '0';
                item.style.transform = 'scale(0.8)';
                setTimeout(() => {
                    item.style.display = 'none';
                }, 300);
            }
        });
    });
});

// Lightbox
const portfolioImages = document.querySelectorAll('.portfolio-item img');
const lightbox = document.getElementById('lightbox');
const lightboxImg = document.getElementById('lightbox-img');
const lightboxCaption = document.getElementById('lightbox-caption');
const close = document.querySelector('.close');

portfolioImages.forEach(img => {
    img.addEventListener('click', () => {
        lightbox.style.display = 'block';
        lightboxImg.src = img.src;
        const title = img.parentElement.querySelector('.overlay h3').textContent;
        const category = img.parentElement.querySelector('.overlay p').textContent;
        lightboxCaption.textContent = `${title} - ${category}`;
    });
});

close.addEventListener('click', () => {
    lightbox.style.display = 'none';
});

lightbox.addEventListener('click', (e) => {
    if (e.target === lightbox) {
        lightbox.style.display = 'none';
    }
});

// Form Validation
const contactForm = document.getElementById('contactForm');
const nameInput = document.getElementById('name');
const emailInput = document.getElementById('email');
const phoneInput = document.getElementById('phone');
const messageInput = document.getElementById('message');
const nameError = document.getElementById('name-error');
const emailError = document.getElementById('email-error');
const phoneError = document.getElementById('phone-error');
const messageError = document.getElementById('message-error');
const formSuccess = document.getElementById('form-success');
const formError = document.getElementById('form-error');

// 

contactForm.addEventListener('submit', (e) => {
    e.preventDefault();
    
    // Reset errors
    nameError.textContent = '';
    emailError.textContent = '';
    messageError.textContent = '';
    formSuccess.style.display = 'none';
    formError.style.display = 'none';
    
    // Validate form
    let isValid = true;
    
    if (!nameInput.value.trim()) {
        nameError.textContent = 'Please enter your name';
        isValid = false;
    }
    
    if (!emailInput.value.trim()) {
        emailError.textContent = 'Please enter your email';
        isValid = false;
    } else if (!isValidEmail(emailInput.value)) {
        emailError.textContent = 'Please enter a valid email address';
        isValid = false;
    }
    
    if (phoneInput.value.trim() && !isValidPhone(phoneInput.value)) {
        phoneError.textContent = 'Please enter a valid phone number';
        isValid = false;
    }
    
    if (!messageInput.value.trim()) {
        messageError.textContent = 'Please enter your message';
        isValid = false;
    }
    
    if (isValid) {
        // Show loading state
        const submitBtn = contactForm.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.textContent;
        submitBtn.textContent = 'Processing...';
        submitBtn.disabled = true;

        // Email sending disabled; show instructions instead
        formSuccess.textContent = 'Thank you for reaching out. Please note that online submissions are currently disabled. We kindly request you to contact us via email at osemclicks@gmail.com';
        formSuccess.style.display = 'block';

        // Reset the form
        contactForm.reset();

        // Reset button
        submitBtn.textContent = originalBtnText;
        submitBtn.disabled = false;
    }
});

// Email validation function
function isValidEmail(email) {
    const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    return emailRegex.test(email);
}

// Phone validation function
function isValidPhone(phone) {
    // Basic phone validation - adjust as needed for your requirements
    const phoneRegex = /^[+]?[(]?[0-9]{3}[)]?[-\s.]?[0-9]{3}[-\s.]?[0-9]{4,6}$/;
    return phoneRegex.test(phone);
}

// Create images folder and placeholder profile image
document.addEventListener('DOMContentLoaded', function() {
    // Set a placeholder image for profile if it doesn't load
    const profileImg = document.getElementById('profile-img');
    profileImg.onerror = function() {
        this.src = 'jmages/Nature/6.JPG';
    };
});

// Scroll to top when page is refreshed
window.onbeforeunload = function() {
    window.scrollTo(0, 0);
};

// Sticky header
window.addEventListener('scroll', function() {
    const header = document.querySelector('header');
    header.classList.toggle('sticky', window.scrollY > 0);
});

// Video slow-start effect
document.addEventListener('DOMContentLoaded', function() {
    const desktopVideo = document.querySelector('.desktop-video');
    const mobileVideo = document.querySelector('.mobile-video');
    
    function applySlowStartEffect(videoElement) {
        if (!videoElement) return;
        
        // Start with a very slow playback rate
        videoElement.playbackRate = 0.2;
        
        // Gradually increase the playback rate
        let currentRate = 0.2;
        const targetRate = 1.0;
        const step = 0.05;
        const interval = 200; // milliseconds
        
        const speedInterval = setInterval(() => {
            currentRate += step;
            videoElement.playbackRate = currentRate;
            
            if (currentRate >= targetRate) {
                videoElement.playbackRate = targetRate;
                clearInterval(speedInterval);
            }
        }, interval);
    }
    
    // Apply the effect to both videos
    if (desktopVideo) {
        desktopVideo.addEventListener('loadedmetadata', function() {
            applySlowStartEffect(desktopVideo);
        });
    }
    
    if (mobileVideo) {
        mobileVideo.addEventListener('loadedmetadata', function() {
            applySlowStartEffect(mobileVideo);
        });
    }
});