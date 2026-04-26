<?php
include 'header.php';

// 1. Local Coin Configuration (No external file needed)
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

// 2. Handle Investment Submission
if(isset($_POST['start_investment'])){
    $plan_name = clean($_POST['plan_name']);
    $amount = floatval($_POST['amount']);
    $roi = floatval($_POST['roi']);
    $pay_method = clean($_POST['pay_method']); // e.g. 'btc'

    // Validate Payment Method
    if(array_key_exists($pay_method, $coins)){
        $coin_db = $coins[$pay_method]['db'];
        $coin_symbol = $coins[$pay_method]['symbol'];

        // Check User Balance for this specific coin
        $q = mysqli_query($link, "SELECT $coin_db FROM users WHERE id='$user_id'");
        $r = mysqli_fetch_assoc($q);
        $balance = floatval($r[$coin_db]);

        // Check if sufficient funds
        if($balance >= $amount){
            
            // A. Deduct Balance
            $new_balance = $balance - $amount;
            mysqli_query($link, "UPDATE users SET $coin_db = '$new_balance' WHERE id='$user_id'");

            // B. Create Investment Record
            $sql = "INSERT INTO investments (user_id, plan_name, amount, pay_method, roi_percent, status) 
                    VALUES ('$user_id', '$plan_name', '$amount', '$coin_symbol', '$roi', 'active')";
            
            if(mysqli_query($link, $sql)){
                
                // C. Record Transaction in History
                $sql_tx = "INSERT INTO transactions (user_id, coin_symbol, type, amount_usd, status, tx_hash) 
                           VALUES ('$user_id', '$coin_symbol', 'investment', '$amount', 'completed', 'INV-" . time() . "')";
                mysqli_query($link, $sql_tx);

               $subject = "Investment Started - $plan_name";

                $body = '
                <div style="font-family: Helvetica, Arial, sans-serif; background-color: #f9fafb; padding: 50px 0;">
                    <div style="max-width: 500px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); overflow: hidden;">
                        
                        <div style="padding: 40px 0 20px; text-align: center;">
                            <div style="background-color: #eef2ff; width: 64px; height: 64px; border-radius: 50%; display: inline-block; line-height: 64px; font-size: 32px; margin-bottom: 20px;">
                                📈
                            </div>
                            <h1 style="color: #111827; margin: 0; font-size: 24px; font-weight: bold;">Investment Confirmed</h1>
                        </div>
                
                        <div style="padding: 20px 40px 40px;">
                            <p style="color: #4b5563; font-size: 16px; text-align: center; margin-bottom: 30px;">
                                Great news! Your funds have been successfully allocated to the <strong>' . $plan_name . '</strong> plan.
                            </p>
                
                            <div style="background-color: #f3f4f6; border-radius: 8px; padding: 20px;">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding-bottom: 10px; color: #6b7280; font-size: 14px;">Plan Name</td>
                                        <td style="padding-bottom: 10px; text-align: right; color: #111827; font-weight: bold; font-size: 14px;">' . $plan_name . '</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px;">Amount Invested</td>
                                        <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; text-align: right; color: #111827; font-weight: bold; font-size: 14px;">$' . $amount . ' ' . $coin_symbol . '</td>
                                    </tr>
                                </table>
                            </div>
                
                            <div style="margin-top: 30px; text-align: center;">
                                <p style="color: #9ca3af; font-size: 12px; margin: 0;">
                                    Your returns will be calculated automatically.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>';
                
                sendMail($email, $subject, $body);

                $alert = "Swal.fire({icon:'success', title:'Investment Active', text:'Your capital is now working for you!', confirmButtonText:'View Dashboard'}).then(()=>{ window.location.href='investments.php'; });";
            } else {
                $alert = "Swal.fire({icon:'error', title:'Database Error', text:'Could not create investment record.'});";
            }
        } else {
            $alert = "Swal.fire({icon:'error', title:'Insufficient Balance', text:'You need $$amount in $coin_symbol to start this plan.'});";
        }
    } else {
        $alert = "Swal.fire({icon:'error', title:'Error', text:'Invalid payment method selected.'});";
    }
}

// 3. Fetch Plans from Database
$plans_query = mysqli_query($link, "SELECT * FROM investment_plans");

// 4. Fetch User's Investment History
$history_query = mysqli_query($link, "SELECT * FROM investments WHERE user_id='$user_id' ORDER BY start_date DESC");
?>

