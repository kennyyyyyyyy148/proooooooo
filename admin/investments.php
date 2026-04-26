<?php
include 'header.php';

$alert = "";

// --- HANDLE 1: GIVE PROFIT & END INVESTMENT ---
if (isset($_POST['give_profit'])) {
    $inv_id = intval($_POST['inv_id']);
    
    // Fetch Investment & User Details
    $q = mysqli_query($link, "SELECT i.*, u.username, u.email FROM investments i JOIN users u ON i.user_id = u.id WHERE i.id='$inv_id' AND i.status='active'");
    
    if (mysqli_num_rows($q) > 0) {
        $inv = mysqli_fetch_assoc($q);
        $user_id = $inv['user_id'];
        $user_email = $inv['email'];
        $amount = floatval($inv['amount']);
        $roi = floatval($inv['roi_percent']);
        $coin = strtoupper($inv['pay_method']); 
        
        // 1. Calculate Profit
        $profit = $amount * ($roi / 100);
        
        // 2. Determine Specific User Balance Column
        $col = "";
        if ($coin == 'BTC') { $col = "btc_balance"; }
        elseif ($coin == 'ETH') { $col = "eth_balance"; }
        elseif ($coin == 'SOL') { $col = "sol_balance"; }
        elseif ($coin == 'BNB') { $col = "bnb_balance"; }
        elseif ($coin == 'TRX') { $col = "trx_balance"; }
        elseif ($coin == 'LTC') { $col = "ltc_balance"; }
        elseif ($coin == 'DOGE') { $col = "doge_balance"; }
        elseif ($coin == 'MATIC') { $col = "matic_balance"; }
        elseif (strpos($coin, 'USDT') !== false) { 
            if (strpos($coin, 'ERC20') !== false) { $col = "usdt_erc20_balance"; }
            else { $col = "usdt_trc20_balance"; }
        } else {
            $col = "usdt_trc20_balance"; 
        }
        
        if ($col != "") {
            // 3. Update User Balance (Add Profit)
            $update_balance = mysqli_query($link, "UPDATE users SET $col = $col + $profit WHERE id='$user_id'");
            
            // 4. Mark Investment as COMPLETED (End it)
            $update_status = mysqli_query($link, "UPDATE investments SET status='completed', end_date=NOW() WHERE id='$inv_id'");

            if ($update_balance && $update_status) {
               // 5. Send Email
            $subject = "Profit Received & Plan Completed";
            
            $body = '
            <div style="font-family: Helvetica, Arial, sans-serif; background-color: #f9fafb; padding: 50px 0;">
                <div style="max-width: 500px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); overflow: hidden;">
                    
                    <div style="padding: 40px 0 20px; text-align: center;">
                        <div style="background-color: #ecfdf5; width: 64px; height: 64px; border-radius: 50%; display: inline-block; line-height: 64px; font-size: 32px; margin-bottom: 20px;">
                            💰
                        </div>
                        <h1 style="color: #111827; margin: 0; font-size: 24px; font-weight: bold;">Investment Completed</h1>
                    </div>
            
                    <div style="padding: 20px 40px 40px;">
                        <p style="color: #4b5563; font-size: 16px; text-align: center; margin-bottom: 30px;">
                            Hello <strong>' . $inv['username'] . '</strong>, congratulations! Your investment plan <strong>' . $inv['plan_name'] . '</strong> has been completed successfully.
                        </p>
            
                        <div style="background-color: #f3f4f6; border-radius: 8px; padding: 20px;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding-bottom: 10px; color: #6b7280; font-size: 14px;">Total Profit Sent</td>
                                    <td style="padding-bottom: 10px; text-align: right; color: #059669; font-weight: bold; font-size: 14px;">$' . $profit . '</td>
                                </tr>
                                <tr>
                                    <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; padding-bottom: 10px; color: #6b7280; font-size: 14px;">Wallet Credited</td>
                                    <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; padding-bottom: 10px; text-align: right; color: #111827; font-weight: bold; font-size: 14px;">' . $coin . '</td>
                                </tr>
                                <tr>
                                    <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px;">Status</td>
                                    <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; text-align: right;">
                                         <span style="background-color: #ecfdf5; color: #059669; font-size: 12px; font-weight: bold; padding: 4px 10px; border-radius: 20px; border: 1px solid #a7f3d0;">Completed</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
            
                        <div style="margin-top: 30px; text-align: center;">
                            <p style="color: #9ca3af; font-size: 12px; margin: 0;">
                                The funds are now available in your main balance.
                            </p>
                        </div>
                    </div>
                </div>
            </div>';
            
            sendMail($user_email, $subject, $body);
                $alert = "Swal.fire({icon: 'success', title: 'Success', text: 'Profit sent ($$profit) and investment closed.'});";
            } else {
                $alert = "Swal.fire({icon: 'error', title: 'Error', text: 'Database update failed.'});";
            }
        } else {
            $alert = "Swal.fire({icon: 'error', title: 'Config Error', text: 'Unknown coin wallet for: $coin'});";
        }
    }
}

