<?php
include 'header.php';

$alert = "";

// --- 1. UPDATE PERSONAL INFO ---
if (isset($_POST['update_profile'])) {
    $phone_val = clean($_POST['phone']);
    $country_val = clean($_POST['country']);
    $address_val = clean($_POST['address']);
    $dob_val = clean($_POST['dob']);

    $sql = "UPDATE users SET phone='$phone_val', country='$country_val', address='$address_val', dob='$dob_val' WHERE id='$user_id'";
    
    if (mysqli_query($link, $sql)) {
        $alert = "Swal.fire({icon: 'success', title: 'Saved', text: 'Profile details updated successfully.'});";
        // Update local variables
        $phone = $phone_val;
        $country = $country_val;
        $address = $address_val;
        $dob = $dob_val;
    } else {
        $alert = "Swal.fire({icon: 'error', title: 'Error', text: 'Failed to update profile.'});";
    }
}

// --- 2. CHANGE PASSWORD ---
if (isset($_POST['change_password'])) {
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if (password_verify($current_pass, $password)) {
        if ($new_pass === $confirm_pass) {
            if (strlen($new_pass) >= 6) {
                $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET password='$new_hash' WHERE id='$user_id'";
                if(mysqli_query($link, $sql)){
                    $alert = "Swal.fire({icon: 'success', title: 'Success', text: 'Password changed successfully.'});";
                }
            } else {
                $alert = "Swal.fire({icon: 'error', title: 'Weak Password', text: 'Password must be at least 6 characters.'});";
            }
        } else {
            $alert = "Swal.fire({icon: 'error', title: 'Mismatch', text: 'New passwords do not match.'});";
        }
    } else {
        $alert = "Swal.fire({icon: 'error', title: 'Invalid', text: 'Current password is incorrect.'});";
    }
}

// --- 3. SET/CHANGE TRANSACTION PIN ---
if (isset($_POST['update_pin'])) {
    $new_pin = clean($_POST['new_pin']);
    $current_pin_input = clean($_POST['current_pin']);

    $can_update = false;
    if (!empty($transaction_pin)) {
        if ($current_pin_input == $transaction_pin) {
            $can_update = true;
        } else {
            $alert = "Swal.fire({icon: 'error', title: 'Incorrect', text: 'Current PIN is wrong.'});";
        }
    } else {
        $can_update = true;
    }

    if ($can_update) {
        if (strlen($new_pin) == 4 && is_numeric($new_pin)) {
            $sql = "UPDATE users SET transaction_pin='$new_pin' WHERE id='$user_id'";
            if(mysqli_query($link, $sql)){
                $alert = "Swal.fire({icon: 'success', title: 'PIN Set', text: 'Transaction PIN updated.'});";
                $transaction_pin = $new_pin; 
            }
        } else {
            $alert = "Swal.fire({icon: 'error', title: 'Invalid Format', text: 'PIN must be exactly 4 digits.'});";
        }
    }
}
?>

