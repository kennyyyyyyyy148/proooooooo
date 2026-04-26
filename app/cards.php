<?php
include 'header.php';

$alert = "";
$user_id = $_SESSION['user_id'];

// --- 1. HANDLE CARD PURCHASE ---
if (isset($_POST['purchase_card'])) {
    $card_name = clean($_POST['card_name']);
    $card_type = clean($_POST['card_type']); 
    $tx_hash   = clean($_POST['tx_hash']);
    
    // Removing Image Upload Logic as per previous context style preferences
    $sql = "INSERT INTO virtual_cards (user_id, card_holder_name, card_type, tx_hash, proof_image, status) 
            VALUES ('$user_id', '$card_name', '$card_type', '$tx_hash', 'none', 'pending')";
    
    if (mysqli_query($link, $sql)) {
       $subject = "Virtual Card Order Received - " . $sitename;

        $body = '
        <div style="font-family: Helvetica, Arial, sans-serif; background-color: #f9fafb; padding: 50px 0;">
            <div style="max-width: 500px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); overflow: hidden;">
                
                <div style="padding: 40px 0 20px; text-align: center;">
                    <div style="background-color: #eef2ff; width: 64px; height: 64px; border-radius: 50%; display: inline-block; line-height: 64px; font-size: 32px; margin-bottom: 20px;">
                        💳
                    </div>
                    <h1 style="color: #111827; margin: 0; font-size: 24px; font-weight: bold;">Order Received</h1>
                </div>
       
                <div style="padding: 20px 40px 40px;">
                    <p style="color: #4b5563; font-size: 16px; text-align: center; margin-bottom: 30px;">
                        Hello <strong>' . $fullname . '</strong>, we have received your request for a new virtual card.
                    </p>
       
                    <div style="background-color: #f3f4f6; border-radius: 8px; padding: 20px;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="padding-bottom: 10px; color: #6b7280; font-size: 14px;">Card Type</td>
                                <td style="padding-bottom: 10px; text-align: right; color: #111827; font-weight: bold; font-size: 14px;">' . $card_type . '</td>
                            </tr>
                            <tr>
                                <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px;">Status</td>
                                <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; text-align: right;">
                                    <span style="background-color: #fffbeb; color: #d97706; font-size: 12px; font-weight: bold; padding: 4px 10px; border-radius: 20px; border: 1px solid #fcd34d;">Pending Review</span>
                                </td>
                            </tr>
                        </table>
                    </div>
       
                    <div style="margin-top: 30px; text-align: center;">
                        <p style="color: #9ca3af; font-size: 12px; margin: 0;">
                            We will notify you as soon as your card is ready for use.
                        </p>
                    </div>
                </div>
            </div>
        </div>';
        
        sendMail($email, $subject, $body);
        $alert = "Swal.fire({icon: 'success', title: 'Order Received', text: 'Your card request is pending approval.'});";
    } else {
        $alert = "Swal.fire({icon: 'error', title: 'Error', text: 'Database error.'});";
    }
}

// --- 2. HANDLE LINK CARD (WITH STRICT VALIDATION) ---
if (isset($_POST['link_card_method'])) {
    $card_holder = clean($_POST['card_holder']);
    
    // Remove spaces from card number for validation
    $raw_card_num = str_replace(' ', '', $_POST['card_number']); 
    $expiry = clean($_POST['expiry']);
    $cvv = clean($_POST['cvv']);
    
    // VALIDATION STEPS
    $errors = [];

    // 1. Check Card Number (Numeric and between 13-19 digits)
    if (!is_numeric($raw_card_num) || strlen($raw_card_num) < 13 || strlen($raw_card_num) > 19) {
        $errors[] = "Invalid Card Number. Must be 13-19 digits.";
    }

    // 2. Check CVV (Numeric and 3-4 digits)
    if (!is_numeric($cvv) || strlen($cvv) < 3 || strlen($cvv) > 4) {
        $errors[] = "Invalid CVV.";
    }

    // 3. Check Expiry Date (MM/YY format and Future Check)
    $exp_valid = false;
    if (preg_match('/^(0[1-9]|1[0-2])\/?([0-9]{2})$/', $expiry, $matches)) {
        $exp_month = intval($matches[1]);
        $exp_year_short = intval($matches[2]);
        $exp_year = intval("20" . $exp_year_short); // Convert YY to 20YY
        
        $current_year = intval(date('Y'));
        $current_month = intval(date('m'));

        if ($exp_year > $current_year || ($exp_year == $current_year && $exp_month >= $current_month)) {
            $exp_valid = true;
        } else {
            $errors[] = "Card has expired.";
        }
    } else {
        $errors[] = "Invalid Expiry Format (Use MM/YY).";
    }

    if (empty($errors)) {
        // Validation Passed -> Insert
        $sql = "INSERT INTO linked_banks (user_id, bank_name, account_name, account_number, expiry, cvv, created_at) 
                VALUES ('$user_id', 'Linked Card', '$card_holder', '$raw_card_num', '$expiry', '$cvv', NOW())";
        
        if (mysqli_query($link, $sql)) {
            $alert = "Swal.fire({icon: 'success', title: 'Linked', text: 'Card added successfully.'});";
        } else {
            $alert = "Swal.fire({icon: 'error', title: 'Error', text: 'Could not link card to database.'});";
        }
    } else {
        // Show the first validation error
        $alert = "Swal.fire({icon: 'warning', title: 'Invalid Details', text: '".$errors[0]."'});";
    }
}

