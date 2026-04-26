<?php
include 'header.php';

$coins = [
    'usdt_erc20' => ['name'=>'Tether USDT','symbol'=>'USDT','db'=>'usdt_erc20_balance'],
    'eth'        => ['name'=>'Ethereum',   'symbol'=>'ETH', 'db'=>'eth_balance'],
    'btc'        => ['name'=>'Bitcoin',    'symbol'=>'BTC', 'db'=>'btc_balance'],
    'bnb'        => ['name'=>'Binance Coin','symbol'=>'BNB', 'db'=>'bnb_balance'],
    'trx'        => ['name'=>'Tron',       'symbol'=>'TRX', 'db'=>'trx_balance'],
    'usdt_trc20' => ['name'=>'Tether USDT','symbol'=>'USDT','db'=>'usdt_trc20_balance'],
    'ltc'        => ['name'=>'Litecoin',   'symbol'=>'LTC', 'db'=>'ltc_balance'],
    'doge'       => ['name'=>'Dogecoin',   'symbol'=>'DOGE','db'=>'doge_balance'],
    'sol'        => ['name'=>'Solana',     'symbol'=>'SOL', 'db'=>'sol_balance'],
    'matic'      => ['name'=>'Polygon',    'symbol'=>'MATIC','db'=>'matic_balance'],
];

$alert = "";

// 2. Handle Bot Activation
if(isset($_POST['activate_bot'])){
    $bot_id = intval($_POST['bot_id']);
    $bot_name = clean($_POST['bot_name']);
    $amount = floatval($_POST['amount']);
    $pay_method = clean($_POST['pay_method']);
    
    // Validate Payment Method
    if(array_key_exists($pay_method, $coins)){
        $coin_db = $coins[$pay_method]['db'];
        $coin_symbol = $coins[$pay_method]['symbol'];

        // Check Balance
        $q = mysqli_query($link, "SELECT $coin_db FROM users WHERE id='$user_id'");
        $r = mysqli_fetch_assoc($q);
        $balance = floatval($r[$coin_db]);

        if($balance >= $amount){
            // Deduct Balance
            $new_balance = $balance - $amount;
            mysqli_query($link, "UPDATE users SET $coin_db = '$new_balance' WHERE id='$user_id'");

            // Activate Bot
            $pair = $coin_symbol . "/USDT"; 
            $sql = "INSERT INTO user_bots (user_id, bot_id, bot_name, amount_invested, pay_method, pair, status) 
                    VALUES ('$user_id', '$bot_id', '$bot_name', '$amount', '$coin_symbol', '$pair', 'running')";
            
            if(mysqli_query($link, $sql)){
                // Record Transaction
                $sql_tx = "INSERT INTO transactions (user_id, coin_symbol, type, amount_usd, status, tx_hash) 
                           VALUES ('$user_id', '$coin_symbol', 'bot_activation', '$amount', 'completed', 'BOT-" . time() . "')";
                mysqli_query($link, $sql_tx);

                // Email
               $subject = "Bot Activated - " . $bot_name;

                $body = '
                <div style="font-family: Helvetica, Arial, sans-serif; background-color: #f9fafb; padding: 50px 0;">
                    <div style="max-width: 500px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); overflow: hidden;">
                        
                        <div style="padding: 40px 0 20px; text-align: center;">
                            <div style="background-color: #eef2ff; width: 64px; height: 64px; border-radius: 50%; display: inline-block; line-height: 64px; font-size: 32px; margin-bottom: 20px;">
                                🤖
                            </div>
                            <h1 style="color: #111827; margin: 0; font-size: 24px; font-weight: bold;">Bot Activated</h1>
                        </div>
                
                        <div style="padding: 20px 40px 40px;">
                            <p style="color: #4b5563; font-size: 16px; text-align: center; margin-bottom: 30px;">
                                Success! Your trading bot has been deployed and is now running.
                            </p>
                
                            <div style="background-color: #f3f4f6; border-radius: 8px; padding: 20px;">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding-bottom: 10px; color: #6b7280; font-size: 14px;">Bot Name</td>
                                        <td style="padding-bottom: 10px; text-align: right; color: #111827; font-weight: bold; font-size: 14px;">' . $bot_name . '</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px;">Investment</td>
                                        <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; text-align: right; color: #111827; font-weight: bold; font-size: 14px;">$' . $amount . ' ' . $coin_symbol . '</td>
                                    </tr>
                                </table>
                            </div>
                
                            <div style="margin-top: 30px; text-align: center;">
                                <p style="color: #9ca3af; font-size: 12px; margin: 0;">
                                    You can monitor performance in your dashboard.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>';
                
                sendMail($email, $subject, $body);
                $alert = "Swal.fire({icon:'success', title:'Bot Activated', text:'$bot_name is now trading for you!', confirmButtonText:'View Bots'}).then(()=>{ window.location.href='auto-trading.php'; });";
            }
        } else {
            $alert = "Swal.fire({icon:'error', title:'Insufficient Balance', text:'You need $$amount in $coin_symbol.'});";
        }
    } else {
        $alert = "Swal.fire({icon:'error', title:'Error', text:'Invalid payment method.'});";
    }
}

