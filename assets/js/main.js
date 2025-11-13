/**
 * Ticket4U - Main JavaScript
 * Interactive Features & UI Enhancements
 */

(function($) {
    'use strict';

    // Site URL - get from current location
    const siteUrl = window.location.origin + '/ticket4u_final';

    // Mobile Menu Toggle
    const mobileMenuToggle = $('#mobileMenuToggle');
    const mobileMenu = $('#mobileMenu');
    const mobileMenuClose = $('#mobileMenuClose');

    mobileMenuToggle.on('click', function() {
        mobileMenu.addClass('active');
        $('body').css('overflow', 'hidden');
    });

    mobileMenuClose.on('click', function() {
        mobileMenu.removeClass('active');
        $('body').css('overflow', '');
    });

    // Close mobile menu when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.mobile-menu, .mobile-menu-toggle').length) {
            mobileMenu.removeClass('active');
            $('body').css('overflow', '');
        }
    });

    // Mobile Submenu Toggle
    $('.mobile-submenu-toggle').on('click', function() {
        $(this).parent('.mobile-submenu').toggleClass('active');
    });

    // Flash Message Auto-hide
    setTimeout(function() {
        $('#flashMessage').fadeOut(400, function() {
            $(this).remove();
        });
    }, 5000);

    // Back to Top Button
    const backToTop = $('#backToTop');
    
    $(window).on('scroll', function() {
        if ($(this).scrollTop() > 300) {
            backToTop.addClass('visible');
        } else {
            backToTop.removeClass('visible');
        }
    });

    backToTop.on('click', function() {
        $('html, body').animate({ scrollTop: 0 }, 600);
    });

    // Form Validation
    $('form').on('submit', function(e) {
        let isValid = true;
        
        $(this).find('.form-control[required]').each(function() {
            const $field = $(this);
            const value = $field.val().trim();
            
            $field.removeClass('error');
            $field.siblings('.form-error').remove();
            
            if (!value) {
                isValid = false;
                $field.addClass('error');
                $field.after('<div class="form-error">This field is required</div>');
            }
            
            // Email validation
            if ($field.attr('type') === 'email' && value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    isValid = false;
                    $field.addClass('error');
                    $field.after('<div class="form-error">Please enter a valid email</div>');
                }
            }
            
            // Password validation
            if ($field.attr('type') === 'password' && value && value.length < 6) {
                isValid = false;
                $field.addClass('error');
                $field.after('<div class="form-error">Password must be at least 6 characters</div>');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            // Scroll to first error
            $('html, body').animate({
                scrollTop: $('.error').first().offset().top - 100
            }, 300);
        }
    });

    // Input focus effects
    $('.form-control').on('focus', function() {
        $(this).parent('.form-group').addClass('focused');
    }).on('blur', function() {
        $(this).parent('.form-group').removeClass('focused');
    });

    // Event Card Wishlist Toggle
    $(document).on('click', '.wishlist-btn', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const eventId = $btn.data('event-id');
        
        $.ajax({
            url: siteUrl + '/toggle-wishlist.php',
            method: 'POST',
            data: { event_id: eventId },
            success: function(response) {
                if (response.success) {
                    $btn.toggleClass('active');
                    $btn.find('i').toggleClass('far fas');
                    
                    // Show notification
                    showNotification(response.message, 'success');
                }
            },
            error: function() {
                showNotification('Please login to add to wishlist', 'error');
            }
        });
    });

    // Show Notification
    function showNotification(message, type) {
        const notification = $('<div>', {
            class: 'flash-message flash-' + type,
            html: `
                <div class="container">
                    <div class="flash-content">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                        <span>${message}</span>
                        <button class="flash-close"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            `
        });
        
        $('body').prepend(notification);
        
        notification.find('.flash-close').on('click', function() {
            notification.fadeOut(300, function() {
                $(this).remove();
            });
        });
        
        setTimeout(function() {
            notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }

    // Dropdown hover (desktop)
    if ($(window).width() > 1024) {
        $('.dropdown').hover(
            function() {
                $(this).find('.dropdown-menu').stop(true, true).fadeIn(200);
            },
            function() {
                $(this).find('.dropdown-menu').stop(true, true).fadeOut(200);
            }
        );
    }

    // Image Lazy Loading
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img.lazy').forEach(img => {
            imageObserver.observe(img);
        });
    }

    // Smooth Scroll for Anchor Links
    $('a[href^="#"]').on('click', function(e) {
        const target = $(this.hash);
        if (target.length) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: target.offset().top - 80
            }, 600);
        }
    });

    // Price Slider (if exists)
    if ($('.price-slider').length) {
        $('.price-slider').on('input', function() {
            const value = $(this).val();
            $(this).siblings('.price-value').text('RM ' + value);
        });
    }

    // Quantity Input
    $(document).on('click', '.qty-btn', function() {
        const $input = $(this).siblings('.qty-input');
        let value = parseInt($input.val());
        const max = parseInt($input.attr('max')) || 10;
        
        if ($(this).hasClass('qty-plus')) {
            if (value < max) {
                $input.val(value + 1).trigger('change');
            }
        } else {
            if (value > 1) {
                $input.val(value - 1).trigger('change');
            }
        }
    });

    // Copy to Clipboard
    $(document).on('click', '.copy-btn', function() {
        const text = $(this).data('copy');
        navigator.clipboard.writeText(text).then(() => {
            showNotification('Copied to clipboard!', 'success');
        });
    });

    // Filter Toggle (Mobile)
    $('.filter-toggle').on('click', function() {
        $('.filters-sidebar').toggleClass('active');
    });

    // Date Picker Enhancement (if using native date input)
    $('input[type="date"]').attr('placeholder', 'dd/mm/yyyy');

    // Ticket Type Selection
    $(document).on('change', '.ticket-select', function() {
        updateBookingSummary();
    });

    function updateBookingSummary() {
        let total = 0;
        let ticketCount = 0;
        
        $('.ticket-select').each(function() {
            const qty = parseInt($(this).val()) || 0;
            const price = parseFloat($(this).data('price')) || 0;
            total += qty * price;
            ticketCount += qty;
        });
        
        const bookingFee = total * 0.05;
        const grandTotal = total + bookingFee;
        
        $('#subtotal').text('RM ' + total.toFixed(2));
        $('#bookingFee').text('RM ' + bookingFee.toFixed(2));
        $('#grandTotal').text('RM ' + grandTotal.toFixed(2));
        $('#ticketCount').text(ticketCount);
    }

    // Initialize tooltips if using Bootstrap
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        $('[data-bs-toggle="tooltip"]').tooltip();
    }

    // Print Ticket
    $(document).on('click', '.print-ticket-btn', function() {
        window.print();
    });

    // Event Search with Debounce
    let searchTimeout;
    $('.search-input').on('keyup', function() {
        clearTimeout(searchTimeout);
        const query = $(this).val();
        
        if (query.length >= 3) {
            searchTimeout = setTimeout(function() {
                performSearch(query);
            }, 500);
        }
    });

    function performSearch(query) {
        $.ajax({
            url: '/api/search-events.php',
            method: 'GET',
            data: { q: query },
            success: function(response) {
                displaySearchResults(response);
            }
        });
    }

    function displaySearchResults(results) {
        // Implement search results display
        console.log('Search results:', results);
    }

    // Initialize page
    $(document).ready(function() {
        console.log('Ticket4U initialized');
        
        // Add animation classes to elements
        $('.fade-in').each(function(i) {
            setTimeout(() => {
                $(this).addClass('visible');
            }, i * 100);
        });
    });

})(jQuery);

