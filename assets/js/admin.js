// Existing JavaScript code for handling image uploads and previews

import Cropper from 'cropperjs'; // Ensure Cropper.js is imported

document.querySelectorAll('input[type="file"]').forEach(input => {
    let cropper; // Variable to hold the cropper instance
    const imagePreview = document.createElement('img'); // Create an image element for cropping
    const cropperContainer = document.createElement('div'); // Create a container for the cropper
    cropperContainer.classList.add('cropper-container'); // Add class for styling
    document.body.appendChild(cropperContainer); // Append cropper container to body

    input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result; // Set the source of the image preview
                cropperContainer.innerHTML = ''; // Clear previous cropper
                cropperContainer.appendChild(imagePreview); // Add image to cropper container

                // Initialize Cropper.js
                cropper = new Cropper(imagePreview, {
                    aspectRatio: 1, // Set aspect ratio for cropping
                    viewMode: 1,
                    autoCropArea: 1,
                });
            }
            reader.readAsDataURL(file);
        }
    });

    // Save cropped image
    document.getElementById('cropButton').addEventListener('click', function() {
        const canvas = cropper.getCroppedCanvas();
        canvas.toBlob(function(blob) {
            const formData = new FormData();
            formData.append('croppedImage', blob, file.name);

            // Replace the original file input with the cropped image
            const newFileInput = document.createElement('input');
            newFileInput.type = 'hidden';
            newFileInput.name = 'instructor_image';
            newFileInput.value = canvas.toDataURL('image/jpeg');
            document.querySelector('form').appendChild(newFileInput);

            cropperContainer.style.display = 'none'; // Hide cropper container
        });
    });

    input.addEventListener('change', function(e) {
        const preview = this.parentElement.querySelector('.image-preview');
        const file = this.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `
                    <img src="${e.target.result}" class="preview-image mt-2" alt="Preview">
                `;
            }
            reader.readAsDataURL(file);
        }
    });
});

// Logic for cropping and resizing images
document.getElementById('instructorImageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const cropperContainer = document.querySelector('.cropper-container');
            const cropperImage = document.getElementById('cropperImage');
            cropperImage.src = e.target.result;
            cropperContainer.style.display = 'block';
            document.getElementById('cropButton').style.display = 'block';

            const cropper = new Cropper(cropperImage, {
                aspectRatio: 1,
                viewMode: 1,
                autoCropArea: 1,
            });

            document.getElementById('cropButton').addEventListener('click', function() {
                const canvas = cropper.getCroppedCanvas();
                canvas.toBlob(function(blob) {
                    const formData = new FormData();
                    formData.append('croppedImage', blob, file.name);

                    // Replace the original file input with the cropped image
                    const newFileInput = document.createElement('input');
                    newFileInput.type = 'hidden';
                    newFileInput.name = 'instructor_image';
                    newFileInput.value = canvas.toDataURL('image/jpeg');
                    document.querySelector('form').appendChild(newFileInput);

                    cropperContainer.style.display = 'none';
                    document.getElementById('cropButton').style.display = 'none';
                });
            });
        }
        reader.readAsDataURL(file);
    }
});

// Logic for resizing images based on user input
document.querySelector('form').addEventListener('submit', function(e) {
    const widthInput = document.querySelector('input[name="width"]');
    const heightInput = document.querySelector('input[name="height"]');
    
    if (widthInput.value && heightInput.value) {
        const width = parseInt(widthInput.value);
        const height = parseInt(heightInput.value);
        
        // Resize logic can be added here if needed
        // For now, we will just log the values
        console.log(`Resizing image to ${width}x${height}`);
    }
});