// --- HANDLE 2: DELETE ---
if (isset($_POST['delete_inv'])) {
    $id = intval($_POST['inv_id']);
    mysqli_query($link, "DELETE FROM investments WHERE id='$id'");
    $alert = "Swal.fire({icon: 'success', title: 'Deleted', text: 'Record removed.', timer: 1500});";
}

// Fetch Investments
$query = "SELECT i.*, u.username, u.email FROM investments i JOIN users u ON i.user_id = u.id ORDER BY i.start_date DESC";
$result = mysqli_query($link, $query);
?>

<div class="flex-1 overflow-y-auto p-6 space-y-6">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">User Investments</h1>
            <p class="text-sm text-slate-500">Manage active portfolios.</p>
        </div>
    </div>

    <div class="glass-panel rounded-2xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="text-xs text-slate-400 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="py-3 pl-6 font-semibold">User</th>
                        <th class="py-3 font-semibold">Plan Details</th>
                        <th class="py-3 font-semibold">Amount / ROI</th>
                        <th class="py-3 font-semibold text-center">Status</th>
                        <th class="py-3 font-semibold">Started</th>
                        <th class="py-3 pr-6 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): 
                            $status_badge = match($row['status']) {
                                'active' => 'bg-green-100 text-green-700',
                                'completed' => 'bg-blue-100 text-blue-700',
                                'cancelled' => 'bg-red-100 text-red-700',
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
                            <td class="py-4">
                                <p class="font-bold text-slate-700"><?php echo $row['plan_name']; ?></p>
                                <span class="text-xs bg-slate-100 text-slate-500 px-2 py-0.5 rounded font-mono">
                                    <?php echo strtoupper($row['pay_method']); ?>
                                </span>
                            </td>
                            <td class="py-4">
                                <p class="font-bold text-slate-800">$<?php echo number_format($row['amount'], 2); ?></p>
                                <p class="text-xs text-green-600 font-bold"><?php echo $row['roi_percent']; ?>% ROI</p>
                            </td>
                            <td class="py-4 text-center">
                                <span class="px-2.5 py-1 rounded-md text-xs font-bold <?php echo $status_badge; ?> capitalize">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td class="py-4 text-xs text-slate-500">
                                <?php echo date("M d, Y", strtotime($row['start_date'])); ?>
                            </td>
                            <td class="py-4 pr-6 text-right">
                                <div class="flex justify-end gap-2">
                                    <?php if($row['status'] == 'active'): ?>
                                        
                                        <form method="POST" onsubmit="return confirm('Send Profit AND Complete this investment? This action is final.');">
                                            <input type="hidden" name="inv_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" name="give_profit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-1.5 px-3 rounded shadow transition-all flex items-center gap-1" title="Pay & Close">
                                                <i class="fa-solid fa-check-double"></i> Pay & End
                                            </button>
                                        </form>

                                    <?php else: ?>
                                        <span class="text-xs text-slate-400 font-bold py-1.5 px-3 border border-slate-200 rounded cursor-not-allowed">Completed</span>
                                    <?php endif; ?>

                                    <form method="POST" onsubmit="return confirm('Delete this record permanently?');">
                                        <input type="hidden" name="inv_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="delete_inv" class="text-red-400 hover:text-red-600 p-1.5 transition-colors" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="py-12 text-center text-slate-400 italic">No investments found.</td></tr>
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