<div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24">

    <div class="max-w-5xl mx-auto space-y-8">
        
        <div class="glass-panel p-8 rounded-3xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-r from-indigo-600 to-violet-600 opacity-90"></div>
            
            <div class="relative flex flex-col md:flex-row items-center md:items-end gap-6 pt-10">
                <div class="w-32 h-32 rounded-full p-1 bg-white dark:bg-[#02040a] relative z-10">
                    <img src="<?php echo $avatar_url; ?>" class="w-full h-full rounded-full object-cover border-4 border-indigo-50 dark:border-white/10">
                    <div class="absolute bottom-2 right-2 w-6 h-6 rounded-full bg-green-500 border-4 border-white dark:border-[#02040a]" title="Online"></div>
                </div>
                
                <div class="flex-1 text-center md:text-left mb-2">
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white"><?php echo $fullname; ?></h1>
                    <p class="text-slate-500 dark:text-slate-400 font-medium">@<?php echo $username; ?> <span class="mx-2">•</span> <?php echo $email; ?></p>
                </div>

                <div class="flex flex-col items-center md:items-end gap-2">
                    <span class="px-4 py-1.5 rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-sm font-bold border border-indigo-100 dark:border-indigo-500/20">
                        Account ID: <?php echo $account_id; ?>
                    </span>
                    <span class="px-4 py-1.5 rounded-full <?php echo ($kyc_status == 'approved') ? 'bg-green-50 text-green-600 border-green-200' : 'bg-orange-50 text-orange-600 border-orange-200'; ?> text-sm font-bold border">
                        KYC: <?php echo ucfirst($kyc_status); ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 glass-panel p-6 md:p-8 rounded-3xl h-fit">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-6 flex items-center gap-2">
                    <i class="fa-regular fa-id-card text-indigo-500"></i> Personal Details
                </h3>
                
                <form method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-2">Full Name (Read Only)</label>
                            <input type="text" value="<?php echo $fullname; ?>" readonly class="w-full bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-slate-500 dark:text-slate-400 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-2">Email Address (Read Only)</label>
                            <input type="text" value="<?php echo $email; ?>" readonly class="w-full bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-slate-500 dark:text-slate-400 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-2">Phone Number</label>
                            <input type="tel" name="phone" value="<?php echo $phone; ?>" placeholder="+1 (555) 000-0000" class="w-full bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 focus:border-indigo-500 focus:outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-2">Date of Birth</label>
                            <input type="date" name="dob" value="<?php echo $dob; ?>" class="w-full bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 focus:border-indigo-500 focus:outline-none transition-all">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-2">Country</label>
                            <div class="relative">
                                <select name="country" class="w-full bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 focus:border-indigo-500 focus:outline-none appearance-none cursor-pointer transition-all">
                                    <option value="">Select Country</option>
                                    <?php 
                                    $countries = [
                                        "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda", "Argentina", "Armenia", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bhutan", "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Côte d'Ivoire", "Cabo Verde", "Cambodia", "Cameroon", "Canada", "Central African Republic", "Chad", "Chile", "China", "Colombia", "Comoros", "Congo (Congo-Brazzaville)", "Costa Rica", "Croatia", "Cuba", "Cyprus", "Czechia", "Democratic Republic of the Congo", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Eswatini", "Ethiopia", "Fiji", "Finland", "France", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Holy See", "Honduras", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Morocco", "Mozambique", "Myanmar (Burma)", "Namibia", "Nauru", "Nepal", "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Korea", "North Macedonia", "Norway", "Oman", "Pakistan", "Palau", "Palestine State", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Qatar", "Romania", "Russia", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Korea", "South Sudan", "Spain", "Sri Lanka", "Sudan", "Suriname", "Sweden", "Switzerland", "Syria", "Tajikistan", "Tanzania", "Thailand", "Timor-Leste", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe"
                                    ];
                                    foreach ($countries as $c) {
                                        $selected = ($country == $c) ? 'selected' : '';
                                        echo "<option value=\"$c\" $selected>$c</option>";
                                    }
                                    ?>
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                            </div>
                        </div>

                        <!-- Spanning 2 columns for the address -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-2">Residential Address</label>
                            <input type="text" name="address" value="<?php echo $address; ?>" placeholder="123 Main Street, Apt 4B" class="w-full bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 focus:border-indigo-500 focus:outline-none transition-all">
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" name="update_profile" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-indigo-500/30 transition-all transform hover:scale-[1.02]">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <div class="space-y-6">
                
                <div class="glass-panel p-6 rounded-3xl border border-indigo-500/20 shadow-lg shadow-indigo-500/5">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-user-secret text-indigo-500"></i> Secret Phrase
                    </h3>
                    <div class="bg-slate-50 dark:bg-[#0B0F19] border border-slate-200 dark:border-white/10 rounded-xl p-4 relative group">
                        <div id="phraseMask" class="font-mono text-slate-500 dark:text-slate-400 tracking-widest blur-sm select-none">
                            •••• •••• •••• •••• •••• •••• •••• ••••
                        </div>
                        <div id="phraseReal" class="hidden font-mono text-indigo-600 dark:text-indigo-400 text-sm break-words leading-relaxed font-bold">
                            <?php echo $secret_phrase; ?>
                        </div>
                        
                        <button onclick="togglePhrase()" class="absolute top-1/2 right-4 transform -translate-y-1/2 text-slate-400 hover:text-indigo-500 transition-colors">
                            <i id="phraseIcon" class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-2 flex items-center gap-1">
                        <i class="fa-solid fa-lock"></i> Keep this phrase safe. Never share it.
                    </p>
                </div>

                <div class="glass-panel p-6 rounded-3xl">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-lock text-indigo-500"></i> Password
                    </h3>
                    <form method="POST" class="space-y-3">
                        <div>
                            <input type="password" name="current_password" placeholder="Current Password" required class="w-full bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 outline-none">
                        </div>
                        <div>
                            <input type="password" name="new_password" placeholder="New Password" required class="w-full bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 outline-none">
                        </div>
                        <div>
                            <input type="password" name="confirm_password" placeholder="Confirm New Password" required class="w-full bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 outline-none">
                        </div>
                        <button type="submit" name="change_password" class="w-full bg-slate-800 dark:bg-white/10 hover:bg-slate-900 dark:hover:bg-white/20 text-white font-bold py-3 rounded-xl transition-all text-sm">
                            Update Password
                        </button>
                    </form>
                </div>

                <div class="glass-panel p-6 rounded-3xl">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-key text-indigo-500"></i> Transaction PIN
                    </h3>
                    <form method="POST" class="space-y-3">
                        <?php if(!empty($transaction_pin)): ?>
                        <div>
                            <input type="password" name="current_pin" placeholder="Current PIN" maxlength="4" required class="w-full bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 outline-none text-center tracking-widest font-bold">
                        </div>
                        <?php else: ?>
                        <div class="p-3 bg-yellow-500/10 border border-yellow-500/20 rounded-xl mb-2">
                            <p class="text-xs text-yellow-600 dark:text-yellow-400">Set a PIN to secure withdrawals.</p>
                        </div>
                        <?php endif; ?>
                        
                        <div>
                            <input type="password" name="new_pin" placeholder="New 4-Digit PIN" maxlength="4" required class="w-full bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 outline-none text-center tracking-widest font-bold">
                        </div>
                        <button type="submit" name="update_pin" class="w-full bg-slate-800 dark:bg-white/10 hover:bg-slate-900 dark:hover:bg-white/20 text-white font-bold py-3 rounded-xl transition-all text-sm">
                            <?php echo empty($transaction_pin) ? 'Set PIN' : 'Change PIN'; ?>
                        </button>
                    </form>
                </div>

            </div>

        </div>

    </div>

</div>

<script>
    // Toggle Secret Phrase Visibility
    function togglePhrase() {
        const mask = document.getElementById('phraseMask');
        const real = document.getElementById('phraseReal');
        const icon = document.getElementById('phraseIcon');
        
        if (real.classList.contains('hidden')) {
            // Show Real
            real.classList.remove('hidden');
            mask.classList.add('hidden');
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            // Hide (Mask)
            real.classList.add('hidden');
            mask.classList.remove('hidden');
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    <?php echo $alert; ?>
</script>

<?php include 'footer.php'; ?>