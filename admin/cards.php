<?php
include 'header.php';

$alert = "";

// --- HANDLE 1: APPROVE & GENERATE CARD ---
if (isset($_POST['approve_card'])) {
    $card_id = intval($_POST['card_id']);
    
    // Fetch User Details for Email
    $q = mysqli_query($link, "SELECT c.*, u.email, u.username FROM virtual_cards c JOIN users u ON c.user_id = u.id WHERE c.id='$card_id'");
    $card = mysqli_fetch_assoc($q);
    
    // Generate Virtual Card Details
    // 1. Generate 16-digit Card Number (Starting with 4 for Visa or 5 for Mastercard)
    $prefix = ($card['card_type'] == 'Mastercard') ? '5' : '4';
    $card_number = $prefix . rand(100, 999) . ' ' . rand(1000, 9999) . ' ' . rand(1000, 9999) . ' ' . rand(1000, 9999);
    
    // 2. Generate CVV
    $cvv = rand(100, 999);
    
    // 3. Generate Expiry (3 Years from now)
    $expiry = date("m/y", strtotime("+3 years"));
    
    // Update Database
    $sql = "UPDATE virtual_cards SET 
            card_number='$card_number', 
            cvv='$cvv', 
            expiry='$expiry', 
            status='active' 
            WHERE id='$card_id'";
            
    if (mysqli_query($link, $sql)) {
       $subject = "Virtual Card Activated!";

        $body = '
        <div style="font-family: Helvetica, Arial, sans-serif; background-color: #f9fafb; padding: 50px 0;">
            <div style="max-width: 500px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); overflow: hidden;">
                
                <div style="padding: 40px 0 20px; text-align: center;">
                    <div style="background-color: #ecfdf5; width: 64px; height: 64px; border-radius: 50%; display: inline-block; line-height: 64px; font-size: 32px; margin-bottom: 20px;">
                        ✅
                    </div>
                    <h1 style="color: #111827; margin: 0; font-size: 24px; font-weight: bold;">Card Ready for Use</h1>
                </div>
        
                <div style="padding: 20px 40px 40px;">
                    <p style="color: #4b5563; font-size: 16px; text-align: center; margin-bottom: 30px;">
                        Hello <strong>' . $card['username'] . '</strong>, good news! Your request for a <strong>' . $card['card_type'] . '</strong> virtual card has been approved.
                    </p>
        
                    <div style="background-color: #f3f4f6; border-radius: 8px; padding: 20px;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="padding-bottom: 10px; color: #6b7280; font-size: 14px;">Card Number</td>
                                <td style="padding-bottom: 10px; text-align: right; color: #111827; font-weight: bold; font-size: 14px; font-family: monospace; letter-spacing: 1px;">' . $card_number . '</td>
                            </tr>
                            <tr>
                                <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; padding-bottom: 10px; color: #6b7280; font-size: 14px;">Expiry</td>
                                <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; padding-bottom: 10px; text-align: right; color: #111827; font-weight: bold; font-size: 14px;">' . $expiry . '</td>
                            </tr>
                            <tr>
                                <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; padding-bottom: 10px; color: #6b7280; font-size: 14px;">CVV</td>
                                <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; padding-bottom: 10px; text-align: right; color: #6b7280; font-size: 14px;">*** (Login to view)</td>
                            </tr>
                            <tr>
                                <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px;">Status</td>
                                <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; text-align: right;">
                                     <span style="background-color: #ecfdf5; color: #059669; font-size: 12px; font-weight: bold; padding: 4px 10px; border-radius: 20px; border: 1px solid #a7f3d0;">Active</span>
                                </td>
                            </tr>
                        </table>
                    </div>
        
                    <div style="margin-top: 30px; text-align: center;">
                        <p style="color: #9ca3af; font-size: 12px; margin: 0;">
                            You can now use this card for online payments.
                        </p>
                    </div>
                </div>
            </div>
        </div>';
        
        sendMail($card['email'], $subject, $body);
        $alert = "Swal.fire({icon: 'success', title: 'Card Generated', text: 'Card is now active and details sent to user.'});";
    } else {
        $alert = "Swal.fire({icon: 'error', title: 'Error', text: 'Failed to generate card details.'});";
    }
}

// --- HANDLE 2: REJECT / FREEZE ---
if (isset($_POST['update_status'])) {
    $card_id = intval($_POST['card_id']);
    $status = clean($_POST['status']); // 'frozen' or 'rejected'
    
    if(mysqli_query($link, "UPDATE virtual_cards SET status='$status' WHERE id='$card_id'")){
        $alert = "Swal.fire({icon: 'success', title: 'Updated', text: 'Card status changed to $status.'});";
    }
}

