<?php
include 'header.php';

$alert = "";

// --- HANDLE 1: ADD NEW BOT ---
if (isset($_POST['add_bot'])) {
    $name = clean($_POST['name']);
    $desc = clean($_POST['description']);
    $roi_min = floatval($_POST['roi_min']);
    $roi_max = floatval($_POST['roi_max']);
    $win_rate = intval($_POST['win_rate']);
    $min_inv = floatval($_POST['min_investment']);
    $risk = clean($_POST['risk_level']);
    $icon = clean($_POST['icon']);
    $color = clean($_POST['color']);

    $sql = "INSERT INTO trading_bots (name, description, roi_min, roi_max, win_rate, min_investment, risk_level, icon, color) 
            VALUES ('$name', '$desc', '$roi_min', '$roi_max', '$win_rate', '$min_inv', '$risk', '$icon', '$color')";
            
    if (mysqli_query($link, $sql)) {
        $alert = "Swal.fire({icon: 'success', title: 'Created', text: 'New trading bot added successfully.'});";
    } else {
        $alert = "Swal.fire({icon: 'error', title: 'Error', text: 'Failed to add bot.'});";
    }
}

// --- HANDLE 2: UPDATE BOT ---
if (isset($_POST['update_bot'])) {
    $id = intval($_POST['bot_id']);
    $name = clean($_POST['name']);
    $desc = clean($_POST['description']);
    $roi_min = floatval($_POST['roi_min']);
    $roi_max = floatval($_POST['roi_max']);
    $win_rate = intval($_POST['win_rate']);
    $min_inv = floatval($_POST['min_investment']);
    $risk = clean($_POST['risk_level']);
    
    // Note: Icon and Color are usually set once, but you can add fields to update them if needed. 
    // For simplicity, we keep the main stats editable here.
    
    $sql = "UPDATE trading_bots SET 
            name='$name', 
            description='$desc', 
            roi_min='$roi_min', 
            roi_max='$roi_max', 
            win_rate='$win_rate', 
            min_investment='$min_inv', 
            risk_level='$risk' 
            WHERE id='$id'";
            
    if (mysqli_query($link, $sql)) {
        $alert = "Swal.fire({icon: 'success', title: 'Updated', text: 'Bot details saved.'});";
    }
}

// --- HANDLE 3: DELETE BOT ---
if (isset($_POST['delete_bot'])) {
    $id = intval($_POST['bot_id']);
    mysqli_query($link, "DELETE FROM trading_bots WHERE id='$id'");
    $alert = "Swal.fire({icon: 'success', title: 'Deleted', text: 'Bot package removed.'});";
}

// Fetch All Bots
$result = mysqli_query($link, "SELECT * FROM trading_bots ORDER BY min_investment ASC");
?>

<div class="flex-1 overflow-y-auto p-6 space-y-6">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Manage Trading Bots</h1>
            <p class="text-sm text-slate-500">Configure AI strategies available for users.</p>
        </div>
        <button onclick="openAddModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-xl shadow transition-all flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> New Bot
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): 
                // Color mapping
                $bg_color = "bg-".$row['color']."-50";
                $text_color = "text-".$row['color']."-600";
                $border_color = "border-".$row['color']."-200";
                
                // Encode for Edit Modal
                $bot_json = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
            ?>
            <div class="glass-panel p-6 rounded-2xl bg-white shadow-sm border border-slate-200 relative group hover:shadow-md transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-xl <?php echo $bg_color; ?> flex items-center justify-center <?php echo $text_color; ?> border <?php echo $border_color; ?>">
                        <i class="fa-solid <?php echo $row['icon']; ?> text-xl"></i>
                    </div>
                    <span class="px-2 py-1 rounded-md bg-slate-100 text-slate-500 text-xs font-bold uppercase tracking-wider">
                        Win Rate: <?php echo $row['win_rate']; ?>%
                    </span>
                </div>
                
                <h3 class="text-lg font-bold text-slate-800 mb-1"><?php echo $row['name']; ?></h3>
                <p class="text-xs text-slate-500 min-h-[40px]"><?php echo $row['description']; ?></p>
                
                <div class="grid grid-cols-2 gap-4 my-4 py-4 border-t border-b border-slate-100">
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase font-bold">Min Investment</p>
                        <p class="text-sm font-bold text-slate-700">$<?php echo number_format($row['min_investment']); ?></p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase font-bold">Daily ROI</p>
                        <p class="text-sm font-bold text-green-600"><?php echo $row['roi_min']; ?>% - <?php echo $row['roi_max']; ?>%</p>
                    </div>
                </div>
                
                <div class="mb-4">
                    <p class="text-[10px] text-slate-400 uppercase font-bold mb-1">Risk Profile</p>
                    <span class="text-xs font-bold text-slate-600 bg-slate-50 px-2 py-1 rounded border border-slate-100 block w-fit">
                        <?php echo $row['risk_level']; ?>
                    </span>
                </div>

                <div class="flex gap-2">
                    <button onclick="openEditModal('<?php echo $bot_json; ?>')" class="flex-1 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition-all">
                        Edit Config
                    </button>
                    <form method="POST" onsubmit="return confirm('Delete this bot package?');">
                        <input type="hidden" name="bot_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="delete_bot" class="px-3 py-2 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 transition-all">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-span-3 text-center py-12 text-slate-400 italic">No trading bots configured. Add one to get started.</div>
        <?php endif; ?>
    </div>

