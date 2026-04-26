<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CryptoInvest AI - Next Gen Trading</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        // Your Custom Palette
                        brand: {
                            primary: '#6366F1', // Indigo for accents
                            secondary: '#818CF8',
                        },
                        dark: { 
                            bg: '#02040a', 
                            panel: '#0B0F19', 
                            border: '#1E293B',
                            text: '#E2E8F0'
                        },
                        light: { 
                            bg: '#F8FAFC', 
                            panel: '#FFFFFF', 
                            border: '#E2E8F0' 
                        }
                    },
                    boxShadow: {
                        'neon': '0 0 20px rgba(99, 102, 241, 0.4)',
                        'glass': '0 8px 32px 0 rgba(0, 0, 0, 0.3)',
                    }
                }
            }
        }
    </script>

    <style>
        /* Custom Utilities */
        body {
            background-color: #02040a; /* Fallback */
            color: #E2E8F0;
        }
        
        /* Glassmorphism Classes */
        .glass-panel {
            background: rgba(11, 15, 25, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(30, 41, 59, 0.5);
        }

        .gradient-text {
            background: linear-gradient(to right, #818CF8, #C7D2FE);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Hide scrollbar for clean UI */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-dark-bg text-dark-text font-sans antialiased selection:bg-brand-primary selection:text-white">

    <nav class="fixed w-full z-50 glass-panel border-b border-dark-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-brand-primary flex items-center justify-center shadow-neon">
                        <i class="fa-solid fa-bolt text-white text-xl"></i>
                    </div>
                    <span class="font-bold text-2xl tracking-tight text-white">Nova<span class="text-brand-primary">Fi</span></span>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-8">
                        <a href="#" class="hover:text-brand-primary transition-colors px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                        <a href="#bots" class="hover:text-brand-primary transition-colors px-3 py-2 rounded-md text-sm font-medium">AI Bots</a>
                        <a href="#market" class="hover:text-brand-primary transition-colors px-3 py-2 rounded-md text-sm font-medium">Market</a>
                        <a href="#wallet" class="hover:text-brand-primary transition-colors px-3 py-2 rounded-md text-sm font-medium">Wallet</a>
                    </div>
                </div>
                <div>
                    <button class="bg-brand-primary hover:bg-brand-secondary text-white px-6 py-2 rounded-full font-medium transition-all shadow-neon hover:scale-105">
                        Connect Wallet
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div class="relative pt-32 pb-20 lg:pt-40 lg:pb-28 overflow-hidden">
        <div class="absolute top-0 left-1/2 w-full -translate-x-1/2 h-full z-0 pointer-events-none">
            <div class="absolute top-20 left-1/4 w-96 h-96 bg-brand-primary/20 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-20 right-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-[100px]"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-12 items-center">
            
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-primary/10 border border-brand-primary/20 text-brand-secondary text-sm mb-6">
                    <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                    AI Trading Engine Live
                </div>
                <h1 class="text-5xl lg:text-7xl font-bold tracking-tight mb-6">
                    Invest in the <br>
                    <span class="gradient-text">Future of DeFi</span>
                </h1>
                <p class="text-lg text-gray-400 mb-8 max-w-lg">
                    Manage your portfolio, swap assets instantly, and let our AI-driven bots grow your wealth. The all-in-one crypto ecosystem.
                </p>
                <div class="flex gap-4">
                    <button class="bg-white text-dark-bg px-8 py-3 rounded-lg font-bold hover:bg-gray-200 transition-colors">
                        Start Trading
                    </button>
                    <button class="border border-dark-border px-8 py-3 rounded-lg font-bold hover:border-brand-primary hover:text-brand-primary transition-colors">
                        View Analytics
                    </button>
                </div>
                
                <div class="mt-12 flex gap-8 items-center text-sm text-gray-500">
                    <div class="flex items-center gap-2">
                        <i class="fa-brands fa-bitcoin text-orange-500 text-xl"></i>
                        <span>BTC <span class="text-white font-mono">$45,230</span> <span class="text-green-400">+2.4%</span></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-brands fa-ethereum text-blue-400 text-xl"></i>
                        <span>ETH <span class="text-white font-mono">$2,890</span> <span class="text-red-400">-0.5%</span></span>
                    </div>
                </div>
            </div>

            <div id="wallet" class="relative">
                <div class="absolute inset-0 bg-brand-primary blur-[40px] opacity-20"></div>
                <div class="relative bg-dark-panel border border-dark-border rounded-2xl shadow-glass overflow-hidden">
                    
                    <div class="p-6 border-b border-dark-border flex justify-between items-center">
                        <div>
                            <p class="text-gray-400 text-sm">Total Balance</p>
                            <h3 class="text-3xl font-bold text-white mt-1">$124,592.00</h3>
                        </div>
                        <div class="bg-dark-bg p-2 rounded-lg border border-dark-border">
                            <i class="fa-solid fa-wallet text-brand-primary"></i>
                            <span class="text-sm ml-2 font-mono">0x4F...8a2</span>
                        </div>
                    </div>

                    <div class="flex border-b border-dark-border">
                        <button onclick="switchTab('send')" id="tab-send" class="flex-1 py-4 text-center font-medium hover:bg-dark-bg/50 transition-colors text-brand-secondary border-b-2 border-brand-primary">Send</button>
                        <button onclick="switchTab('receive')" id="tab-receive" class="flex-1 py-4 text-center font-medium hover:bg-dark-bg/50 transition-colors text-gray-400 border-b-2 border-transparent">Receive</button>
                        <button onclick="switchTab('swap')" id="tab-swap" class="flex-1 py-4 text-center font-medium hover:bg-dark-bg/50 transition-colors text-gray-400 border-b-2 border-transparent">Swap</button>
                    </div>

                    <div class="p-6 h-80">
                        
                        <div id="view-send" class="space-y-4">
                            <div>
                                <label class="block text-xs text-gray-400 mb-1 uppercase tracking-wider">Recipient Address</label>
                                <div class="relative">
                                    <input type="text" placeholder="0x..." class="w-full bg-dark-bg border border-dark-border rounded-lg p-3 pl-10 text-white focus:outline-none focus:border-brand-primary focus:ring-1 focus:ring-brand-primary transition-all">
                                    <i class="fa-solid fa-qrcode absolute left-3 top-3.5 text-gray-500"></i>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1 uppercase tracking-wider">Amount (ETH)</label>
                                <div class="flex gap-2">
                                    <input type="number" placeholder="0.00" class="w-full bg-dark-bg border border-dark-border rounded-lg p-3 text-white focus:outline-none focus:border-brand-primary">
                                    <button class="bg-dark-bg border border-dark-border px-3 rounded-lg text-xs hover:text-white transition-colors">MAX</button>
                                </div>
                            </div>
                            <button class="w-full mt-4 bg-brand-primary hover:bg-brand-secondary text-white font-bold py-3 rounded-lg shadow-neon transition-all">
                                Send Assets
                            </button>
                        </div>

                        <div id="view-receive" class="hidden flex-col items-center justify-center h-full text-center space-y-4">
                            <div class="bg-white p-2 rounded-lg">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=0x123456789CryptoWalletAddress" alt="QR" class="w-32 h-32">
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">Your ERC-20 Address</p>
                                <div class="flex items-center gap-2 bg-dark-bg px-3 py-2 rounded-lg border border-dark-border">
                                    <span class="font-mono text-sm text-brand-secondary">0x71C...92B</span>
                                    <button class="text-gray-400 hover:text-white"><i class="fa-regular fa-copy"></i></button>
                                </div>
                            </div>
                        </div>

                        <div id="view-swap" class="hidden space-y-3">
                            <div class="bg-dark-bg p-3 rounded-lg border border-dark-border">
                                <div class="flex justify-between text-xs text-gray-400 mb-1">
                                    <span>You pay</span>
                                    <span>Balance: 2.4 ETH</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <input type="number" placeholder="0.0" class="bg-transparent text-xl font-bold text-white w-24 focus:outline-none">
                                    <button class="flex items-center gap-2 bg-dark-panel px-2 py-1 rounded-full border border-dark-border hover:border-brand-primary">
                                        <i class="fa-brands fa-ethereum text-blue-400"></i> ETH <i class="fa-solid fa-chevron-down text-xs"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="flex justify-center -my-2 relative z-10">
                                <div class="bg-dark-panel border border-dark-border p-2 rounded-full cursor-pointer hover:border-brand-primary transition-colors">
                                    <i class="fa-solid fa-arrow-down text-brand-primary"></i>
                                </div>
                            </div>

                            <div class="bg-dark-bg p-3 rounded-lg border border-dark-border">
                                <div class="flex justify-between text-xs text-gray-400 mb-1">
                                    <span>You receive</span>
                                    <span>~ $0.00</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <input type="number" placeholder="0.0" class="bg-transparent text-xl font-bold text-white w-24 focus:outline-none">
                                    <button class="flex items-center gap-2 bg-dark-panel px-2 py-1 rounded-full border border-dark-border hover:border-brand-primary">
                                        <i class="fa-solid fa-circle-dollar-to-slot text-green-400"></i> USDT <i class="fa-solid fa-chevron-down text-xs"></i>
                                    </button>
                                </div>
                            </div>

                            <button class="w-full bg-brand-primary hover:bg-brand-secondary text-white font-bold py-3 rounded-lg shadow-neon transition-all">
                                Swap Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="bots" class="py-20 bg-dark-bg border-t border-dark-border/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Smart <span class="text-brand-primary">Investment Bots</span></h2>
                <p class="text-gray-400 max-w-2xl mx-auto">Choose an automated strategy that fits your risk appetite. Our bots work 24/7 to maximize your APY.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-dark-panel border border-dark-border rounded-xl p-6 hover:shadow-neon transition-all duration-300 group hover:-translate-y-2">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center mb-4 text-blue-400 group-hover:text-white group-hover:bg-blue-500 transition-colors">
                        <i class="fa-solid fa-shield-halved text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Stable Saver</h3>
                    <p class="text-sm text-gray-400 mb-4">Low risk, steady returns using stablecoin arbitrage strategies.</p>
                    <div class="flex justify-between items-end border-t border-dark-border pt-4">
                        <div>
                            <p class="text-xs text-gray-500">APY</p>
                            <p class="text-2xl font-bold text-green-400">8.5%</p>
                        </div>
                        <button class="text-sm bg-dark-bg border border-dark-border px-4 py-2 rounded hover:text-brand-primary">Deploy</button>
                    </div>
                </div>

                <div class="relative bg-dark-panel border border-brand-primary rounded-xl p-6 shadow-neon transform scale-105 z-10">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-brand-primary text-white text-xs px-3 py-1 rounded-full uppercase font-bold tracking-wider">
                        Most Popular
                    </div>
                    <div class="w-12 h-12 bg-brand-primary/20 rounded-lg flex items-center justify-center mb-4 text-brand-primary group-hover:text-white transition-colors">
                        <i class="fa-solid fa-robot text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Neural Net AI</h3>
                    <p class="text-sm text-gray-400 mb-4">Machine learning powered trend analysis for blue-chip cryptos.</p>
                    <div class="flex justify-between items-end border-t border-dark-border pt-4">
                        <div>
                            <p class="text-xs text-gray-500">APY</p>
                            <p class="text-2xl font-bold text-brand-primary">24.0%</p>
                        </div>
                        <button class="text-sm bg-brand-primary text-white px-4 py-2 rounded hover:bg-brand-secondary shadow-lg">Deploy</button>
                    </div>
                </div>

                <div class="bg-dark-panel border border-dark-border rounded-xl p-6 hover:shadow-neon transition-all duration-300 group hover:-translate-y-2">
                    <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center mb-4 text-purple-400 group-hover:text-white group-hover:bg-purple-500 transition-colors">
                        <i class="fa-solid fa-rocket text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Degen Momentum</h3>
                    <p class="text-sm text-gray-400 mb-4">High risk, high reward strategy focusing on new token launches.</p>
                    <div class="flex justify-between items-end border-t border-dark-border pt-4">
                        <div>
                            <p class="text-xs text-gray-500">APY</p>
                            <p class="text-2xl font-bold text-purple-400">140%</p>
                        </div>
                        <button class="text-sm bg-dark-bg border border-dark-border px-4 py-2 rounded hover:text-brand-primary">Deploy</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="market" class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-8">
            <h2 class="text-3xl font-bold">Market <span class="text-gray-500">Overview</span></h2>
            <a href="#" class="text-brand-primary hover:underline">View All Assets</a>
        </div>
        
        <div class="bg-dark-panel border border-dark-border rounded-xl overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-dark-bg text-gray-400 text-sm uppercase">
                    <tr>
                        <th class="px-6 py-4">Asset</th>
                        <th class="px-6 py-4">Price</th>
                        <th class="px-6 py-4">24h Change</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-border text-sm">
                    <tr class="hover:bg-dark-bg/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <i class="fa-brands fa-bitcoin text-orange-500 text-2xl"></i>
                                <div>
                                    <div class="font-bold text-white">Bitcoin</div>
                                    <div class="text-gray-500 text-xs">BTC</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-mono text-white">$45,230.50</td>
                        <td class="px-6 py-4 text-green-400">+2.45%</td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-brand-primary hover:text-white border border-brand-primary hover:bg-brand-primary px-3 py-1 rounded transition-all">Trade</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-dark-bg/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <i class="fa-brands fa-ethereum text-blue-500 text-2xl"></i>
                                <div>
                                    <div class="font-bold text-white">Ethereum</div>
                                    <div class="text-gray-500 text-xs">ETH</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-mono text-white">$2,890.12</td>
                        <td class="px-6 py-4 text-red-400">-0.56%</td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-brand-primary hover:text-white border border-brand-primary hover:bg-brand-primary px-3 py-1 rounded transition-all">Trade</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-dark-bg/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 rounded-full bg-teal-500 flex items-center justify-center text-white text-xs font-bold">S</div>
                                <div>
                                    <div class="font-bold text-white">Solana</div>
                                    <div class="text-gray-500 text-xs">SOL</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-mono text-white">$98.45</td>
                        <td class="px-6 py-4 text-green-400">+5.12%</td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-brand-primary hover:text-white border border-brand-primary hover:bg-brand-primary px-3 py-1 rounded transition-all">Trade</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Wallet Tab Switching Logic
        function switchTab(tabName) {
            // 1. Hide all views
            document.getElementById('view-send').classList.add('hidden');
            document.getElementById('view-receive').classList.add('hidden');
            document.getElementById('view-receive').classList.remove('flex'); // Remove flex specifically for Receive
            document.getElementById('view-swap').classList.add('hidden');

            // 2. Reset tab styling (Text color and bottom border)
            const tabs = ['send', 'receive', 'swap'];
            tabs.forEach(t => {
                const el = document.getElementById('tab-' + t);
                el.classList.remove('text-brand-secondary', 'border-brand-primary');
                el.classList.add('text-gray-400', 'border-transparent');
            });

            // 3. Show selected view
            const selectedView = document.getElementById('view-' + tabName);
            selectedView.classList.remove('hidden');
            
            // Special layout case for Receive tab (needs flex for centering)
            if(tabName === 'receive') {
                selectedView.classList.add('flex');
            }

            // 4. Highlight selected tab
            const selectedTab = document.getElementById('tab-' + tabName);
            selectedTab.classList.remove('text-gray-400', 'border-transparent');
            selectedTab.classList.add('text-brand-secondary', 'border-brand-primary');
        }
    </script>
</body>
</html>