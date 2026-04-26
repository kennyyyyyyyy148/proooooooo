<?php
include 'header.php';

$alert = "";

// --- HANDLE 1: ADD NEW PLAN ---
if (isset($_POST['add_plan'])) {
    $name = clean($_POST['name']);
    $min_deposit = floatval($_POST['min_deposit']);
    $roi = floatval($_POST['roi']);
    $duration = intval($_POST['duration']);
    $risk = clean($_POST['risk_level']);
    $desc = clean($_POST['description']);
    $icon = clean($_POST['icon']); // e.g., fa-seedling
    $color = clean($_POST['color']); // e.g., teal, indigo

    $sql = "INSERT INTO investment_plans (name, min_deposit, roi, duration, risk_level, description, icon, color) 
            VALUES ('$name', '$min_deposit', '$roi', '$duration', '$risk', '$desc', '$icon', '$color')";
            
    if (mysqli_query($link, $sql)) {
        $alert = "Swal.fire({icon: 'success', title: 'Created', text: 'New plan added successfully.'});";
    } else {
        $alert = "Swal.fire({icon: 'error', title: 'Error', text: 'Failed to add plan.'});";
    }
}

// --- HANDLE 2: UPDATE PLAN ---
if (isset($_POST['update_plan'])) {
    $id = intval($_POST['plan_id']);
    $name = clean($_POST['name']);
    $min_deposit = floatval($_POST['min_deposit']);
    $roi = floatval($_POST['roi']);
    $duration = intval($_POST['duration']);
    $risk = clean($_POST['risk_level']);
    $desc = clean($_POST['description']);
    
    $sql = "UPDATE investment_plans SET 
            name='$name', 
            min_deposit='$min_deposit', 
            roi='$roi', 
            duration='$duration', 
            risk_level='$risk', 
            description='$desc' 
            WHERE id='$id'";
            
    if (mysqli_query($link, $sql)) {
        $alert = "Swal.fire({icon: 'success', title: 'Updated', text: 'Plan details saved.'});";
    }
}

// --- HANDLE 3: DELETE PLAN ---
if (isset($_POST['delete_plan'])) {
    $id = intval($_POST['plan_id']);
    mysqli_query($link, "DELETE FROM investment_plans WHERE id='$id'");
    $alert = "Swal.fire({icon: 'success', title: 'Deleted', text: 'Plan removed.'});";
}

// Fetch All Plans
$result = mysqli_query($link, "SELECT * FROM investment_plans ORDER BY min_deposit ASC");
?>

<div class="flex-1 overflow-y-auto p-6 space-y-6">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Investment Plans</h1>
            <p class="text-sm text-slate-500">Configure the packages available to users.</p>
        </div>
        <button onclick="openAddModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-xl shadow transition-all flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> New Plan
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): 
                // Color mapping for admin view
                $bg_color = "bg-".$row['color']."-50";
                $text_color = "text-".$row['color']."-600";
                $border_color = "border-".$row['color']."-200";
                
                // Encode for Edit Modal
                $plan_json = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
            ?>
            <div class="glass-panel p-6 rounded-2xl bg-white shadow-sm border border-slate-200 relative group hover:shadow-md transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-xl <?php echo $bg_color; ?> flex items-center justify-center <?php echo $text_color; ?> border <?php echo $border_color; ?>">
                        <i class="fa-solid <?php echo $row['icon']; ?> text-xl"></i>
                    </div>
                    <span class="px-2 py-1 rounded-md bg-slate-100 text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <?php echo $row['risk_level']; ?> Risk
                    </span>
                </div>
                
                <h3 class="text-lg font-bold text-slate-800 mb-1"><?php echo $row['name']; ?></h3>
                <p class="text-xs text-slate-500 min-h-[40px]"><?php echo $row['description']; ?></p>
                
                <div class="grid grid-cols-2 gap-4 my-4 py-4 border-t border-b border-slate-100">
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase font-bold">Min Deposit</p>
                        <p class="text-sm font-bold text-slate-700">$<?php echo number_format($row['min_deposit']); ?></p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase font-bold">Daily ROI</p>
                        <p class="text-sm font-bold text-green-600"><?php echo $row['roi']; ?>%</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase font-bold">Duration</p>
                        <p class="text-sm font-bold text-slate-700"><?php echo $row['duration']; ?> Days</p>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button onclick="openEditModal('<?php echo $plan_json; ?>')" class="flex-1 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition-all">
                        Edit Details
                    </button>
                    <form method="POST" onsubmit="return confirm('Delete this plan?');">
                        <input type="hidden" name="plan_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="delete_plan" class="px-3 py-2 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 transition-all">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-span-3 text-center py-12 text-slate-400 italic">No investment plans found. Create one to get started.</div>
        <?php endif; ?>
    </div>

