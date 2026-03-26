<?php
require_once 'includes/init.php';

$user = new User();
$test_user = 'test_pw_change_' . time();
$password = 'Password123';
$new_pw = 'NewPassword456';

echo "1. Registering test user: $test_user\n";
$res = $user->register([
    'username' => $test_user,
    'password' => $password,
    'full_name' => 'Test User',
    'email' => $test_user . '@example.com'
]);

if (!$res['success']) {
    die("Failed to register: " . $res['message'] . "\n");
}
$user_id = $res['user_id'];
echo "User registered with ID: $user_id\n";

echo "\n2. Changing password (wrong old password)...\n";
$res = $user->changePassword($user_id, 'WrongPassword', $new_pw);
echo "Result: " . ($res['success'] ? 'SUCCESS' : 'FAILED') . " - " . $res['message'] . "\n";

echo "\n3. Changing password (weak new password)...\n";
$res = $user->changePassword($user_id, $password, 'weak');
echo "Result: " . ($res['success'] ? 'SUCCESS' : 'FAILED') . " - " . $res['message'] . "\n";

echo "\n4. Changing password (correct)...\n";
$res = $user->changePassword($user_id, $password, $new_pw);
echo "Result: " . ($res['success'] ? 'SUCCESS' : 'FAILED') . " - " . $res['message'] . "\n";

echo "\n5. Try login with new password...\n";
$res = $user->login($test_user, $new_pw);
echo "Result: " . ($res['success'] ? 'SUCCESS' : 'FAILED') . " - " . $res['message'] . "\n";

echo "\nCleaning up...\n";
$db = getDB();
$db->exec("DELETE FROM users WHERE user_id = $user_id");
echo "Done.\n";
