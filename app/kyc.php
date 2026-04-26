<?php
include 'header.php'; 

$alert = "";

// --- HANDLE FORM SUBMISSION ---
if (isset($_POST['submit_kyc'])) {
  
    // 1. Sanitize Personal Info
    $full_name = clean($_POST['fullname']); 
    $dob       = clean($_POST['dob']);
    $country   = clean($_POST['country']);
    $phone     = clean($_POST['phone']);
    $address   = clean($_POST['address']);

    // 2. Handle File Uploads
    $upload_dir = "../uploads/kyc/";
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) { mkdir($upload_dir, 0777, true); }

    $front_name = $_FILES['id_front']['name'];
    $back_name  = $_FILES['id_back']['name'];
    
    $front_tmp = $_FILES['id_front']['tmp_name'];
    $back_tmp  = $_FILES['id_back']['tmp_name'];

    // Generate unique filenames
    $front_new_name = $user_id . "_front_" . time() . "_" . $front_name;
    $back_new_name  = $user_id . "_back_" . time() . "_" . $back_name;

    // Validation: Ensure both files are selected
    if (!empty($front_name) && !empty($back_name)) {
        
        // Move files
        $upload_front = move_uploaded_file($front_tmp, $upload_dir . $front_new_name);
        $upload_back  = move_uploaded_file($back_tmp, $upload_dir . $back_new_name);

        if ($upload_front && $upload_back) {
            
            // 3. Update Database
            $sql = "UPDATE users SET 
                    full_name = '$full_name',
                    dob = '$dob',
                    country = '$country',
                    phone = '$phone',
                    address = '$address',
                    kyc_front = '$front_new_name',
                    kyc_back = '$back_new_name',
                    kyc_status = 'pending' 
                    WHERE id = '$user_id'";

            if (mysqli_query($link, $sql)) {
                $alert = "Swal.fire({
                    icon: 'success', 
                    title: 'Submitted!', 
                    text: 'Your documents are under review. This usually takes 24 hours.',
                    confirmButtonText: 'Back to Dashboard'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'dashboard.php';
                    }
                });";
            } else {
                $alert = "Swal.fire({icon: 'error', title: 'Database Error', text: 'Could not save your data.'});";
            }

        } else {
            $alert = "Swal.fire({icon: 'error', title: 'Upload Failed', text: 'Failed to upload images. Ensure they are JPG/PNG/PDF.'});";
        }
    } else {
        $alert = "Swal.fire({icon: 'warning', title: 'Missing Files', text: 'Please upload both front and back of your ID.'});";
    }
}
?>

