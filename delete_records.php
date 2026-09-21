<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

function showUsage(): void
{
    echo <<<HELP
Usage: php delete_records.php <command> [options]

Commands:
  user <id>              Delete a user by ID
  user email <email>     Delete a user by email
  booking <id>           Delete a booking by ID (cascades to items, addons, downpayments, post-productions, client accounts)
  delete-all-users       Delete all non-admin users
  delete-all-bookings    Delete all bookings (cascades to related records)
  list-users             List all users
  list-bookings          List all bookings

HELP;
    exit(1);
}

function listUsers(): void
{
    $users = DB::table('users')->select('id', 'name', 'email', 'role')->orderBy('id')->get();

    if ($users->isEmpty()) {
        echo "No users found.\n";
        return;
    }

    echo str_pad('ID', 5) . str_pad('Name', 20) . str_pad('Email', 30) . "Role\n";
    echo str_repeat('-', 65) . "\n";
    foreach ($users as $u) {
        echo str_pad($u->id, 5)
            . str_pad($u->name, 20)
            . str_pad($u->email, 30)
            . $u->role . "\n";
    }
}

function listBookings(): void
{
    $bookings = DB::table('bookings')
        ->select('id', 'client_name', 'client_email', 'event_date', 'status', 'total_price')
        ->orderBy('id')
        ->get();

    if ($bookings->isEmpty()) {
        echo "No bookings found.\n";
        return;
    }

    echo str_pad('ID', 5) . str_pad('Client', 20) . str_pad('Email', 25) . str_pad('Date', 12) . str_pad('Status', 12) . "Price\n";
    echo str_repeat('-', 90) . "\n";
    foreach ($bookings as $b) {
        echo str_pad($b->id, 5)
            . str_pad($b->client_name, 20)
            . str_pad($b->client_email ?? '', 25)
            . str_pad($b->event_date, 12)
            . str_pad($b->status, 12)
            . number_format((float) $b->total_price, 2) . "\n";
    }
}

function deleteUser(int $id): void
{
    $user = DB::table('users')->select('id', 'name', 'email', 'role')->where('id', $id)->first();

    if (!$user) {
        echo "User #$id not found.\n";
        exit(1);
    }

    if ($user->role === 'admin') {
        $count = DB::table('users')->where('role', 'admin')->count();
        if ($count <= 1) {
            echo "Cannot delete the last admin user.\n";
            exit(1);
        }
    }

    DB::table('users')->where('id', $id)->delete();
    echo "Deleted user: {$user->name} ({$user->email})\n";
}

function deleteUserByEmail(string $email): void
{
    $user = DB::table('users')->select('id', 'name', 'email', 'role')->where('email', $email)->first();

    if (!$user) {
        echo "User with email '$email' not found.\n";
        exit(1);
    }

    if ($user->role === 'admin') {
        $count = DB::table('users')->where('role', 'admin')->count();
        if ($count <= 1) {
            echo "Cannot delete the last admin user.\n";
            exit(1);
        }
    }

    DB::table('users')->where('id', $user->id)->delete();
    echo "Deleted user: {$user->name} ({$user->email})\n";
}

function deleteBooking(int $id): void
{
    $booking = DB::table('bookings')
        ->select('id', 'client_name', 'client_email', 'event_date', 'status')
        ->where('id', $id)
        ->first();

    if (!$booking) {
        echo "Booking #$id not found.\n";
        exit(1);
    }

    echo "Deleting booking #{$booking->id}: {$booking->client_name} ({$booking->event_date}) [{$booking->status}]\n";
    echo "  - Related booking_items, booking_addons, downpayments, post_productions, and client_accounts will be cascade-deleted.\n";

    DB::table('bookings')->where('id', $id)->delete();
    echo "Booking #$id deleted.\n";
}

function deleteAllUsers(): void
{
    $adminCount = DB::table('users')->where('role', 'admin')->count();
    $totalCount = DB::table('users')->count();

    if ($totalCount === 0) {
        echo "No users to delete.\n";
        return;
    }

    $nonAdminCount = DB::table('users')->where('role', '!=', 'admin')->count();

    if ($nonAdminCount === 0) {
        echo "Only admin user(s) remain. Nothing to delete (admins are protected).\n";
        return;
    }

    DB::table('users')->where('role', '!=', 'admin')->delete();
    echo "Deleted $nonAdminCount non-admin user(s). $adminCount admin(s) preserved.\n";
}

function deleteAllBookings(): void
{
    $count = DB::table('bookings')->count();

    if ($count === 0) {
        echo "No bookings to delete.\n";
        return;
    }

    echo "Deleting $count booking(s) and all related records (items, addons, downpayments, post-productions, client accounts)...\n";

    DB::table('bookings')->delete();
    echo "All bookings deleted.\n";
}

if ($argc < 2) {
    showUsage();
}

$command = $argv[1];

match ($command) {
    'list-users' => listUsers(),
    'list-bookings' => listBookings(),
    'delete-all-users' => deleteAllUsers(),
    'delete-all-bookings' => deleteAllBookings(),
    'user' => isset($argv[2]) && $argv[2] === 'email'
        ? deleteUserByEmail($argv[3] ?? showUsage())
        : deleteUser((int) ($argv[2] ?? showUsage())),
    'booking' => deleteBooking((int) ($argv[2] ?? showUsage())),
    default => showUsage(),
};