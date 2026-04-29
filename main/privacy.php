<?php
include '../config/config.php';
?>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - <?php echo $sitename; ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Space Grotesk', 'sans-serif'],
                    },
                    colors: {
                        brand: { primary: '#6366F1', secondary: '#818CF8', accent: '#4F46E5', dark: '#312E81' },
                        dark: { bg: '#02040a', panel: '#0B0F19', card: '#111827', border: '#1E293B', text: '#E2E8F0', muted: '#94A3B8' }
                    }
                }
            }
        }
    </script>
    <style>body { background-color: #02040a; color: #E2E8F0; }</style>
</head>
<body class="antialiased overflow-x-hidden selection:bg-brand-primary selection:text-white">

    <!-- Navbar -->
    <nav class="sticky top-0 z-40 bg-dark-bg/90 backdrop-blur-md border-b border-dark-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="index.php" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-brand-primary flex items-center justify-center shadow-neon">
                        <span class="font-bold text-white text-xl">TA</span>
                    </div>
                    <span class="font-sans font-bold text-xl tracking-tight text-white"><?php echo $sitename; ?></span>
                </a>
                <div class="flex items-center gap-4">
                    <a href="index.php" class="text-sm font-medium hover:text-brand-primary transition-colors text-gray-300">Back to Home</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <section class="py-20 relative">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl md:text-5xl font-display font-black text-white mb-4">Privacy <span class="text-brand-primary">Policy</span></h1>
            <p class="text-dark-muted mb-10">Last updated: <?php echo date("F d, Y"); ?></p>

            <div class="space-y-8 text-gray-300 bg-dark-panel p-8 rounded-2xl border border-dark-border shadow-2xl">
                
                <div>
                    <h2 class="text-2xl font-bold text-white mb-3">1. Information We Collect</h2>
                    <p class="leading-relaxed">
                        At <?php echo $sitename; ?>, we collect information that you provide directly to us when you open an account, verify your identity (KYC), make a transaction, or communicate with our support team. This may include your name, email address, physical address, financial details, and digital wallet addresses.
                    </p>
                </div>

                <div>
                    <h2 class="text-2xl font-bold text-white mb-3">2. How We Use Your Information</h2>
                    <p class="leading-relaxed">
                        We use the information we collect to operate, maintain, and improve our services. This includes processing transactions, issuing virtual cards, calculating staking rewards, complying with Anti-Money Laundering (AML) regulations, and protecting against malicious or fraudulent activities.
                    </p>
                </div>

                <div>
                    <h2 class="text-2xl font-bold text-white mb-3">3. Blockchain Data Security</h2>
                    <p class="leading-relaxed">
                        Due to the public nature of blockchain technology, transactions involving crypto assets are public and traceable. However, <?php echo $sitename; ?> uses military-grade encryption to protect your private keys and personal data on our internal servers.
                    </p>
                </div>

                <div>
                    <h2 class="text-2xl font-bold text-white mb-3">4. Third-Party Sharing</h2>
                    <p class="leading-relaxed">
                        We do not sell your personal data. We may share necessary information with trusted third-party service providers (such as card issuers or KYC verifiers) strictly to facilitate the services we provide to you.
                    </p>
                </div>

                <div>
                    <h2 class="text-2xl font-bold text-white mb-3">5. Contact Us</h2>
                    <p class="leading-relaxed">
                        If you have any questions regarding this Privacy Policy or how we handle your data, please contact our support team at <strong class="text-brand-primary"><?php echo $site_email; ?></strong>.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark-bg pt-16 pb-8 border-t border-dark-border mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-12">
                <div>
                    <h3 class="text-xl font-bold text-white mb-4"><?php echo $sitename; ?></h3>
                    <p class="text-sm text-dark-muted">The future of decentralized finance, investment, and payments.</p>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm text-dark-muted">
                        <li><a href="index.php#wallet" class="hover:text-brand-primary">Wallet</a></li>
                        <li><a href="index.php#cards" class="hover:text-brand-primary">Card</a></li>
                        <li><a href="index.php#invest" class="hover:text-brand-primary">Plans</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-4">Legal</h4>
                    <ul class="space-y-2 text-sm text-dark-muted">
                        <li><a href="privacy.php" class="hover:text-brand-primary text-white">Privacy Policy</a></li>
                        <li><a href="terms.php" class="hover:text-brand-primary">Terms of Service</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-4">Contact</h4>
                    <ul class="space-y-2 text-sm text-dark-muted">
                        <li><?php echo $site_email; ?></li>
                        <li><?php echo $site_phone ?? 'Support Hotline'; ?></li>
                    </ul>
                </div>
            </div>
            <div class="text-center text-xs text-dark-muted pt-8 border-t border-dark-border">
                &copy; <?php echo date("Y"); ?> <?php echo $sitename; ?>. All rights reserved.
            </div>
        </div>
    </footer>
</body>
</html>