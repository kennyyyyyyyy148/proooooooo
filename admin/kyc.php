<?php
include 'header.php';

$alert = "";

// --- HANDLE 1: APPROVE KYC ---
if (isset($_POST['approve_kyc'])) {
    $user_id = intval($_POST['user_id']);
    
    // Fetch User Email for Notification
    $q = mysqli_query($link, "SELECT email, username FROM users WHERE id='$user_id'");
    $user = mysqli_fetch_assoc($q);
    
    // Update Status to 'approved'
    $sql = "UPDATE users SET kyc_status='approved' WHERE id='$user_id'";
    
    if (mysqli_query($link, $sql)) {
        // Send Email
       $subject = "KYC Verification Approved!";

$body = '
<div style="font-family: Helvetica, Arial, sans-serif; background-color: #f9fafb; padding: 50px 0;">
    <div style="max-width: 500px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); overflow: hidden;">
        
        <div style="padding: 40px 0 20px; text-align: center;">
            <div style="background-color: #ecfdf5; width: 64px; height: 64px; border-radius: 50%; display: inline-block; line-height: 64px; font-size: 32px; margin-bottom: 20px;">
                🪪
            </div>
            <h1 style="color: #111827; margin: 0; font-size: 24px; font-weight: bold;">Identity Verified</h1>
        </div>

        <div style="padding: 20px 40px 40px;">
            <p style="color: #4b5563; font-size: 16px; text-align: center; margin-bottom: 30px;">
                Hello <strong>' . $user['username'] . '</strong>, great news! Your identity documents have been reviewed and approved.
            </p>

            <div style="background-color: #f3f4f6; border-radius: 8px; padding: 20px; text-align: center; margin-bottom: 30px;">
                <p style="color: #6b7280; font-size: 14px; margin: 0 0 10px 0;">Account Status</p>
                <span style="background-color: #ecfdf5; color: #059669; font-size: 14px; font-weight: bold; padding: 6px 15px; border-radius: 20px; border: 1px solid #a7f3d0;">✅ Verified</span>
                <p style="color: #059669; font-size: 13px; margin: 15px 0 0 0;">
                    All account limits have been lifted.
                </p>
            </div>

            
        </div>
    </div>
</div>';

sendMail($user['email'], $subject, $body);
        $alert = "Swal.fire({icon: 'success', title: 'Verified', text: 'User KYC has been approved.'});";
    } else {
        $alert = "Swal.fire({icon: 'error', title: 'Error', text: 'Database update failed.'});";
    }
}

// --- HANDLE 2: REJECT KYC ---
if (isset($_POST['reject_kyc'])) {
    $user_id = intval($_POST['user_id']);
    
    // Fetch User Email
    $q = mysqli_query($link, "SELECT email, username FROM users WHERE id='$user_id'");
    $user = mysqli_fetch_assoc($q);
    
    // Update Status to 'rejected'
    $sql = "UPDATE users SET kyc_status='rejected' WHERE id='$user_id'";
    
    if (mysqli_query($link, $sql)) {
        // Send Email
        $subject = "KYC Verification Failed";
        $body = "
        <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
            <h2 style='color: #EF4444;'>Verification Rejected</h2>
            <p>Hello <strong>{$user['username']}</strong>,</p>
            <p>Unfortunately, we could not verify your identity based on the documents provided.</p>
            <p>Please ensure your documents are clear, valid, and readable, then try submitting again.</p>
        </div>";
        
        sendMail($user['email'], $subject, $body);
        $alert = "Swal.fire({icon: 'success', title: 'Rejected', text: 'User KYC marked as rejected.'});";
    }
}

// --- FETCH PENDING REQUESTS ---
// We look for users where kyc_status is 'pending'
$query = "SELECT * FROM users WHERE kyc_status='pending' ORDER BY created_at DESC";
$result = mysqli_query($link, $query);
?>

