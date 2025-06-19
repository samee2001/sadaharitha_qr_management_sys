// Show/hide animation for success message
document.addEventListener('DOMContentLoaded', function() {
    const successAlert = document.getElementById('successAlert');
    if (successAlert) {
        successAlert.style.display = 'block';
        setTimeout(() => {
            successAlert.style.opacity = '1';
        }, 100);
        
        // Auto-hide after 3 seconds
        setTimeout(() => {
            successAlert.style.opacity = '0';
            setTimeout(() => {
                successAlert.style.display = 'none';
            }, 300);
        },1000);
    }
});