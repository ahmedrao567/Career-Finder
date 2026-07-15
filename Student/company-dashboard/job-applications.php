<?php
include 'config.php';

$company_id = $_SESSION['company_id'];

// Handle application actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $application_id = $_POST['application_id'];
    $new_status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE job_applications SET status = ? WHERE id = ? AND job_id IN (SELECT id FROM jobs WHERE company_id = ?)");
    if ($stmt->execute([$new_status, $application_id, $company_id])) {
        $_SESSION['success'] = "Application status updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update application status.";
    }
    header("Location: job-applications.php");
    exit();
}

// Get company's jobs
function getCompanyJobs($pdo, $company_id) {
    $stmt = $pdo->prepare("SELECT id, title FROM jobs WHERE company_id = ? ORDER BY title");
    $stmt->execute([$company_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get job applications
function getJobApplications($pdo, $company_id, $filters = []) {
    $whereConditions = ["j.company_id = ?"];
    $params = [$company_id];

    if (!empty($filters['status'])) {
        $whereConditions[] = "ja.status = ?";
        $params[] = $filters['status'];
    }

    if (!empty($filters['job_id'])) {
        $whereConditions[] = "ja.job_id = ?";
        $params[] = $filters['job_id'];
    }

    $whereClause = implode(" AND ", $whereConditions);

    $sql = "
        SELECT 
            ja.*,
            j.title as job_title,
            j.location as job_location,
            j.description,
            j.requirements,
            u.full_name as applicant_name,
            u.email as applicant_email,
            u.username as applicant_username,
            up.designation as applicant_designation,
            up.profile_photo as applicant_photo
        FROM job_applications ja
        INNER JOIN jobs j ON ja.job_id = j.id
        INNER JOIN users u ON ja.user_id = u.id
        LEFT JOIN user_profiles up ON u.id = up.user_id
        WHERE $whereClause
        ORDER BY ja.applied_at DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Filters
$filters = [
    'status' => $_GET['status'] ?? '',
    'job_id' => $_GET['job_id'] ?? ''
];

$applications = getJobApplications($pdo, $company_id, $filters);
$company_jobs = getCompanyJobs($pdo, $company_id);

// Stats
$total_applications = count($applications);
$pending_count = count(array_filter($applications, fn($app) => $app['status'] === 'pending'));
$reviewed_count = count(array_filter($applications, fn($app) => $app['status'] === 'reviewed'));
$accepted_count = count(array_filter($applications, fn($app) => $app['status'] === 'accepted'));
$rejected_count = count(array_filter($applications, fn($app) => $app['status'] === 'rejected'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Job Applications</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
.status-badge { display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; }
.status-pending { background-color: #FEF3C7; color: #92400E; }
.status-reviewed { background-color: #DBEAFE; color: #1E40AF; }
.status-accepted { background-color: #D1FAE5; color: #065F46; }
.status-rejected { background-color: #FEE2E2; color: #991B1B; }
</style>
</head>
<body class="bg-gray-50">
<?php include 'navbar.php'; ?>

<div class="max-w-7xl mx-auto py-8 px-4">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Job Applications</h1>
        <p class="text-gray-600 mt-1">Manage all applications for your company's jobs</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 flex justify-between items-center">
            <div><p class="text-2xl font-bold"><?php echo $total_applications; ?></p><p class="text-gray-600 text-sm">Total</p></div>
            <i class="fas fa-file-alt text-blue-500 text-2xl"></i>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 flex justify-between items-center">
            <div><p class="text-2xl font-bold"><?php echo $pending_count; ?></p><p class="text-gray-600 text-sm">Pending</p></div>
            <i class="fas fa-clock text-yellow-500 text-2xl"></i>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 flex justify-between items-center">
            <div><p class="text-2xl font-bold"><?php echo $reviewed_count; ?></p><p class="text-gray-600 text-sm">Reviewed</p></div>
            <i class="fas fa-eye text-green-500 text-2xl"></i>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 flex justify-between items-center">
            <div><p class="text-2xl font-bold"><?php echo $accepted_count + $rejected_count; ?></p><p class="text-gray-600 text-sm">Decided</p></div>
            <i class="fas fa-check-circle text-purple-500 text-2xl"></i>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-gray-700 mb-1">Filter by Job</label>
                <select name="job_id" class="w-full border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-500">
                    <option value="">All Jobs</option>
                    <?php foreach ($company_jobs as $job): ?>
                        <option value="<?php echo $job['id']; ?>" <?php echo $filters['job_id'] == $job['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($job['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 mb-1">Filter by Status</label>
                <select name="status" class="w-full border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-500">
                    <option value="">All Statuses</option>
                    <option value="pending" <?php echo $filters['status']==='pending'?'selected':'';?>>Pending</option>
                    <option value="reviewed" <?php echo $filters['status']==='reviewed'?'selected':'';?>>Reviewed</option>
                    <option value="accepted" <?php echo $filters['status']==='accepted'?'selected':'';?>>Accepted</option>
                    <option value="rejected" <?php echo $filters['status']==='rejected'?'selected':'';?>>Rejected</option>
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Apply</button>
                <a href="job-applications.php" class="px-4 py-2 border rounded hover:bg-gray-50">Clear</a>
            </div>
        </form>
    </div>

    <!-- Applications -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 divide-y divide-gray-200">
        <?php if($applications): ?>
            <?php foreach($applications as $app): ?>
                <?php
                    $cv_path = "../user-dashboard/assets/uploads/cvs/".$app['cv_file'];
                    $cv_exists = !empty($app['cv_file']) && file_exists($cv_path);
                    $job_text = ($app['description']??'') . "\n" . ($app['requirements']??'');
                ?>
                <div class="p-6 flex justify-between space-x-6 hover:bg-gray-50 transition">
                    <div class="flex-1 flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <?php if(!empty($app['applicant_photo'])): ?>
                                <img src="../user-dashboard/assets/uploads/<?php echo $app['applicant_photo']; ?>" class="w-12 h-12 rounded-full object-cover">
                            <?php else: ?>
                                <div class="w-12 h-12 rounded-full bg-blue-500 text-white flex items-center justify-center font-semibold">
                                    <?php echo strtoupper(substr($app['applicant_name'],0,1)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <h3 class="font-semibold text-gray-900"><?php echo htmlspecialchars($app['applicant_name']); ?></h3>
                                <span class="status-badge status-<?php echo $app['status']; ?>"><?php echo ucfirst($app['status']); ?></span>
                            </div>
                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($app['applicant_email']); ?></p>
                            <?php if(!empty($app['applicant_designation'])): ?>
                                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($app['applicant_designation']); ?></p>
                            <?php endif; ?>
                            <p class="text-sm mt-1"><strong>Job:</strong> <?php echo htmlspecialchars($app['job_title']); ?></p>
                            <p class="text-sm"><strong>Location:</strong> <?php echo htmlspecialchars($app['job_location']); ?></p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col space-y-3 w-64">
                        <!-- Match Score Card -->
                        <div id="match-score-<?php echo $app['id']; ?>" class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4">
                            <?php
                            $match_data = null;
                            $match_error = null;
                            
                            if($cv_exists && !empty(trim($job_text))){
                                try {
                                    $curl = curl_init();
                                    $formData = [
                                        'job_text' => $job_text,
                                        'cv_file' => new CURLFile(realpath($cv_path))
                                    ];
                                    curl_setopt_array($curl, [
                                        CURLOPT_URL => 'http://model-api:8002/match-score',
                                        CURLOPT_POST => true,
                                        CURLOPT_POSTFIELDS => $formData,
                                        CURLOPT_RETURNTRANSFER => true,
                                        CURLOPT_TIMEOUT => 120,
                                        CURLOPT_CONNECTTIMEOUT => 10
                                    ]);
                                    $response = curl_exec($curl);
                                    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                                    
                                    if($httpcode == 200){
                                        $match_data = json_decode($response, true);
                                    } else {
                                        $match_error = "API Error (Code: $httpcode)";
                                    }
                                } catch (Exception $e) {
                                    $match_error = "Connection failed";
                                }
                            } else {
                                $match_error = "CV or job info missing";
                            }
                            
                            if($match_data):
                                $score = $match_data['match_score'];
                                $similarity = $match_data['embedding_similarity'];
                                $confidence = $match_data['ml_confidence'];
                                $category = $match_data['cv_category'];
                                
                                // Determine color based on score
                                if($score >= 80) { $color_class = 'text-green-700 bg-green-100'; $bar_class = 'bg-green-500'; $badge = 'bg-green-100 text-green-800'; }
                                elseif($score >= 60) { $color_class = 'text-yellow-700 bg-yellow-100'; $bar_class = 'bg-yellow-500'; $badge = 'bg-yellow-100 text-yellow-800'; }
                                elseif($score >= 40) { $color_class = 'text-orange-700 bg-orange-100'; $bar_class = 'bg-orange-500'; $badge = 'bg-orange-100 text-orange-800'; }
                                else { $color_class = 'text-red-700 bg-red-100'; $bar_class = 'bg-red-500'; $badge = 'bg-red-100 text-red-800'; }
                            ?>
                                <!-- Main Score -->
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <p class="text-xs text-gray-600 font-medium">Overall Match</p>
                                        <p class="text-2xl font-bold text-gray-900"><?php echo $score; ?>%</p>
                                    </div>
                                    <div class="<?php echo $color_class; ?> w-16 h-16 rounded-full flex items-center justify-center">
                                        <i class="fas fa-check text-xl"></i>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div class="mb-3">
                                    <div class="w-full bg-gray-300 rounded-full h-2">
                                        <div class="<?php echo $bar_class; ?> h-2 rounded-full" style="width: <?php echo $score; ?>%"></div>
                                    </div>
                                </div>

                                <!-- Metrics Grid -->
                                <div class="grid grid-cols-2 gap-2 mb-3">
                                    <div class="bg-white border border-gray-200 rounded p-2">
                                        <p class="text-xs text-gray-600">Semantic Match</p>
                                        <p class="text-sm font-semibold text-gray-900"><?php echo round($similarity, 1); ?>%</p>
                                    </div>
                                    <div class="bg-white border border-gray-200 rounded p-2">
                                        <p class="text-xs text-gray-600">Confidence</p>
                                        <p class="text-sm font-semibold text-gray-900"><?php echo round($confidence, 1); ?>%</p>
                                    </div>
                                </div>

                                <!-- Category Badge -->
                                <div class="<?php echo $badge; ?> rounded px-2 py-1 text-xs font-medium text-center">
                                    Category: <?php echo ucfirst($category); ?>
                                </div>
                            <?php else: ?>
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-exclamation-circle text-gray-400"></i>
                                    <div>
                                        <p class="text-xs text-gray-600 font-medium">Match Score</p>
                                        <p class="text-sm text-gray-700"><?php echo $match_error; ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-2">
                            <?php if($cv_exists): ?>
                                <a href="<?php echo $cv_path; ?>" target="_blank" class="flex-1 bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 text-sm flex items-center justify-center transition">
                                    <i class="fas fa-download mr-1"></i> View CV
                                </a>
                            <?php else: ?>
                                <span class="flex-1 bg-gray-300 text-gray-600 px-3 py-2 rounded text-sm flex items-center justify-center">
                                    <i class="fas fa-times mr-1"></i> No CV
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Update Status Form -->
                        <form method="POST" class="flex flex-col space-y-2">
                            <input type="hidden" name="application_id" value="<?php echo $app['id']; ?>">
                            <select name="status" class="w-full border border-gray-300 px-3 py-2 rounded text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="pending" <?php echo $app['status']==='pending'?'selected':'';?>>📋 Pending</option>
                                <option value="reviewed" <?php echo $app['status']==='reviewed'?'selected':'';?>>👀 Reviewed</option>
                                <option value="accepted" <?php echo $app['status']==='accepted'?'selected':'';?>>✅ Accepted</option>
                                <option value="rejected" <?php echo $app['status']==='rejected'?'selected':'';?>>❌ Rejected</option>
                            </select>
                            <button type="submit" name="update_status" class="w-full bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded text-sm font-medium transition">Update Status</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="p-12 text-center text-gray-500">
                <i class="fas fa-file-alt text-4xl mb-2"></i>
                <p>No applications found</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal -->
<div id="applicationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 hidden"></div>

<script>
function openApplicationModal(applicationId){
    fetch('get-application-details.php?id='+applicationId)
    .then(res=>res.text())
    .then(html=>{
        const modal = document.getElementById('applicationModal');
        modal.innerHTML = html;
        modal.classList.remove('hidden');
    })
    .catch(err=>alert('Failed to load application details'));
}

// Close modal by clicking outside
document.addEventListener('click', e=>{
    if(e.target.classList.contains('fixed') && e.target.id==='applicationModal'){
        e.target.classList.add('hidden');
    }
});
</script>

</body>
</html>