<div class="flex-1 overflow-y-auto p-6 space-y-6">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">KYC Requests</h1>
            <p class="text-sm text-slate-500">Review identity documents submitted by users.</p>
        </div>
    </div>

    <div class="glass-panel rounded-2xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="text-xs text-slate-400 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="py-3 pl-6 font-semibold">User Details</th>
                        <th class="py-3 font-semibold">Document Front</th>
                        <th class="py-3 font-semibold">Document Back</th>
                        <th class="py-3 font-semibold text-center">Submitted</th>
                        <th class="py-3 pr-6 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-4 pl-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-sm font-bold text-slate-500 border border-slate-200">
                                        <?php echo strtoupper(substr($row['username'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800"><?php echo htmlspecialchars($row['username']); ?></p>
                                        <p class="text-xs text-slate-500"><?php echo htmlspecialchars($row['email']); ?></p>
                                        <p class="text-[10px] text-slate-400 mt-0.5"><i class="fa-solid fa-phone"></i> <?php echo $row['phone']; ?></p>
                                    </div>
                                </div>
                            </td>

                            <td class="py-4">
                                <?php if(!empty($row['kyc_front'])): ?>
                                    <a href="../uploads/kyc/<?php echo $row['kyc_front']; ?>" target="_blank" class="group relative block w-24 h-16 rounded-lg overflow-hidden border border-slate-200 hover:border-indigo-500 transition-all">
                                        <img src="../uploads/kyc/<?php echo $row['kyc_front']; ?>" alt="Front ID" class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                                        <div class="absolute inset-0 bg-black/30 group-hover:bg-black/0 transition-colors flex items-center justify-center">
                                            <i class="fa-solid fa-eye text-white text-xs opacity-0 group-hover:opacity-100 transition-opacity drop-shadow-md"></i>
                                        </div>
                                    </a>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold mt-1 block">Front Side</span>
                                <?php else: ?>
                                    <span class="text-xs text-slate-400 italic">No file</span>
                                <?php endif; ?>
                            </td>

                            <td class="py-4">
                                <?php if(!empty($row['kyc_back'])): ?>
                                    <a href="../uploads/kyc/<?php echo $row['kyc_back']; ?>" target="_blank" class="group relative block w-24 h-16 rounded-lg overflow-hidden border border-slate-200 hover:border-indigo-500 transition-all">
                                        <img src="../uploads/kyc/<?php echo $row['kyc_back']; ?>" alt="Back ID" class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                                        <div class="absolute inset-0 bg-black/30 group-hover:bg-black/0 transition-colors flex items-center justify-center">
                                            <i class="fa-solid fa-eye text-white text-xs opacity-0 group-hover:opacity-100 transition-opacity drop-shadow-md"></i>
                                        </div>
                                    </a>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold mt-1 block">Back Side</span>
                                <?php else: ?>
                                    <span class="text-xs text-slate-400 italic">No file</span>
                                <?php endif; ?>
                            </td>

                            <td class="py-4 text-center">
                                <span class="bg-orange-50 text-orange-600 px-2 py-1 rounded text-xs font-bold border border-orange-100 animate-pulse">
                                    Pending Review
                                </span>
                            </td>

                            <td class="py-4 pr-6 text-right">
                                <div class="flex justify-end gap-2">
                                    <form method="POST" onsubmit="return confirm('Approve this user\'s KYC?');">
                                        <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="approve_kyc" class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-1.5 px-3 rounded shadow transition-all flex items-center gap-1">
                                            <i class="fa-solid fa-check"></i> Approve
                                        </button>
                                    </form>

                                    <form method="POST" onsubmit="return confirm('Reject this document?');">
                                        <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="reject_kyc" class="bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold py-1.5 px-3 rounded border border-red-200 transition-all flex items-center gap-1">
                                            <i class="fa-solid fa-xmark"></i> Reject
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 italic">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300">
                                    <i class="fa-solid fa-check-double text-2xl"></i>
                                </div>
                                <p>All caught up!</p>
                                <p class="text-xs mt-1">No pending KYC requests found.</p>
                            </td>
                        </tr>
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