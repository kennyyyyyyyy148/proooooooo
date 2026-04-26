<?php
include 'header.php';

$alert = "";

// --- HANDLE APPROVAL (Mark Complete & Add Hash) ---
if (isset($_POST['confirm_approval'])) {
    $tx_id = intval($_POST['tx_id']);
    $real_tx_hash = clean($_POST['real_tx_hash']);

    // Fetch details for email
    $query = mysqli_query($link, "SELECT t.*, u.username, u.email FROM transactions t JOIN users u ON t.user_id = u.id WHERE t.id='$tx_id'");
    $tx = mysqli_fetch_assoc($query);

    if ($tx && $tx['status'] == 'pending') {
        // Update Status and Hash
        $update = mysqli_query($link, "UPDATE transactions SET status='completed', tx_hash='$real_tx_hash' WHERE id='$tx_id'");
        
        if ($update) {
           // Email User
$subject = "Withdrawal Sent - " . $sitename;

$body = '
<div style="font-family: Helvetica, Arial, sans-serif; background-color: #f9fafb; padding: 50px 0;">
    <div style="max-width: 500px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); overflow: hidden;">
        
        <div style="padding: 40px 0 20px; text-align: center;">
            <div style="background-color: #ecfdf5; width: 64px; height: 64px; border-radius: 50%; display: inline-block; line-height: 64px; font-size: 32px; margin-bottom: 20px;">
                ✅
            </div>
            <h1 style="color: #111827; margin: 0; font-size: 24px; font-weight: bold;">Withdrawal Successful</h1>
        </div>

        <div style="padding: 20px 40px 40px;">
            <p style="color: #4b5563; font-size: 16px; text-align: center; margin-bottom: 30px;">
                Hello <strong>' . $tx['username'] . '</strong>, your withdrawal request has been processed and funds have been sent to your wallet.
            </p>

            <div style="background-color: #f3f4f6; border-radius: 8px; padding: 20px;">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="padding-bottom: 10px; color: #6b7280; font-size: 14px;">Amount Sent</td>
                        <td style="padding-bottom: 10px; text-align: right; color: #111827; font-weight: bold; font-size: 14px;">$' . number_format($tx['amount_usd'], 2) . '</td>
                    </tr>
                    <tr>
                        <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; padding-bottom: 10px; color: #6b7280; font-size: 14px;">Transaction ID</td>
                        <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; padding-bottom: 10px; text-align: right; color: #111827; font-size: 12px; font-family: monospace; word-break: break-all;">' . $real_tx_hash . '</td>
                    </tr>
                    <tr>
                        <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px;">Status</td>
                        <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; text-align: right;">
                             <span style="background-color: #ecfdf5; color: #059669; font-size: 12px; font-weight: bold; padding: 4px 10px; border-radius: 20px; border: 1px solid #a7f3d0;">Sent</span>
                        </td>
                    </tr>
                </table>
            </div>

            <div style="margin-top: 30px; text-align: center;">
                <p style="color: #9ca3af; font-size: 12px; margin: 0;">
                    Thank you for choosing ' . $sitename . '.
                </p>
            </div>
        </div>
    </div>
</div>';

sendMail($tx['email'], $subject, $body);
            $alert = "Swal.fire({icon: 'success', title: 'Processed', text: 'Withdrawal marked as complete.'});";
        }
    }
}

// --- HANDLE REJECTION (Refund User) ---
if (isset($_POST['reject_withdrawal'])) {
    $tx_id = intval($_POST['tx_id']);

    // 1. Fetch Transaction to get amount & symbol
    $query = mysqli_query($link, "SELECT * FROM transactions WHERE id='$tx_id' AND status='pending'");
    
    if (mysqli_num_rows($query) > 0) {
        $tx = mysqli_fetch_assoc($query);
        $user_id = $tx['user_id'];
        $amount_usd = floatval($tx['amount_usd']);
        $symbol = $tx['coin_symbol'];
        $network = $tx['network'];

        // 2. Identify Wallet to Refund
        $balance_column = "";
        if ($symbol == 'BTC') { $balance_column = "btc_balance"; }
        elseif ($symbol == 'ETH') { $balance_column = "eth_balance"; }
        elseif ($symbol == 'SOL') { $balance_column = "sol_balance"; }
        elseif ($symbol == 'TRX') { $balance_column = "trx_balance"; }
        elseif ($symbol == 'BNB') { $balance_column = "bnb_balance"; }
        elseif ($symbol == 'LTC') { $balance_column = "ltc_balance"; }
        elseif ($symbol == 'DOGE') { $balance_column = "doge_balance"; }
        elseif ($symbol == 'MATIC') { $balance_column = "matic_balance"; }
        elseif ($symbol == 'USDT') {
            if ($network == 'TRC20') { $balance_column = "usdt_trc20_balance"; }
            elseif ($network == 'ERC20') { $balance_column = "usdt_erc20_balance"; }
        }

        // 3. Process Refund
        if ($balance_column != "") {
            // Add money back
            mysqli_query($link, "UPDATE users SET $balance_column = $balance_column + $amount_usd WHERE id='$user_id'");
            // Mark as Rejected
            mysqli_query($link, "UPDATE transactions SET status='rejected' WHERE id='$tx_id'");
            
            // Notify User
            $user_q = mysqli_query($link, "SELECT email FROM users WHERE id='$user_id'");
            $user_email = mysqli_fetch_assoc($user_q)['email'];
            sendMail($user_email, "Withdrawal Refunded", "<p>Your withdrawal of $$amount_usd was rejected and funds have been returned to your balance.</p>");

            $alert = "Swal.fire({icon: 'success', title: 'Refunded', text: 'User has been refunded.'});";
        } else {
            $alert = "Swal.fire({icon: 'error', title: 'Error', text: 'Could not find user wallet to refund.'});";
        }
    }
}

