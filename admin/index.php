<?php
include 'header.php';

// --- HELPER FUNCTIONS ---
function get_sum($link, $table, $column, $condition = "") {
    $sql = "SELECT SUM($column) as total FROM $table $condition";
    $query = mysqli_query($link, $sql);
    if($query) {
        $row = mysqli_fetch_assoc($query);
        return $row['total'] ?? 0;
    }
    return 0;
}

function get_count($link, $table, $condition = "") {
    $sql = "SELECT COUNT(*) as total FROM $table $condition";
    $query = mysqli_query($link, $sql);
    if($query) {
        $row = mysqli_fetch_assoc($query);
        return $row['total'];
    }
    return 0;
}

// --- 1. GET COUNTS (Metrics) ---
$total_users = get_count($link, "users");
$pending_deposits = get_count($link, "transactions", "WHERE type='deposit' AND status='pending'");
$pending_withdrawals = get_count($link, "transactions", "WHERE type='withdrawal' AND status='pending'");
$active_bots = get_count($link, "user_bots", "WHERE status='running'");
$active_investments = get_count($link, "investments", "WHERE status='active'");
$active_cards = get_count($link, "virtual_cards", "WHERE status='active'");

// --- 2. GET FINANCIAL TOTALS (Money Volume) ---
$total_deposits_vol = get_sum($link, "transactions", "amount_usd", "WHERE type='deposit' AND status='completed'");
$total_withdrawals_vol = get_sum($link, "transactions", "amount_usd", "WHERE type='withdrawal' AND status='completed'");
$total_invested_cap = get_sum($link, "investments", "amount", "WHERE status='active'");
$total_bot_cap = get_sum($link, "user_bots", "amount_invested", "WHERE status='running'");

// Recent Transactions
$recent_q = mysqli_query($link, "SELECT t.*, u.username AS fullname, u.email FROM transactions t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC LIMIT 8");
?>

