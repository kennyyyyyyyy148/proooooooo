<?php
include 'header.php';

$alert = "";

// --- HANDLE DEPOSIT APPROVAL ---
if (isset($_POST['approve_deposit'])) {
    $tx_id = intval($_POST['tx_id']);

    // 1. Fetch Transaction & User Details
    $query = mysqli_query($link, "SELECT t.*, u.username, u.email FROM transactions t JOIN users u ON t.user_id = u.id WHERE t.id='$tx_id' AND t.status='pending' AND t.type='deposit'");
    
    if (mysqli_num_rows($query) > 0) {
        $tx = mysqli_fetch_assoc($query);
        $user_id = $tx['user_id'];
        $user_email = $tx['email'];
        $username = $tx['username'];
        $amount_usd = floatval($tx['amount_usd']);
        $symbol = $tx['coin_symbol'];
        $network = $tx['network'];
        $tx_hash = $tx['tx_hash'];

        // 2. Determine Which DB Column to Update
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

        if ($balance_column != "") {
            // 3. Add Funds to User
            $update_user = mysqli_query($link, "UPDATE users SET $balance_column = $balance_column + $amount_usd WHERE id='$user_id'");

            // 4. Mark Transaction as Completed & Send Email
            if ($update_user) {
                mysqli_query($link, "UPDATE transactions SET status='completed' WHERE id='$tx_id'");
                
                // --- SEND EMAIL NOTIFICATION ---
        $subject = "Deposit Confirmed - " . $sitename;
        
        $body = '
        <div style="font-family: Helvetica, Arial, sans-serif; background-color: #f9fafb; padding: 50px 0;">
            <div style="max-width: 500px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); overflow: hidden;">
                
                <div style="padding: 40px 0 20px; text-align: center;">
                    <div style="background-color: #ecfdf5; width: 64px; height: 64px; border-radius: 50%; display: inline-block; line-height: 64px; font-size: 32px; margin-bottom: 20px;">
                        ✅
                    </div>
                    <h1 style="color: #111827; margin: 0; font-size: 24px; font-weight: bold;">Deposit Successful</h1>
                </div>
        
                <div style="padding: 20px 40px 40px;">
                    <p style="color: #4b5563; font-size: 16px; text-align: center; margin-bottom: 30px;">
                        Hello <strong>' . $username . '</strong>, your deposit has been successfully processed and credited to your account.
                    </p>
        
                    <div style="background-color: #f3f4f6; border-radius: 8px; padding: 20px;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="padding-bottom: 10px; color: #6b7280; font-size: 14px;">Amount</td>
                                <td style="padding-bottom: 10px; text-align: right; color: #111827; font-weight: bold; font-size: 14px;">$' . $amount_usd . '</td>
                            </tr>
                            <tr>
                                <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; padding-bottom: 10px; color: #6b7280; font-size: 14px;">Asset</td>
                                <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; padding-bottom: 10px; text-align: right; color: #111827; font-weight: bold; font-size: 14px;">' . $symbol . ' (' . $network . ')</td>
                            </tr>
                            <tr>
                                <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; padding-bottom: 10px; color: #6b7280; font-size: 14px;">Transaction ID</td>
                                <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; padding-bottom: 10px; text-align: right; color: #111827; font-size: 12px; font-family: monospace; word-break: break-all;">' . $tx_hash . '</td>
                            </tr>
                            <tr>
                                <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px;">Status</td>
                                <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; text-align: right;">
                                     <span style="background-color: #ecfdf5; color: #059669; font-size: 12px; font-weight: bold; padding: 4px 10px; border-radius: 20px; border: 1px solid #a7f3d0;">Completed</span>
                                </td>
                            </tr>
                        </table>
                    </div>
        
                 
        
                    <div style="margin-top: 30px; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 20px;">
                        <p style="color: #9ca3af; font-size: 12px; margin: 0;">
                            &copy; ' . date("Y") . ' ' . $sitename . '. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </div>';
        
        sendMail($user_email, $subject, $body);

                $alert = "Swal.fire({icon: 'success', title: 'Approved!', text: 'Funds credited and email sent to user.'});";
            } else {
                $alert = "Swal.fire({icon: 'error', title: 'Error', text: 'Failed to update user balance.'});";
            }
        } else {
            $alert = "Swal.fire({icon: 'error', title: 'Config Error', text: 'Could not identify coin wallet column.'});";
        }
    } else {
        $alert = "Swal.fire({icon: 'warning', title: 'Action Failed', text: 'Transaction already processed or invalid.'});";
    }
}

