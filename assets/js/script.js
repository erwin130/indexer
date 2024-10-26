document.addEventListener('DOMContentLoaded', function() {
    const domainsTextarea = document.getElementById('domains');
    const fileUpload = document.getElementById('file-upload');
    const checkButton = document.getElementById('check-button');
    const loadingDiv = document.getElementById('loading');
    const errorDiv = document.getElementById('error');
    const resultsDiv = document.getElementById('results');
    const resultsBody = document.getElementById('results-body');
    
    // Handle file upload
    fileUpload.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                domainsTextarea.value = e.target.result;
            };
            reader.readAsText(file);
        }
    });
    
    // Handle form submission
    checkButton.addEventListener('click', async function() {
        try {
            // Reset UI
            errorDiv.classList.add('hidden');
            resultsDiv.classList.add('hidden');
            loadingDiv.classList.remove('hidden');
            checkButton.disabled = true;
            
            // Get domains
            const domains = domainsTextarea.value
                .split('\n')
                .map(d => d.trim())
                .filter(d => d);
                
            if (domains.length === 0) {
                throw new Error('Please enter at least one domain');
            }
            
            // Make API request
            const response = await fetch('api/check-index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ domains })
            });
            
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Invalid response format from server');
            }
            
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.error || 'Failed to check domains');
            }
            
            displayResults(data.data);
            
        } catch (error) {
            console.error('Error:', error);
            showError(error.message || 'An unexpected error occurred');
        } finally {
            loadingDiv.classList.add('hidden');
            checkButton.disabled = false;
        }
    });
    
    function showError(message) {
        errorDiv.textContent = message;
        errorDiv.classList.remove('hidden');
        loadingDiv.classList.add('hidden');
    }
    
    function displayResults(results) {
        resultsBody.innerHTML = '';
        
        results.forEach(result => {
            const row = document.createElement('tr');
            
            // Domain cell
            const domainCell = document.createElement('td');
            domainCell.className = 'px-6 py-4 whitespace-nowrap';
            domainCell.textContent = result.domain;
            
            // Google cell
            const googleCell = document.createElement('td');
            googleCell.className = 'px-6 py-4 whitespace-nowrap text-center';
            googleCell.innerHTML = getStatusIcon(result.google);
            
            // Bing cell
            const bingCell = document.createElement('td');
            bingCell.className = 'px-6 py-4 whitespace-nowrap text-center';
            bingCell.innerHTML = getStatusIcon(result.bing);
            
            row.appendChild(domainCell);
            row.appendChild(googleCell);
            row.appendChild(bingCell);
            
            // Add error message if exists
            if (result.error) {
                const errorCell = document.createElement('td');
                errorCell.className = 'px-6 py-4 text-red-500 text-sm';
                errorCell.textContent = result.error;
                row.appendChild(errorCell);
            }
            
            resultsBody.appendChild(row);
        });
        
        resultsDiv.classList.remove('hidden');
    }
    
    function getStatusIcon(status) {
        if (status === true) {
            return '<svg class="w-6 h-6 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
        } else {
            return '<svg class="w-6 h-6 text-red-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
        }
    }
});