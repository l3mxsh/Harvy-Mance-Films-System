<?php

$dbPath = __DIR__ . '/database/database.sqlite';

if (!file_exists($dbPath)) {
    die("Database not found at: $dbPath\n");
}

$db = new PDO('sqlite:' . $dbPath);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function showUsage(): void
{
    echo <<<HELP
Usage: php delete_records.php <command> [options]

Commands:
  user <id>              Delete a user by ID
  user email <email>     Delete a user by email
  booking <id>           Delete a booking by ID (cascades to items, addons, downpayments, post-productions, customer accounts)
  delete-all-users       Delete all non-admin users
  delete-all-bookings    Delete all bookings (cascades to related records)
  list-users             List all users
  list-bookings          List all bookings

HELP;
    exit(1);
}

function listUsers(PDO $db): void
{
    $stmt = $db->query("SELECT id, name, email, role FROM users ORDER BY id");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($users)) {
        echo "No users found.\n";
        return;
    }

    echo str_pad('ID', 5) . str_pad('Name', 20) . str_pad('Email', 30) . "Role\n";
    echo str_repeat('-', 65) . "\n";
    foreach ($users as $u) {
        echo str_pad($u['id'], 5)
            . str_pad($u['name'], 20)
            . str_pad($u['email'], 30)
            . $u['role'] . "\n";
    }
}

function listBookings(PDO $db): void
{
    $stmt = $db->query("SELECT id, client_name, client_email, event_date, status, total_price FROM bookings ORDER BY id");
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($bookings)) {
        echo "No bookings found.\n";
        return;
    }

    echo str_pad('ID', 5) . str_pad('Client', 20) . str_pad('Email', 25) . str_pad('Date', 12) . str_pad('Status', 12) . "Price\n";
    echo str_repeat('-', 90) . "\n";
    foreach ($bookings as $b) {
        echo str_pad($b['id'], 5)
            . str_pad($b['client_name'], 20)
            . str_pad($b['client_email'] ?? '', 25)
            . str_pad($b['event_date'], 12)
            . str_pad($b['status'], 12)
            . number_format($b['total_price'], 2) . "\n";
    }
}

function deleteUser(PDO $db, int $id): void
{
    $stmt = $db->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "User #$id not found.\n";
        exit(1);
    }

    if ($user['role'] === 'admin') {
        $count = $db->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
        if ($count <= 1) {
            echo "Cannot delete the last admin user.\n";
            exit(1);
        }
    }

    $db->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    echo "Deleted user: {$user['name']} ({$user['email']})\n";
}

function deleteUserByEmail(PDO $db, string $email): void
{
    $stmt = $db->prepare("SELECT id, name, email, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "User with email '$email' not found.\n";
        exit(1);
    }

    if ($user['role'] === 'admin') {
        $count = $db->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
        if ($count <= 1) {
            echo "Cannot delete the last admin user.\n";
            exit(1);
        }
    }

    $db->prepare("DELETE FROM users WHERE id = ?")->execute([$user['id']]);
    echo "Deleted user: {$user['name']} ({$user['email']})\n";
}

function deleteBooking(PDO $db, int $id): void
{
    $stmt = $db->prepare("SELECT id, client_name, client_email, event_date, status FROM bookings WHERE id = ?");
    $stmt->execute([$id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        echo "Booking #$id not found.\n";
        exit(1);
    }

    echo "Deleting booking #{$booking['id']}: {$booking['client_name']} ({$booking['event_date']}) [{$booking['status']}]\n";
    echo "  - Related booking_items, booking_addons, downpayments, post_productions, and customer_accounts will be cascade-deleted.\n";

    $db->prepare("DELETE FROM bookings WHERE id = ?")->execute([$id]);
    echo "Booking #$id deleted.\n";
}

function deleteAllUsers(PDO $db): void
{
    $adminCount = $db->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
    $totalCount = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();

    if ($totalCount === 0) {
        echo "No users to delete.\n";
        return;
    }

    $nonAdminCount = $db->query("SELECT COUNT(*) FROM users WHERE role != 'admin'")->fetchColumn();

    if ($nonAdminCount === 0) {
        echo "Only admin user(s) remain. Nothing to delete (admins are protected).\n";
        return;
    }

    $db->exec("DELETE FROM users WHERE role != 'admin'");
    echo "Deleted $nonAdminCount non-admin user(s). $adminCount admin(s) preserved.\n";
}

function deleteAllBookings(PDO $db): void
{
    $count = $db->query("SELECT COUNT(*) FROM bookings")->fetchColumn();

    if ($count === 0) {
        echo "No bookings to delete.\n";
        return;
    }

    echo "Deleting $count booking(s) and all related records (items, addons, downpayments, post-productions, customer accounts)...\n";

    $db->exec("DELETE FROM bookings");
    echo "All bookings deleted.\n";
}

if ($argc < 2) {
    showUsage();
}

$command = $argv[1];

match ($command) {
    'list-users' => listUsers($db),
    'list-bookings' => listBookings($db),
    'delete-all-users' => deleteAllUsers($db),
    'delete-all-bookings' => deleteAllBookings($db),
    'user' => isset($argv[2]) && $argv[2] === 'email'
        ? deleteUserByEmail($db, $argv[3] ?? showUsage())
        : deleteUser($db, (int)($argv[2] ?? showUsage())),
    'booking' => deleteBooking($db, (int)($argv[2] ?? showUsage())),
    default => showUsage(),
};
