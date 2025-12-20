document.addEventListener('DOMContentLoaded', function() {
    const kudosForms = document.querySelectorAll('form.kudos');
    
    kudosForms.forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const button = form.querySelector('button[type="submit"]');
            const img = button.querySelector('img');
            const kudosDisplay = form.closest('.activity-footer').querySelector('.kudos-display button');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            try {
                const response = await fetch('/kudos', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                const responseData = await response.json();
                
                if (responseData.success) {
                    kudosDisplay.textContent = `${responseData.kudos_count} kudos`;
                    
                    img.src = responseData.liked 
                        ? "/images/icons/social/liked.png"
                        : "/images/icons/social/like.png";
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    });
});




