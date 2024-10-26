<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Engine Index Checker</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-6 text-center">Search Engine Index Checker</h1>
            
            <!-- Form Input -->
            <div class="mb-6">
  <h2 class="text-lg font-semibold mb-3 text-center">Check Domain Indexing</h2>
  <div class="space-y-4">
    <div>
      <label class="block mb-2">Enter domains (one per line):</label>
      <textarea id="domains" class="w-full h-32 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="example.com&#10;another-domain.com"></textarea>
                    </div>
                    
                    <div>
                        <label class="block mb-2">Or upload a file (CSV/TXT):</label>
                        <input 
                            type="file" 
                            id="file-upload" 
                            accept=".csv,.txt"
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <button 
                        id="check-button"
                        class="w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Check Domains
                    </button>
                </div>
            </div>
            
            <!-- Loading Indicator -->
            <div id="loading" class="hidden">
                <div class="flex items-center justify-center space-x-2">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div>
                    <span>Checking domains...</span>
                </div>
            </div>
            
            <!-- Error Message -->
            <div id="error" class="hidden bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4"></div>
            
            <!-- Results Table -->
            <div id="results" class="hidden">
                <h3 class="text-lg font-semibold mb-3">Results</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Domain</th>
                                <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Google</th>
                                <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Bing</th>
                            </tr>
                        </thead>
                        <tbody id="results-body" class="bg-white divide-y divide-gray-200"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/js/script.js"></script>
</body>
</html>