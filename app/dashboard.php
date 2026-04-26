<?php
include 'header.php'; 

// --- 1. FETCH USER'S CARD DATA ---
$card_query = mysqli_query($link, "SELECT * FROM virtual_cards WHERE user_id = '$user_id' ORDER BY id DESC LIMIT 1");
$my_card = mysqli_fetch_assoc($card_query);
$card_status = $my_card ? $my_card['status'] : 'none';

// --- 2. SET CARD VARIABLES (Default to Placeholder) ---
$card_number_view = "•••• •••• •••• 8824";
$card_holder_view = strtoupper($fullname);
$card_expiry_view = "00/00";
$card_cvv_view = "***";
$card_type_label = "Platinum Elite";
$card_icon = "fa-cc-visa"; 
$real_card_number = "0000000000000000"; // Fallback for QR

// Update variables if card exists
if ($card_status == 'active' || $card_status == 'frozen') {
    $real_card_number = $my_card['card_number'];
    // Mask for front view
    $card_number_view = "•••• •••• •••• " . substr($real_card_number, -4);
    
    $card_holder_view = strtoupper($my_card['card_holder_name']);
    $card_expiry_view = $my_card['expiry'];
    $card_cvv_view = $my_card['cvv'];
    $card_type_label = "Virtual " . ucfirst($my_card['card_type']);
    
    if (strtolower($my_card['card_type']) == 'mastercard') {
        $card_icon = "fa-cc-mastercard";
    }
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .perspective-1000 { perspective: 1000px; }
    .transform-style-3d { transform-style: preserve-3d; }
    .backface-hidden { backface-visibility: hidden; -webkit-backface-visibility: hidden; }
    .rotate-y-180 { transform: rotateY(180deg); }
    .card-inner {
        transition: transform 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        width: 100%; height: 100%; position: relative;
    }
    /* FLIP ON HOVER */
    .group:hover .card-inner { transform: rotateY(180deg); }
</style>

<div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 space-y-6">

    <div class="w-full rounded-3xl overflow-hidden relative shadow-2xl shadow-indigo-500/20 dark:shadow-none border border-slate-200 dark:border-white/10 group">
        <div class="absolute inset-0 bg-gradient-to-r from-indigo-900 to-slate-900 z-0"></div>
        <div class="relative z-10 p-6 md:p-10 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="max-w-2xl">
                <span class="inline-block py-1 px-3 rounded-lg bg-indigo-500/20 border border-indigo-500/30 text-indigo-300 text-xs font-bold uppercase tracking-wider mb-3">Special Offer</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-2 leading-tight">
                    Earn <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">12.5% APY</span> on USDT
                </h2>
                <p class="text-indigo-100 text-sm md:text-base max-w-lg">
                    Stake your idle assets and watch your portfolio grow with Fchain's automated yield optimizer.
                </p>
            </div>
            <a href="investments.php" class="bg-white text-indigo-900 font-bold py-3.5 px-8 rounded-xl shadow-lg hover:bg-indigo-50 transition-transform transform hover:-translate-y-1 w-full md:w-auto text-center">
                Start Investing
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="col-span-1 lg:col-span-2 glass-panel rounded-3xl p-6 md:p-8 relative overflow-hidden border border-slate-200 dark:border-white/5">
    <div class="flex flex-col md:flex-row justify-between items-end gap-6">
        <div class="w-full">
            <p class="text-slate-500 dark:text-slate-400 font-medium mb-1">Total Portfolio Value</p>
            <div class="flex items-baseline gap-2">
                <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    $<?php echo number_format($total_balance, 2); ?>
                </h1>
            </div>
            <div class="flex items-center gap-2 mt-2">
                <span class="bg-green-500/10 text-green-600 dark:text-green-400 px-2 py-0.5 rounded text-sm font-bold flex items-center gap-1">
                    <i class="fa-solid fa-arrow-trend-up"></i> +0.00%
                </span>
                <span class="text-slate-400 dark:text-slate-500 text-sm">(Real-time)</span>
            </div>
        </div>
    </div> 
    <br>

    <div class="grid grid-cols-3 gap-2 sm:gap-3 w-full md:flex md:w-auto">
        
        <a href="assets.php" class="flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-2 bg-slate-900 text-white dark:bg-white dark:text-black font-bold py-3 px-2 sm:px-6 rounded-2xl hover:bg-slate-800 dark:hover:bg-slate-200 transition-colors shadow-lg shadow-slate-200/50 dark:shadow-[0_0_15px_rgba(255,255,255,0.3)] md:flex-1 text-[10px] sm:text-sm">
            <i class="fa-solid fa-plus text-sm sm:text-base"></i> 
            <span>Receive</span>
        </a>

        <a href="assets.php" class="flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-2 bg-slate-200 text-slate-700 hover:bg-slate-300 dark:bg-[#1e293b]/50 dark:text-white dark:hover:bg-indigo-600 font-semibold py-3 px-2 sm:px-6 rounded-2xl border border-transparent dark:border-white/10 dark:hover:border-indigo-500 transition-all md:flex-1 text-[10px] sm:text-sm">
            <i class="fa-solid fa-repeat text-sm sm:text-base"></i> 
            <span>Swap</span>
        </a>

        <a href="send.php" class="flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-2 bg-slate-200 text-slate-700 hover:bg-slate-300 dark:bg-[#1e293b]/50 dark:text-white dark:hover:bg-[#1e293b] font-semibold py-3 px-2 sm:px-6 rounded-2xl border border-transparent dark:border-white/10 transition-colors md:flex-1 text-[10px] sm:text-sm">
            <i class="fa-regular fa-paper-plane text-sm sm:text-base"></i> 
            <span>Send</span>
        </a>

    </div>
</div>
        <a href="cards.php" class="col-span-1 h-64 perspective-1000 group cursor-pointer block">
            <div class="relative w-full h-full text-white transform-style-3d card-inner">
                
                <div class="absolute inset-0 w-full h-full bg-gradient-to-br from-[#6366f1] via-[#4338ca] to-[#312e81] rounded-3xl p-6 shadow-2xl backface-hidden border border-white/10 flex flex-col justify-between overflow-hidden z-20">
                    
                    <?php if($card_status == 'pending'): ?>
                        <div class="absolute inset-0 bg-black/60 z-30 flex items-center justify-center backdrop-blur-sm">
                            <div class="text-center">
                                <i class="fa-solid fa-clock text-3xl mb-2 text-yellow-400"></i>
                                <p class="font-bold tracking-widest uppercase text-sm">Issuance Pending</p>
                            </div>
                        </div>
                    <?php elseif($card_status == 'frozen'): ?>
                        <div class="absolute inset-0 bg-black/60 z-30 flex items-center justify-center backdrop-blur-sm">
                            <div class="text-center">
                                <i class="fa-solid fa-lock text-3xl mb-2 text-red-400"></i>
                                <p class="font-bold tracking-widest uppercase text-sm">Card Frozen</p>
                            </div>
                        </div>
                    <?php elseif($card_status == 'none'): ?>
                        <div class="absolute inset-0 bg-indigo-900/80 z-30 flex items-center justify-center backdrop-blur-[2px]">
                            <div class="bg-white text-indigo-900 px-6 py-2 rounded-full font-bold shadow-lg text-sm">
                                Apply for Card
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                    
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <h3 class="font-bold text-lg italic tracking-wider"><?php echo $sitename; ?></h3>
                            <p class="text-[10px] text-indigo-200 uppercase tracking-widest mt-1"><?php echo $card_type_label; ?></p>
                        </div>
                        <i class="fa-brands <?php echo $card_icon; ?> text-4xl opacity-80"></i>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-9 bg-gradient-to-br from-yellow-200 to-yellow-500 rounded-md shadow-md border border-white/20"></div>
                        <i class="fa-solid fa-wifi rotate-90 text-white/50"></i>
                    </div>
                    
                    <div>
                        <p class="font-mono text-xl tracking-[4px] shadow-black drop-shadow-md mb-4">
                            <?php echo $card_number_view; ?>
                        </p>
                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-[9px] text-indigo-200 uppercase">Card Holder</p>
                                <p class="font-medium tracking-wide text-sm"><?php echo $card_holder_view; ?></p>
                            </div>
                            <div>
                                <p class="text-[9px] text-indigo-200 uppercase text-right">Expires</p>
                                <p class="font-medium tracking-wide text-sm"><?php echo $card_expiry_view; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="absolute inset-0 w-full h-full bg-[#1e1b4b] rounded-3xl shadow-2xl backface-hidden rotate-y-180 border border-white/10 overflow-hidden z-10">
                    <div class="w-full h-12 bg-black mt-6"></div>
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <div class="flex-1 mr-4">
                                <p class="text-[10px] text-indigo-300 mb-1">Authorized Signature</p>
                                <div class="h-8 bg-white w-full rounded flex items-center justify-end px-2">
                                    <span class="font-mono text-black text-sm font-bold italic"><?php echo $card_cvv_view; ?></span>
                                </div>
                            </div>
                            <div class="w-14 h-14 bg-white/5 rounded-lg flex items-center justify-center">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo $real_card_number; ?>" class="w-10 h-10 opacity-80">
                            </div>
                        </div>
                        <p class="text-[10px] text-indigo-300 leading-relaxed text-justify">
                            This card is issued by <?php echo $sitename; ?>. Use of this card constitutes acceptance of terms. Electronic use only.
                        </p>
                    </div>
                </div>

            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="col-span-2 glass-panel rounded-3xl p-6 border border-slate-200 dark:border-white/5">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-lg text-slate-800 dark:text-white">Market Performance</h3>
                <div class="flex bg-slate-100 dark:bg-black/30 rounded-lg p-1">
                    <button class="px-3 py-1 rounded text-xs text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">1D</button>
                    <button class="px-3 py-1 rounded text-xs bg-indigo-600 text-white shadow">1W</button>
                    <button class="px-3 py-1 rounded text-xs text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">1M</button>
                </div>
            </div>
            <div class="h-64 w-full">
                <canvas id="marketChart"></canvas>
            </div>
        </div>

        <div class="col-span-1 glass-panel rounded-3xl p-6 border border-slate-200 dark:border-white/5">
            <h3 class="font-bold text-lg mb-4 text-slate-800 dark:text-white">Trending Assets</h3>
            <div class="space-y-4">
                
                <a href="coin-details.php?coin=btc" class="flex items-center justify-between group cursor-pointer block hover:bg-slate-100 dark:hover:bg-white/5 p-2 rounded-xl transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-orange-500/10 flex items-center justify-center border border-orange-500/20 group-hover:border-orange-500 transition-colors">
                            <i class="fa-brands fa-bitcoin text-orange-500 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-sm text-slate-800 dark:text-white">Bitcoin</p>
                            <p class="text-xs text-slate-500">BTC</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-sm text-slate-800 dark:text-white">$<?php echo number_format($row['btc_balance'], 2); ?></p>
                        <p class="text-xs text-green-500 dark:text-green-400">Balance</p>
                    </div>
                </a>

                <div class="h-px bg-slate-200 dark:bg-white/5"></div>

                <a href="coin-details.php?coin=eth" class="flex items-center justify-between group cursor-pointer block hover:bg-slate-100 dark:hover:bg-white/5 p-2 rounded-xl transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-500/10 flex items-center justify-center border border-indigo-500/20 group-hover:border-indigo-500 transition-colors">
                            <i class="fa-brands fa-ethereum text-indigo-500 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-sm text-slate-800 dark:text-white">Ethereum</p>
                            <p class="text-xs text-slate-500">ETH</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-sm text-slate-800 dark:text-white">$<?php echo number_format($row['eth_balance'], 2); ?></p>
                        <p class="text-xs text-green-500 dark:text-green-400">Balance</p>
                    </div>
                </a>

                <div class="h-px bg-slate-200 dark:bg-white/5"></div>

                <a href="coin-details.php?coin=sol" class="flex items-center justify-between group cursor-pointer block hover:bg-slate-100 dark:hover:bg-white/5 p-2 rounded-xl transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-teal-500/10 flex items-center justify-center border border-teal-500/20 group-hover:border-teal-500 transition-colors">
                            <span class="font-bold text-teal-400">S</span>
                        </div>
                        <div>
                            <p class="font-bold text-sm text-slate-800 dark:text-white">Solana</p>
                            <p class="text-xs text-slate-500">SOL</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-sm text-slate-800 dark:text-white">$<?php echo number_format($row['sol_balance'], 2); ?></p>
                        <p class="text-xs text-green-500 dark:text-green-400">Balance</p>
                    </div>
                </a>

            </div>
            <a href="assets.php" class="block w-full text-center mt-6 py-3 rounded-xl border border-slate-200 dark:border-white/10 text-xs font-bold uppercase tracking-wider hover:bg-slate-100 dark:hover:bg-white/5 transition-colors text-slate-500 dark:text-slate-400">
                View All Assets
            </a>
        </div>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('marketChart').getContext('2d');

    // 1. Create Gradient
    let gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(99, 102, 241, 0.5)'); 
    gradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)'); 

    // 2. Fetch Real Data (Bitcoin 7-Day History)
    fetch('https://min-api.cryptocompare.com/data/v2/histoday?fsym=BTC&tsym=USD&limit=7')
    .then(res => res.json())
    .then(data => {
        const history = data.Data.Data;
        const labels = history.map(item => new Date(item.time * 1000).toLocaleDateString('en-US', {weekday: 'short'}));
        const prices = history.map(item => item.close);

        // 3. Render Chart
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Bitcoin Price (USD)',
                    data: prices,
                    borderColor: '#6366f1',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#94a3b8' } },
                    y: { display: false }
                },
                interaction: { intersect: false, mode: 'index' }
            }
        });
    })
    .catch(err => console.error("Chart Error:", err));
});
</script>

<?php include 'footer.php'; ?>