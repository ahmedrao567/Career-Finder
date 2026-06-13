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
        
        .program-card {
            transition: all 0.3s ease;
        }
        .program-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
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

                <!-- Programs Container -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Academic Programs</h2>
                            <p class="text-gray-600 mt-1">Manage your university's academic programs and merit information</p>
                        </div>
                        <button onclick="openAddProgramModal()" 
                                class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center">
                            <i class="fas fa-plus mr-2"></i> Add New Program
                        </button>
                    </div>

                    <?php
                    // Get programs from database
                    $stmt = $pdo->prepare("SELECT * FROM programs WHERE university_id = ? ORDER BY created_at DESC");
                    $stmt->execute([$university_id]);
                    $programs = $stmt->fetchAll();
                    ?>

                    <?php if (empty($programs)): ?>
                        <!-- Empty State -->
                        <div class="text-center py-12">
                            <div class="w-24 h-24 mx-auto mb-6 bg-purple-100 rounded-full flex items-center justify-center">
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="programsGrid">
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

                <!-- Password Section -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Account Security</h2>
                    
                    <?php if (!empty($message)): ?>
                        <div class="mb-4 text-center text-sm font-medium 
                            <?= strpos($message, 'successfully') ? 'text-green-600' : 'text-red-600' ?>">
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="space-y-4">
                        <div>
                            <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                            <input type="password" name="password" id="password"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                minlength="6" required>
                        </div>

                        <div>
                            <label for="confirm_password" class="block text-gray-700 font-medium mb-2">Confirm Password</label>
                            <input type="password" name="confirm_password" id="confirm_password"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                minlength="6" required>
                        </div>

                        <button type="submit"
                            class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                            Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Campuses Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Campuses</h2>
                    <p class="text-gray-600 mt-1">Manage your university campuses and locations</p>
                </div>
                <button onclick="openCampusesModal()"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition">
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
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($campuses as $index => $campus): ?>
                        <?php if (is_array($campus) && isset($campus['name'])): ?>
                            <div class="border border-gray-200 rounded-xl p-5 hover:shadow-md transition">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="font-semibold text-gray-900 text-lg"><?php echo htmlspecialchars($campus['name']); ?></h3>
                                    <div class="flex space-x-2">
                                        <button onclick="editCampus(<?php echo $index; ?>)" class="text-blue-600 hover:text-blue-800 p-1">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteCampus(<?php echo $index; ?>)" class="text-red-600 hover:text-red-800 p-1">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <?php if (!empty($campus['address'])): ?>
                                    <div class="flex items-start text-gray-600 mb-2">
                                        <i class="fas fa-map-marker-alt mt-1 mr-2 text-purple-500"></i>
                                        <span class="text-sm"><?php echo htmlspecialchars($campus['address']); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($campus['phone'])): ?>
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-phone mr-2 text-blue-500"></i>
                                        <span class="text-sm"><?php echo htmlspecialchars($campus['phone']); ?></span>
                                    </div>
                                <?php endif; ?>
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

    <!-- Include Modals -->
    <?php include 'modals/edit-profile-modal.php'; ?>
    <?php include 'modals/logo-modal.php'; ?>
    <?php include 'modals/campuses-modal.php'; ?>
    <?php include 'modals/contact-modal.php'; ?>
    
    <!-- Add Program Modal -->
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

    <!-- Delete Program Confirmation Modal -->
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

    <!-- Toast Notification -->
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
            document.getElementById('program_name').value = decodeHtmlEntities(programName);
            document.getElementById('program_category').value = decodeHtmlEntities(programCategory);
            document.getElementById('closing_merit').value = closingMerit;
            document.getElementById('saveBtnText').textContent = 'Update Program';
            openModal('programsModal');
        }

        function openDeleteProgramModal(programId) {
            window.currentProgramId = programId;
            openModal('deleteProgramModal');
        }

        function decodeHtmlEntities(text) {
            const textArea = document.createElement('textarea');
            textArea.innerHTML = text;
            return textArea.value;
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

        // Original functions for other modals
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

        // Prevent modal close when clicking inside modal content
        document.addEventListener('click', function(e) {
            if (e.target.closest('.modal-content')) {
                e.stopPropagation();
            }
        });
    </script>
</body>
</html>