// --- HANDLE DELETE ---
if (isset($_POST['delete_tx'])) {
    $tx_id = intval($_POST['tx_id']);
    mysqli_query($link, "DELETE FROM transactions WHERE id='$tx_id'");
    $alert = "Swal.fire({icon: 'success', title: 'Deleted', text: 'Record removed permanently.', timer: 1500});";
}

// --- FETCH DATA ---
$sql = "SELECT t.*, u.username, u.email 
        FROM transactions t 
        JOIN users u ON t.user_id = u.id 
        WHERE t.type = 'withdrawal' 
        ORDER BY t.created_at DESC";
$result = mysqli_query($link, $sql);
?>

<div class="flex-1 overflow-y-auto p-6 space-y-6">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Manage Withdrawals</h1>
            <p class="text-sm text-slate-500">Process outgoing payments and refunds.</p>
        </div>
    </div>

    <div class="glass-panel rounded-2xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="text-xs text-slate-400 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="py-3 pl-6 font-semibold">User Info</th>
                        <th class="py-3 font-semibold">Asset Details</th>
                        <th class="py-3 font-semibold">Amount</th>
                        <th class="py-3 font-semibold">Status</th>
                        <th class="py-3 font-semibold">Current Hash</th>
                        <th class="py-3 pr-6 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): 
                            $status_badge = match($row['status']) {
                                'completed' => 'bg-green-100 text-green-700',
                                'pending' => 'bg-orange-100 text-orange-700',
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
                            <td class="py-4 font-medium text-slate-700">
                                <?php echo $row['coin_symbol']; ?> 
                                <span class="text-xs text-slate-400 bg-slate-100 px-1 rounded ml-1"><?php echo $row['network']; ?></span>
                            </td>
                            <td class="py-4 font-mono font-bold text-slate-800">
                                $<?php echo number_format($row['amount_usd'], 2); ?>
                            </td>
                            <td class="py-4">
                                <span class="px-2.5 py-1 rounded-md text-xs font-bold <?php echo $status_badge; ?> capitalize">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td class="py-4 text-xs font-mono text-slate-500 max-w-[150px] truncate" title="<?php echo $row['tx_hash']; ?>">
                                <?php echo $row['tx_hash']; ?>
                            </td>
                            <td class="py-4 pr-6 text-right">
                                <div class="flex justify-end gap-2">
                                    <?php if($row['status'] == 'pending'): ?>
                                        <button onclick="openApproveModal(<?php echo $row['id']; ?>)" class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-1.5 px-3 rounded shadow transition-all">
                                            <i class="fa-solid fa-paper-plane"></i> Send
                                        </button>
                                        
                                    <?php else: ?>
                                        <span class="text-xs text-slate-400 font-bold py-1.5 px-3 border border-slate-200 rounded cursor-not-allowed">
                                            Closed
                                        </span>
                                    <?php endif; ?>

                                    <form method="POST" onsubmit="return confirm('Delete this record?');">
                                        <input type="hidden" name="tx_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="delete_tx" class="text-red-400 hover:text-red-600 p-1.5 transition-colors" title="Delete History">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="py-12 text-center text-slate-400 italic">No withdrawal requests found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="approveModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl p-6 shadow-2xl relative">
        <button onclick="closeApproveModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-xl"></i></button>
        
        <h3 class="text-lg font-bold text-slate-800 mb-1">Confirm Withdrawal</h3>
        <p class="text-sm text-slate-500 mb-4">Enter the blockchain Transaction Hash (TXID) to prove payment.</p>
        
        <form method="POST">
            <input type="hidden" name="tx_id" id="modalTxId">
            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Transaction Hash</label>
                <input type="text" name="real_tx_hash" required placeholder="0x..." class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 text-sm font-mono focus:outline-none focus:border-indigo-500">
            </div>
            <button type="submit" name="confirm_approval" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl transition-all">
                Confirm & Send Email
            </button>
        </form>
    </div>
</div>

<script>
    function openApproveModal(id) {
        document.getElementById('modalTxId').value = id;
        document.getElementById('approveModal').classList.remove('hidden');
    }
    
    function closeApproveModal() {
        document.getElementById('approveModal').classList.add('hidden');
    }

    // Close on outside click
    document.getElementById('approveModal').addEventListener('click', function(e) {
        if (e.target === this) closeApproveModal();
    });

    <?php echo $alert; ?>
</script>

<?php include 'footer.php'; ?>