<div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 space-y-6">

    <div class="w-full max-w-6xl mx-auto">
        
        <?php if ($kyc_status == 'pending'): ?>
            <div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
                <div class="glass-panel w-full max-w-2xl rounded-3xl p-8 md:p-12 border border-amber-200/50 dark:border-amber-500/10 shadow-2xl relative overflow-hidden">
                    
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-32 bg-amber-500/20 rounded-full blur-[50px] pointer-events-none"></div>

                    <div class="relative z-10">
                        <div class="w-24 h-24 mx-auto bg-amber-50 dark:bg-amber-500/10 rounded-full flex items-center justify-center mb-6 shadow-inner relative">
                            <div class="absolute inset-0 rounded-full border-4 border-amber-500/30 border-t-amber-500 animate-spin"></div>
                            <i class="fa-solid fa-hourglass-half text-4xl text-amber-500"></i>
                        </div>

                        <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-3">Verification in Progress</h1>
                        <p class="text-slate-500 dark:text-slate-400 text-lg mb-8 max-w-md mx-auto">
                            We have received your documents and they are currently under review. This process usually takes 24-48 hours.
                        </p>

                        <div class="bg-amber-50 dark:bg-amber-500/5 border border-amber-100 dark:border-amber-500/10 rounded-xl p-4 flex items-center gap-4 text-left max-w-md mx-auto">
                            <i class="fa-solid fa-circle-info text-amber-500 text-xl"></i>
                            <div>
                                <p class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase">Status</p>
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Pending Administrative Review</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif ($kyc_status == 'approved'): ?>
            <div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
                <div class="glass-panel w-full max-w-2xl rounded-3xl p-8 md:p-12 border border-emerald-200/50 dark:border-emerald-500/10 shadow-2xl relative overflow-hidden">
                    
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-32 bg-emerald-500/20 rounded-full blur-[50px] pointer-events-none"></div>

                    <div class="relative z-10">
                        <div class="w-24 h-24 mx-auto bg-emerald-50 dark:bg-emerald-500/10 rounded-full flex items-center justify-center mb-6 shadow-lg shadow-emerald-500/20">
                            <i class="fa-solid fa-shield-check text-4xl text-emerald-500"></i>
                        </div>

                        <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-3">Identity Verified</h1>
                        <p class="text-slate-500 dark:text-slate-400 text-lg mb-8 max-w-md mx-auto">
                            Great news! Your identity has been successfully verified. You now have full access to all premium features and higher withdrawal limits.
                        </p>

                       <a href="dashboard.php">
                            <button class="px-10 py-4 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/40 hover:scale-[1.01] transition-all uppercase tracking-wide text-sm flex items-center gap-2 mx-auto">
                                Go to Dashboard <i class="fa-solid fa-arrow-right"></i>
                            </button>
                        </a>

                        
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight uppercase">Identity Verification</h1>
                    <p class="text-slate-500 dark:text-slate-400 mt-2">Complete AML/KYC to unlock higher withdrawal limits and premium features.</p>
                </div>
                
                <div class="flex items-center gap-4 bg-slate-100 dark:bg-white/5 px-6 py-3 rounded-2xl">
                    <div class="flex items-center gap-2">
                        <div id="step1-indicator" class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold shadow-lg shadow-indigo-500/30 transition-all">1</div>
                        <span class="text-sm font-bold text-indigo-600 hidden md:block">Personal</span>
                    </div>
                    <div class="w-12 h-1 bg-slate-200 dark:bg-white/10 rounded-full relative overflow-hidden">
                        <div id="step-bar" class="absolute left-0 top-0 h-full w-0 bg-indigo-600 transition-all duration-500"></div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div id="step2-indicator" class="w-8 h-8 rounded-full bg-slate-200 dark:bg-white/10 text-slate-500 dark:text-slate-400 flex items-center justify-center font-bold transition-all">2</div>
                        <span class="text-sm font-bold text-slate-500 dark:text-slate-400 hidden md:block">Documents</span>
                    </div>
                </div>
            </div>

            <form method="POST" enctype="multipart/form-data" class="glass-panel w-full rounded-3xl p-6 md:p-10 border border-slate-200 dark:border-white/5 shadow-2xl relative overflow-hidden">
                
                <div id="step1" class="active-step">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-100 dark:border-white/5 pb-4">Personal Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Full Name</label>
                            <input type="text" name="fullname" value="<?php echo $fullname; ?>" placeholder="John Doe" required class="w-full bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-4 px-5 focus:outline-none focus:border-indigo-500 transition-all">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Date of Birth</label>
                            <input type="date" name="dob" required class="w-full bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-4 px-5 focus:outline-none focus:border-indigo-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Nationality</label>
                            <div class="relative">
                                <select name="country" required class="w-full bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-4 px-5 focus:outline-none focus:border-indigo-500 appearance-none cursor-pointer">
                                    <option value="">Select Country</option>
                                    <option value="United States">United States</option>
                                    <option value="United Kingdom">United Kingdom</option>
                                    <option value="Nigeria">Nigeria</option>
                                    </select>
                                <i class="fa-solid fa-chevron-down absolute right-5 top-1/2 transform -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Phone Number</label>
                            <input type="tel" name="phone" placeholder="+1 (555) 000-0000" required class="w-full bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-4 px-5 focus:outline-none focus:border-indigo-500 transition-all">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Residential Address</label>
                            <input type="text" name="address" placeholder="123 Main Street, Apt 4B" required class="w-full bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-4 px-5 focus:outline-none focus:border-indigo-500 transition-all">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" onclick="nextStep()" class="px-10 py-4 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-bold shadow-lg hover:shadow-indigo-500/25 hover:scale-[1.01] transition-all uppercase tracking-wide text-sm flex items-center gap-2">
                            Next Step <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <div id="step2" class="hidden-step" style="display: none;">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Document Verification</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-8 pb-4 border-b border-slate-100 dark:border-white/5">Please upload a clear photo of your Government-issued Identity Card (ID), Passport, or Driver's License.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                        
                        <div class="group">
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">ID Front Side</p>
                            <div class="border-2 border-dashed border-slate-300 dark:border-white/10 rounded-2xl h-56 flex flex-col items-center justify-center text-center cursor-pointer hover:border-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-500/5 transition-all relative overflow-hidden bg-slate-50/50 dark:bg-black/20">
                                <input type="file" name="id_front" id="fileFront" onchange="updateFileName('fileFront', 'textFront')" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div class="w-14 h-14 bg-slate-200 dark:bg-white/5 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                    <i class="fa-regular fa-id-card text-2xl text-slate-400 group-hover:text-indigo-500 transition-colors"></i>
                                </div>
                                <p id="textFront" class="text-sm font-bold text-slate-600 dark:text-slate-300 group-hover:text-indigo-500 transition-colors">Upload Front Side</p>
                                <p class="text-[10px] text-slate-400 mt-2">PNG, JPG or PDF (Max 10MB)</p>
                            </div>
                        </div>

                        <div class="group">
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">ID Back Side</p>
                            <div class="border-2 border-dashed border-slate-300 dark:border-white/10 rounded-2xl h-56 flex flex-col items-center justify-center text-center cursor-pointer hover:border-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-500/5 transition-all relative overflow-hidden bg-slate-50/50 dark:bg-black/20">
                                <input type="file" name="id_back" id="fileBack" onchange="updateFileName('fileBack', 'textBack')" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div class="w-14 h-14 bg-slate-200 dark:bg-white/5 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                    <i class="fa-regular fa-image text-2xl text-slate-400 group-hover:text-indigo-500 transition-colors"></i>
                                </div>
                                <p id="textBack" class="text-sm font-bold text-slate-600 dark:text-slate-300 group-hover:text-indigo-500 transition-colors">Upload Back Side</p>
                                <p class="text-[10px] text-slate-400 mt-2">PNG, JPG or PDF (Max 10MB)</p>
                            </div>
                        </div>

                    </div>

                    <div class="flex justify-between items-center pt-4 border-t border-slate-100 dark:border-white/5">
                        <button type="button" onclick="prevStep()" class="px-8 py-4 rounded-xl bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-white font-bold hover:bg-slate-200 dark:hover:bg-white/10 transition-all uppercase tracking-wide text-sm border border-slate-200 dark:border-white/5 flex items-center gap-2">
                            <i class="fa-solid fa-arrow-left"></i> Go Back
                        </button>
                        <button type="submit" name="submit_kyc" class="px-10 py-4 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-bold shadow-lg hover:shadow-indigo-500/25 hover:scale-[1.01] transition-all uppercase tracking-wide text-sm flex items-center gap-2">
                            <i class="fa-solid fa-check-circle"></i> Submit Verification
                        </button>
                    </div>
                </div>
            </form>

            <div class="mt-8 flex justify-center">
                <div class="flex items-center gap-2 text-slate-400 dark:text-slate-500 text-xs">
                    <i class="fa-solid fa-lock"></i>
                    <span>Your data is encrypted and securely stored.</span>
                </div>
            </div>

        <?php endif; ?>

    </div>