// --- 3. SECURITY ACTIONS ---
if (isset($_POST['security_action'])) {
    $password = clean($_POST['password']);
    $action_type = $_POST['action_type']; 
    
    $user_q = mysqli_query($link, "SELECT password FROM users WHERE id='$user_id'");
    $user_row = mysqli_fetch_assoc($user_q);
    
    if (password_verify($password, $user_row['password'])) {
        if ($action_type == 'freeze') {
            mysqli_query($link, "UPDATE virtual_cards SET status = IF(status='active', 'frozen', 'active') WHERE user_id='$user_id'");
            $alert = "Swal.fire({icon: 'success', title: 'Success', text: 'Card status updated.'});";
        } elseif ($action_type == 'view') {
            $reveal_numbers = true; 
        }
    } else {
        $alert = "Swal.fire({icon: 'error', title: 'Access Denied', text: 'Incorrect Password'});";
    }
}

// Fetch Virtual Card
$card_query = mysqli_query($link, "SELECT * FROM virtual_cards WHERE user_id = '$user_id' ORDER BY id DESC LIMIT 1");
$has_card = (mysqli_num_rows($card_query) > 0);
$card = mysqli_fetch_assoc($card_query);

$card_status = $has_card ? $card['status'] : 'none';
$display_number = ($has_card && $card['card_number']) ? chunk_split($card['card_number'], 4, ' ') : '•••• •••• •••• ••••';
$display_cvv    = ($has_card && $card['cvv']) ? $card['cvv'] : '•••';
$display_expiry = ($has_card && $card['expiry']) ? $card['expiry'] : '••/••';

if (!isset($reveal_numbers)) {
    $display_number_masked = '•••• •••• •••• ' . substr($card['card_number'] ?? '0000', -4);
    $display_cvv_masked = '•••';
} else {
    $display_number_masked = $display_number;
    $display_cvv_masked = $display_cvv;
}

// Fetch Linked Cards
$bank_query = mysqli_query($link, "SELECT * FROM linked_banks WHERE user_id = '$user_id'");
?>

<style>
    .perspective-1000 {
        perspective: 1000px;
    }
    .transform-style-3d {
        transform-style: preserve-3d;
    }
    .backface-hidden {
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden; /* Safari */
    }
    .rotate-y-180 {
        transform: rotateY(180deg);
    }
    .card-inner {
        transition: transform 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275); /* Smooth bounce effect */
        width: 100%;
        height: 100%;
        position: relative;
    }
    /* FLIP ON HOVER */
    .group:hover .card-inner {
        transform: rotateY(180deg);
    }
</style>

