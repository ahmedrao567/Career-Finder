<?php
include 'config.php';

// Get programs for this university
$university_id = $_SESSION['university_id'];
$stmt = $pdo->prepare("SELECT * FROM programs WHERE university_id = ? ORDER BY created_at DESC");
$stmt->execute([$university_id]);
$programs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programs Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .program-card {
            transition: all 0.3s ease;
        }
        .program-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .modal-overlay {
            backdrop-filter: blur(5px);
        }
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Programs Management</h1>
                <p class="text-gray-600 mt-2">Manage all your university programs in one place</p>
            </div>
            <button onclick="openAddProgramModal()" 
                    class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center">
                <i class="fas fa-plus mr-2"></i> Add New Program
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg mr-4">
                        <i class="fas fa-graduation-cap text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Total Programs</p>
                        <h3 class="text-2xl font-bold text-gray-900"><?php echo count($programs); ?></h3>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg mr-4">
                        <i class="fas fa-tags text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Categories</p>
                        <h3 class="text-2xl font-bold text-gray-900">
                            <?php 
                            $categories = array_unique(array_column($programs, 'program_category'));
                            echo count($categories);
                            ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg mr-4">
                        <i class="fas fa-chart-line text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Avg. Closing Merit</p>
                        <h3 class="text-2xl font-bold text-gray-900">
                            <?php 
                            if (!empty($programs)) {
                                $merits = array_column($programs, 'closing_merit');
                                echo number_format(array_sum($merits) / count($merits), 2) . '%';
                            } else {
                                echo '0%';
                            }
                            ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Programs Container -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200">
                <div class="px-6 py-4">
                    <div class="flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-900">All Programs</h2>
                        <div class="relative">
                            <input type="text" id="searchPrograms" placeholder="Search programs..." 
                                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <?php if (empty($programs)): ?>
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <div class="mx-auto w-24 h-24 bg-purple-100 rounded-full flex items-center justify-center mb-6">
                            <i class="fas fa-graduation-cap text-purple-600 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">No programs yet</h3>
                        <p class="text-gray-600 mb-6">Start by adding your first program to showcase your offerings</p>
                        <button onclick="openAddProgramModal()" 
                                class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-8 rounded-lg transition duration-200 inline-flex items-center">
                            <i class="fas fa-plus mr-2"></i> Add Your First Program
                        </button>
                    </div>
                <?php else: ?>
                    <!-- Programs Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="programsGrid">
                        <?php foreach ($programs as $program): ?>
                            <div class="program-card bg-white border border-gray-200 rounded-xl p-5 hover:border-purple-300">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <span class="inline-block bg-purple-100 text-purple-800 text-xs font-semibold px-3 py-1 rounded-full mb-2">
                                            <?php echo htmlspecialchars($program['program_category']); ?>
                                        </span>
                                        <h4 class="font-bold text-gray-900 text-lg"><?php echo htmlspecialchars($program['program_name']); ?></h4>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button onclick="openEditProgramModal(
                                            <?php echo $program['id']; ?>,
                                            '<?php echo addslashes($program['program_name']); ?>',
                                            '<?php echo addslashes($program['program_category']); ?>',
                                            <?php echo $program['closing_merit']; ?>
                                        )" 
                                                class="text-blue-600 hover:text-blue-800 p-2 rounded-full hover:bg-blue-50 transition">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="openDeleteProgramModal(<?php echo $program['id']; ?>)" 
                                                class="text-red-600 hover:text-red-800 p-2 rounded-full hover:bg-red-50 transition">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="space-y-3">
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-chart-line text-green-500 mr-2"></i>
                                        <span class="text-sm">Closing Merit:</span>
                                        <span class="ml-auto font-semibold text-gray-900"><?php echo $program['closing_merit']; ?>%</span>
                                    </div>
                                    
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-calendar text-blue-500 mr-2"></i>
                                        <span class="text-sm">Added:</span>
                                        <span class="ml-auto text-sm text-gray-500">
                                            <?php echo date('M d, Y', strtotime($program['created_at'])); ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <?php if ($program['closing_merit'] >= 90): ?>
                                    <div class="mt-4 inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                        <i class="fas fa-star mr-1"></i> Highly Competitive
                                    </div>
                                <?php elseif ($program['closing_merit'] >= 75): ?>
                                    <div class="mt-4 inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">
                                        <i class="fas fa-chart-line mr-1"></i> Moderately Competitive
                                    </div>
                                <?php else: ?>
                                    <div class="mt-4 inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                                        <i class="fas fa-check mr-1"></i> Standard Admission
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Summary -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex justify-between items-center text-sm text-gray-600">
                            <div>
                                <i class="fas fa-info-circle mr-2"></i>
                                Showing <?php echo count($programs); ?> program(s)
                            </div>
                            <div>
                                <button onclick="openAddProgramModal()" 
                                        class="text-purple-600 hover:text-purple-800 font-medium">
                                    <i class="fas fa-plus mr-1"></i> Add Another Program
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add/Edit Program Modal -->
    <div id="programsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-overlay hidden">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 modal-content">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h3 class="text-2xl font-bold text-gray-900" id="programModalTitle">Add New Program</h3>
                <button type="button" onclick="closeModal('programsModal')" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="programForm" class="p-6 space-y-6">
                <input type="hidden" id="program_id" name="program_id" value="">
                
                <!-- Program Name -->
                <div>
                    <label for="program_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Program Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="program_name" name="program_name" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 input-focus transition"
                        placeholder="e.g., Bachelor of Computer Science">
                </div>

                <!-- Program Category -->
                <div>
                    <label for="program_category" class="block text-sm font-medium text-gray-700 mb-2">
                        Program Category <span class="text-red-500">*</span>
                    </label>
                    <select id="program_category" name="program_category" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 input-focus transition">
                        <option value="">Select Category</option>
                        <option value="Engineering & Technology">Engineering & Technology</option>
                        <option value="Computer Science & IT">Computer Science & IT</option>
                        <option value="Business & Management">Business & Management</option>
                        <option value="Medical & Health Sciences">Medical & Health Sciences</option>
                        <option value="Natural Sciences">Natural Sciences</option>
                        <option value="Social Sciences">Social Sciences</option>
                        <option value="Arts & Humanities">Arts & Humanities</option>
                        <option value="Law & Legal Studies">Law & Legal Studies</option>
                        <option value="Education">Education</option>
                        <option value="Agriculture">Agriculture</option>
                        <option value="Architecture">Architecture</option>
                        <option value="Pharmacy">Pharmacy</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <!-- Closing Merit -->
                <div>
                    <label for="closing_merit" class="block text-sm font-medium text-gray-700 mb-2">
                        Last Closing Merit <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" id="closing_merit" name="closing_merit" step="0.01" min="0" max="100" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 input-focus transition pr-12"
                            placeholder="e.g., 85.50">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <span class="text-gray-500">%</span>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Enter the percentage score for last year's closing merit</p>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal('programsModal')"
                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                        Cancel
                    </button>
                    <button type="submit" id="saveProgramBtn"
                        class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium">
                        <i class="fas fa-save mr-2"></i>
                        <span id="saveBtnText">Save Program</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteProgramModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-overlay hidden">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 modal-content">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Delete Program</h3>
                <p class="text-gray-600 text-center mb-6">Are you sure you want to delete this program? This action cannot be undone.</p>
                
                <div class="flex justify-center space-x-3">
                    <button type="button" onclick="closeModal('deleteProgramModal')"
                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                        Cancel
                    </button>
                    <button type="button" id="confirmDeleteProgram"
                        class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                        <i class="fas fa-trash mr-2"></i>
                        Delete Program
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Toast -->
    <div id="toast" class="fixed top-4 right-4 z-50 hidden">
        <div class="bg-white border rounded-xl shadow-lg p-4 max-w-sm">
            <div class="flex items-center">
                <div id="toastIcon" class="mr-3"></div>
                <div>
                    <h4 id="toastTitle" class="font-semibold text-gray-900"></h4>
                    <p id="toastMessage" class="text-gray-600 text-sm mt-1"></p>
                </div>
                <button onclick="hideToast()" class="ml-4 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        // Modal Functions
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Program Modal Functions
        function openAddProgramModal() {
            document.getElementById('programModalTitle').textContent = 'Add New Program';
            document.getElementById('programForm').reset();
            document.getElementById('program_id').value = '';
            document.getElementById('saveBtnText').textContent = 'Save Program';
            openModal('programsModal');
        }

        function openEditProgramModal(programId, programName, programCategory, closingMerit) {
            document.getElementById('programModalTitle').textContent = 'Edit Program';
            document.getElementById('program_id').value = programId;
            document.getElementById('program_name').value = programName;
            document.getElementById('program_category').value = programCategory;
            document.getElementById('closing_merit').value = closingMerit;
            document.getElementById('saveBtnText').textContent = 'Update Program';
            openModal('programsModal');
        }

        function openDeleteProgramModal(programId) {
            window.currentProgramId = programId;
            openModal('deleteProgramModal');
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                const modalId = e.target.id;
                closeModal(modalId);
            }
        });

        // Toast Notification
        function showToast(type, title, message) {
            const toast = document.getElementById('toast');
            const icon = document.getElementById('toastIcon');
            const toastTitle = document.getElementById('toastTitle');
            const toastMessage = document.getElementById('toastMessage');
            
            // Set colors and icon based on type
            let bgColor, iconHtml;
            if (type === 'success') {
                bgColor = 'border-green-200 bg-green-50';
                iconHtml = '<div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center"><i class="fas fa-check text-green-600"></i></div>';
            } else if (type === 'error') {
                bgColor = 'border-red-200 bg-red-50';
                iconHtml = '<div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center"><i class="fas fa-times text-red-600"></i></div>';
            } else {
                bgColor = 'border-blue-200 bg-blue-50';
                iconHtml = '<div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center"><i class="fas fa-info text-blue-600"></i></div>';
            }
            
            toast.className = `fixed top-4 right-4 z-50 ${bgColor} border rounded-xl shadow-lg p-4 max-w-sm`;
            icon.innerHTML = iconHtml;
            toastTitle.textContent = title;
            toastMessage.textContent = message;
            toast.classList.remove('hidden');
            
            // Auto-hide after 5 seconds
            setTimeout(hideToast, 5000);
        }

        function hideToast() {
            document.getElementById('toast').classList.add('hidden');
        }

        // Handle program form submission
        document.getElementById('programForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'save_program');
            
            // Show loading state
            const submitBtn = document.getElementById('saveProgramBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
            submitBtn.disabled = true;
            
            fetch('save_program.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', 'Success!', data.message);
                    closeModal('programsModal');
                    
                    // Reload the page to show updated programs
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showToast('error', 'Error!', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Network Error', 'Failed to save program. Please try again.');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });

        // Handle delete confirmation
        document.getElementById('confirmDeleteProgram').addEventListener('click', function() {
            if (!window.currentProgramId) return;
            
            const formData = new FormData();
            formData.append('action', 'delete_program');
            formData.append('program_id', window.currentProgramId);
            
            // Show loading state
            const deleteBtn = this;
            const originalText = deleteBtn.innerHTML;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Deleting...';
            deleteBtn.disabled = true;
            
            fetch('save_program.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', 'Success!', data.message);
                    closeModal('deleteProgramModal');
                    
                    // Reload the page to show updated programs
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showToast('error', 'Error!', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Network Error', 'Failed to delete program. Please try again.');
            })
            .finally(() => {
                deleteBtn.innerHTML = originalText;
                deleteBtn.disabled = false;
                window.currentProgramId = null;
            });
        });

        // Search functionality
        document.getElementById('searchPrograms').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const programCards = document.querySelectorAll('.program-card');
            
            programCards.forEach(card => {
                const programName = card.querySelector('h4').textContent.toLowerCase();
                const programCategory = card.querySelector('span').textContent.toLowerCase();
                
                if (programName.includes(searchTerm) || programCategory.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>