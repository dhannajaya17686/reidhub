// Event Form Handler
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('create-event-form') || document.getElementById('edit-event-form');
    const fileInput = document.getElementById('event-image');
    const uploadTrigger = document.getElementById('upload-trigger');
    const filePreview = document.getElementById('file-preview');
    const previewImage = document.getElementById('preview-image');
    const previewRemove = document.getElementById('preview-remove');

    if (!form) return;

    // File upload handling
    if (uploadTrigger) {
        uploadTrigger.addEventListener('click', () => fileInput.click());
    }

    if (fileInput) {
        fileInput.addEventListener('change', function (e) {
            handleFileSelect(e.target.files[0]);
        });

        // Drag and drop
        const dropZone = document.getElementById('file-upload-area');
        if (dropZone) {
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.style.backgroundColor = '#f0f0f0';
            });

            dropZone.addEventListener('dragleave', () => {
                dropZone.style.backgroundColor = '';
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.style.backgroundColor = '';
                if (e.dataTransfer.files.length) {
                    fileInput.files = e.dataTransfer.files;
                    handleFileSelect(e.dataTransfer.files[0]);
                }
            });
        }
    }

    if (previewRemove) {
        previewRemove.addEventListener('click', (e) => {
            e.preventDefault();
            fileInput.value = '';
            if (document.getElementById('create-event-form')) {
                // Creating new event - hide preview
                filePreview.style.display = 'none';
            }
            // For edit, keep showing the old image
        });
    }

    // Form submission
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Clear previous errors
        document.querySelectorAll('.form-error').forEach(el => el.textContent = '');

        const formData = new FormData(form);
        const isEdit = !!document.getElementById('edit-event-form');

        console.log('Submitting form to:', form.action);
        console.log('Form data entries:');
        for (let [key, value] of formData.entries()) {
            if (value instanceof File) {
                console.log(key, '- File:', value.name, value.size, 'bytes');
            } else {
                console.log(key, '=', value);
            }
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });

            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers.get('content-type'));

            const responseText = await response.text();
            console.log('Response text:', responseText);

            let data;
            try {
                data = JSON.parse(responseText);
            } catch (parseError) {
                console.error('JSON parse error:', parseError);
                console.error('Response was:', responseText);
                alert('Server returned invalid response: ' + responseText.substring(0, 200));
                return;
            }

            if (data.success) {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.href = '/dashboard/community/events';
                }
            } else {
                // Show error message
                alert(data.message || 'An error occurred');

                // Display field-specific errors if available
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const errorElement = document.getElementById(field + '-error');
                        if (errorElement) {
                            errorElement.textContent = data.errors[field];
                        }
                    });
                }
            }
        } catch (error) {
            console.error('Fetch error:', error);
            alert('An error occurred while processing the form: ' + error.message);
        }
    });

    function handleFileSelect(file) {
        if (!file) return;

        // Validate file type
        if (!file.type.startsWith('image/')) {
            alert('Please select a valid image file');
            return;
        }

        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB');
            return;
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = (e) => {
            if (filePreview) {
                previewImage.src = e.target.result;
                filePreview.style.display = 'block';
            }
        };
        reader.readAsDataURL(file);
    }

    // Show existing preview on page load
    if (document.getElementById('edit-event-form') && previewImage.src) {
        filePreview.style.display = 'block';
    }
});