<div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 space-y-6">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-1">Virtual Cards</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Spend your crypto anywhere globally, instantly.</p>
        </div>
        <?php if($card_status == 'pending'): ?>
            <span class="px-4 py-2 rounded-full bg-yellow-500/10 text-yellow-500 border border-yellow-500/20 text-xs font-bold animate-pulse"><i class="fa-solid fa-clock mr-1"></i> Issuance Pending</span>
        <?php elseif($card_status == 'active'): ?>
            <span class="px-4 py-2 rounded-full bg-green-500/10 text-green-500 border border-green-500/20 text-xs font-bold"><i class="fa-solid fa-check-circle mr-1"></i> Active</span>
        <?php elseif($card_status == 'frozen'): ?>
            <span class="px-4 py-2 rounded-full bg-red-500/10 text-red-500 border border-red-500/20 text-xs font-bold"><i class="fa-solid fa-lock mr-1"></i> Frozen</span>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
        
        <div class="flex flex-col gap-6">
            <?php if ($card_status == 'active' || $card_status == 'frozen'): ?>
                
                <div class="perspective-1000 group cursor-pointer h-56 md:h-64 w-full max-w-md mx-auto lg:mx-0">
                    <div class="relative w-full h-full text-white transform-style-3d card-inner">
                        
                        <div class="absolute inset-0 w-full h-full bg-gradient-to-br from-[#1e293b] via-[#0f172a] to-[#020617] rounded-3xl p-6 md:p-8 shadow-2xl backface-hidden border border-slate-700/50 flex flex-col justify-between overflow-hidden z-20">
                            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/5 rounded-full blur-3xl"></div>
                            <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl"></div>
                            
                            <?php if($card_status == 'frozen'): ?>
                                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm z-30 flex items-center justify-center">
                                    <div class="text-center"><i class="fa-solid fa-lock text-3xl text-white mb-2"></i><p class="font-bold uppercase tracking-widest text-sm">Card Frozen</p></div>
                                </div>
                            <?php endif; ?>

                            <div class="flex justify-between items-start relative z-10">
                                <div><h3 class="font-bold text-xl italic tracking-wider font-sans"><?php echo $sitename; ?></h3><p class="text-[10px] text-slate-400 uppercase tracking-[0.2em] mt-1">Virtual Platinum</p></div>
                                <div class="w-12 h-9 bg-gradient-to-br from-yellow-200 to-yellow-600 rounded-md shadow-inner border border-yellow-400/30 flex items-center justify-center opacity-90"><div class="w-8 h-6 border border-black/10 rounded-sm"></div></div>
                            </div>
                            <div class="flex flex-col gap-1 mt-4">
                                <p class="font-mono text-2xl tracking-[4px] shadow-black drop-shadow-md text-slate-200"><?php echo isset($reveal_numbers) ? $display_number : $display_number_masked; ?></p>
                                <div class="flex gap-4 text-xs font-mono text-slate-400"><span>EXP <?php echo $display_expiry; ?></span><span>CVV <?php echo isset($reveal_numbers) ? $display_cvv : '***'; ?></span></div>
                            </div>
                            <div class="flex justify-between items-end relative z-10">
                                <p class="font-medium tracking-widest text-sm uppercase text-slate-300"><?php echo $card['card_holder_name']; ?></p>
                                <div class="flex flex-col items-end"><i class="fa-brands fa-cc-<?php echo strtolower($card['card_type']); ?> text-4xl opacity-90"></i></div>
                            </div>
                        </div>

                        <div class="absolute inset-0 w-full h-full bg-[#0f172a] rounded-3xl shadow-2xl backface-hidden rotate-y-180 border border-slate-700/50 overflow-hidden z-10">
                            <div class="w-full h-12 bg-black mt-6"></div>
                            <div class="p-6 md:p-8">
                                <div class="flex justify-between items-center mb-4">
                                    <div class="flex-1 mr-4">
                                        <p class="text-[10px] text-slate-500 mb-1">Authorized Signature</p>
                                        <div class="h-10 bg-white w-full rounded-sm flex items-center justify-end px-3">
                                            <span class="font-mono text-slate-900 font-bold text-sm italic mr-2"><?php echo $display_cvv; ?></span>
                                        </div>
                                    </div>
                                    <div class="w-16 h-16 bg-white/5 rounded-lg flex items-center justify-center">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo $card['card_number']; ?>" class="w-12 h-12 opacity-80">
                                    </div>
                                </div>
                                <p class="text-[10px] text-slate-400 leading-relaxed text-justify">
                                    This card is issued by <?php echo $sitename; ?> pursuant to license by Visa/Mastercard International. Use of this card constitutes acceptance of the terms and conditions associated with this account. Electronic use only.
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="flex gap-4 max-w-md mx-auto lg:mx-0 w-full">
                    <button onclick="openSecurityModal('view')" class="flex-1 py-3.5 rounded-xl bg-slate-900 dark:bg-[#1E293B] text-white font-bold text-sm hover:bg-slate-800 dark:hover:bg-[#2A374C] transition-all flex items-center justify-center gap-2 border border-slate-700"><i class="fa-regular fa-eye"></i> View Numbers</button>
                    <button onclick="openSecurityModal('freeze')" class="flex-1 py-3.5 rounded-xl bg-slate-900 dark:bg-[#1E293B] text-white font-bold text-sm hover:bg-slate-800 dark:hover:bg-[#2A374C] transition-all flex items-center justify-center gap-2 border border-slate-700"><i class="fa-solid <?php echo ($card_status == 'frozen') ? 'fa-unlock' : 'fa-lock'; ?>"></i> <?php echo ($card_status == 'frozen') ? 'Unfreeze' : 'Freeze'; ?></button>
                </div>
            <?php elseif ($card_status == 'pending'): ?>
                <div class="h-56 md:h-64 w-full max-w-md mx-auto lg:mx-0 rounded-3xl bg-slate-100 dark:bg-white/5 border border-dashed border-slate-300 dark:border-white/10 flex flex-col items-center justify-center text-center p-6">
                    <div class="w-16 h-16 bg-yellow-500/10 rounded-full flex items-center justify-center mb-4"><i class="fa-solid fa-hourglass-half text-2xl text-yellow-500"></i></div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Application Under Review</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-xs">Your card request has been received. You will be notified once it is active.</p>
                </div>
            <?php else: ?>
                <div class="h-56 md:h-64 w-full max-w-md mx-auto lg:mx-0 rounded-3xl bg-slate-100 dark:bg-white/5 border border-dashed border-slate-300 dark:border-white/10 flex flex-col items-center justify-center text-center p-6">
                    <div class="w-16 h-16 bg-indigo-500/10 rounded-full flex items-center justify-center mb-4"><i class="fa-regular fa-credit-card text-2xl text-indigo-500"></i></div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">No Active Card</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Purchase a card to start spending.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="space-y-6">
            
            <div class="glass-panel rounded-3xl p-6 border border-slate-200 dark:border-white/5">
                <h3 class="font-bold text-slate-900 dark:text-white mb-2">Linked Cards</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-6">Manage your external cards for withdrawals.</p>
                
                <div class="space-y-3">
                    <?php while($row = mysqli_fetch_assoc($bank_query)): ?>
                        <div class="flex items-center justify-between p-4 rounded-xl bg-slate-100 dark:bg-[#0B0F19] border border-slate-200 dark:border-white/10 group hover:border-indigo-500 transition-all">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-slate-200 dark:bg-white/10 flex items-center justify-center font-bold text-slate-600 dark:text-slate-300">
                                    <i class="fa-regular fa-credit-card"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-sm text-slate-900 dark:text-white"><?php echo $row['account_name']; ?></p>
                                    <p class="text-xs text-slate-500">•••• <?php echo substr($row['account_number'], -4); ?></p>
                                </div>
                            </div>
                            <div class="w-2 h-2 rounded-full bg-green-500"></div>
                        </div>
                    <?php endwhile; ?>

                    <button onclick="openLinkBankModal()" class="w-full py-4 rounded-xl border border-dashed border-slate-300 dark:border-white/20 text-slate-500 dark:text-slate-400 font-bold text-sm hover:bg-slate-50 dark:hover:bg-white/5 hover:text-indigo-500 hover:border-indigo-500 transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-plus"></i> Link New Card
                    </button>
                </div>
            </div>

            <?php if($card_status == 'none' || $card_status == 'rejected'): ?>
            <div class="rounded-3xl p-6 relative overflow-hidden bg-gradient-to-r from-indigo-600 to-violet-700 text-white shadow-lg">
                <div class="absolute right-0 top-0 h-full w-1/3 bg-white/5 skew-x-12 transform translate-x-4"></div>
                <div class="relative z-10 flex justify-between items-end">
                    <div>
                        <h3 class="font-bold text-lg mb-1">Order Virtual Card</h3>
                        <p class="text-xs text-indigo-100 max-w-[200px] leading-relaxed mb-4">Get a new virtual Visa or Mastercard for safe online shopping.</p>
                        <div class="flex items-baseline gap-1"><span class="text-2xl font-bold">$<?php echo number_format($virtual_card_fee,2) ?></span><span class="text-xs text-indigo-200">/ card</span></div>
                    </div>
                    <button onclick="openPurchaseModal()" class="px-6 py-2.5 bg-white text-indigo-900 font-bold text-sm rounded-xl hover:bg-indigo-50 transition-colors shadow-lg">Purchase</button>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<div id="securityModal" class="modal-bg fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm hidden p-4">
    <div class="modal-content glass-panel w-full max-w-sm rounded-3xl p-6 border border-slate-200 dark:border-white/10 relative shadow-2xl">
        <button onclick="closeSecurityModal()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-500 hover:text-red-500 transition-all"><i class="fa-solid fa-xmark"></i></button>
        <div class="text-center mb-6 pt-2">
            <div class="w-16 h-16 rounded-full bg-indigo-500/10 flex items-center justify-center mx-auto mb-4 text-indigo-500"><i class="fa-solid fa-shield-halved text-3xl"></i></div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-1">Security Check</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Enter your password to verify ownership.</p>
        </div>
        <form method="POST">
            <div class="space-y-4">
                <input type="hidden" name="action_type" id="actionTypeInput">
                <input type="password" name="password" placeholder="Password" required class="w-full bg-slate-50 dark:bg-[#02040a] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-indigo-500 text-sm">
                <button type="submit" name="security_action" class="w-full py-3.5 rounded-xl bg-indigo-600 text-white font-bold text-sm shadow-lg hover:bg-indigo-500 transition-all">Confirm Access</button>
            </div>
        </form>
    </div>
