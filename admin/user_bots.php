<?php
include 'header.php';

$alert = "";

// --- HANDLE 1: UPDATE PROFIT/LOSS ---
if (isset($_POST['update_pl'])) {
    $bot_id = intval($_POST['bot_id']);
    $new_pl = floatval($_POST['profit_loss']); // Can be positive or negative
    
    $query = "UPDATE user_bots SET profit_loss = '$new_pl' WHERE id='$bot_id'";
    if (mysqli_query($link, $query)) {
        $alert = "Swal.fire({icon: 'success', title: 'Updated', text: 'Bot Profit/Loss updated successfully.'});";
    } else {
        $alert = "Swal.fire({icon: 'error', title: 'Error', text: 'Failed to update database.'});";
    }
}

// --- HANDLE 2: END BOT (Credit Funds back to User) ---
if (isset($_POST['end_bot'])) {
    $bot_id = intval($_POST['bot_id']);
    
    // Fetch Bot & User Details
    $q = mysqli_query($link, "SELECT b.*, u.username, u.email FROM user_bots b JOIN users u ON b.user_id = u.id WHERE b.id='$bot_id' AND b.status='running'");
    
    if (mysqli_num_rows($q) > 0) {
        $bot = mysqli_fetch_assoc($q);
        $user_id = $bot['user_id'];
        $email = $bot['email'];
        $invested = floatval($bot['amount_invested']);
        $pl = floatval($bot['profit_loss']);
        $coin = strtoupper($bot['pay_method']);
        
        // Calculate Total Return (Invested + Profit/Loss)
        $total_return = $invested + $pl;
        
        // Determine Wallet Column
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
        } else { $col = "usdt_trc20_balance"; }
        
        if ($col != "") {
            // 1. Credit User Balance
            $upd_user = mysqli_query($link, "UPDATE users SET $col = $col + $total_return WHERE id='$user_id'");
            
            // 2. Stop Bot
            $upd_bot = mysqli_query($link, "UPDATE user_bots SET status='stopped' WHERE id='$bot_id'");
            
            if ($upd_user && $upd_bot) {
              // 3. Send Email
                $subject = "Trading Bot Stopped - " . $sitename;
                
                $body = '
                <div style="font-family: Helvetica, Arial, sans-serif; background-color: #f9fafb; padding: 50px 0;">
                    <div style="max-width: 500px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); overflow: hidden;">
                        
                        <div style="padding: 40px 0 20px; text-align: center;">
                            <div style="background-color: #eef2ff; width: 64px; height: 64px; border-radius: 50%; display: inline-block; line-height: 64px; font-size: 32px; margin-bottom: 20px;">
                                🏁
                            </div>
                            <h1 style="color: #111827; margin: 0; font-size: 24px; font-weight: bold;">Bot Session Ended</h1>
                        </div>
                
                        <div style="padding: 20px 40px 40px;">
                            <p style="color: #4b5563; font-size: 16px; text-align: center; margin-bottom: 30px;">
                                Hello <strong>' . $bot['username'] . '</strong>, your trading bot <strong>' . $bot['bot_name'] . '</strong> has finished its session.
                            </p>
                
                            <div style="background-color: #f3f4f6; border-radius: 8px; padding: 20px;">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding-bottom: 10px; color: #6b7280; font-size: 14px;">Invested</td>
                                        <td style="padding-bottom: 10px; text-align: right; color: #111827; font-weight: bold; font-size: 14px;">$' . $invested . '</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; padding-bottom: 10px; color: #6b7280; font-size: 14px;">Final P/L</td>
                                        <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; padding-bottom: 10px; text-align: right; color: #059669; font-weight: bold; font-size: 14px;">$' . $pl . '</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; padding-bottom: 10px; color: #6b7280; font-size: 14px;">Total Returned</td>
                                        <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; padding-bottom: 10px; text-align: right; color: #111827; font-weight: 800; font-size: 16px;">$' . $total_return . '</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px;">Wallet</td>
                                        <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; text-align: right; color: #111827; font-weight: bold; font-size: 14px;">' . $coin . '</td>
                                    </tr>
                                </table>
                            </div>
                
                            <div style="margin-top: 30px; text-align: center;">
                                <p style="color: #9ca3af; font-size: 12px; margin: 0;">
                                    Funds have been credited to your balance.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>';
                
                sendMail($email, $subject, $body);
                $alert = "Swal.fire({icon: 'success', title: 'Bot Stopped', text: '$$total_return returned to user balance.'});";
            }
        } else {
            $alert = "Swal.fire({icon: 'error', title: 'Error', text: 'Could not identify wallet for $coin.'});";
        }
    }
}

// --- HANDLE 3: DELETE BOT ---
if (isset($_POST['delete_bot'])) {
    $id = intval($_POST['bot_id']);
    mysqli_query($link, "DELETE FROM user_bots WHERE id='$id'");
    $alert = "Swal.fire({icon: 'success', title: 'Deleted', text: 'Bot record removed.', timer: 1500});";
}

