<?php
include 'config.php';

$university_id = $_SESSION['university_id'];
$university = getUniversityProfile($pdo, $university_id);
$categories = getUniversityCategories();
$provinces = getProvinces();

// Handle campuses and contact info JSON decoding
$campuses = [];
$contact_info = [];

if ($university['campuses']) {
    $campuses = json_decode($university['campuses'], true) ?: [];
}
if ($university['contact_info']) {
    $contact_info = json_decode($university['contact_info'], true) ?: [];
} // your DB connection file

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (strlen($password) < 6) {
        $message = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        // Hash password before saving (for security)
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert into universities_detail table
        $stmt = $pdo->prepare("UPDATE universities SET password = :password WHERE id = :university_id");
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':university_id', $university_id);

        if ($stmt->execute()) {
            $message = "Password saved successfully!";
        } else {
            $message = "Error saving password.";
        }
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Profile - CareerFinder</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
        }

        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.2);
        }

        .modal-overlay {
            backdrop-filter: blur(4px);
        }

        .modal-content {
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="bg-gray-50">
    <?php include 'navbar.php'; ?>

    <div class="max-w-6xl mx-auto py-8 px-4">
        <!-- Cover Photo Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="relative">
                <!-- Cover Photo -->
                <div class="h-48 bg-gradient-to-r from-purple-600 to-blue-600 rounded-t-lg relative">
                    <!-- Cover photo can be added later -->
                </div>

                <!-- University Logo and Basic Info -->
                <div class="absolute -bottom-8 left-8">
                    <div class="relative">
                        <div class="w-32 h-32 bg-white rounded-full p-2 shadow-lg">
                            <div class="w-full h-full bg-purple-200 rounded-full flex items-center justify-center overflow-hidden">
                                <?php if ($university['logo']): ?>
                                    <img src="assets/uploads/<?php echo $university['logo']; ?>"
                                        alt="<?php echo htmlspecialchars($university['university_name']); ?>"
                                        class="w-full h-full object-cover rounded-full">
                                <?php else: ?>
                                    <div class="w-full h-full bg-purple-500 rounded-full flex items-center justify-center text-white text-4xl font-bold">
                                        <?php echo strtoupper(substr($university['university_name'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <!-- Logo Upload Button -->
                        <button onclick="openLogoModal()"
                            class="absolute bottom-2 right-2 bg-purple-600 hover:bg-purple-700 text-white p-2 rounded-full shadow-lg transition">
                            <i class="fas fa-camera text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- University Info -->
            <div class="pt-16 pb-6 px-8">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900"><?php echo htmlspecialchars($university['university_name']); ?></h1>
                        <p class="text-gray-600 text-lg mt-1">
                            <?php echo $university['category'] ?: 'Add your category'; ?>
                        </p>
                        <div class="flex flex-wrap gap-4 mt-4">
                            <p class="text-gray-600 flex items-center">
                                <i class="fas fa-envelope mr-2"></i>
                                <?php echo htmlspecialchars($university['email']); ?>
                            </p>
                            <?php if ($university['city']): ?>
                                <p class="text-gray-600 flex items-center">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    <?php echo htmlspecialchars($university['city']); ?>
                                    <?php echo $university['province'] ? ', ' . htmlspecialchars($university['province']) : ''; ?>
                                </p>
                            <?php endif; ?>
                            <?php if ($university['established_year']): ?>
                                <p class="text-gray-600 flex items-center">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    Established <?php echo htmlspecialchars($university['established_year']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <button onclick="openEditProfileModal()"
                        class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition">
                        <i class="fas fa-edit"></i>
                        <span>Edit Profile</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- About Section -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">About University</h2>
                        <button onclick="openEditProfileModal()" class="text-purple-600 hover:text-purple-700">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <?php if ($university['sector']): ?>
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="font-medium text-gray-700">Sector:</span>
                                <span class="text-gray-900"><?php echo htmlspecialchars($university['sector']); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if ($university['chartered_by']): ?>
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="font-medium text-gray-700">Chartered By:</span>
                                <span class="text-gray-900"><?php echo htmlspecialchars($university['chartered_by']); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if ($university['is_recognized'] !== null): ?>
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="font-medium text-gray-700">Recognized:</span>
                                <span class="text-gray-900">
                                    <?php echo $university['is_recognized'] ?
                                        '<span class="text-green-600 flex items-center"><i class="fas fa-check-circle mr-2"></i>Yes</span>' :
                                        '<span class="text-red-600 flex items-center"><i class="fas fa-times-circle mr-2"></i>No</span>'; ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Campuses Section -->
                <!-- Campuses Section -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Campuses</h2>
                        <button onclick="openCampusesModal()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition">
                            <i class="fas fa-plus"></i>
                            <span>Manage Campuses</span>
                        </button>
                    </div>

                    <?php
                    // Proper JSON decoding with error handling
                    $campuses = [];
                    if ($university['campuses']) {
                        $decoded_campuses = json_decode($university['campuses'], true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded_campuses)) {
                            $campuses = $decoded_campuses;
                        }
                    }
                    ?>

                    <?php if (!empty($campuses)): ?>
                        <div class="space-y-3">
                            <?php foreach ($campuses as $index => $campus): ?>
                                <?php if (is_array($campus) && isset($campus['name'])): ?>
                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                        <div>
                                            <h3 class="font-semibold text-gray-900"><?php echo htmlspecialchars($campus['name']); ?></h3>
                                            <?php if (!empty($campus['address'])): ?>
                                                <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($campus['address']); ?></p>
                                            <?php endif; ?>
                                            <?php if (!empty($campus['phone'])): ?>
                                                <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($campus['phone']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button onclick="editCampus(<?php echo $index; ?>)" class="text-blue-600 hover:text-blue-800 p-2">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="deleteCampus(<?php echo $index; ?>)" class="text-red-600 hover:text-red-800 p-2">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-building text-3xl mb-3"></i>
                            <p>No campuses added yet</p>
                            <p class="text-sm mt-2">Add your university campuses to show location information</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Contact Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Contact Information</h2>

                    <?php if (!empty($contact_info)): ?>
                        <div class="space-y-3">
                            <?php if (!empty($contact_info['phone'])): ?>
                                <div class="flex items-center text-gray-700">
                                    <i class="fas fa-phone text-purple-600 w-6"></i>
                                    <span class="ml-3"><?php echo htmlspecialchars($contact_info['phone']); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($contact_info['website'])): ?>
                                <div class="flex items-center text-gray-700">
                                    <i class="fas fa-globe text-purple-600 w-6"></i>
                                    <a href="<?php echo htmlspecialchars($contact_info['website']); ?>" target="_blank" class="ml-3 text-blue-600 hover:underline">
                                        <?php echo htmlspecialchars($contact_info['website']); ?>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($contact_info['address'])): ?>
                                <div class="flex items-center text-gray-700">
                                    <i class="fas fa-map-marker-alt text-purple-600 w-6"></i>
                                    <span class="ml-3"><?php echo htmlspecialchars($contact_info['address']); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-gray-500">
                            <i class="fas fa-address-book text-2xl mb-2"></i>
                            <p class="text-sm">No contact information added</p>
                        </div>
                    <?php endif; ?>

                    <button onclick="openContactModal()" class="w-full mt-4 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 rounded-lg transition">
                        <i class="fas fa-edit mr-2"></i>Edit Contact Info
                    </button>
                </div>

                <!-- Quick Stats -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">University Stats</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-medium <?php echo $university['is_active'] ? 'text-green-600' : 'text-red-600'; ?>">
                                <?php echo $university['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Member Since:</span>
                            <span class="font-medium text-gray-900">
                                <?php echo date('M Y', strtotime($university['created_at'])); ?>
                            </span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="font-medium text-gray-900">
                                <?php echo date('M Y', strtotime($university['updated_at'])); ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-100 flex items-center justify-center min-h-screen">
                    <div class="bg-white p-8 rounded-2xl shadow-md w-full max-w-md">
                        <h2 class="text-2xl font-semibold text-center mb-6">Set University Password</h2>

                        <?php if (!empty($message)): ?>
                            <div class="mb-4 text-center text-sm font-medium 
                        <?= strpos($message, 'successfully') ? 'text-green-600' : 'text-red-600' ?>">
                                <?= htmlspecialchars($message) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="space-y-5">
                            <div>
                                <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                                <input type="password" name="password" id="password"
                                    class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    minlength="6" required>
                            </div>

                            <div>
                                <label for="confirm_password" class="block text-gray-700 font-medium mb-2">Confirm Password</label>
                                <input type="password" name="confirm_password" id="confirm_password"
                                    class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    minlength="6" required>
                            </div>

                            <button type="submit"
                                class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                                Save Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Programs Section -->
        <!-- Programs Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Academic Programs</h2>
                    <p class="text-gray-600 mt-1">Manage your university's academic programs and merit information</p>
                </div>
                <button onclick="openProgramsModal()"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition transform hover:scale-105">
                    <i class="fas fa-plus"></i>
                    <span>Add Program</span>
                </button>
            </div>

            <?php
            // Get university programs
            $programs = getUniversityPrograms($pdo, $university_id);
            ?>

            <?php if (!empty($programs)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php foreach ($programs as $program): ?>
                        <div class="border border-gray-200 rounded-xl p-5 hover:shadow-lg transition-all duration-300 bg-gradient-to-br from-white to-gray-50">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="font-bold text-lg text-gray-900 line-clamp-2"><?php echo htmlspecialchars($program['program_name']); ?></h3>
                                <div class="flex space-x-1">
                                    <button onclick="editProgram(
                                <?php echo $program['id']; ?>, 
                                '<?php echo addslashes($program['program_name']); ?>', 
                                '<?php echo addslashes($program['program_category']); ?>', 
                                <?php echo $program['closing_merit']; ?>
                            )" class="text-blue-600 hover:text-blue-800 p-1 transition" title="Edit Program">
                                        <i class="fas fa-edit text-sm"></i>
                                    </button>
                                    <button onclick="confirmDeleteProgram(<?php echo $program['id']; ?>)"
                                        class="text-red-600 hover:text-red-800 p-1 transition" title="Delete Program">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-tag mr-2 text-purple-500"></i>
                                    <span><?php echo htmlspecialchars($program['program_category']); ?></span>
                                </div>

                                <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
                                    <span class="text-sm font-medium text-gray-700">Closing Merit:</span>
                                    <div class="flex items-center space-x-1">
                                        <span class="text-lg font-bold text-purple-600">
                                            <?php echo number_format($program['closing_merit'], 2); ?>
                                        </span>
                                        <span class="text-sm text-gray-500">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <div class="w-24 h-24 mx-auto mb-4 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-purple-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Programs Added</h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">Start by adding your university's academic programs to help students discover educational opportunities.</p>
                    <button onclick="openProgramsModal()"
                        class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-lg inline-flex items-center space-x-2 transition transform hover:scale-105">
                        <i class="fas fa-plus"></i>
                        <span>Add Your First Program</span>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Include Modals -->
    <?php include 'modals/edit-profile-modal.php'; ?>
    <?php include 'modals/logo-modal.php'; ?>
    <?php include 'modals/campuses-modal.php'; ?>
    <?php include 'modals/contact-modal.php'; ?>
    <?php include 'modals/programs-modal.php'; ?>

    <script>
        // Modal Functions
        function openEditProfileModal() {
            console.log('Opening edit profile modal');
            document.getElementById('editProfileModal').classList.remove('hidden');
        }

        function openLogoModal() {
            console.log('Opening logo modal');
            document.getElementById('logoModal').classList.remove('hidden');
        }

        function openCampusesModal() {
            console.log('Opening campuses modal');
            document.getElementById('campusesModal').classList.remove('hidden');
        }

        function openContactModal() {
            console.log('Opening contact modal');
            document.getElementById('contactModal').classList.remove('hidden');
        }

        function closeModal(modalId) {
            console.log('Closing modal:', modalId);
            document.getElementById(modalId).classList.add('hidden');
        }

        function editCampus(index) {
            console.log('Edit campus:', index);
            // Implementation for editing campus
            alert('Edit campus functionality will be implemented soon for campus at index: ' + index);
        }

        function deleteCampus(index) {
            if (confirm('Are you sure you want to delete this campus?')) {
                console.log('Delete campus:', index);
                // Implementation for deleting campus
                alert('Delete campus functionality will be implemented soon for campus at index: ' + index);
            }
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                const modalId = e.target.id;
                closeModal(modalId);
            }
        });

        // Prevent modal close when clicking inside modal content
        document.addEventListener('click', function(e) {
            if (e.target.closest('.modal-content')) {
                e.stopPropagation();
            }
        });


        // Program Management Functions
        // Program Management Functions
        let currentProgramId = null;

        function openProgramsModal() {
            console.log('Opening programs modal');
            document.getElementById('programModalTitle').textContent = 'Add New Program';
            document.getElementById('programForm').reset();
            document.getElementById('program_id').value = '';
            currentProgramId = null;
            document.getElementById('programsModal').classList.remove('hidden');
        }

        function editProgram(id, name, category, merit) {
            console.log('Editing program:', {
                id,
                name,
                category,
                merit
            });

            // Decode any encoded characters
            const decodedName = decodeHtmlEntities(name);
            const decodedCategory = decodeHtmlEntities(category);

            document.getElementById('programModalTitle').textContent = 'Edit Program';
            document.getElementById('program_id').value = id;
            document.getElementById('program_name').value = decodedName;
            document.getElementById('program_category').value = decodedCategory;
            document.getElementById('closing_merit').value = parseFloat(merit).toFixed(2);
            currentProgramId = id;
            document.getElementById('programsModal').classList.remove('hidden');
        }

        function decodeHtmlEntities(text) {
            const textArea = document.createElement('textarea');
            textArea.innerHTML = text;
            return textArea.value;
        }

        function confirmDeleteProgram(programId) {
            console.log('Confirming delete for program:', programId);
            currentProgramId = programId;
            document.getElementById('deleteProgramModal').classList.remove('hidden');
        }

        // Initialize event listeners when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            initializeProgramHandlers();
        });

        function initializeProgramHandlers() {
            // Program form submission
            const programForm = document.getElementById('programForm');
            if (programForm) {
                programForm.addEventListener('submit', handleProgramSubmit);
            }

            // Delete confirmation button
            const confirmDeleteBtn = document.getElementById('confirmDeleteProgram');
            if (confirmDeleteBtn) {
                confirmDeleteBtn.addEventListener('click', handleProgramDelete);
            }

            // Close modal buttons
            const closeButtons = document.querySelectorAll('[onclick^="closeModal"]');
            closeButtons.forEach(button => {
                const originalOnClick = button.getAttribute('onclick');
                button.removeAttribute('onclick');
                button.addEventListener('click', function() {
                    const modalId = originalOnClick.match(/closeModal\('([^']+)'\)/)[1];
                    closeModal(modalId);
                });
            });
        }

        function handleProgramSubmit(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);
            formData.append('action', 'save_program');

            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
            submitBtn.disabled = true;

            fetch('programs_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Save response:', data);
                    if (data.success) {
                        closeModal('programsModal');
                        showNotification(data.message, 'success');
                        // Reload after a short delay to see the notification
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showNotification(data.message || 'Unknown error occurred', 'error');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showNotification('Network error occurred while saving the program.', 'error');
                })
                .finally(() => {
                    // Restore button state
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        }

        function handleProgramDelete() {
            if (!currentProgramId) {
                showNotification('No program selected for deletion', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'delete_program');
            formData.append('program_id', currentProgramId);

            // Show loading state
            const deleteBtn = this;
            const originalText = deleteBtn.innerHTML;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Deleting...';
            deleteBtn.disabled = true;

            fetch('programs_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Delete response:', data);
                    if (data.success) {
                        closeModal('deleteProgramModal');
                        showNotification(data.message, 'success');
                        // Reload after a short delay to see the notification
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showNotification(data.message || 'Unknown error occurred', 'error');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showNotification('Network error occurred while deleting the program.', 'error');
                })
                .finally(() => {
                    // Restore button state
                    deleteBtn.innerHTML = originalText;
                    deleteBtn.disabled = false;
                    currentProgramId = null;
                });
        }

        // Enhanced close modal function
        function closeModal(modalId) {
            console.log('Closing modal:', modalId);
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
            }
            // Reset current program ID when closing delete modal
            if (modalId === 'deleteProgramModal') {
                currentProgramId = null;
            }
        }

        // Enhanced notification function
        function showNotification(message, type = 'info') {
            // Remove existing notifications
            const existingNotifications = document.querySelectorAll('.custom-notification');
            existingNotifications.forEach(notification => notification.remove());

            const notification = document.createElement('div');
            notification.className = `custom-notification fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg border-l-4 transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-50 border-green-500 text-green-700' :
        type === 'error' ? 'bg-red-50 border-red-500 text-red-700' :
        'bg-blue-50 border-blue-500 text-blue-700'
    }`;

            const iconClass = type === 'success' ? 'fa-check-circle' :
                type === 'error' ? 'fa-exclamation-circle' :
                'fa-info-circle';

            notification.innerHTML = `
        <div class="flex items-center space-x-3">
            <i class="fas ${iconClass}"></i>
            <span class="font-medium">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

            document.body.appendChild(notification);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }
    </script>
</body>

</html>