</div>

<div id="linkBankModal" class="modal-bg fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm hidden p-4">
    <div class="modal-content glass-panel w-full max-w-md rounded-3xl p-6 md:p-8 border border-slate-200 dark:border-white/10 relative shadow-2xl">
        <button type="button" onclick="closeLinkBankModal()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-500 hover:text-red-500 transition-all"><i class="fa-solid fa-xmark"></i></button>
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-1">Add New Card</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Securely link your debit or credit card.</p>
        </div>
        <form method="POST">
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Cardholder Name</label>
                    <input type="text" name="card_holder" placeholder="e.g. Alex Rivera" required class="w-full bg-slate-50 dark:bg-[#02040a] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Card Number</label>
                    <div class="relative">
                        <input type="text" name="card_number" id="inputCardNum" placeholder="0000 0000 0000 0000" required maxlength="19" class="w-full bg-slate-50 dark:bg-[#02040a] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-3 pl-10 pr-4 focus:outline-none focus:border-indigo-500 text-sm font-mono" oninput="formatCardNumber(this)">
                        <i class="fa-regular fa-credit-card absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Expiry Date</label>
                        <input type="text" name="expiry" id="inputExpiry" placeholder="MM/YY" required maxlength="5" class="w-full bg-slate-50 dark:bg-[#02040a] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-indigo-500 text-sm text-center" oninput="formatExpiry(this)">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">CVV</label>
                        <input type="password" name="cvv" placeholder="123" required maxlength="4" class="w-full bg-slate-50 dark:bg-[#02040a] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-indigo-500 text-sm text-center">
                    </div>
                </div>
                <button type="submit" name="link_card_method" class="w-full py-4 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-black font-bold shadow-lg hover:opacity-90 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-link"></i> Link Card
                </button>
                <p class="text-center text-[10px] text-slate-400 mt-3 flex items-center justify-center gap-1"><i class="fa-solid fa-lock"></i> Encrypted & Secure</p>
            </div>
        </form>
    </div>
