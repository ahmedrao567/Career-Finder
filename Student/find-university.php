<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Admission Predictor - Pakistan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e40af',
                        secondary: '#3b82f6',
                        accent: '#6366f1',
                        success: '#10b981',
                        warning: '#f59e0b',
                        danger: '#ef4444'
                    }
                }
            }
        }
    </script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .loading-spinner {
            border: 3px solid #f3f4f6;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="gradient-bg text-white py-12">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                <i class="fas fa-university mr-3"></i>
                University Admission Predictor
            </h1>
            <p class="text-xl opacity-90 max-w-3xl mx-auto">
                Discover your ideal university in Pakistan based on your academic performance and preferences
            </p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8 -mt-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Input Section -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl p-6 sticky top-8 card-hover">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-user-graduate text-primary mr-3"></i>
                        Your Profile
                    </h2>

                    <form id="meritForm" class="space-y-6">
                        <!-- Matric Marks -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-award text-primary mr-2"></i>
                                Matric Marks (%)
                            </label>
                            <div class="relative">
                                <input type="number" id="matricMarks" min="0" max="100" step="0.01" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 pr-12" 
                                       placeholder="85.5" required>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-gray-500">%</span>
                                </div>
                            </div>
                        </div>

                        <!-- FSC Marks -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-graduation-cap text-primary mr-2"></i>
                                FSC Marks (%)
                            </label>
                            <div class="relative">
                                <input type="number" id="fscMarks" min="0" max="100" step="0.01" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 pr-12" 
                                       placeholder="78.2" required>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-gray-500">%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Preference Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-heart text-primary mr-2"></i>
                                Field Preference
                            </label>
                            <select id="preference" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200" required>
                                <option value="">Choose your field...</option>
                                <option value="Engineering & Technology">Engineering & Technology</option>
                                <option value="Medical & Health Sciences">Medical & Health Sciences</option>
                                <option value="Computer Science & IT">Computer Science & IT</option>
                                <option value="Business & Management">Business & Management</option>
                                <option value="Natural Sciences">Natural Sciences</option>
                                <option value="Arts & Humanities">Arts & Humanities</option>
                                <option value="Social Sciences">Social Sciences</option>
                                <option value="Law & Legal Studies">Law & Legal Studies</option>
                                <option value="Education">Education</option>
                                <option value="Agriculture">Agriculture</option>
                                <option value="Architecture & Planning">Architecture & Planning</option>
                                <option value="Pharmacy">Pharmacy</option>
                                <option value="Media & Communication">Media & Communication</option>
                            </select>
                        </div>

                        <!-- Calculate Button -->
                        <button type="submit" id="calculateBtn"
                                class="w-full bg-gradient-to-r from-primary to-accent text-white py-4 px-6 rounded-xl font-semibold text-lg hover:from-blue-700 hover:to-indigo-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                            <i class="fas fa-search mr-2"></i>
                            Find My Universities
                        </button>
                    </form>

                    <!-- Quick Tips -->
                    <div class="mt-8 p-4 bg-blue-50 rounded-xl border border-blue-200">
                        <h3 class="font-semibold text-blue-800 mb-2 flex items-center">
                            <i class="fas fa-lightbulb mr-2"></i>
                            Pro Tip
                        </h3>
                        <p class="text-sm text-blue-600">
                            Entry test marks are calculated as the average of your Matric and FSC scores. Focus on improving both for better chances!
                        </p>
                    </div>
                </div>
            </div>

            <!-- Results Section -->
            <div class="lg:col-span-3">
                <!-- Loading State -->
                <div id="loadingSection" class="hidden bg-white rounded-2xl shadow-xl p-12 text-center">
                    <div class="loading-spinner mx-auto mb-4"></div>
                    <h3 class="text-xl font-semibold text-gray-700">Finding Your Best Universities</h3>
                    <p class="text-gray-500 mt-2">Analyzing your scores and preferences...</p>
                </div>

                <!-- Overall Merit Score -->
                <div id="meritScoreSection" class="hidden bg-white rounded-2xl shadow-xl p-6 mb-8 fade-in">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Overall Merit -->
                        <div class="bg-gradient-to-br from-green-50 to-emerald-100 p-6 rounded-xl border border-green-200 text-center card-hover">
                            <div class="text-4xl font-bold text-green-600 mb-2" id="overallMerit">0.00%</div>
                            <div class="text-green-700 font-semibold">Overall Merit Score</div>
                            <div class="text-sm text-green-600 mt-2" id="meritCategory">-</div>
                        </div>

                        <!-- Field Preference -->
                        <div class="bg-gradient-to-br from-blue-50 to-cyan-100 p-6 rounded-xl border border-blue-200 text-center card-hover">
                            <div class="text-2xl font-bold text-blue-600 mb-2" id="preferenceName">-</div>
                            <div class="text-blue-700 font-semibold">Your Preference</div>
                            <div class="text-sm text-blue-600 mt-2" id="preferenceDescription">-</div>
                        </div>

                        <!-- Admission Chance -->
                        <div class="bg-gradient-to-br from-purple-50 to-indigo-100 p-6 rounded-xl border border-purple-200 text-center card-hover">
                            <div class="text-2xl font-bold text-purple-600 mb-2" id="admissionChance">-</div>
                            <div class="text-purple-700 font-semibold">Admission Outlook</div>
                            <div class="text-sm text-purple-600 mt-2" id="chanceDescription">-</div>
                        </div>
                    </div>

                    <!-- Score Breakdown -->
                    <div class="mt-6 bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-chart-pie text-primary mr-2"></i>
                            Score Breakdown
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center" id="scoreBreakdown">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- University Suggestions -->
                <div id="universitySuggestions" class="hidden fade-in">
                    <!-- High Chance Universities -->
                    <div id="highChanceSection" class="hidden bg-white rounded-2xl shadow-xl p-6 mb-8">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                                <i class="fas fa-trophy text-success mr-3"></i>
                                High Chance Universities
                            </h2>
                            <span class="bg-success text-white px-3 py-1 rounded-full text-sm font-semibold">
                                <i class="fas fa-check-circle mr-1"></i>
                                Excellent Match
                            </span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="highChanceList">
                            <!-- High chance universities will be populated here -->
                        </div>
                    </div>

                    <!-- Moderate Chance Universities -->
                    <div id="moderateChanceSection" class="hidden bg-white rounded-2xl shadow-xl p-6 mb-8">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                                <i class="fas fa-star text-warning mr-3"></i>
                                Moderate Chance Universities
                            </h2>
                            <span class="bg-warning text-white px-3 py-1 rounded-full text-sm font-semibold">
                                <i class="fas fa-star-half-alt mr-1"></i>
                                Good Match
                            </span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="moderateChanceList">
                            <!-- Moderate chance universities will be populated here -->
                        </div>
                    </div>

                    <!-- Reach Universities -->
                    <div id="reachSection" class="hidden bg-white rounded-2xl shadow-xl p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                                <i class="fas fa-bullseye text-danger mr-3"></i>
                                Reach Universities
                            </h2>
                            <span class="bg-danger text-white px-3 py-1 rounded-full text-sm font-semibold">
                                <i class="fas fa-flag mr-1"></i>
                                Challenging
                            </span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="reachList">
                            <!-- Reach universities will be populated here -->
                        </div>
                    </div>

                    <!-- No Results Message -->
                    <div id="noResultsSection" class="hidden bg-white rounded-2xl shadow-xl p-12 text-center">
                        <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-university text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No Universities Found</h3>
                        <p class="text-gray-500 mb-4">We couldn't find any universities matching your criteria.</p>
                        <p class="text-sm text-gray-400">Try adjusting your scores or field preference</p>
                    </div>
                </div>

                <!-- Initial State - Field Information -->
                <div id="fieldInfo" class="bg-white rounded-2xl shadow-xl p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-info-circle text-primary mr-3"></i>
                        Field Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="fieldInfoContent">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-16 py-8">
        <div class="container mx-auto px-4 text-center">
            <p class="text-gray-400">
                &copy; 2024 University Admission Predictor. Designed for Pakistani Students.
            </p>
            <p class="text-gray-500 text-sm mt-2">
                Predictions are based on historical data and merit trends. Actual admission may vary.
            </p>
        </div>
    </footer>

    <script>
        // Global variables
        let universitiesData = [];
        let fieldDescriptions = {};

        // Field descriptions
        const initializeFieldDescriptions = () => {
            fieldDescriptions = {
                'Engineering & Technology': {
                    name: 'Engineering & Technology',
                    description: 'Build the future with innovation and technology',
                    icon: 'cogs',
                    color: 'orange',
                    weightage: { matric: 0.10, fsc: 0.40, test: 0.50 }
                },
                'Medical & Health Sciences': {
                    name: 'Medical Sciences',
                    description: 'Serve humanity through healthcare and medicine',
                    icon: 'heartbeat',
                    color: 'red',
                    weightage: { matric: 0.10, fsc: 0.40, test: 0.50 }
                },
                'Computer Science & IT': {
                    name: 'Computer Science & IT',
                    description: 'Shape the digital world with code and innovation',
                    icon: 'laptop-code',
                    color: 'blue',
                    weightage: { matric: 0.15, fsc: 0.45, test: 0.40 }
                },
                'Business & Management': {
                    name: 'Business & Management',
                    description: 'Lead organizations and drive economic growth',
                    icon: 'chart-line',
                    color: 'green',
                    weightage: { matric: 0.15, fsc: 0.45, test: 0.40 }
                },
                'Natural Sciences': {
                    name: 'Natural Sciences',
                    description: 'Explore the mysteries of the natural world',
                    icon: 'atom',
                    color: 'purple',
                    weightage: { matric: 0.20, fsc: 0.50, test: 0.30 }
                },
                'Arts & Humanities': {
                    name: 'Arts & Humanities',
                    description: 'Express creativity and understand human culture',
                    icon: 'palette',
                    color: 'pink',
                    weightage: { matric: 0.25, fsc: 0.55, test: 0.20 }
                },
                'Social Sciences': {
                    name: 'Social Sciences',
                    description: 'Understand human behavior and society',
                    icon: 'users',
                    color: 'indigo',
                    weightage: { matric: 0.20, fsc: 0.50, test: 0.30 }
                },
                'Law & Legal Studies': {
                    name: 'Law & Legal Studies',
                    description: 'Uphold justice and legal systems',
                    icon: 'balance-scale',
                    color: 'yellow',
                    weightage: { matric: 0.20, fsc: 0.50, test: 0.30 }
                },
                'Education': {
                    name: 'Education',
                    description: 'Shape future generations through teaching',
                    icon: 'chalkboard-teacher',
                    color: 'teal',
                    weightage: { matric: 0.25, fsc: 0.55, test: 0.20 }
                },
                'Agriculture': {
                    name: 'Agriculture',
                    description: 'Advance food production and sustainability',
                    icon: 'tractor',
                    color: 'lime',
                    weightage: { matric: 0.20, fsc: 0.50, test: 0.30 }
                },
                'Architecture & Planning': {
                    name: 'Architecture & Planning',
                    description: 'Design sustainable and beautiful spaces',
                    icon: 'ruler-combined',
                    color: 'amber',
                    weightage: { matric: 0.15, fsc: 0.45, test: 0.40 }
                },
                'Pharmacy': {
                    name: 'Pharmacy',
                    description: 'Develop medicines and healthcare solutions',
                    icon: 'pills',
                    color: 'emerald',
                    weightage: { matric: 0.15, fsc: 0.45, test: 0.40 }
                },
                'Media & Communication': {
                    name: 'Media & Communication',
                    description: 'Create compelling stories and content',
                    icon: 'broadcast-tower',
                    color: 'rose',
                    weightage: { matric: 0.20, fsc: 0.50, test: 0.30 }
                }
            };
        };

        // Initialize field info display
        const initializeFieldInfo = () => {
            const container = document.getElementById('fieldInfoContent');
            let html = '';
            
            Object.entries(fieldDescriptions).forEach(([key, field]) => {
                html += `
                    <div class="bg-gradient-to-br from-${field.color}-50 to-${field.color}-100 p-6 rounded-xl border border-${field.color}-200 card-hover">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-${field.color}-500 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-${field.icon} text-white text-xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-${field.color}-800">${field.name}</h3>
                        </div>
                        <p class="text-${field.color}-700 text-sm mb-4">
                            ${field.description}
                        </p>
                        <div class="text-xs text-${field.color}-600">
                            <i class="fas fa-percentage mr-1"></i>
                            Formula: Matric ${(field.weightage.matric * 100)}% + FSC ${(field.weightage.fsc * 100)}% + Test ${(field.weightage.test * 100)}%
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        };

        // Fetch universities data from database
        const fetchUniversitiesData = async () => {
            try {
                const response = await fetch('fetch_universities.php');
                const data = await response.json();
                
                if (data.success) {
                    universitiesData = data.universities;
                    console.log('Loaded universities:', universitiesData);
                } else {
                    throw new Error(data.message || 'Failed to load universities data');
                }
            } catch (error) {
                console.error('Error fetching universities:', error);
                showNotification('Error loading university data. Please try again.', 'error');
            }
        };

        // Form submission
        document.getElementById('meritForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            await calculateAndSuggest();
        });

        // Main calculation function
        async function calculateAndSuggest() {
            const matricMarks = parseFloat(document.getElementById('matricMarks').value);
            const fscMarks = parseFloat(document.getElementById('fscMarks').value);
            const preference = document.getElementById('preference').value;

            if (!preference || isNaN(matricMarks) || isNaN(fscMarks)) {
                alert('Please fill in all fields with valid numbers.');
                return;
            }

            // Show loading state
            showLoadingState(true);

            // Ensure we have universities data
            if (universitiesData.length === 0) {
                await fetchUniversitiesData();
            }

            // Calculate entry test marks (average of matric and fsc)
            const entryTestMarks = (matricMarks + fscMarks) / 2;

            // Calculate overall merit based on preference category
            const meritScore = calculateMeritScore(matricMarks, fscMarks, entryTestMarks, preference);

            // Display results
            displayResults(meritScore, preference, matricMarks, fscMarks, entryTestMarks);

            // Suggest universities
            suggestUniversities(meritScore, preference);

            // Hide loading state
            showLoadingState(false);
        }

        function calculateMeritScore(matric, fsc, test, preference) {
            const fieldInfo = fieldDescriptions[preference];
            if (!fieldInfo) {
                // Default weightage if field not found
                return (matric * 0.15) + (fsc * 0.45) + (test * 0.40);
            }
            
            const weights = fieldInfo.weightage;
            return (matric * weights.matric) + (fsc * weights.fsc) + (test * weights.test);
        }

        function displayResults(meritScore, preference, matric, fsc, test) {
            // Show merit score section
            document.getElementById('meritScoreSection').classList.remove('hidden');
            document.getElementById('fieldInfo').classList.add('hidden');
            
            // Update merit score
            document.getElementById('overallMerit').textContent = meritScore.toFixed(2) + '%';

            // Update preference info
            const fieldInfo = fieldDescriptions[preference] || { name: preference, description: 'Your chosen field' };
            document.getElementById('preferenceName').textContent = fieldInfo.name;
            document.getElementById('preferenceDescription').textContent = fieldInfo.description;

            // Determine admission chance
            let admissionChance, chanceDescription, chanceColor;
            if (meritScore >= 80) {
                admissionChance = 'Excellent';
                chanceDescription = 'High probability of admission in top universities';
                chanceColor = 'text-green-600';
            } else if (meritScore >= 70) {
                admissionChance = 'Good';
                chanceDescription = 'Good chances in reputable universities';
                chanceColor = 'text-blue-600';
            } else if (meritScore >= 60) {
                admissionChance = 'Moderate';
                chanceDescription = 'Consider multiple options and backups';
                chanceColor = 'text-yellow-600';
            } else {
                admissionChance = 'Improve';
                chanceDescription = 'Focus on improving your scores';
                chanceColor = 'text-red-600';
            }

            document.getElementById('admissionChance').textContent = admissionChance;
            document.getElementById('admissionChance').className = `text-2xl font-bold ${chanceColor} mb-2`;
            document.getElementById('chanceDescription').textContent = chanceDescription;

            // Update merit category
            document.getElementById('meritCategory').textContent = getMeritCategory(meritScore);

            // Update score breakdown
            updateScoreBreakdown(matric, fsc, test, preference);
        }

        function updateScoreBreakdown(matric, fsc, test, preference) {
            const fieldInfo = fieldDescriptions[preference];
            const weights = fieldInfo ? fieldInfo.weightage : { matric: 0.15, fsc: 0.45, test: 0.40 };
            
            const breakdown = document.getElementById('scoreBreakdown');
            breakdown.innerHTML = `
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600 mb-1">${(matric * weights.matric).toFixed(1)}%</div>
                    <div class="text-sm text-gray-600">Matric (${(weights.matric * 100)}%)</div>
                    <div class="text-xs text-gray-500">${matric}% × ${(weights.matric * 100)}%</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600 mb-1">${(fsc * weights.fsc).toFixed(1)}%</div>
                    <div class="text-sm text-gray-600">FSC (${(weights.fsc * 100)}%)</div>
                    <div class="text-xs text-gray-500">${fsc}% × ${(weights.fsc * 100)}%</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600 mb-1">${(test * weights.test).toFixed(1)}%</div>
                    <div class="text-sm text-gray-600">Test (${(weights.test * 100)}%)</div>
                    <div class="text-xs text-gray-500">${test.toFixed(1)}% × ${(weights.test * 100)}%</div>
                </div>
            `;
        }

        function getMeritCategory(score) {
            if (score >= 90) return 'Outstanding';
            if (score >= 80) return 'Excellent';
            if (score >= 70) return 'Very Good';
            if (score >= 60) return 'Good';
            if (score >= 50) return 'Average';
            return 'Needs Improvement';
        }

        function suggestUniversities(meritScore, preference) {
            // Filter universities by preference and calculate chances
            const suggestions = universitiesData.map(uni => {
                // Find relevant programs for this preference
                const relevantPrograms = uni.programs.filter(program => 
                    program.category === preference
                );
                
                if (relevantPrograms.length === 0) return null;

                // Find the program with the lowest closing merit (easiest to get into)
                const bestProgram = relevantPrograms.reduce((best, current) => 
                    current.closing_merit < best.closing_merit ? current : best
                );

                const scoreDifference = meritScore - bestProgram.closing_merit;
                let chance;

                if (scoreDifference >= 5) chance = 'high';
                else if (scoreDifference >= -2) chance = 'moderate';
                else chance = 'reach';

                return {
                    ...uni,
                    bestProgram: bestProgram.name,
                    closingMerit: bestProgram.closing_merit,
                    scoreDifference: scoreDifference,
                    chance: chance
                };
            }).filter(uni => uni !== null);

            // Display suggestions
            displayUniversitySuggestions(suggestions);
        }

        function displayUniversitySuggestions(suggestions) {
            const highChance = suggestions.filter(uni => uni.chance === 'high');
            const moderateChance = suggestions.filter(uni => uni.chance === 'moderate');
            const reach = suggestions.filter(uni => uni.chance === 'reach');

            // Show/hide sections based on results
            document.getElementById('highChanceSection').classList.toggle('hidden', highChance.length === 0);
            document.getElementById('moderateChanceSection').classList.toggle('hidden', moderateChance.length === 0);
            document.getElementById('reachSection').classList.toggle('hidden', reach.length === 0);
            document.getElementById('noResultsSection').classList.toggle('hidden', suggestions.length > 0);

            // Display university lists
            displayUniversityList('highChanceList', highChance, 'success');
            displayUniversityList('moderateChanceList', moderateChance, 'warning');
            displayUniversityList('reachList', reach, 'danger');

            // Show suggestions section
            document.getElementById('universitySuggestions').classList.remove('hidden');
        }

        function displayUniversityList(elementId, universities, color) {
            const container = document.getElementById(elementId);
            
            if (universities.length === 0) {
                container.innerHTML = '';
                return;
            }

            container.innerHTML = universities.map(uni => {
                const differenceText = uni.scoreDifference >= 0 ? 
                    `+${uni.scoreDifference.toFixed(1)}% above` : 
                    `${Math.abs(uni.scoreDifference).toFixed(1)}% below`;
                
                const differenceColor = uni.scoreDifference >= 0 ? 'text-green-600' : 'text-red-600';

                return `
                    <div class="bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition-all duration-200 card-hover">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="font-bold text-lg text-gray-900">${uni.name}</h3>
                            <span class="bg-${color}-100 text-${color}-600 px-2 py-1 rounded-full text-xs font-semibold">
                                ${uni.type || 'Public'}
                            </span>
                        </div>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-map-marker-alt mr-2 text-${color}-500"></i>
                                ${uni.location}
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-graduation-cap mr-2 text-${color}-500"></i>
                                ${uni.bestProgram}
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                            <span class="text-sm font-medium text-gray-700">Closing Merit:</span>
                            <span class="text-lg font-bold text-${color}-600">
                                ${uni.closingMerit}%
                            </span>
                        </div>
                        
                        <div class="mt-3 text-xs ${differenceColor}">
                            <i class="fas fa-chart-line mr-1"></i>
                            Your score: ${differenceText}
                        </div>
                    </div>
                `;
            }).join('');
        }

        function showLoadingState(show) {
            if (show) {
                document.getElementById('loadingSection').classList.remove('hidden');
                document.getElementById('meritScoreSection').classList.add('hidden');
                document.getElementById('universitySuggestions').classList.add('hidden');
                document.getElementById('fieldInfo').classList.add('hidden');
                document.getElementById('calculateBtn').disabled = true;
            } else {
                document.getElementById('loadingSection').classList.add('hidden');
                document.getElementById('calculateBtn').disabled = false;
            }
        }

        function showNotification(message, type = 'info') {
            // Simple notification implementation
            alert(message);
        }

        // Input validation
        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('input', function() {
                let value = parseFloat(this.value);
                if (value < 0) this.value = 0;
                if (value > 100) this.value = 100;
                if (this.value.length > 5) this.value = this.value.slice(0, 5);
            });
        });

        // Initialize the application
        document.addEventListener('DOMContentLoaded', async function() {
            // Initialize field descriptions
            initializeFieldDescriptions();
            initializeFieldInfo();
            
            // Load universities data in background
            await fetchUniversitiesData();
        });
    </script>
</body>
</html>