</div>

<div id="botModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl p-6 md:p-8 shadow-2xl relative max-h-[90vh] overflow-y-auto">
        <button onclick="closeModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-xl"></i></button>
        
        <h3 id="modalTitle" class="text-xl font-bold text-slate-800 mb-6">Add New Bot</h3>
        
        <form method="POST">
            <input type="hidden" name="bot_id" id="botId">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Bot Name</label>
                    <input type="text" name="name" id="botName" required class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Description</label>
                    <textarea name="description" id="botDesc" rows="2" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-indigo-500 outline-none"></textarea>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Min ROI (%)</label>
                        <input type="number" step="0.01" name="roi_min" id="botRoiMin" required class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Max ROI (%)</label>
                        <input type="number" step="0.01" name="roi_max" id="botRoiMax" required class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-indigo-500 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Win Rate (%)</label>
                        <input type="number" name="win_rate" id="botWinRate" required class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Min Investment ($)</label>
                        <input type="number" step="any" name="min_investment" id="botMinInv" required class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-indigo-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Risk Level Label</label>
                    <input type="text" name="risk_level" id="botRisk" placeholder="e.g. High Freq, Low Risk" required class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-indigo-500 outline-none">
                </div>

                <div id="newBotFields" class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Icon Class</label>
                        <input type="text" name="icon" placeholder="fa-robot" value="fa-robot" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-indigo-500 outline-none">
                        <p class="text-[10px] text-slate-400 mt-1">FontAwesome (e.g. fa-bolt)</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Theme Color</label>
                        <select name="color" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm bg-white focus:border-indigo-500 outline-none">
                            <option value="indigo">Indigo</option>
                            <option value="purple">Purple</option>
                            <option value="blue">Blue</option>
                            <option value="green">Green</option>
                            <option value="pink">Pink</option>
                            <option value="teal">Teal</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" name="add_bot" id="submitBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition-all w-full md:w-auto">
                        Create Bot
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('modalTitle').innerText = 'Add New Bot';
        document.getElementById('submitBtn').name = 'add_bot';
        document.getElementById('submitBtn').innerText = 'Create Bot';
        document.getElementById('newBotFields').classList.remove('hidden'); 
        
        // Reset form
        document.getElementById('botId').value = '';
        document.getElementById('botName').value = '';
        document.getElementById('botDesc').value = '';
        document.getElementById('botRoiMin').value = '';
        document.getElementById('botRoiMax').value = '';
        document.getElementById('botWinRate').value = '';
        document.getElementById('botMinInv').value = '';
        document.getElementById('botRisk').value = '';
        
        document.getElementById('botModal').classList.remove('hidden');
    }

    function openEditModal(json) {
        const bot = JSON.parse(json);
        
        document.getElementById('modalTitle').innerText = 'Edit Bot Config';
        document.getElementById('submitBtn').name = 'update_bot';
        document.getElementById('submitBtn').innerText = 'Save Changes';
        document.getElementById('newBotFields').classList.add('hidden'); // Hide icon/color on edit
        
        // Populate form
        document.getElementById('botId').value = bot.id;
        document.getElementById('botName').value = bot.name;
        document.getElementById('botDesc').value = bot.description;
        document.getElementById('botRoiMin').value = bot.roi_min;
        document.getElementById('botRoiMax').value = bot.roi_max;
        document.getElementById('botWinRate').value = bot.win_rate;
        document.getElementById('botMinInv').value = bot.min_investment;
        document.getElementById('botRisk').value = bot.risk_level;
        
        document.getElementById('botModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('botModal').classList.add('hidden');
    }

    document.getElementById('botModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    <?php echo $alert; ?>
</script>

<?php include 'footer.php'; ?>