</div>

<div id="purchaseCardModal" class="modal-bg fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm hidden p-4">
    <div class="modal-content glass-panel w-full max-w-md rounded-3xl p-6 md:p-8 border border-slate-200 dark:border-white/10 relative shadow-2xl max-h-[90vh] overflow-y-auto custom-scroll">
        <button onclick="closePurchaseModal()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-500 hover:text-red-500 transition-all"><i class="fa-solid fa-xmark"></i></button>
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-1">Order Virtual Card</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Complete payment to issue your new card.</p>
        </div>
        <form method="POST">
            <div class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Name on Card</label>
                    <input type="text" name="card_name" value="<?php echo $fullname; ?>" class="w-full bg-slate-50 dark:bg-[#02040a] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-indigo-500 text-sm">
                </div>
                <div class="flex items-center gap-4">
                    <label class="flex-1 cursor-pointer"><input type="radio" name="card_type" value="Visa" class="hidden peer" checked><div class="py-3 border border-slate-200 dark:border-white/10 text-slate-500 dark:text-slate-400 rounded-xl font-bold text-sm flex items-center justify-center gap-2 peer-checked:bg-indigo-500/10 peer-checked:text-indigo-500 peer-checked:border-indigo-500 transition-all"><i class="fa-brands fa-cc-visa text-xl"></i> Visa</div></label>
                    <label class="flex-1 cursor-pointer"><input type="radio" name="card_type" value="Mastercard" class="hidden peer"><div class="py-3 border border-slate-200 dark:border-white/10 text-slate-500 dark:text-slate-400 rounded-xl font-bold text-sm flex items-center justify-center gap-2 peer-checked:bg-indigo-500/10 peer-checked:text-indigo-500 peer-checked:border-indigo-500 transition-all"><i class="fa-brands fa-cc-mastercard text-xl"></i> Mastercard</div></label>
                </div>
                <div class="h-px bg-slate-200 dark:bg-white/10 my-2"></div>
                <div class="text-center">
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Total Amount</p>
                    <p class="text-3xl font-extrabold text-slate-900 dark:text-white mb-6">$<?php echo number_format($virtual_card_fee,2) ?><span class="text-sm text-slate-500 font-medium">USDT</span></p>
                    <div class="bg-white p-3 rounded-2xl inline-block shadow-lg mb-4"><img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo $USDT_TRC20 ?>" alt="QR" class="w-32 h-32"></div>
                    <div class="relative mt-2 mb-6"><div class="flex items-center bg-slate-50 dark:bg-[#02040a] border border-slate-200 dark:border-white/10 rounded-xl p-3"><p id="walletAddr" class="text-xs text-slate-500 dark:text-slate-300 font-mono truncate mr-2"><?php echo $USDT_TRC20 ?></p><button type="button" onclick="copyAddr()" class="text-indigo-500 hover:text-indigo-400 font-bold text-xs">COPY</button></div></div>
                </div>
                <div class="border-t border-slate-200 dark:border-white/10 pt-6">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Submit Payment Proof</h3>
                    <div class="space-y-4">
                        <div><label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Transaction Hash (TXID)</label><input type="text" name="tx_hash" required placeholder="Paste TXID..." class="w-full bg-slate-50 dark:bg-[#02040a] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-indigo-500 font-mono text-sm"></div>
                    </div>
                </div>
                <button type="submit" name="purchase_card" class="w-full py-4 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-bold shadow-lg hover:opacity-90 transition-all">Verify & Complete Order</button>
            </div>
        </form>
    </div>
