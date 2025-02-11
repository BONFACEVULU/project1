// Image preview and upload handling
document.addEventListener('DOMContentLoaded', function() {
    const imageInputs = document.querySelectorAll('input[type="file"]');
    
    imageInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = this.files[0];
            const preview = this.closest('.image-upload-container').querySelector('.image-preview');
            
            if (file) {
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <img src="${e.target.result}" class="preview-image" alt="Preview">
                        <div class="preview-overlay">
                            <span class="preview-text">Preview</span>
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
                
                // Validate file
                if (!file.type.match('image.*')) {
                    alert('Please upload an image file');
                    this.value = '';
                    preview.innerHTML = '';
                    return;
                }
                
                if (file.size > 5 * 1024 * 1024) { // 5MB
                    alert('File is too large. Maximum size is 5MB');
                    this.value = '';
                    preview.innerHTML = '';
                    return;
                }
            }
        });
    });
});

// Form submission with image upload
function submitForm(formElement) {
    formElement.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitButton = formElement.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        submitButton.disabled = true;
        
        try {
            const formData = new FormData(this);
            const response = await fetch('process_upload.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Update hidden input with image path
                const imagePathInput = formElement.querySelector('input[name="image_path"]');
                if (imagePathInput) {
                    imagePathInput.value = result.path;
                }
                
                // Submit the form
                formElement.submit();
            } else {
                alert(result.error || 'Upload failed');
            }
        } catch (error) {
            alert('An error occurred. Please try again.');
        } finally {
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        }
    });
}