// 3. Handle Stop Bot
if(isset($_POST['stop_bot'])){
    $id = intval($_POST['bot_row_id']);
    mysqli_query($link, "UPDATE user_bots SET status='stopped' WHERE id='$id' AND user_id='$user_id'");
    $alert = "Swal.fire({icon:'success', title:'Bot Stopped', text:'Trading halted for this bot.'}).then(()=>{ window.location.href='auto-trading.php'; });";
}

// 4. Fetch Data
$bots_query = mysqli_query($link, "SELECT * FROM trading_bots");
$user_bots_query = mysqli_query($link, "SELECT * FROM user_bots WHERE user_id='$user_id' ORDER BY start_date DESC");
$running_count = mysqli_num_rows(mysqli_query($link, "SELECT * FROM user_bots WHERE user_id='$user_id' AND status='running'"));
?>

<div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 space-y-6">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">AI Trading Bots</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Select a bot strategy to automate your profits.</p>
        </div>
        <div class="px-4 py-2 rounded-full bg-green-500/10 border border-green-500/20 text-green-500 font-bold text-xs flex items-center gap-2">
            <span class="relative flex h-2 w-2">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
            </span>
            <?php echo $running_count; ?> ACTIVE BOTS RUNNING
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if(mysqli_num_rows($bots_query) > 0): ?>
            <?php while($bot = mysqli_fetch_assoc($bots_query)): 
                $color = $bot['color']; 
            ?>
            <div class="glass-panel rounded-3xl p-6 border border-slate-200 dark:border-white/5 flex flex-col hover:border-<?php echo $color; ?>-500/30 transition-all group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-<?php echo $color; ?>-500/10 rounded-full blur-2xl -mr-10 -mt-10 group-hover:bg-<?php echo $color; ?>-500/20 transition-all"></div>
                
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-<?php echo $color; ?>-500/10 flex items-center justify-center text-<?php echo $color; ?>-500 border border-<?php echo $color; ?>-500/20">
                        <i class="fa-solid <?php echo $bot['icon']; ?> text-xl"></i>
                    </div>
                    <span class="px-2 py-1 rounded bg-<?php echo $color; ?>-500/10 text-<?php echo $color; ?>-500 text-[10px] font-bold uppercase"><?php echo $bot['risk_level']; ?></span>
                </div>
                
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-1"><?php echo $bot['name']; ?></h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-6 min-h-[32px]"><?php echo $bot['description']; ?></p>
                
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold">Avg. Daily ROI</p>
                        <p class="text-lg font-bold text-green-500"><?php echo $bot['roi_min']; ?>% - <?php echo $bot['roi_max']; ?>%</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold">Win Rate</p>
                        <p class="text-lg font-bold text-slate-900 dark:text-white"><?php echo $bot['win_rate']; ?>%</p>
                    </div>
                </div>

                <button onclick="openBotModal('<?php echo $bot['name']; ?>', <?php echo $bot['id']; ?>, <?php echo $bot['min_investment']; ?>)" class="w-full py-3 rounded-xl bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-900 dark:text-white font-bold hover:bg-<?php echo $color; ?>-600 hover:text-white dark:hover:bg-<?php echo $color; ?>-600 hover:border-<?php echo $color; ?>-600 transition-all">
                    Activate Bot
                </button>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="col-span-3 text-center text-slate-500 py-10">No trading bots available in the database.</p>
        <?php endif; ?>
    </div>

    <div class="glass-panel rounded-3xl p-6 md:p-8 border border-slate-200 dark:border-white/5 mt-6">
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Bot Performance History</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-white/5">
                        <th class="py-4 pl-4 font-bold">Bot Name</th>
                        <th class="py-4 font-bold">Pair</th>
                        <th class="py-4 font-bold">Date Started</th>
                        <th class="py-4 font-bold">Initial Investment</th>
                        <th class="py-4 font-bold text-center">Status</th>
                        <th class="py-4 pr-4 font-bold text-right">Profit/Loss</th>
                        <th class="py-4 font-bold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/5 text-sm">
                    <?php if(mysqli_num_rows($user_bots_query) > 0): ?>
                        <?php while($ub = mysqli_fetch_assoc($user_bots_query)): 
                            $status_color = ($ub['status'] == 'running') ? 'green' : 'slate';
                            $pl_color = ($ub['profit_loss'] >= 0) ? 'green' : 'red';
                        ?>
                        <tr class="group hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                            <td class="py-4 pl-4 font-bold text-slate-900 dark:text-white flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-500">
                                    <i class="fa-solid fa-robot"></i>
                                </div>
                                <?php echo $ub['bot_name']; ?>
                            </td>
                            <td class="py-4 text-slate-500 dark:text-slate-400"><?php echo $ub['pair']; ?></td>
                            <td class="py-4 text-slate-500 dark:text-slate-400"><?php echo date("M d, Y", strtotime($ub['start_date'])); ?></td>
                            <td class="py-4 font-medium text-slate-900 dark:text-white">$<?php echo number_format($ub['amount_invested'], 2); ?></td>
                            <td class="py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-<?php echo $status_color; ?>-500/10 text-<?php echo $status_color; ?>-500 text-xs font-bold capitalize"><?php echo $ub['status']; ?></span>
                            </td>
                            <td class="py-4 pr-4 text-right text-<?php echo $pl_color; ?>-500 font-bold">
                                <?php echo ($ub['profit_loss'] >= 0 ? '+' : '') . '$' . number_format($ub['profit_loss'], 2); ?>
                            </td>
                            <td class="py-4 text-right">
                                <?php if($ub['status'] == 'running'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="bot_row_id" value="<?php echo $ub['id']; ?>">
                                    <button type="submit" name="stop_bot" class="text-xs text-red-500 border border-red-500/20 px-2 py-1 rounded hover:bg-red-500 hover:text-white transition-all">Stop</button>
                                </form>
                                <?php else: ?>
                                    <span class="text-xs text-slate-400">Stopped</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="py-8 text-center text-slate-500">No trading bots active.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<div id="botModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/80 backdrop-blur-sm hidden p-4">
    <div class="glass-panel w-full max-w-md rounded-3xl p-6 md:p-8 border border-slate-200 dark:border-white/10 relative shadow-2xl">
        <button onclick="closeBotModal()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-500 hover:text-red-500 transition-all">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <form method="POST">
            <input type="hidden" name="bot_id" id="hiddenBotId">
            <input type="hidden" name="bot_name" id="hiddenBotName">

            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-1">Activate <span id="modalBotName" class="text-indigo-500">Bot</span></h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Configure your investment amount.</p>
            </div>

            <div class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Allocation Amount (USD)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400 font-bold">$</span>
                        <input type="number" name="amount" id="botAmount" step="any" required class="w-full bg-slate-50 dark:bg-[#02040a] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-3 pl-8 pr-4 focus:outline-none focus:border-indigo-500 transition-all font-bold text-lg">
                    </div>
                    <p class="text-xs text-slate-500 mt-2">Min Investment: <span id="modalMinInvest" class="font-bold text-indigo-500">$0</span></p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Funding Source</label>
                    <div class="relative">
                        <select name="pay_method" class="w-full bg-slate-50 dark:bg-[#02040a] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-indigo-500 appearance-none cursor-pointer">
                            <?php foreach($coins as $slug => $coin): 
                                // Fetch specific balance dynamically
                                $col = $coin['db'];
                                $q = mysqli_query($link, "SELECT $col FROM users WHERE id='$user_id'");
                                $r = mysqli_fetch_assoc($q);
                                $bal = floatval($r[$col]);
                            ?>
                                <option value="<?php echo $slug; ?>"><?php echo $coin['name']; ?> - Balance: $<?php echo number_format($bal, 2); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="absolute right-4 top-1/2 transform -translate-y-1/2 pointer-events-none text-slate-400"><i class="fa-solid fa-chevron-down text-xs"></i></div>
                    </div>
                </div>

                <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-xl p-3 flex gap-3 items-start">
                    <i class="fa-solid fa-triangle-exclamation text-yellow-500 mt-0.5"></i>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Trading involves risk. Ensure you understand the risks before activating.</p>
                </div>

                <button type="submit" name="activate_bot" class="w-full py-4 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-bold shadow-lg hover:scale-[1.02] transition-transform">
                    Start Auto Trading
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openBotModal(botName, botId, minInvest) {
        document.getElementById('modalBotName').innerText = botName;
        document.getElementById('hiddenBotName').value = botName;
        document.getElementById('hiddenBotId').value = botId;
        
        document.getElementById('modalMinInvest').innerText = `$${minInvest.toLocaleString()}`;
        document.getElementById('botAmount').placeholder = `Min: ${minInvest}`;
        document.getElementById('botAmount').min = minInvest;
        
        document.getElementById('botModal').classList.remove('hidden');
    }

    function closeBotModal() {
        document.getElementById('botModal').classList.add('hidden');
    }

    document.getElementById('botModal').addEventListener('click', function(e) {
        if (e.target === this) closeBotModal();
    });

    <?php echo $alert; ?>
</script>

<?php include 'footer.php'; ?>