// Fetch Active Bots First
$query = "SELECT b.*, u.username, u.email FROM user_bots b JOIN users u ON b.user_id = u.id ORDER BY b.status ASC, b.start_date DESC";
$result = mysqli_query($link, $query);
?>

<div class="flex-1 overflow-y-auto p-6 space-y-6">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Active Trading Bots</h1>
            <p class="text-sm text-slate-500">Monitor and manage user trading sessions.</p>
        </div>
    </div>

    <div class="glass-panel rounded-2xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="text-xs text-slate-400 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="py-3 pl-6 font-semibold">User Info</th>
                        <th class="py-3 font-semibold">Bot Details</th>
                        <th class="py-3 font-semibold">Invested</th>
                        <th class="py-3 font-semibold">Profit / Loss</th>
                        <th class="py-3 font-semibold text-center">Status</th>
                        <th class="py-3 pr-6 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): 
                            $status_badge = ($row['status'] == 'running') ? 'bg-purple-100 text-purple-700 animate-pulse' : 'bg-slate-100 text-slate-600';
                            $pl_color = ($row['profit_loss'] >= 0) ? 'text-green-600' : 'text-red-600';
                            $pl_sign = ($row['profit_loss'] >= 0) ? '+' : '';
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
                                <p class="font-bold text-slate-700"><?php echo $row['bot_name']; ?></p>
                                <p class="text-xs text-slate-500 font-mono"><?php echo $row['pair']; ?></p>
                            </td>
                            <td class="py-4">
                                <p class="font-bold text-slate-800">$<?php echo number_format($row['amount_invested'], 2); ?></p>
                                <span class="text-xs bg-slate-100 text-slate-500 px-2 py-0.5 rounded font-mono">
                                    <?php echo strtoupper($row['pay_method']); ?>
                                </span>
                            </td>
                            <td class="py-4">
                                <p class="font-bold <?php echo $pl_color; ?> text-base">
                                    <?php echo $pl_sign . '$' . number_format($row['profit_loss'], 2); ?>
                                </p>
                            </td>
                            <td class="py-4 text-center">
                                <span class="px-2.5 py-1 rounded-md text-xs font-bold <?php echo $status_badge; ?> capitalize">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td class="py-4 pr-6 text-right">
                                <div class="flex justify-end gap-2">
                                    <?php if($row['status'] == 'running'): ?>
                                        <button onclick="openPLModal(<?php echo $row['id']; ?>, <?php echo $row['profit_loss']; ?>)" class="bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-bold py-1.5 px-3 rounded border border-blue-200 transition-all flex items-center gap-1">
                                            <i class="fa-solid fa-chart-line"></i> P/L
                                        </button>

                                        <form method="POST" onsubmit="return confirm('Stop this bot? Funds + Profit will be returned to user.');">
                                            <input type="hidden" name="bot_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" name="end_bot" class="bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold py-1.5 px-3 rounded border border-red-200 transition-all flex items-center gap-1">
                                                <i class="fa-solid fa-power-off"></i> Stop
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-400 font-bold py-1.5 px-3 border border-slate-200 rounded cursor-not-allowed">Stopped</span>
                                    <?php endif; ?>

                                    <form method="POST" onsubmit="return confirm('Delete history?');">
                                        <input type="hidden" name="bot_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="delete_bot" class="text-slate-400 hover:text-red-500 p-1.5 transition-colors">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="py-12 text-center text-slate-400 italic">No bots found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="plModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl p-6 shadow-2xl relative">
        <button onclick="closePLModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-xl"></i></button>
        
        <h3 class="text-lg font-bold text-slate-800 mb-1">Update Profit/Loss</h3>
        <p class="text-sm text-slate-500 mb-4">Set the current earnings for this bot.</p>
        
        <form method="POST">
            <input type="hidden" name="bot_id" id="modalBotId">
            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Profit / Loss ($)</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold">$</span>
                    <input type="number" step="any" name="profit_loss" id="modalPL" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-8 pr-4 text-sm font-bold text-slate-800 focus:outline-none focus:border-indigo-500">
                </div>
                <p class="text-[10px] text-slate-400 mt-1">Use negative sign (e.g., -10) for loss.</p>
            </div>
            <button type="submit" name="update_pl" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl transition-all">
                Save Changes
            </button>
        </form>
    </div>
</div>

<script>
    function openPLModal(id, currentPL) {
        document.getElementById('modalBotId').value = id;
        document.getElementById('modalPL').value = currentPL;
        document.getElementById('plModal').classList.remove('hidden');
    }
    
    function closePLModal() {
        document.getElementById('plModal').classList.add('hidden');
    }

    document.getElementById('plModal').addEventListener('click', function(e) {
        if (e.target === this) closePLModal();
    });

    <?php echo $alert; ?>
</script>

<?php include 'footer.php'; ?>