// Wishlist toggle function (global scope for onclick handlers)
function toggleWishlist(eventId, button) {
    // Check if user is logged in (button will only show if logged in on some pages)
    fetch(window.location.origin + '/ticket4u_final/toggle-wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        credentials: 'same-origin', // Include cookies for session
        body: 'event_id=' + eventId
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || 'Request failed');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Toggle button state
            const btn = button || document.querySelector(`[onclick*="${eventId}"]`);
            if (btn) {
                const icon = btn.querySelector('i');
                if (data.action === 'added') {
                    btn.classList.add('active');
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    btn.setAttribute('title', 'Remove from wishlist');
                } else {
                    btn.classList.remove('active');
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    btn.setAttribute('title', 'Add to wishlist');
                }
            }
            
            // Show notification
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Wishlist Error:', error);
        showNotification(error.message || 'An error occurred. Please try again.', 'error');
    });
}

// Show notification helper
function showNotification(message, type) {
    // Create notification element if it doesn't exist
    let notification = document.getElementById('notification');
    if (!notification) {
        notification = document.createElement('div');
        notification.id = 'notification';
        notification.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            z-index: 10000;
            animation: slideInRight 0.3s ease-out;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        document.body.appendChild(notification);
    }
    
    // Set message and color
    notification.textContent = message;
    notification.style.background = type === 'success' 
        ? 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)'
        : 'linear-gradient(135deg, #f5576c 0%, #f093fb 100%)';
    notification.style.display = 'block';
    
    // Auto hide after 3 seconds
    setTimeout(() => {
        notification.style.display = 'none';
    }, 3000);
}
