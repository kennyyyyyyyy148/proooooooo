<?php
include 'header.php';

$platforms = [
    [
        'name' => 'Binance',
        'logo' => 'https://cryptologos.cc/logos/binance-coin-bnb-logo.svg',
        'description' => 'Global leader. Best for low fees and high liquidity.',
        'assets' => ['BTC', 'ETH', 'USDT', 'BNB'],
        'url' => 'https://www.binance.com/en/crypto/buy',
        'badge' => 'Top Choice',
        'color' => '#F3BA2F'
    ],
    [
        'name' => 'Coinbase',
        'logo' => 'Coinbase.jpg',
        'description' => 'The most trusted platform for beginners. Easy UI.',
        'assets' => ['BTC', 'ETH', 'SOL', 'USDC'],
        'url' => 'https://www.coinbase.com/buy-crypto',
        'badge' => 'Easiest',
        'color' => '#0052FF'
    ],
    [
        'name' => 'MoonPay',
        'logo' => 'MoonPay.png',
        'description' => 'Buy instantly with Credit Card, Apple Pay, or Google Pay.',
        'assets' => ['USDT', 'ETH', 'TRX', 'LTC'],
        'url' => 'https://www.moonpay.com/buy',
        'badge' => 'Instant',
        'color' => '#7D16FF'
    ],
    [
        'name' => 'Bybit',
        'logo' => 'Bybit.png',
        'description' => 'Excellent P2P options and zero-fee bank deposits.',
        'assets' => ['USDT', 'BTC', 'XRP', 'ETH'],
        'url' => 'https://www.bybit.com/fiat/purchase/crypto',
        'badge' => 'Low Fee',
        'color' => '#FFB11A'
    ]
];
?>

<style>
    .market-card {
        background: linear-gradient(145deg, rgba(30, 41, 59, 0.4), rgba(15, 23, 42, 0.6));
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        transition: all 0.3s ease;
    }
    .market-card:hover {
        border-color: rgba(99, 102, 241, 0.4);
        transform: translateY(-5px);
        box-shadow: 0 20px 40px -10px rgba(0,0,0,0.5);
    }
    .step-number {
        background: linear-gradient(135deg, #6366f1, #a855f7);
    }
</style>

<div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 bg-[#02040a]">
    
    <div class="max-w-6xl mx-auto mb-10">
        <h1 class="text-3xl font-black text-white tracking-tight mb-2">Buy Crypto Assets</h1>
        <p class="text-slate-500 font-medium">Select a verified gateway to fund your account securely.</p>
    </div>

    <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <?php 
        $steps = [
            ['title' => 'Choose Platform', 'desc' => 'Select an exchange below that supports your payment method.'],
            ['title' => 'Purchase Asset', 'desc' => 'Buy BTC, ETH, or USDT using your Bank or Credit Card.'],
            ['title' => 'Transfer to App', 'desc' => 'Send the purchased crypto to your wallet address in this app.'],
        ];
        foreach($steps as $i => $step): ?>
        <div class="flex items-start gap-4 p-5 rounded-2xl bg-white/5 border border-white/5">
            <div class="step-number w-8 h-8 rounded-lg flex items-center justify-center text-white font-black shrink-0 shadow-lg">
                <?php echo $i + 1; ?>
            </div>
            <div>
                <h4 class="text-sm font-bold text-white uppercase tracking-wider"><?php echo $step['title']; ?></h4>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed"><?php echo $step['desc']; ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8">
        <?php foreach($platforms as $p): ?>
        <div class="market-card rounded-[2rem] p-8 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-start mb-6">
                    <div class="w-14 h-14 rounded-2xl bg-slate-900/50 p-3 border border-white/10 flex items-center justify-center">
                        <img src="<?php echo $p['logo']; ?>" class="w-full h-full object-contain">
                    </div>
                    <span class="px-3 py-1 rounded-full bg-indigo-500/10 text-indigo-400 text-[10px] font-black uppercase tracking-widest border border-indigo-500/20">
                        <?php echo $p['badge']; ?>
                    </span>
                </div>

                <h2 class="text-2xl font-black text-white mb-2"><?php echo $p['name']; ?></h2>
                <p class="text-sm text-slate-400 mb-6 leading-relaxed">
                    <?php echo $p['description']; ?>
                </p>

                <div class="flex flex-wrap gap-2 mb-8">
                    <?php foreach($p['assets'] as $asset): ?>
                    <span class="px-3 py-1.5 rounded-lg bg-white/5 border border-white/5 text-[11px] font-bold text-slate-300 font-mono">
                        <?php echo $asset; ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>

            <a href="<?php echo $p['url']; ?>" target="_blank" 
               class="w-full py-4 rounded-2xl text-center text-sm font-black uppercase tracking-widest transition-all active:scale-95 shadow-xl flex items-center justify-center gap-3"
               style="background: <?php echo $p['color']; ?>; color: #000;">
                Buy on <?php echo $p['name']; ?> 
                <i class="fa-solid fa-external-link text-xs"></i>
            </a>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="max-w-6xl mx-auto mt-12 p-6 rounded-[2rem] bg-indigo-600/5 border border-indigo-500/10 flex flex-col md:flex-row items-center gap-6">
        <div class="w-16 h-16 rounded-2xl bg-indigo-600/20 flex items-center justify-center text-indigo-400 shrink-0">
            <i class="fa-solid fa-shield-check text-3xl"></i>
        </div>
        <div class="text-center md:text-left">
            <h4 class="text-lg font-bold text-white">Security & Verification</h4>
            <p class="text-sm text-slate-500 mt-1 leading-relaxed">
                Most platforms will require **Identification (KYC)** to process your first purchase. Ensure you have a valid ID ready. 
                Always double-check your wallet address before initiating a transfer.
            </p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>