</div>

<div id="planModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl p-6 md:p-8 shadow-2xl relative max-h-[90vh] overflow-y-auto">
        <button onclick="closeModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-xl"></i></button>
        
        <h3 id="modalTitle" class="text-xl font-bold text-slate-800 mb-6">Add New Plan</h3>
        
        <form method="POST">
            <input type="hidden" name="plan_id" id="planId">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Plan Name</label>
                    <input type="text" name="name" id="planName" required class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-indigo-500 outline-none">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Min Deposit ($)</label>
                        <input type="number" step="any" name="min_deposit" id="planMin" required class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Daily ROI (%)</label>
                        <input type="number" step="0.01" name="roi" id="planRoi" required class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-indigo-500 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Duration (Days)</label>
                        <input type="number" name="duration" id="planDuration" required class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Risk Level</label>
                        <select name="risk_level" id="planRisk" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm bg-white focus:border-indigo-500 outline-none">
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Description</label>
                    <textarea name="description" id="planDesc" rows="2" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-indigo-500 outline-none"></textarea>
                </div>

                <div id="newPlanFields" class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Icon Class</label>
                        <input type="text" name="icon" placeholder="fa-seedling" value="fa-chart-line" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-indigo-500 outline-none">
                        <p class="text-[10px] text-slate-400 mt-1">FontAwesome Class</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Theme Color</label>
                        <select name="color" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm bg-white focus:border-indigo-500 outline-none">
                            <option value="teal">Teal</option>
                            <option value="indigo" selected>Indigo</option>
                            <option value="purple">Purple</option>
                            <option value="blue">Blue</option>
                            <option value="orange">Orange</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" name="add_plan" id="submitBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition-all w-full md:w-auto">
                        Create Plan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('modalTitle').innerText = 'Add New Plan';
        document.getElementById('submitBtn').name = 'add_plan';
        document.getElementById('submitBtn').innerText = 'Create Plan';
        document.getElementById('newPlanFields').classList.remove('hidden'); // Show icon/color options
        
        // Reset form
        document.getElementById('planId').value = '';
        document.getElementById('planName').value = '';
        document.getElementById('planMin').value = '';
        document.getElementById('planRoi').value = '';
        document.getElementById('planDuration').value = '';
        document.getElementById('planDesc').value = '';
        
        document.getElementById('planModal').classList.remove('hidden');
    }

    function openEditModal(json) {
        const plan = JSON.parse(json);
        
        document.getElementById('modalTitle').innerText = 'Edit Plan';
        document.getElementById('submitBtn').name = 'update_plan';
        document.getElementById('submitBtn').innerText = 'Save Changes';
        document.getElementById('newPlanFields').classList.add('hidden'); // Hide icon/color options (keep simple)
        
        // Populate form
        document.getElementById('planId').value = plan.id;
        document.getElementById('planName').value = plan.name;
        document.getElementById('planMin').value = plan.min_deposit;
        document.getElementById('planRoi').value = plan.roi;
        document.getElementById('planDuration').value = plan.duration;
        document.getElementById('planRisk').value = plan.risk_level;
        document.getElementById('planDesc').value = plan.description;
        
        document.getElementById('planModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('planModal').classList.add('hidden');
    }

    document.getElementById('planModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    <?php echo $alert; ?>
</script>

<?php include 'footer.php'; ?>