</div>

<script>
    // --- MULTI-STEP FORM VISUAL LOGIC ---
    function nextStep() {
        // Validate Step 1 Inputs (Optional, basic HTML5 validation handles form submit)
        const inputs = document.querySelectorAll('#step1 input, #step1 select');
        let valid = true;
        inputs.forEach(input => {
            if (!input.checkValidity()) {
                input.reportValidity();
                valid = false;
            }
        });
        if (!valid) return;

        // Transition
        document.getElementById('step1').classList.remove('active-step');
        document.getElementById('step1').classList.add('hidden-step');
        
        setTimeout(() => {
            document.getElementById('step1').style.display = 'none';
            document.getElementById('step2').style.display = 'block';
            setTimeout(() => {
                document.getElementById('step2').classList.remove('hidden-step');
                document.getElementById('step2').classList.add('active-step');
            }, 50);
        }, 300);

        // Update UI
        document.getElementById('step-bar').style.width = '100%';
        document.getElementById('step2-indicator').classList.remove('bg-slate-200', 'dark:bg-white/10', 'text-slate-500', 'dark:text-slate-400');
        document.getElementById('step2-indicator').classList.add('bg-indigo-600', 'text-white', 'shadow-lg', 'shadow-indigo-500/30');
    }

    function prevStep() {
        document.getElementById('step2').classList.remove('active-step');
        document.getElementById('step2').classList.add('hidden-step');
        
        setTimeout(() => {
            document.getElementById('step2').style.display = 'none';
            document.getElementById('step1').style.display = 'block';
            setTimeout(() => {
                document.getElementById('step1').classList.remove('hidden-step');
                document.getElementById('step1').classList.add('active-step');
            }, 50);
        }, 300);

        document.getElementById('step-bar').style.width = '0%';
        document.getElementById('step2-indicator').classList.add('bg-slate-200', 'dark:bg-white/10', 'text-slate-500', 'dark:text-slate-400');
        document.getElementById('step2-indicator').classList.remove('bg-indigo-600', 'text-white', 'shadow-lg', 'shadow-indigo-500/30');
    }

    // --- SHOW FILE NAME ON UPLOAD ---
    function updateFileName(inputId, textId) {
        const input = document.getElementById(inputId);
        const textDisplay = document.getElementById(textId);
        if (input.files && input.files.length > 0) {
            textDisplay.innerText = input.files[0].name;
            textDisplay.classList.add('text-indigo-500');
        }
    }

    // --- INJECT SWEETALERT ---
    <?php echo $alert; ?>
</script>

<?php
include 'footer.php'; 
?>