<div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 space-y-6">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-2">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-1">Investment Plans</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Maximize your wealth with our curated algorithmic strategies.</p>
        </div>
        <div class="bg-slate-100 dark:bg-white/5 p-1 rounded-xl flex">
            <!--<button class="px-6 py-2 rounded-lg bg-indigo-600 text-white shadow-md text-sm font-bold transition-all">Staking</button>-->
            <!--<button class="px-6 py-2 rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white text-sm font-bold transition-all">Farming</button>-->
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php if(mysqli_num_rows($plans_query) > 0): ?>
            <?php while($plan = mysqli_fetch_assoc($plans_query)): 
                $color = $plan['color']; // e.g. indigo, teal, purple
            ?>
            <div class="glass-panel rounded-3xl p-6 border border-slate-200 dark:border-white/5 flex flex-col hover:border-<?php echo $color; ?>-500/30 transition-all duration-300 group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-<?php echo $color; ?>-500/10 rounded-full blur-2xl -mr-10 -mt-10 group-hover:bg-<?php echo $color; ?>-500/20 transition-all"></div>
                
                <div class="w-12 h-12 rounded-2xl bg-<?php echo $color; ?>-500/10 flex items-center justify-center mb-6 text-<?php echo $color; ?>-500 border border-<?php echo $color; ?>-500/20">
                    <i class="fa-solid <?php echo $plan['icon']; ?> text-xl"></i>
                </div>
                
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-2"><?php echo $plan['name']; ?></h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-6"><?php echo $plan['description']; ?></p>
                
                <div class="space-y-4 mb-8 flex-1">
                    <div class="flex justify-between text-sm border-b border-slate-100 dark:border-white/5 pb-3">
                        <span class="text-slate-500 dark:text-slate-400 font-medium uppercase text-[10px] tracking-wider">Min Deposit</span>
                        <span class="text-slate-900 dark:text-white font-bold">$<?php echo number_format($plan['min_deposit']); ?></span>
                    </div>
                    <div class="flex justify-between text-sm border-b border-slate-100 dark:border-white/5 pb-3">
                        <span class="text-slate-500 dark:text-slate-400 font-medium uppercase text-[10px] tracking-wider">ROI (Est.)</span>
                        <span class="text-<?php echo $color; ?>-500 font-bold"><?php echo $plan['roi']; ?>% Daily</span>
                    </div>
                    <div class="flex justify-between text-sm border-b border-slate-100 dark:border-white/5 pb-3">
                        <span class="text-slate-500 dark:text-slate-400 font-medium uppercase text-[10px] tracking-wider">Duration</span>
                        <span class="text-slate-900 dark:text-white font-bold"><?php echo $plan['duration']; ?> Days</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500 dark:text-slate-400 font-medium uppercase text-[10px] tracking-wider">Risk Level</span>
                        <span class="px-2 py-0.5 rounded bg-<?php echo $color; ?>-500/10 text-<?php echo $color; ?>-500 text-[10px] font-bold uppercase"><?php echo $plan['risk_level']; ?></span>
                    </div>
                </div>

                <button onclick="openModal('<?php echo $plan['name']; ?>', <?php echo $plan['min_deposit']; ?>, <?php echo $plan['roi']; ?>)" class="w-full py-3.5 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-black font-bold hover:bg-slate-800 dark:hover:bg-slate-200 transition-all shadow-lg">
                    Select Plan
                </button>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-slate-500 dark:text-slate-400 col-span-3 text-center py-10">No investment plans available at the moment.</p>
        <?php endif; ?>
    </div>

    <div class="glass-panel rounded-3xl p-6 md:p-8 border border-slate-200 dark:border-white/5 mt-6">
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Investment History</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-white/5">
                        <th class="py-4 pl-4 font-bold">Plan Name</th>
                        <th class="py-4 font-bold">Amount Invested</th>
                        <th class="py-4 font-bold">Date Started</th>
                        <th class="py-4 font-bold">Total ROI</th>
                        <th class="py-4 font-bold text-center">Status</th>
                        <th class="py-4 pr-4 font-bold text-right">Est. Daily Profit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/5 text-sm">
                    <?php if(mysqli_num_rows($history_query) > 0): ?>
                        <?php while($inv = mysqli_fetch_assoc($history_query)): 
                            $profit = $inv['amount'] * ($inv['roi_percent'] / 100);
                        ?>
                        <tr class="group hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                            <td class="py-4 pl-4 font-bold text-slate-900 dark:text-white flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-500">
                                    <i class="fa-solid fa-chart-pie"></i>
                                </div>
                                <?php echo $inv['plan_name']; ?>
                            </td>
                            <td class="py-4 font-medium text-slate-900 dark:text-white">$<?php echo number_format($inv['amount'], 2); ?></td>
                            <td class="py-4 text-slate-500 dark:text-slate-400"><?php echo date("M d, Y", strtotime($inv['start_date'])); ?></td>
                            <td class="py-4 text-slate-500 dark:text-slate-400"><?php echo $inv['roi_percent']; ?>%</td>
                            <td class="py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-green-500/10 text-green-500 text-xs font-bold capitalize"><?php echo $inv['status']; ?></span>
                            </td>
                            <td class="py-4 pr-4 text-right text-green-500 font-bold">+$<?php echo number_format($profit, 2); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="py-8 text-center text-slate-500">No active investments found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<div id="investModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/80 backdrop-blur-sm hidden p-4">
    <div class="glass-panel w-full max-w-md rounded-3xl p-6 md:p-8 border border-slate-200 dark:border-white/10 relative shadow-2xl">
        <button onclick="closeModal()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-500 hover:text-red-500 transition-all">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <form method="POST">
            <input type="hidden" name="plan_name" id="hiddenPlanName">
            <input type="hidden" name="roi" id="hiddenRoi">

            <div class="mb-6">
                <h2 id="modalTitle" class="text-2xl font-bold text-slate-900 dark:text-white mb-1">Confirm Investment</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Enter amount and choose payment method.</p>
            </div>

            <div class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Amount to Invest (USD)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400 font-bold">$</span>
                        <input type="number" name="amount" id="investAmount" step="any" oninput="calculateReturns()" required class="w-full bg-slate-50 dark:bg-[#02040a] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-3 pl-8 pr-4 focus:outline-none focus:border-indigo-500 transition-all font-bold text-lg">
                    </div>
                    <p class="text-xs text-slate-500 mt-2">Min Deposit: <span id="modalMinDeposit" class="font-bold text-indigo-500">$0</span></p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Pay With</label>
                    <div class="relative">
                        <select name="pay_method" class="w-full bg-slate-50 dark:bg-[#02040a] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-indigo-500 appearance-none cursor-pointer">
                            <?php 
                            // LOOP THROUGH LOCAL COINS ARRAY TO SHOW BALANCES
                            foreach($coins as $slug => $coin): 
                                $col = $coin['db'];
                                $q = mysqli_query($link, "SELECT $col FROM users WHERE id='$user_id'");
                                $r = mysqli_fetch_assoc($q);
                                $bal = floatval($r[$col]);
                            ?>
                                <option value="<?php echo $slug; ?>"><?php echo $coin['name']; ?> - Balance: $<?php echo number_format($bal, 2); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="absolute right-4 top-1/2 transform -translate-y-1/2 pointer-events-none text-slate-400">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-indigo-50 dark:bg-indigo-500/10 rounded-xl p-4 border border-indigo-100 dark:border-indigo-500/20">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Est. Daily Return</span>
                        <span id="modalRoiPercentage" class="text-xs font-bold text-indigo-500 bg-indigo-100 dark:bg-indigo-500/20 px-2 py-0.5 rounded">0%</span>
                    </div>
                    <p id="modalEstReturn" class="text-2xl font-extrabold text-slate-900 dark:text-white">$0.00</p>
                </div>

                <button type="submit" name="start_investment" class="w-full py-4 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-bold shadow-lg hover:scale-[1.02] transition-transform">
                    Confirm & Start Plan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentRoi = 0;

    function openModal(planName, minDeposit, roi) {
        document.getElementById('modalTitle').innerText = `Invest in ${planName}`;
        document.getElementById('modalMinDeposit').innerText = `$${minDeposit.toLocaleString()}`;
        document.getElementById('investAmount').placeholder = `Min: ${minDeposit}`;
        document.getElementById('investAmount').value = minDeposit; 
        document.getElementById('investAmount').min = minDeposit; 
        
        document.getElementById('hiddenPlanName').value = planName;
        document.getElementById('hiddenRoi').value = roi;
        document.getElementById('modalRoiPercentage').innerText = `${roi}%`;
        
        currentRoi = roi;
        calculateReturns();
        document.getElementById('investModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('investModal').classList.add('hidden');
    }

    function calculateReturns() {
        const amount = parseFloat(document.getElementById('investAmount').value) || 0;
        const monthlyReturn = amount * (currentRoi / 100);
        document.getElementById('modalEstReturn').innerText = `$${monthlyReturn.toFixed(2)}`;
    }

    <?php echo $alert; ?>
</script>

<?php include 'footer.php'; ?>