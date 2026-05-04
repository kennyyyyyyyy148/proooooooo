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
                                    <option value="Afghanistan">Afghanistan</option>
                                    <option value="Albania">Albania</option>
                                    <option value="Algeria">Algeria</option>
                                    <option value="Andorra">Andorra</option>
                                    <option value="Angola">Angola</option>
                                    <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                                    <option value="Argentina">Argentina</option>
                                    <option value="Armenia">Armenia</option>
                                    <option value="Australia">Australia</option>
                                    <option value="Austria">Austria</option>
                                    <option value="Azerbaijan">Azerbaijan</option>
                                    <option value="Bahamas">Bahamas</option>
                                    <option value="Bahrain">Bahrain</option>
                                    <option value="Bangladesh">Bangladesh</option>
                                    <option value="Barbados">Barbados</option>
                                    <option value="Belarus">Belarus</option>
                                    <option value="Belgium">Belgium</option>
                                    <option value="Belize">Belize</option>
                                    <option value="Benin">Benin</option>
                                    <option value="Bhutan">Bhutan</option>
                                    <option value="Bolivia">Bolivia</option>
                                    <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                                    <option value="Botswana">Botswana</option>
                                    <option value="Brazil">Brazil</option>
                                    <option value="Brunei">Brunei</option>
                                    <option value="Bulgaria">Bulgaria</option>
                                    <option value="Burkina Faso">Burkina Faso</option>
                                    <option value="Burundi">Burundi</option>
                                    <option value="Côte d'Ivoire">Côte d'Ivoire</option>
                                    <option value="Cabo Verde">Cabo Verde</option>
                                    <option value="Cambodia">Cambodia</option>
                                    <option value="Cameroon">Cameroon</option>
                                    <option value="Canada">Canada</option>
                                    <option value="Central African Republic">Central African Republic</option>
                                    <option value="Chad">Chad</option>
                                    <option value="Chile">Chile</option>
                                    <option value="China">China</option>
                                    <option value="Colombia">Colombia</option>
                                    <option value="Comoros">Comoros</option>
                                    <option value="Congo (Congo-Brazzaville)">Congo (Congo-Brazzaville)</option>
                                    <option value="Costa Rica">Costa Rica</option>
                                    <option value="Croatia">Croatia</option>
                                    <option value="Cuba">Cuba</option>
                                    <option value="Cyprus">Cyprus</option>
                                    <option value="Czechia (Czech Republic)">Czechia (Czech Republic)</option>
                                    <option value="Democratic Republic of the Congo">Democratic Republic of the Congo</option>
                                    <option value="Denmark">Denmark</option>
                                    <option value="Djibouti">Djibouti</option>
                                    <option value="Dominica">Dominica</option>
                                    <option value="Dominican Republic">Dominican Republic</option>
                                    <option value="Ecuador">Ecuador</option>
                                    <option value="Egypt">Egypt</option>
                                    <option value="El Salvador">El Salvador</option>
                                    <option value="Equatorial Guinea">Equatorial Guinea</option>
                                    <option value="Eritrea">Eritrea</option>
                                    <option value="Estonia">Estonia</option>
                                    <option value="Eswatini (fmr. 'Swaziland')">Eswatini</option>
                                    <option value="Ethiopia">Ethiopia</option>
                                    <option value="Fiji">Fiji</option>
                                    <option value="Finland">Finland</option>
                                    <option value="France">France</option>
                                    <option value="Gabon">Gabon</option>
                                    <option value="Gambia">Gambia</option>
                                    <option value="Georgia">Georgia</option>
                                    <option value="Germany">Germany</option>
                                    <option value="Ghana">Ghana</option>
                                    <option value="Greece">Greece</option>
                                    <option value="Grenada">Grenada</option>
                                    <option value="Guatemala">Guatemala</option>
                                    <option value="Guinea">Guinea</option>
                                    <option value="Guinea-Bissau">Guinea-Bissau</option>
                                    <option value="Guyana">Guyana</option>
                                    <option value="Haiti">Haiti</option>
                                    <option value="Holy See">Holy See</option>
                                    <option value="Honduras">Honduras</option>
                                    <option value="Hungary">Hungary</option>
                                    <option value="Iceland">Iceland</option>
                                    <option value="India">India</option>
                                    <option value="Indonesia">Indonesia</option>
                                    <option value="Iran">Iran</option>
                                    <option value="Iraq">Iraq</option>
                                    <option value="Ireland">Ireland</option>
                                    <option value="Israel">Israel</option>
                                    <option value="Italy">Italy</option>
                                    <option value="Jamaica">Jamaica</option>
                                    <option value="Japan">Japan</option>
                                    <option value="Jordan">Jordan</option>
                                    <option value="Kazakhstan">Kazakhstan</option>
                                    <option value="Kenya">Kenya</option>
                                    <option value="Kiribati">Kiribati</option>
                                    <option value="Kuwait">Kuwait</option>
                                    <option value="Kyrgyzstan">Kyrgyzstan</option>
                                    <option value="Laos">Laos</option>
                                    <option value="Latvia">Latvia</option>
                                    <option value="Lebanon">Lebanon</option>
                                    <option value="Lesotho">Lesotho</option>
                                    <option value="Liberia">Liberia</option>
                                    <option value="Libya">Libya</option>
                                    <option value="Liechtenstein">Liechtenstein</option>
                                    <option value="Lithuania">Lithuania</option>
                                    <option value="Luxembourg">Luxembourg</option>
                                    <option value="Madagascar">Madagascar</option>
                                    <option value="Malawi">Malawi</option>
                                    <option value="Malaysia">Malaysia</option>
                                    <option value="Maldives">Maldives</option>
                                    <option value="Mali">Mali</option>
                                    <option value="Malta">Malta</option>
                                    <option value="Marshall Islands">Marshall Islands</option>
                                    <option value="Mauritania">Mauritania</option>
                                    <option value="Mauritius">Mauritius</option>
                                    <option value="Mexico">Mexico</option>
                                    <option value="Micronesia">Micronesia</option>
                                    <option value="Moldova">Moldova</option>
                                    <option value="Monaco">Monaco</option>
                                    <option value="Mongolia">Mongolia</option>
                                    <option value="Montenegro">Montenegro</option>
                                    <option value="Morocco">Morocco</option>
                                    <option value="Mozambique">Mozambique</option>
                                    <option value="Myanmar (formerly Burma)">Myanmar (Burma)</option>
                                    <option value="Namibia">Namibia</option>
                                    <option value="Nauru">Nauru</option>
                                    <option value="Nepal">Nepal</option>
                                    <option value="Netherlands">Netherlands</option>
                                    <option value="New Zealand">New Zealand</option>
                                    <option value="Nicaragua">Nicaragua</option>
                                    <option value="Niger">Niger</option>
                                    <option value="Nigeria">Nigeria</option>
                                    <option value="North Korea">North Korea</option>
                                    <option value="North Macedonia">North Macedonia</option>
                                    <option value="Norway">Norway</option>
                                    <option value="Oman">Oman</option>
                                    <option value="Pakistan">Pakistan</option>
                                    <option value="Palau">Palau</option>
                                    <option value="Palestine State">Palestine State</option>
                                    <option value="Panama">Panama</option>
                                    <option value="Papua New Guinea">Papua New Guinea</option>
                                    <option value="Paraguay">Paraguay</option>
                                    <option value="Peru">Peru</option>
                                    <option value="Philippines">Philippines</option>
                                    <option value="Poland">Poland</option>
                                    <option value="Portugal">Portugal</option>
                                    <option value="Qatar">Qatar</option>
                                    <option value="Romania">Romania</option>
                                    <option value="Russia">Russia</option>
                                    <option value="Rwanda">Rwanda</option>
                                    <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                                    <option value="Saint Lucia">Saint Lucia</option>
                                    <option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
                                    <option value="Samoa">Samoa</option>
                                    <option value="San Marino">San Marino</option>
                                    <option value="Sao Tome and Principe">Sao Tome and Principe</option>
                                    <option value="Saudi Arabia">Saudi Arabia</option>
                                    <option value="Senegal">Senegal</option>
                                    <option value="Serbia">Serbia</option>
                                    <option value="Seychelles">Seychelles</option>
                                    <option value="Sierra Leone">Sierra Leone</option>
                                    <option value="Singapore">Singapore</option>
                                    <option value="Slovakia">Slovakia</option>
                                    <option value="Slovenia">Slovenia</option>
                                    <option value="Solomon Islands">Solomon Islands</option>
                                    <option value="Somalia">Somalia</option>
                                    <option value="South Africa">South Africa</option>
                                    <option value="South Korea">South Korea</option>
                                    <option value="South Sudan">South Sudan</option>
                                    <option value="Spain">Spain</option>
                                    <option value="Sri Lanka">Sri Lanka</option>
                                    <option value="Sudan">Sudan</option>
                                    <option value="Suriname">Suriname</option>
                                    <option value="Sweden">Sweden</option>
                                    <option value="Switzerland">Switzerland</option>
                                    <option value="Syria">Syria</option>
                                    <option value="Tajikistan">Tajikistan</option>
                                    <option value="Tanzania">Tanzania</option>
                                    <option value="Thailand">Thailand</option>
                                    <option value="Timor-Leste">Timor-Leste</option>
                                    <option value="Togo">Togo</option>
                                    <option value="Tonga">Tonga</option>
                                    <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                                    <option value="Tunisia">Tunisia</option>
                                    <option value="Turkey">Turkey</option>
                                    <option value="Turkmenistan">Turkmenistan</option>
                                    <option value="Tuvalu">Tuvalu</option>
                                    <option value="Uganda">Uganda</option>
                                    <option value="Ukraine">Ukraine</option>
                                    <option value="United Arab Emirates">United Arab Emirates</option>
                                    <option value="United Kingdom">United Kingdom</option>
                                    <option value="United States">United States</option>
                                    <option value="Uruguay">Uruguay</option>
                                    <option value="Uzbekistan">Uzbekistan</option>
                                    <option value="Vanuatu">Vanuatu</option>
                                    <option value="Venezuela">Venezuela</option>
                                    <option value="Vietnam">Vietnam</option>
                                    <option value="Yemen">Yemen</option>
                                    <option value="Zambia">Zambia</option>
                                    <option value="Zimbabwe">Zimbabwe</option>
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