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
const messageInput = document.getElementById('message');
const nameError = document.getElementById('name-error');
const emailError = document.getElementById('email-error');
const messageError = document.getElementById('message-error');
const formSuccess = document.getElementById('form-success');

contactForm.addEventListener('submit', (e) => {
    e.preventDefault();
    
    // Reset errors
    nameError.textContent = '';
    emailError.textContent = '';
    messageError.textContent = '';
    formSuccess.style.display = 'none';
    
    // Display unavailability message
    formSuccess.textContent = 'This contact form feature is currently unavailable. Please contact us directly at osemclicks@gmail.com or keerthanpoojai2004@gmail.com';
    formSuccess.style.display = 'block';
    
    // Reset the form
    contactForm.reset();
});

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