<div class="flex-1 overflow-y-auto p-6 space-y-8">

    <div>
        <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4">Operational Metrics</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            
            <div class="glass-panel p-4 rounded-xl bg-white shadow-sm border border-slate-200">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center"><i class="fa-solid fa-users"></i></div>
                    <p class="text-xs font-bold text-slate-500 uppercase">Users</p>
                </div>
                <h3 class="text-2xl font-extrabold text-slate-800"><?php echo number_format($total_users); ?></h3>
            </div>

            <div class="glass-panel p-4 rounded-xl bg-white shadow-sm border border-green-200 <?php echo ($pending_deposits > 0) ? 'animate-pulse bg-green-50' : ''; ?>">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-green-100 text-green-600 flex items-center justify-center"><i class="fa-solid fa-arrow-down"></i></div>
                    <p class="text-xs font-bold text-slate-500 uppercase">Pending Dep.</p>
                </div>
                <h3 class="text-2xl font-extrabold text-slate-800"><?php echo number_format($pending_deposits); ?></h3>
            </div>

            <div class="glass-panel p-4 rounded-xl bg-white shadow-sm border border-orange-200 <?php echo ($pending_withdrawals > 0) ? 'animate-pulse bg-orange-50' : ''; ?>">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center"><i class="fa-solid fa-arrow-up"></i></div>
                    <p class="text-xs font-bold text-slate-500 uppercase">Pending W/D</p>
                </div>
                <h3 class="text-2xl font-extrabold text-slate-800"><?php echo number_format($pending_withdrawals); ?></h3>
            </div>

            <div class="glass-panel p-4 rounded-xl bg-white shadow-sm border border-slate-200">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center"><i class="fa-solid fa-robot"></i></div>
                    <p class="text-xs font-bold text-slate-500 uppercase">Active Bots</p>
                </div>
                <h3 class="text-2xl font-extrabold text-slate-800"><?php echo number_format($active_bots); ?></h3>
            </div>

            <div class="glass-panel p-4 rounded-xl bg-white shadow-sm border border-slate-200">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fa-solid fa-chart-pie"></i></div>
                    <p class="text-xs font-bold text-slate-500 uppercase">Active Plans</p>
                </div>
                <h3 class="text-2xl font-extrabold text-slate-800"><?php echo number_format($active_investments); ?></h3>
            </div>

            <div class="glass-panel p-4 rounded-xl bg-white shadow-sm border border-slate-200">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-pink-50 text-pink-600 flex items-center justify-center"><i class="fa-regular fa-credit-card"></i></div>
                    <p class="text-xs font-bold text-slate-500 uppercase">Active Cards</p>
                </div>
                <h3 class="text-2xl font-extrabold text-slate-800"><?php echo number_format($active_cards); ?></h3>
            </div>

        </div>
    </div>

    <div>
        <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4">Financial Volume</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <div class="glass-panel p-6 rounded-2xl bg-white shadow-sm border-l-4 border-green-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Total Deposited</p>
                        <h3 class="text-2xl font-extrabold text-slate-800 mt-1">$<?php echo number_format($total_deposits_vol, 2); ?></h3>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center"><i class="fa-solid fa-wallet"></i></div>
                </div>
            </div>

            <div class="glass-panel p-6 rounded-2xl bg-white shadow-sm border-l-4 border-red-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Total Withdrawn</p>
                        <h3 class="text-2xl font-extrabold text-slate-800 mt-1">$<?php echo number_format($total_withdrawals_vol, 2); ?></h3>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center"><i class="fa-solid fa-money-bill-transfer"></i></div>
                </div>
            </div>

            <div class="glass-panel p-6 rounded-2xl bg-white shadow-sm border-l-4 border-blue-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Invested Capital</p>
                        <h3 class="text-2xl font-extrabold text-slate-800 mt-1">$<?php echo number_format($total_invested_cap, 2); ?></h3>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fa-solid fa-chart-line"></i></div>
                </div>
            </div>

            <div class="glass-panel p-6 rounded-2xl bg-white shadow-sm border-l-4 border-purple-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Bot Capital</p>
                        <h3 class="text-2xl font-extrabold text-slate-800 mt-1">$<?php echo number_format($total_bot_cap, 2); ?></h3>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center"><i class="fa-solid fa-robot"></i></div>
                </div>
            </div>

        </div>
    </div>

    <div class="grid grid-cols-1 gap-6">
        <div class="glass-panel rounded-2xl bg-white shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-lg text-slate-800">Recent Activity</h3>
                <a href="transactions.php" class="text-xs font-bold text-indigo-600 hover:underline">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="text-xs text-slate-400 uppercase bg-slate-50">
                        <tr>
                            <th class="py-3 pl-6 font-semibold">User</th>
                            <th class="py-3 font-semibold">Type</th>
                            <th class="py-3 font-semibold">Amount</th>
                            <th class="py-3 text-center font-semibold">Status</th>
                            <th class="py-3 text-right pr-6 font-semibold">Date</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-100">
                        <?php if($recent_q && mysqli_num_rows($recent_q) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($recent_q)): 
                                $status_badge = match($row['status']) {
                                    'completed' => 'bg-green-100 text-green-700',
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'failed', 'rejected' => 'bg-red-100 text-red-700',
                                    default => 'bg-slate-100 text-slate-600'
                                };
                                $type_label = str_replace('_', ' ', $row['type']);
                            ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3 pl-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-500 border border-slate-200">
                                            <?php echo strtoupper(substr($row['fullname'] ?? 'U', 0, 1)); ?>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800"><?php echo htmlspecialchars($row['fullname'] ?? 'User'); ?></p>
                                            <p class="text-xs text-slate-500"><?php echo htmlspecialchars($row['email']); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 capitalize text-slate-600 font-medium">
                                    <?php echo $type_label; ?>
                                </td>
                                <td class="py-3 font-mono font-bold text-slate-800">
                                    $<?php echo number_format($row['amount_usd'], 2); ?>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="px-2.5 py-1 rounded-md text-xs font-bold <?php echo $status_badge; ?> capitalize">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td class="py-3 text-right pr-6 text-slate-500 text-xs">
                                    <?php echo date("M d, H:i", strtotime($row['created_at'])); ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="py-8 text-center text-slate-400 italic">No recent activity found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<?php include 'footer.php'; ?>