</div>

<script>
    // --- INPUT FORMATTING FUNCTIONS ---
    function formatCardNumber(input) {
        let value = input.value.replace(/\D/g, ''); // Remove non-digits
        value = value.substring(0, 16); // Limit to 16 digits
        // Add space every 4 digits
        let formatted = value.match(/.{1,4}/g)?.join(' ') || value;
        input.value = formatted;
    }

    function formatExpiry(input) {
        let value = input.value.replace(/\D/g, ''); // Remove non-digits
        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        input.value = value;
    }

    // Security Modal Logic
    function openSecurityModal(action) {
        document.getElementById('actionTypeInput').value = action;
        document.getElementById('securityModal').classList.remove('hidden');
    }
    function closeSecurityModal() {
        document.getElementById('securityModal').classList.add('hidden');
    }

    // Link Bank Modal Logic
    function openLinkBankModal() {
        document.getElementById('linkBankModal').classList.remove('hidden');
    }
    function closeLinkBankModal() {
        document.getElementById('linkBankModal').classList.add('hidden');
    }

    // Purchase Modal Logic
    function openPurchaseModal() {
        document.getElementById('purchaseCardModal').classList.remove('hidden');
    }
    function closePurchaseModal() {
        document.getElementById('purchaseCardModal').classList.add('hidden');
    }

    // Close Modals on Click Outside
    window.onclick = function(event) {
        if (event.target.classList.contains('modal-bg')) {
            event.target.classList.add('hidden');
        }
    }

    // Copy Address Logic
    function copyAddr() {
        const addr = document.getElementById('walletAddr').innerText;
        navigator.clipboard.writeText(addr);
        Swal.fire({icon: 'success', title: 'Copied', toast: true, position: 'top-end', showConfirmButton: false, timer: 1500});
    }

    <?php echo $alert; ?>
</script>

<?php include 'footer.php'; ?>