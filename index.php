<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover">
    <title>Image Gallery</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            background-color: #000;
            user-select: none; /* Standard syntax */
            -webkit-user-select: none; /* Safari */
            -moz-user-select: none; /* Firefox */
            -ms-user-select: none; /* Internet Explorer/Edge */
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            padding: 10px;
            align-items: center;
            margin: auto;
        }

        .image-container {
            width: calc(100% / 4 - 20px);
            margin: 10px;
            box-sizing: border-box;
            cursor: pointer;
            overflow: hidden;
            border-radius: 10px;
        }

        .image-container img {
            width: 100%;
            height: 300px;
            display: block;
            object-fit: cover;
            transition: transform 0.3s ease;
            border-radius: 10px;
        }

        .image-container:hover img {
            transform: scale(1.05);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        .modal-content {
            margin: auto;
            display: block;
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            position: absolute;
            border-radius: 5px;
        }
        .fullsc {
            position: absolute;
            top: 15px;
            left: 35px;
            color: white;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
            z-index: 99;
        }
        .fullsc:hover {
            color: #666;
            transform:scale(1.1);
        }
        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: white;
            font-size: 38px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
            z-index: 99;
        }
        .close:hover {
            color: red;
            transform:scale(1.2);
        }

        .nav-button {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            font-size: 30px;
            cursor: pointer;
            padding: 10px;
            z-index: 2;
            transition: 0.2s;
        }
        .nav-button:hover {
            color:#822dff;
        }

        .prev {
            left: 15px;
        }

        .next {
            right: 15px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .image-container {
                width: calc(100% / 2 - 20px);
            }
            .container {
                padding: 5px;
            }
            .image-container img {
                height: 180px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <?php
    $image_dir = 'images/';
    $images = glob($image_dir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);

    if ($images) {
        foreach ($images as $image) {
            echo '<div class="image-container" onclick="openModal(\'' . addslashes($image) . '\')">';
            echo '<img loading="lazy" src="' . $image . '" alt="Image">';
            echo '</div>';
        }
    } else {
        echo '<p>No images found in the directory.</p>';
    }
    ?>
</div>

<div id="myModal" class="modal">
    <span class="close" onclick="closeModal()">&times;</span>
    <span class="fullsc" onclick="">&#10697;</span>
    <span class="nav-button prev" onclick="changeImage(-1)">&#10094;</span>
    <img class="modal-content" id="img01">
    <span class="nav-button next" onclick="changeImage(1)">&#10095;</span>
</div>

<script>
    let images = <?php echo json_encode($images); ?>; // Pass images array to JavaScript
    let currentIndex = 0; // Track the current image index
    let touchStartX = 0; // Track the starting X coordinate for touch

    function openModal(imgSrc) {
        currentIndex = images.indexOf(imgSrc); // Set current index
        document.getElementById("myModal").style.display = "block";
        document.body.style.overflow = 'hidden'; // Prevent scrolling

        // Set the initial image and opacity
        const imgElement = document.getElementById("img01");
        imgElement.src = imgSrc;
        imgElement.style.opacity = 1; // Ensure it is visible

        // Add event listeners for keyboard, mouse scroll, and touch navigation
        document.addEventListener('keydown', handleKeydown);
        document.addEventListener('wheel', handleScroll);
        document.addEventListener('touchstart', handleTouchStart);
        document.addEventListener('touchend', handleTouchEnd);
    }

    function closeModal() {
        document.getElementById("myModal").style.display = "none";
        document.body.style.overflow = 'auto'; // Allow scrolling again

        // Remove event listeners when modal is closed
        document.removeEventListener('keydown', handleKeydown);
        document.removeEventListener('wheel', handleScroll);
        document.removeEventListener('touchstart', handleTouchStart);
        document.removeEventListener('touchend', handleTouchEnd);
    }

    function handleKeydown(event) {
        if (event.key === "ArrowRight") {
            changeImage(1); // Next image
        } else if (event.key === "ArrowLeft") {
            changeImage(-1); // Previous image
        } else if (event.key === "Escape") {
            closeModal(); // Close modal on Escape key
        }
    }

    function handleScroll(event) {
        event.preventDefault(); // Prevent default scroll behavior
        if (event.deltaY > 0) {
            changeImage(1); // Scroll down -> Next image
        } else {
            changeImage(-1); // Scroll up -> Previous image
        }
    }

    function handleTouchStart(event) {
        touchStartX = event.touches[0].clientX; // Get the initial touch position
    }

    function handleTouchEnd(event) {
        let touchEndX = event.changedTouches[0].clientX; // Get the final touch position
        let swipeThreshold = 50; // Minimum distance for a swipe

        if (touchEndX - touchStartX > swipeThreshold) {
            changeImage(-1); // Swipe right -> Previous image
        } else if (touchStartX - touchEndX > swipeThreshold) {
            changeImage(1); // Swipe left -> Next image
        }
    }

    function changeImage(direction) {
        currentIndex += direction; // Update the current index
        // Wrap around if needed
        if (currentIndex >= images.length) {
            currentIndex = 0; // Loop to first image
        } else if (currentIndex < 0) {
            currentIndex = images.length - 1; // Loop to last image
        }

        const imgElement = document.getElementById("img01");
        imgElement.style.opacity = 0; // Start with the image hidden

        // Create a new Image object to preload the next image
        const newImage = new Image();
        newImage.src = images[currentIndex];

        newImage.onload = function() {
            imgElement.src = newImage.src; // Change to the new image
            imgElement.style.transition = "opacity 0.5s"; // Set transition
            imgElement.style.opacity = 1; // Fade in the image
        };

        // Optionally, reset the transition style if needed after a delay
        setTimeout(() => {
            imgElement.style.transition = ""; // Reset transition
        }, 100); // Match this with the transition duration
    }
</script>
  
    
<script>
// Function to navigate between image containers using keyboard
function handleImageNavigation(event) {
    const images = document.querySelectorAll('.image-container');
    const currentIndex = Array.from(images).findIndex(image => 
        image === document.activeElement || image.contains(document.activeElement)
    );

    switch(event.key) {
        case 'ArrowRight':
        case 'ArrowDown':
            // Move to next image container, wrap around to first if at end
            const nextIndex = (currentIndex + 1) % images.length;
            images[nextIndex].focus();
            break;
        case 'ArrowLeft':
        case 'ArrowUp':
            // Move to previous image container, wrap around to last if at beginning
            const prevIndex = (currentIndex - 1 + images.length) % images.length;
            images[prevIndex].focus();
            break;
        case 'Enter':
            // If an image container is focused, open the modal for that image
            if (currentIndex !== -1) {
                const imgSrc = images[currentIndex].querySelector('img').src;
                openModal(imgSrc);
            }
            break;
    }
}

// Add event listener for keyboard navigation
document.addEventListener('keydown', handleImageNavigation);

// Make image containers focusable and add tabindex
document.querySelectorAll('.image-container').forEach((imageContainer, index) => {
    imageContainer.setAttribute('tabindex', '0');
    imageContainer.setAttribute('aria-label', `Image ${index + 1}`);
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const fullScreenToggle = document.querySelector('.fullsc');

    fullScreenToggle.addEventListener('click', () => {
        if (!document.fullscreenElement) {
            // Request fullscreen on the document
            document.documentElement.requestFullscreen().catch(err => {
                console.error(`Error attempting to enable full-screen mode: ${err.message} (${err.name})`);
            });
        } else {
            // Exit fullscreen
            document.exitFullscreen();
        }
    });
});
</script>

</body>
</html>