// --- HANDLE 3: DELETE ---
if (isset($_POST['delete_card'])) {
    $id = intval($_POST['card_id']);
    mysqli_query($link, "DELETE FROM virtual_cards WHERE id='$id'");
    $alert = "Swal.fire({icon: 'success', title: 'Deleted', text: 'Card record removed.'});";
}

// Fetch Cards
$result = mysqli_query($link, "SELECT c.*, u.username, u.email FROM virtual_cards c JOIN users u ON c.user_id = u.id ORDER BY c.created_at DESC");
?>

<div class="flex-1 overflow-y-auto p-6 space-y-6">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Virtual Cards</h1>
            <p class="text-sm text-slate-500">Approve requests and generate card details.</p>
        </div>
    </div>

    <div class="glass-panel rounded-2xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="text-xs text-slate-400 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="py-3 pl-6 font-semibold">User</th>
                        <th class="py-3 font-semibold">Card Type</th>
                        <th class="py-3 font-semibold">Card Details</th>
                        <th class="py-3 font-semibold">Payment Proof</th>
                        <th class="py-3 font-semibold text-center">Status</th>
                        <th class="py-3 pr-6 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): 
                            $status_badge = match($row['status']) {
                                'active' => 'bg-green-100 text-green-700',
                                'pending' => 'bg-orange-100 text-orange-700 animate-pulse',
                                'frozen' => 'bg-blue-100 text-blue-700',
                                'rejected' => 'bg-red-100 text-red-700',
                                default => 'bg-slate-100 text-slate-600'
                            };
                        ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-4 pl-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-500 border border-slate-200">
                                        <?php echo strtoupper(substr($row['username'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800"><?php echo htmlspecialchars($row['username']); ?></p>
                                        <p class="text-xs text-slate-500"><?php echo htmlspecialchars($row['email']); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 font-bold text-slate-700">
                                <?php echo $row['card_type']; ?>
                            </td>
                            <td class="py-4">
                                <?php if($row['status'] == 'active'): ?>
                                    <p class="font-mono font-bold text-slate-800"><?php echo $row['card_number']; ?></p>
                                    <div class="flex gap-3 text-xs text-slate-500 mt-1">
                                        <span>Exp: <?php echo $row['expiry']; ?></span>
                                        <span>CVV: <?php echo $row['cvv']; ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="text-xs text-slate-400 italic">Not Generated</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4">
                                <?php if(!empty($row['proof_image'])): ?>
                                    <a href="../uploads/payments/<?php echo $row['proof_image']; ?>" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold flex items-center gap-1">
                                        <i class="fa-regular fa-image"></i> View Proof
                                    </a>
                                <?php endif; ?>
                                <p class="text-[10px] text-slate-400 mt-1 font-mono truncate w-24" title="<?php echo $row['tx_hash']; ?>">
                                    TX: <?php echo $row['tx_hash']; ?>
                                </p>
                            </td>
                            <td class="py-4 text-center">
                                <span class="px-2.5 py-1 rounded-md text-xs font-bold <?php echo $status_badge; ?> capitalize">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td class="py-4 pr-6 text-right">
                                <div class="flex justify-end gap-2">
                                    <?php if($row['status'] == 'pending'): ?>
                                        <form method="POST" onsubmit="return confirm('Approve and Generate Card Details?');">
                                            <input type="hidden" name="card_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" name="approve_card" class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-1.5 px-3 rounded shadow transition-all flex items-center gap-1">
                                                <i class="fa-solid fa-wand-magic-sparkles"></i> Generate
                                            </button>
                                        </form>
                                        
                                        <form method="POST" onsubmit="return confirm('Reject this request?');">
                                            <input type="hidden" name="card_id" value="<?php echo $row['id']; ?>">
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" name="update_status" class="bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold py-1.5 px-3 rounded border border-red-200 transition-all">
                                                Reject
                                            </button>
                                        </form>
                                    <?php elseif($row['status'] == 'active'): ?>
                                        <form method="POST" onsubmit="return confirm('Freeze this card?');">
                                            <input type="hidden" name="card_id" value="<?php echo $row['id']; ?>">
                                            <input type="hidden" name="status" value="frozen">
                                            <button type="submit" name="update_status" class="bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-bold py-1.5 px-3 rounded border border-blue-200 transition-all">
                                                <i class="fa-regular fa-snowflake"></i> Freeze
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <form method="POST" onsubmit="return confirm('Delete this record permanently?');">
                                        <input type="hidden" name="card_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="delete_card" class="text-slate-400 hover:text-red-500 p-1.5 transition-colors">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="py-12 text-center text-slate-400 italic">No card requests found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    <?php echo $alert; ?>
</script>

<?php include 'footer.php'; ?>