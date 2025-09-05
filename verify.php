<?php
require_once __DIR__ . "/../config/db.php";

$status = "error";
$message = "❌ No token provided.";
$redirect = "/BARANGAY_INFORMATION_SYSTEM/frontend/pages/login.php";

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $pdo->prepare("SELECT * FROM verifications WHERE token = ? AND expires_at > NOW()");
    $stmt->execute([$token]);
    $verification = $stmt->fetch();

    if ($verification) {
        $user_id = $verification['user_id'];

        // mark user as Verified
        $stmt = $pdo->prepare("UPDATE users SET status = 'Verified' WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // mark verification as used
        $stmt = $pdo->prepare("UPDATE verifications SET verified_at = NOW() WHERE verification_id = ?");
        $stmt->execute([$verification['verification_id']]);

        $status = "success";
        $message = "✅ Your account has been verified! Redirecting to login...";
        $redirect = "/BARANGAY_INFORMATION_SYSTEM/frontend/pages/login.php";
    } else {
        $status = "error";
        $message = "❌ Invalid or expired token. Redirecting to signup...";
        $redirect = "/BARANGAY_INFORMATION_SYSTEM/frontend/paages/signup.php";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Auto redirect after 3 seconds -->
    <meta http-equiv="refresh" content="3;url=<?= $redirect ?>">
</head>

<body class="bg-gray-900 flex items-center justify-center min-h-screen">
    <div class="bg-white/10 backdrop-blur-md p-8 rounded-2xl shadow-lg w-full max-w-md text-center">
        <?php if ($status === "success"): ?>
            <div class="text-green-400 text-6xl mb-4">✔</div>
            <h1 class="text-2xl font-bold text-green-400 mb-2">Account Verified</h1>
            <p class="text-gray-200 mb-6"><?= htmlspecialchars($message) ?></p>
            <a href="<?= $redirect ?>"
                class="px-6 py-2 bg-green-500 text-white rounded-lg font-medium hover:bg-green-600 transition">
                Go to Login
            </a>
        <?php else: ?>
            <div class="text-red-400 text-6xl mb-4">✖</div>
            <h1 class="text-2xl font-bold text-red-400 mb-2">Verification Failed</h1>
            <p class="text-gray-200 mb-6"><?= htmlspecialchars($message) ?></p>
            <a href="<?= $redirect ?>"
                class="px-6 py-2 bg-red-500 text-white rounded-lg font-medium hover:bg-red-600 transition">
                Try Again
            </a>
        <?php endif; ?>
        <p class="text-gray-400 text-sm mt-4">Redirecting in 3 seconds...</p>
    </div>
</body>

</html>
