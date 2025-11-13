<!-- Campuses Management Modal -->
<div id="campusesModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden modal-overlay">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto modal-content">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-semibold text-gray-900">Manage Campuses</h3>
                <button onclick="closeModal('campusesModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <div class="p-6">
            <!-- Add New Campus Form -->
            <form id="addCampusForm" class="mb-6 p-4 border border-gray-200 rounded-lg">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Add New Campus</h4>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Campus Name *</label>
                        <input type="text" name="campus_name" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 input-focus"
                               placeholder="e.g., Main Campus, City Campus">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <textarea name="campus_address" rows="2"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 input-focus"
                                  placeholder="Full campus address"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" name="campus_phone"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 input-focus"
                               placeholder="e.g., (042) 123-4567">
                    </div>
                    <button type="button" onclick="addCampus()"
                            class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg transition">
                        <i class="fas fa-plus mr-2"></i>Add Campus
                    </button>
                </div>
            </form>

            <!-- Current Campuses List -->
            <div id="campusesList">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Current Campuses</h4>
                <div id="campusesContainer" class="space-y-3">
                    <!-- Campuses will be dynamically added here -->
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                <button type="button" onclick="closeModal('campusesModal')"
                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition">
                    Cancel
                </button>
                <button type="button" onclick="saveCampuses()"
                        class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium transition">
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let campuses = <?php echo json_encode($campuses); ?>;

    function renderCampuses() {
        const container = document.getElementById('campusesContainer');
        container.innerHTML = '';

        if (campuses.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-building text-3xl mb-3"></i>
                    <p>No campuses added yet</p>
                </div>
            `;
            return;
        }

        campuses.forEach((campus, index) => {
            const campusElement = document.createElement('div');
            campusElement.className = 'flex items-center justify-between p-3 border border-gray-200 rounded-lg';
            campusElement.innerHTML = `
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900">${campus.name}</h3>
                    ${campus.address ? `<p class="text-gray-600 text-sm">${campus.address}</p>` : ''}
                    ${campus.phone ? `<p class="text-gray-600 text-sm">${campus.phone}</p>` : ''}
                </div>
                <div class="flex space-x-2">
                    <button onclick="editExistingCampus(${index})" class="text-blue-600 hover:text-blue-800 p-2">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="removeCampus(${index})" class="text-red-600 hover:text-red-800 p-2">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            container.appendChild(campusElement);
        });
    }

    function addCampus() {
        const form = document.getElementById('addCampusForm');
        const name = form.querySelector('[name="campus_name"]').value.trim();
        const address = form.querySelector('[name="campus_address"]').value.trim();
        const phone = form.querySelector('[name="campus_phone"]').value.trim();

        if (!name) {
            alert('Please enter a campus name');
            return;
        }

        campuses.push({
            name: name,
            address: address,
            phone: phone
        });

        form.reset();
        renderCampuses();
        showNotification('Campus added successfully!', 'success');
    }

    function removeCampus(index) {
        if (confirm('Are you sure you want to remove this campus?')) {
            campuses.splice(index, 1);
            renderCampuses();
            showNotification('Campus removed successfully!', 'success');
        }
    }

    function editExistingCampus(index) {
        const campus = campuses[index];
        const newName = prompt('Enter new campus name:', campus.name);
        if (newName !== null) {
            const newAddress = prompt('Enter new campus address:', campus.address || '');
            const newPhone = prompt('Enter new campus phone:', campus.phone || '');
            
            campuses[index] = {
                name: newName.trim(),
                address: newAddress ? newAddress.trim() : '',
                phone: newPhone ? newPhone.trim() : ''
            };
            
            renderCampuses();
            showNotification('Campus updated successfully!', 'success');
        }
    }

    function saveCampuses() {
        // This would typically send data to server via AJAX
        console.log('Saving campuses:', campuses);
        showNotification('Campuses saved successfully!', 'success');
        closeModal('campusesModal');
    }

    // Initialize campuses list on modal open
    document.addEventListener('DOMContentLoaded', function() {
        const campusesModal = document.getElementById('campusesModal');
        campusesModal.addEventListener('click', function(e) {
            if (e.target === this) {
                renderCampuses();
            }
        });
    });

    function showNotification(message, type = 'info') {
        // Simple notification implementation
        alert(message);
    }

    // Initial render
    renderCampuses();
</script>