// --- HANDLE DELETE ---
if (isset($_POST['delete_tx'])) {
    $tx_id = intval($_POST['tx_id']);
    mysqli_query($link, "DELETE FROM transactions WHERE id='$tx_id'");
    $alert = "Swal.fire({icon: 'success', title: 'Deleted', text: 'Transaction removed.', timer: 1500});";
}

// --- FETCH DEPOSITS ---
$sql = "SELECT t.*, u.username, u.email 
        FROM transactions t 
        JOIN users u ON t.user_id = u.id 
        WHERE t.type = 'deposit' 
        ORDER BY t.created_at DESC";
$result = mysqli_query($link, $sql);
?>

<div class="flex-1 overflow-y-auto p-6 space-y-6">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Manage Deposits</h1>
            <p class="text-sm text-slate-500">Review and approve incoming payments.</p>
        </div>
    </div>

    <div class="glass-panel rounded-2xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="text-xs text-slate-400 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="py-3 pl-6 font-semibold">User Info</th>
                        <th class="py-3 font-semibold">Asset / Network</th>
                        <th class="py-3 font-semibold">Amount (USD)</th>
                        <th class="py-3 font-semibold">TX Hash / Proof</th>
                        <th class="py-3 font-semibold text-center">Status</th>
                        <th class="py-3 pr-6 text-right font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): 
                            $status_badge = match($row['status']) {
                                'completed' => 'bg-green-100 text-green-700',
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'failed', 'rejected' => 'bg-red-100 text-red-700',
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

                            <td class="py-4">
                                <p class="font-bold text-slate-800">$<?php echo number_format($row['amount_usd'], 2); ?></p>
                                <p class="text-xs text-slate-500"><?php echo $row['amount_crypto']; ?></p>
                            </td>

                            <td class="py-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs text-slate-500 bg-slate-100 px-2 py-1 rounded max-w-[120px] truncate" title="<?php echo $row['tx_hash']; ?>">
                                        <?php echo $row['tx_hash']; ?>
                                    </span>
                                    <button type="button" onclick="copyHash('<?php echo $row['tx_hash']; ?>')" class="text-indigo-500 hover:text-indigo-700 text-xs p-1">
                                        <i class="fa-regular fa-copy"></i>
                                    </button>
                                </div>
                            </td>

                            <td class="py-4 text-center">
                                <span class="px-2.5 py-1 rounded-md text-xs font-bold <?php echo $status_badge; ?> capitalize">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>

                            <td class="py-4 pr-6 text-right">
                                <div class="flex justify-end gap-2">
                                    <?php if($row['status'] == 'pending'): ?>
                                    <form method="POST" onsubmit="return confirm('Confirm deposit approval? This will add funds to the user.');">
                                        <input type="hidden" name="tx_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="approve_deposit" class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-1.5 px-3 rounded shadow transition-all flex items-center gap-1">
                                            <i class="fa-solid fa-check"></i> Approve
                                        </button>
                                    </form>
                                    <?php else: ?>
                                        <button disabled class="bg-slate-100 text-slate-400 text-xs font-bold py-1.5 px-3 rounded cursor-not-allowed">
                                            <i class="fa-solid fa-check"></i> Done
                                        </button>
                                    <?php endif; ?>

                                    <form method="POST" onsubmit="return confirm('Delete this record permanently?');">
                                        <input type="hidden" name="tx_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="delete_tx" class="bg-red-50 hover:bg-red-100 text-red-500 p-1.5 rounded transition-colors" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 italic">
                                <i class="fa-solid fa-inbox text-3xl mb-2 opacity-50 block"></i>
                                No deposit requests found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // FIXED COPY FUNCTION
    function copyHash(text) {
        // Fallback for secure context requirement
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => {
                showToast();
            });
        } else {
            // Fallback method
            let textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.left = "-9999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                showToast();
            } catch (err) {
                console.error('Fallback copy failed', err);
            }
            document.body.removeChild(textArea);
        }
    }

    function showToast() {
        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
        });
    }

    <?php echo $alert; ?>
</script>

<?php include 'footer.php'; ?>