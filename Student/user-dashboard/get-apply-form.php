<?php
include 'config.php';

if (isset($_GET['id'])) {
    $job_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("
        SELECT j.*, c.company_name 
        FROM jobs j 
        LEFT JOIN companies c ON j.company_id = c.id 
        WHERE j.id = ? AND j.is_active = 1
    ");
    $stmt->execute([$job_id]);
    $job = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($job) {
?>
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-semibold text-gray-900">Apply for <?php echo htmlspecialchars($job['title']); ?></h3>
                    <button onclick="closeModal('applyModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <p class="text-gray-600 mt-1">at <?php echo htmlspecialchars($job['company_name']); ?></p>
            </div>

            <form action="apply-job.php" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">

                <!-- CV Upload -->
                <!-- Update the CV upload section in get-apply-form.php -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Your CV *</label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 transition bg-gray-50">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6" id="uploadArea">
                                <i class="fas fa-file-upload text-2xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-500" id="uploadText">Click to upload your CV</p>
                                <p class="text-xs text-gray-400 mt-1">PDF, DOC, DOCX up to 5MB</p>
                            </div>
                            <input id="cvFile" type="file" name="cv_file" class="hidden"
                                accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                required onchange="previewCVFile(this)">
                        </label>
                    </div>
                    <div id="cvFilePreview" class="mt-4 hidden">
                        <p class="text-sm text-gray-700 mb-2">Selected file:</p>
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-file-pdf text-green-600"></i>
                                <span id="cvFileName" class="text-sm font-medium text-green-700"></span>
                            </div>
                            <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded">Ready to upload</span>
                        </div>
                    </div>
                    <div id="fileError" class="mt-2 hidden">
                        <p class="text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <span id="errorText"></span>
                        </p>
                    </div>
                </div>

                <!-- Cover Letter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cover Letter</label>
                    <textarea name="cover_letter" rows="6"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 input-focus resize-none"
                        placeholder="Tell the employer why you're interested in this position and why you'd be a good fit..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">Optional but recommended</p>
                </div>

                <!-- Application Tips -->
                <div class="bg-blue-50 rounded-lg p-4">
                    <h4 class="font-semibold text-blue-900 mb-2 flex items-center">
                        <i class="fas fa-lightbulb mr-2"></i>
                        Application Tips
                    </h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Tailor your cover letter to this specific job</li>
                        <li>• Highlight relevant experience and skills</li>
                        <li>• Keep your CV updated and professional</li>
                        <li>• Proofread before submitting</li>
                    </ul>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal('applyModal')"
                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition">
                        Submit Application
                    </button>
                </div>
            </form>
        </div>

        <script>
            function previewCVFile(input) {
                const fileName = document.getElementById('cvFileName');
                const previewContainer = document.getElementById('cvFilePreview');
                const uploadText = document.getElementById('uploadText');
                const uploadArea = document.getElementById('uploadArea');
                const fileError = document.getElementById('fileError');
                const errorText = document.getElementById('errorText');

                // Reset previous errors
                fileError.classList.add('hidden');
                input.classList.remove('border-red-500');

                if (input.files && input.files[0]) {
                    const file = input.files[0];
                    const fileSize = file.size / 1024 / 1024; // MB
                    const fileExtension = file.name.split('.').pop().toLowerCase();
                    const allowedExtensions = ['pdf', 'doc', 'docx'];

                    // Validate file type
                    if (!allowedExtensions.includes(fileExtension)) {
                        errorText.textContent = 'Only PDF, DOC, and DOCX files are allowed.';
                        fileError.classList.remove('hidden');
                        input.classList.add('border-red-500');
                        return;
                    }

                    // Validate file size (5MB max)
                    if (fileSize > 5) {
                        errorText.textContent = 'File size must be less than 5MB. Your file: ' + fileSize.toFixed(2) + 'MB';
                        fileError.classList.remove('hidden');
                        input.classList.add('border-red-500');
                        return;
                    }

                    // Valid file
                    fileName.textContent = file.name;
                    previewContainer.classList.remove('hidden');
                    uploadText.textContent = 'File selected: ' + file.name;
                    uploadArea.classList.add('bg-green-50', 'border-green-200');
                }
            }

            // Form validation
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.querySelector('form');
                const cvFile = document.getElementById('cvFile');

                form.addEventListener('submit', function(e) {
                    if (!cvFile.files || cvFile.files.length === 0) {
                        e.preventDefault();
                        const fileError = document.getElementById('fileError');
                        const errorText = document.getElementById('errorText');
                        errorText.textContent = 'Please upload your CV before submitting.';
                        fileError.classList.remove('hidden');
                        cvFile.classList.add('border-red-500');
                        return false;
                    }
                    return true;
                });
            });
        </script>
<?php
    }
}
?>