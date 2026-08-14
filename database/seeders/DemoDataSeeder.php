<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Addon;
use App\Models\Booking;
use App\Models\CancellationRequest;
use App\Models\ClientAccount;
use App\Models\Downpayment;
use App\Models\OutsourcedStaff;
use App\Models\Package;
use App\Models\PostProduction;
use App\Models\PostProductionTask;
use App\Models\RescheduleRequest;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\StaffSchedule;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    private array $teams = [];

    private array $staff = [];

    private array $outsourced = [];

    private $packages = null;

    private $addons = null;

    /**
     * Run the database seeds.
     *
     * NOTE: This is a demo seeder. It wipes all workflow/transactional tables
     * (bookings, payments, staff, teams, logs, etc.) and rebuilds them with
     * realistic data so every screen has content. Packages, add-ons and
     * inventory created by the other seeders are kept.
     */
    public function run(): void
    {
        $this->cleanup();

        $this->createSettings();
        $this->createStaffAndTeams();
        $this->loadReferenceData();
        $this->createBookingsAndPayments();
        $this->createSchedules();
        $this->createReschedules();
        $this->createCancellations();
        $this->createPostProduction();
        $this->createActivityLogs();

        $this->printSummary();
    }

    /* ======================================================================
     * CLEANUP
     * ==================================================================== */

    private function cleanup(): void
    {
        PostProductionTask::query()->delete();
        PostProduction::query()->delete();
        CancellationRequest::query()->delete();
        RescheduleRequest::query()->delete();
        Downpayment::query()->delete();
        ClientAccount::query()->delete();
        StaffSchedule::query()->delete();
        DB::table('booking_items')->delete();
        DB::table('booking_addons')->delete();
        Booking::query()->delete();
        ActivityLog::query()->delete();
        DB::table('outsourced_staff_team')->delete();
        DB::table('staff_team')->delete();
        OutsourcedStaff::query()->delete();
        Staff::query()->delete();
        Team::query()->delete();
    }

    /* ======================================================================
     * SETTINGS
     * ==================================================================== */

    private function createSettings(): void
    {
        Setting::setValue('client_auto_delete_days', '30');
        Setting::setValue('reschedule_lead_time_days', '5');
        Setting::setValue('refund_policy', json_encode([
            ['days' => 14, 'percent' => 100],
            ['days' => 7,  'percent' => 50],
            ['days' => 0,  'percent' => 0],
        ]));
    }

    /* ======================================================================
     * STAFF + TEAMS
     * ==================================================================== */

    private function createStaffAndTeams(): void
    {
        $definitions = [
            ['name' => 'Juan Dela Cruz',    'email' => 'juan@harvymance.com',    'contact_number' => '0917-111-1111', 'status' => 'active',   'is_outsourced' => false, 'is_temporary' => false, 'temp_expires_at' => null],
            ['name' => 'Maria Santos',      'email' => 'maria@harvymance.com',   'contact_number' => '0917-222-2222', 'status' => 'active',   'is_outsourced' => false, 'is_temporary' => false, 'temp_expires_at' => null],
            ['name' => 'Jose Rizal',        'email' => 'jose@harvymance.com',    'contact_number' => '0917-333-3333', 'status' => 'active',   'is_outsourced' => false, 'is_temporary' => false, 'temp_expires_at' => null],
            ['name' => 'Andres Bonifacio',  'email' => 'andres@harvymance.com',  'contact_number' => '0917-444-4444', 'status' => 'active',   'is_outsourced' => false, 'is_temporary' => false, 'temp_expires_at' => null],
            ['name' => 'Gabriela Silang',   'email' => 'gabriela@harvymance.com','contact_number' => '0917-555-5555', 'status' => 'active',   'is_outsourced' => false, 'is_temporary' => false, 'temp_expires_at' => null],
            ['name' => 'Diego Silang',      'email' => 'diego@harvymance.com',   'contact_number' => '0917-666-6666', 'status' => 'inactive', 'is_outsourced' => false, 'is_temporary' => false, 'temp_expires_at' => null],
            ['name' => 'Rachel Villanueva', 'email' => 'rachel@harvymance.com',  'contact_number' => '0917-777-7777', 'status' => 'active',   'is_outsourced' => true,  'is_temporary' => false, 'temp_expires_at' => null],
            ['name' => 'Mark Anthony Reyes','email' => 'mark@harvymance.com',    'contact_number' => '0917-888-8888', 'status' => 'active',   'is_outsourced' => true,  'is_temporary' => true,  'temp_expires_at' => now()->addDays(30)],
        ];

        foreach ($definitions as $def) {
            $staff = Staff::create([
                'name'            => $def['name'],
                'email'           => $def['email'],
                'password'        => 'password',
                'contact_number'  => $def['contact_number'],
                'status'          => $def['status'],
                'is_outsourced'   => $def['is_outsourced'],
                'is_temporary'    => $def['is_temporary'],
                'temp_expires_at' => $def['temp_expires_at'],
                'last_login_at'   => in_array($def['email'], [
                    'juan@harvymance.com', 'maria@harvymance.com', 'rachel@harvymance.com',
                ]) ? now()->subHours(random_int(2, 72)) : null,
            ]);
            $this->staff[$def['email']] = $staff;
        }

        $alpha = Team::create(['name' => 'Team Alpha', 'description' => 'Primary photography and videography team.', 'status' => 'active']);
        $beta  = Team::create(['name' => 'Team Beta',  'description' => 'Secondary team for events and coverage.',      'status' => 'active']);
        $gamma = Team::create(['name' => 'Team Gamma', 'description' => 'Special events and drone coverage team.',      'status' => 'active']);

        $alpha->members()->attach([
            $this->staff['juan@harvymance.com']->id,
            $this->staff['maria@harvymance.com']->id,
            $this->staff['jose@harvymance.com']->id,
        ]);

        $beta->members()->attach([
            $this->staff['andres@harvymance.com']->id,
            $this->staff['gabriela@harvymance.com']->id,
            $this->staff['rachel@harvymance.com']->id,
        ]);

        $gamma->members()->attach([
            $this->staff['juan@harvymance.com']->id,
            $this->staff['mark@harvymance.com']->id,
        ]);

        $sarah = OutsourcedStaff::create([
            'name'           => 'Sarah Lim',
            'email'          => 'sarah.lim@outsourced.ph',
            'contact_number' => '0917-999-0001',
            'notes'          => 'Freelance photo editor, Manila-based.',
        ]);

        $kim = OutsourcedStaff::create([
            'name'           => 'Kim Tan',
            'email'          => 'kim.tan@outsourced.ph',
            'contact_number' => '0917-999-0002',
            'notes'          => 'Freelance colorist for video projects.',
        ]);

        $this->outsourced['sarah'] = $sarah;
        $this->outsourced['kim'] = $kim;

        $beta->outsourcedMembers()->attach($sarah->id);
        $gamma->outsourcedMembers()->attach($kim->id);

        $this->teams['alpha'] = $alpha;
        $this->teams['beta']  = $beta;
        $this->teams['gamma'] = $gamma;
    }

    /* ======================================================================
     * REFERENCE DATA
     * ==================================================================== */

    private function loadReferenceData(): void
    {
        $this->packages = Package::all()->keyBy('name');
        $this->addons = Addon::all()->keyBy('name');
    }

    /* ======================================================================
     * BOOKINGS + PAYMENTS
     * ==================================================================== */

    private function createBookingsAndPayments(): void
    {
        $wedding = $this->packages['Wedding Package']->id;
        $debut   = $this->packages['Debut Package']->id;
        $event   = $this->packages['Event Package']->id;

        $drone = $this->addons['Drone Aerial Coverage']->id;
        $sde   = $this->addons['Same-Day Edit (SDE) Video']->id;
        $teaser = $this->addons['Teaser Video']->id;
        $social = $this->addons['Social Media Content Package']->id;
        $preevent = $this->addons['Pre-Event Shoot']->id;
        $live  = $this->addons['Live Streaming Service']->id;
        $rush  = $this->addons['Rush Delivery']->id;
        $premiumAlbum = $this->addons['Premium Album Upgrade']->id;
        $print = $this->addons['Print Package Upgrade']->id;
        $cloud = $this->addons['Cloud Storage Extension']->id;

        /* ----------------------------------------------------------------
         * 1. PENDING (no payment submitted)
         * -------------------------------------------------------------- */
        $pkg1 = $this->makeBooking([
            'booking_ref'    => 'HMF-2608-PND1',
            'package_id'     => $wedding,
            'addon_ids'      => [$drone, $live],
            'event_type'     => 'Church Wedding',
            'client_name'    => 'Maria Clara Dela Cruz',
            'client_email'   => 'mariaclara.delacruz@gmail.com',
            'client_phone'   => '0917-234-5678',
            'event_date'     => '2026-09-05',
            'event_time'     => '09:00',
            'event_venue'    => 'Immaculate Conception Parish',
            'event_address'  => 'Bacoor City, Cavite',
            'event_description' => 'Full Catholic wedding coverage with drone shots and live streaming for overseas relatives.',
            'notes'          => 'Couple would like extra shots near the garden after the ceremony.',
            'status'         => 'pending',
            'item_status'    => 'reserved',
            'account'        => ['must_change_password' => true],
        ]);

        $pkg2 = $this->makeBooking([
            'booking_ref'    => 'HMF-2608-PND2',
            'package_id'     => $debut,
            'addon_ids'      => [$preevent],
            'event_type'     => 'Debut Party',
            'client_name'    => 'Angelica Reyes',
            'client_email'   => 'angelica.reyes@gmail.com',
            'client_phone'   => '0918-345-6789',
            'event_date'     => '2026-09-13',
            'event_time'     => '16:00',
            'event_venue'    => 'Twin Lakes Country Club',
            'event_address'  => 'Tagaytay City, Cavite',
            'event_description' => '18th birthday debut with pre-debut shoot.',
            'notes'          => null,
            'status'         => 'pending',
            'item_status'    => 'reserved',
            'account'        => ['must_change_password' => true],
        ]);

        /* ----------------------------------------------------------------
         * 2. APPROVED (assigned team, no payment yet) + pending reschedule
         * -------------------------------------------------------------- */
        $app1 = $this->makeBooking([
            'booking_ref'    => 'HMF-2608-APR1',
            'package_id'     => $wedding,
            'addon_ids'      => [$teaser],
            'event_type'     => 'Church Wedding',
            'client_name'    => 'Katrina Villanueva',
            'client_email'   => 'katrina.villanueva@yahoo.com',
            'client_phone'   => '0919-456-7890',
            'event_date'     => '2026-09-19',
            'event_time'     => '09:00',
            'event_venue'    => 'St. Michael the Archangel Church',
            'event_address'  => 'Molino, Bacoor City, Cavite',
            'event_description' => 'Wedding with teaser video for social media.',
            'notes'          => null,
            'status'         => 'approved',
            'team_id'        => $this->teams['alpha']->id,
            'item_status'    => 'reserved',
            'account'        => ['must_change_password' => true],
        ]);

        /* ----------------------------------------------------------------
         * 3. APPROVED + payment submitted (downpayment pending review)
         * -------------------------------------------------------------- */
        $app2 = $this->makeBooking([
            'booking_ref'    => 'HMF-2608-APR2',
            'package_id'     => $event,
            'addon_ids'      => [$live, $cloud],
            'event_type'     => 'Corporate Event',
            'client_name'    => 'Paolo & Andrea Ramos',
            'client_email'   => 'paolo.andrea.ramos@gmail.com',
            'client_phone'   => '0920-567-8901',
            'event_date'     => '2026-09-26',
            'event_time'     => '14:00',
            'event_venue'    => 'SMX Convention Center',
            'event_address'  => 'Seashell Lane, Pasay City',
            'event_description' => 'Product launch event with livestream coverage.',
            'notes'          => null,
            'status'         => 'approved',
            'team_id'        => $this->teams['beta']->id,
            'item_status'    => 'reserved',
            'account'        => ['must_change_password' => true],
        ]);

        /* ----------------------------------------------------------------
         * 4. ONGOING (downpayment verified) + rejected reschedule
         * -------------------------------------------------------------- */
        $ong1 = $this->makeBooking([
            'booking_ref'    => 'HMF-2608-ONG1',
            'package_id'     => $wedding,
            'addon_ids'      => [$sde],
            'event_type'     => 'Wedding',
            'client_name'    => 'Carmela Santos',
            'client_email'   => 'carmela.santos@gmail.com',
            'client_phone'   => '0921-678-9012',
            'event_date'     => '2026-08-22',
            'event_time'     => '10:00',
            'event_venue'    => 'Villa Milagros Garden',
            'event_address'  => 'Tagaytay City, Cavite',
            'event_description' => 'Garden wedding with same-day edit video.',
            'notes'          => null,
            'status'         => 'ongoing',
            'team_id'        => $this->teams['alpha']->id,
            'item_status'    => 'in_use',
            'account'        => ['must_change_password' => false],
        ]);

        /* ----------------------------------------------------------------
         * 5. APPROVED + payment submitted (rejected row + resubmitted row)
         *    Shows the "multiple payment rows" behavior.
         * -------------------------------------------------------------- */
        $app3 = $this->makeBooking([
            'booking_ref'    => 'HMF-2608-APR3',
            'package_id'     => $wedding,
            'addon_ids'      => [$social, $rush],
            'event_type'     => 'Wedding',
            'client_name'    => 'Jenny Dela Vega',
            'client_email'   => 'jenny.delavega@gmail.com',
            'client_phone'   => '0922-789-0123',
            'event_date'     => '2026-08-23',
            'event_time'     => '09:00',
            'event_venue'    => 'San Antonio de Padua Church',
            'event_address'  => 'Pila, Laguna',
            'event_description' => 'Wedding with social media content package.',
            'notes'          => null,
            'status'         => 'approved',
            'team_id'        => $this->teams['alpha']->id,
            'item_status'    => 'reserved',
            'account'        => ['must_change_password' => false],
        ]);

        /* ----------------------------------------------------------------
         * 6. APPROVED + payment rejected (rejected downpayment only)
         * -------------------------------------------------------------- */
        $app4 = $this->makeBooking([
            'booking_ref'    => 'HMF-2608-APR4',
            'package_id'     => $debut,
            'addon_ids'      => [$drone, $print],
            'event_type'     => 'Debut Party',
            'client_name'    => 'Danica Lim',
            'client_email'   => 'danica.lim@gmail.com',
            'client_phone'   => '0923-890-1234',
            'event_date'     => '2026-08-29',
            'event_time'     => '17:00',
            'event_venue'    => 'Maharlika Hall',
            'event_address'  => 'Cavite City',
            'event_description' => 'Debut with drone aerial coverage.',
            'notes'          => null,
            'status'         => 'approved',
            'team_id'        => $this->teams['beta']->id,
            'item_status'    => 'reserved',
            'account'        => ['must_change_password' => true],
        ]);

        /* ----------------------------------------------------------------
         * 7. ONGOING (downpayment verified, approved reschedule moved date)
         * -------------------------------------------------------------- */
        $ong2 = $this->makeBooking([
            'booking_ref'    => 'HMF-2608-ONG2',
            'package_id'     => $wedding,
            'addon_ids'      => [$drone],
            'event_type'     => 'Wedding',
            'client_name'    => 'Bea Fernandez',
            'client_email'   => 'bea.fernandez@gmail.com',
            'client_phone'   => '0924-901-2345',
            'event_date'     => '2026-08-23',
            'event_time'     => '15:00',
            'event_venue'    => 'Nurture Wellness Village',
            'event_address'  => 'Tagaytay City, Cavite',
            'event_description' => 'Garden wedding rescheduled from August 16.',
            'notes'          => null,
            'status'         => 'ongoing',
            'team_id'        => $this->teams['gamma']->id,
            'item_status'    => 'in_use',
            'reschedule_used' => false,
            'account'        => ['must_change_password' => false],
        ]);

        /* ----------------------------------------------------------------
         * 8. COMPLETED + post-production in progress
         * -------------------------------------------------------------- */
        $com1 = $this->makeBooking([
            'booking_ref'    => 'HMF-2608-CMP1',
            'package_id'     => $wedding,
            'addon_ids'      => [$sde],
            'event_type'     => 'Wedding',
            'client_name'    => 'Mariz Aquino',
            'client_email'   => 'mariz.aquino@gmail.com',
            'client_phone'   => '0925-012-3456',
            'event_date'     => '2026-08-01',
            'event_time'     => '09:00',
            'event_venue'    => 'Imus Cathedral',
            'event_address'  => 'Imus, Cavite',
            'event_description' => 'Wedding coverage with SDE.',
            'notes'          => null,
            'status'         => 'completed',
            'team_id'        => $this->teams['beta']->id,
            'item_status'    => 'returned',
            'event_completed_at' => '2026-08-01 21:00:00',
            'account'        => ['must_change_password' => false],
        ]);

        /* ----------------------------------------------------------------
         * 9. COMPLETED + post-production READY, fully paid, deliverables
         *    still locked -> "Unlock Deliverables" button visible.
         * -------------------------------------------------------------- */
        $com2 = $this->makeBooking([
            'booking_ref'    => 'HMF-2608-CMP2',
            'package_id'     => $wedding,
            'addon_ids'      => [$premiumAlbum, $sde],
            'event_type'     => 'Wedding',
            'client_name'    => 'Alyssa Gomez',
            'client_email'   => 'alyssa.gomez@gmail.com',
            'client_phone'   => '0926-123-4567',
            'event_date'     => '2026-07-25',
            'event_time'     => '09:00',
            'event_venue'    => 'Villa Escudero',
            'event_address'  => 'San Pablo City, Laguna',
            'event_description' => 'Wedding with premium album upgrade.',
            'notes'          => null,
            'status'         => 'completed',
            'team_id'        => $this->teams['alpha']->id,
            'item_status'    => 'returned',
            'event_completed_at' => '2026-07-25 21:00:00',
            'account'        => ['must_change_password' => false],
        ]);

        /* ----------------------------------------------------------------
         * 10. COMPLETED + delivered + unlocked (Upcoming Archiving window,
         *     delivered a few days ago -> account still active)
         * -------------------------------------------------------------- */
        $del1 = $this->makeBooking([
            'booking_ref'    => 'HMF-2608-DLV1',
            'package_id'     => $wedding,
            'addon_ids'      => [$social, $teaser],
            'event_type'     => 'Wedding',
            'client_name'    => 'Trisha Navarro',
            'client_email'   => 'trisha.navarro@gmail.com',
            'client_phone'   => '0927-234-5678',
            'event_date'     => '2026-07-28',
            'event_time'     => '09:00',
            'event_venue'    => 'Hacienda Isabella',
            'event_address'  => 'Alfonso, Cavite',
            'event_description' => 'Wedding with teaser and social media content.',
            'notes'          => null,
            'status'         => 'completed',
            'team_id'        => $this->teams['beta']->id,
            'item_status'    => 'returned',
            'event_completed_at' => '2026-07-28 21:00:00',
            'deliverables_unlocked' => true,
            'delivered_at'   => '2026-08-11 15:30:00',
            'account'        => ['must_change_password' => false],
        ]);

        /* ----------------------------------------------------------------
         * 11. REJECTED
         * -------------------------------------------------------------- */
        $rej1 = $this->makeBooking([
            'booking_ref'    => 'HMF-2608-REJ1',
            'package_id'     => $event,
            'addon_ids'      => [$rush],
            'event_type'     => 'Birthday Celebration',
            'client_name'    => 'Rodel Mercado',
            'client_email'   => 'rodel.mercado@gmail.com',
            'client_phone'   => '0928-345-6789',
            'event_date'     => '2026-09-10',
            'event_time'     => '13:00',
            'event_venue'    => 'Barangay Covered Court',
            'event_address'  => 'Dasmariñas City, Cavite',
            'event_description' => 'Village-wide birthday celebration.',
            'notes'          => null,
            'status'         => 'rejected',
            'rejection_reason' => 'The studio is fully booked for the requested date. Please choose a different date.',
            'item_status'    => 'cancelled',
            'account'        => ['must_change_password' => true],
        ]);

        /* ----------------------------------------------------------------
         * 12. CANCELLED - zero-paid auto refund (no payment was made)
         * -------------------------------------------------------------- */
        $can1 = $this->makeBooking([
            'booking_ref'    => 'HMF-2608-CAN1',
            'package_id'     => $debut,
            'addon_ids'      => [$preevent],
            'event_type'     => 'Debut Party',
            'client_name'    => 'Jomel Santiago',
            'client_email'   => 'jomel.santiago@gmail.com',
            'client_phone'   => '0929-456-7890',
            'event_date'     => '2026-08-20',
            'event_time'     => '15:00',
            'event_venue'    => 'Tagaytay Country Hotel',
            'event_address'  => 'Tagaytay City, Cavite',
            'event_description' => 'Debut celebration.',
            'notes'          => null,
            'status'         => 'cancelled',
            'item_status'    => 'cancelled',
            'account'        => ['must_change_password' => true],
        ]);

        /* ----------------------------------------------------------------
         * 13. CANCELLED + refunded (with refund reference + proof)
         * -------------------------------------------------------------- */
        $can2 = $this->makeBooking([
            'booking_ref'    => 'HMF-2608-CAN2',
            'package_id'     => $wedding,
            'addon_ids'      => [$drone],
            'event_type'     => 'Wedding',
            'client_name'    => 'Hannah Delos Santos',
            'client_email'   => 'hannah.ds@gmail.com',
            'client_phone'   => '0930-567-8901',
            'event_date'     => '2026-09-02',
            'event_time'     => '09:00',
            'event_venue'    => 'Imus Cathedral',
            'event_address'  => 'Imus, Cavite',
            'event_description' => 'Wedding coverage.',
            'notes'          => null,
            'status'         => 'cancelled',
            'item_status'    => 'cancelled',
            'account'        => ['must_change_password' => false],
        ]);

        /* ----------------------------------------------------------------
         * 14. ONGOING + rejected cancellation request (booking stays active)
         * -------------------------------------------------------------- */
        $can3 = $this->makeBooking([
            'booking_ref'    => 'HMF-2608-CAN3',
            'package_id'     => $event,
            'addon_ids'      => [$sde, $social],
            'event_type'     => '18th Birthday',
            'client_name'    => 'Nicole Aguilar',
            'client_email'   => 'nicole.aguilar@gmail.com',
            'client_phone'   => '0931-678-9012',
            'event_date'     => '2026-08-24',
            'event_time'     => '16:00',
            'event_venue'    => 'Fiesta Pavilion',
            'event_address'  => 'Tagaytay City, Cavite',
            'event_description' => '18th birthday with SDE and social media clips.',
            'notes'          => null,
            'status'         => 'ongoing',
            'team_id'        => $this->teams['beta']->id,
            'item_status'    => 'in_use',
            'account'        => ['must_change_password' => false],
        ]);

        /* ----------------------------------------------------------------
         * 15. ONGOING + pending cancellation request (50% refund computed)
         * -------------------------------------------------------------- */
        $can4 = $this->makeBooking([
            'booking_ref'    => 'HMF-2608-CAN4',
            'package_id'     => $wedding,
            'addon_ids'      => [$drone],
            'event_type'     => 'Wedding',
            'client_name'    => 'Kyla Mendoza',
            'client_email'   => 'kyla.mendoza@gmail.com',
            'client_phone'   => '0932-789-0123',
            'event_date'     => '2026-08-25',
            'event_time'     => '09:00',
            'event_venue'    => 'Our Lady of the Pillar Church',
            'event_address'  => 'Imus, Cavite',
            'event_description' => 'Wedding with drone coverage.',
            'notes'          => null,
            'status'         => 'ongoing',
            'team_id'        => $this->teams['alpha']->id,
            'item_status'    => 'in_use',
            'account'        => ['must_change_password' => true],
        ]);

        /* ----------------------------------------------------------------
         * 16. DELIVERED + ARCHIVED client account
         * -------------------------------------------------------------- */
        $arch = $this->makeBooking([
            'booking_ref'    => 'HMF-2608-ARC1',
            'package_id'     => $wedding,
            'addon_ids'      => [$sde],
            'event_type'     => 'Wedding',
            'client_name'    => 'Michelle Cuevas',
            'client_email'   => 'michelle.cuevas@gmail.com',
            'client_phone'   => '0933-890-1234',
            'event_date'     => '2026-06-20',
            'event_time'     => '09:00',
            'event_venue'    => 'Tagaytay International Convention Center',
            'event_address'  => 'Tagaytay City, Cavite',
            'event_description' => 'Wedding coverage with SDE.',
            'notes'          => null,
            'status'         => 'delivered',
            'team_id'        => $this->teams['gamma']->id,
            'item_status'    => 'returned',
            'event_completed_at' => '2026-06-20 21:00:00',
            'deliverables_unlocked' => true,
            'delivered_at'   => '2026-06-30 14:00:00',
            'account'        => ['must_change_password' => false, 'archived_at' => '2026-07-30 09:00:00'],
        ]);

        /* ==================================================================
         * PAYMENTS
         * ================================================================== */

        // APP2: downpayment pending review
        $this->addPayment($app2, 'downpayment', 7050.00, 'pending', null, '2026-08-13 10:15:00');

        // ONG1: downpayment verified -> ongoing
        $this->addPayment($ong1, 'downpayment', 15900.00, 'verified', '2026-08-12 09:30:00', '2026-08-10 11:00:00');

        // APP3: rejected row + resubmitted row (multiple payment rows)
        $this->addPayment($app3, 'downpayment', 15750.00, 'rejected', null, '2026-08-05 16:40:00',
            'The payment proof is unclear and the reference number is not visible. Please upload a clearer screenshot of your GCash transaction.');
        $this->addPayment($app3, 'downpayment', 15750.00, 'pending', null, '2026-08-13 14:05:00');

        // APP4: rejected downpayment only -> payment_rejected
        $this->addPayment($app4, 'downpayment', 9750.00, 'rejected', null, '2026-08-08 13:20:00',
            'The amount transferred does not match the required downpayment. Kindly verify and resubmit.');

        // ONG2: downpayment verified
        $this->addPayment($ong2, 'downpayment', 15000.00, 'verified', '2026-08-09 10:00:00', '2026-08-07 09:45:00');

        // COM1: downpayment verified (final not yet paid)
        $this->addPayment($com1, 'downpayment', 15900.00, 'verified', '2026-07-26 11:15:00', '2026-07-23 17:00:00');

        // COM2: downpayment + final verified -> fully paid, ready to unlock
        $this->addPayment($com2, 'downpayment', 17400.00, 'verified', '2026-07-20 09:20:00', '2026-07-18 15:10:00');
        $this->addPayment($com2, 'final', 40600.00, 'verified', '2026-08-10 10:45:00', '2026-08-08 12:30:00');

        // DEL1: downpayment + final verified -> fully paid + delivered
        $this->addPayment($del1, 'downpayment', 15600.00, 'verified', '2026-07-22 14:00:00', '2026-07-21 09:30:00');
        $this->addPayment($del1, 'final', 36400.00, 'verified', '2026-08-11 10:00:00', '2026-08-10 11:20:00');

        // CAN2: downpayment was verified before cancellation
        $this->addPayment($can2, 'downpayment', 15000.00, 'verified', '2026-08-06 09:30:00', '2026-08-04 16:00:00');

        // CAN3: downpayment verified (booking stays ongoing)
        $this->addPayment($can3, 'downpayment', 8100.00, 'verified', '2026-08-13 11:40:00', '2026-08-11 18:25:00');

        // CAN4: downpayment verified (pending cancellation, 50% refundable)
        $this->addPayment($can4, 'downpayment', 15000.00, 'verified', '2026-08-12 10:10:00', '2026-08-10 14:50:00');

        // ARCH: downpayment + final verified
        $this->addPayment($arch, 'downpayment', 15900.00, 'verified', '2026-06-15 09:00:00', '2026-06-13 11:00:00');
        $this->addPayment($arch, 'final', 37100.00, 'verified', '2026-06-29 15:30:00', '2026-06-28 10:00:00');
    }

    /**
     * Create a booking plus its package inventory items, add-ons and client
     * account in one go.
     */
    private function makeBooking(array $data): Booking
    {
        $package = Package::findOrFail($data['package_id']);
        $addons  = Addon::whereIn('id', $data['addon_ids'] ?? [])->get();

        $totalPrice   = (float) $package->price + $addons->sum('price');
        $downpayment  = round($totalPrice * 0.30, 2);

        $booking = Booking::create([
            'booking_ref'          => $data['booking_ref'],
            'package_id'           => $data['package_id'],
            'team_id'              => $data['team_id'] ?? null,
            'event_type'           => $data['event_type'] ?? null,
            'client_name'          => $data['client_name'],
            'client_email'         => $data['client_email'],
            'client_phone'         => $data['client_phone'],
            'event_date'           => $data['event_date'],
            'event_time'           => $data['event_time'] ?? '09:00',
            'event_venue'          => $data['event_venue'] ?? null,
            'event_address'        => $data['event_address'] ?? null,
            'event_description'    => $data['event_description'] ?? null,
            'total_price'          => $totalPrice,
            'downpayment_amount'   => $downpayment,
            'status'               => $data['status'],
            'payment_status'       => $data['payment_status'] ?? null,
            'final_payment_status' => $data['final_payment_status'] ?? null,
            'deliverables_unlocked'=> $data['deliverables_unlocked'] ?? false,
            'rejection_reason'     => $data['rejection_reason'] ?? null,
            'post_production_status' => $data['post_production_status'] ?? null,
            'event_completed_at'   => $data['event_completed_at'] ?? null,
            'delivered_at'         => $data['delivered_at'] ?? null,
            'notes'                => $data['notes'] ?? null,
            'terms_agreed'         => true,
            'reschedule_used'      => $data['reschedule_used'] ?? false,
        ]);

        // Booking items from package inventory
        foreach ($package->inventory as $item) {
            $booking->items()->create([
                'inventory_item_id' => $item->id,
                'quantity'          => $item->pivot->quantity,
                'status'            => $data['item_status'],
            ]);
        }

        // Attach add-ons
        foreach ($addons as $addon) {
            $booking->addons()->attach($addon->id, ['price' => $addon->price]);

            foreach ($addon->inventory as $item) {
                $existing = $booking->items()->where('inventory_item_id', $item->id)->first();

                if ($existing) {
                    $existing->update(['quantity' => $existing->quantity + $item->pivot->quantity]);
                } else {
                    $booking->items()->create([
                        'inventory_item_id' => $item->id,
                        'quantity'          => $item->pivot->quantity,
                        'status'            => $data['item_status'],
                    ]);
                }
            }
        }

        // Client account (control number = booking ref)
        $accountData = $data['account'] ?? [];
        $mustChange  = $accountData['must_change_password'] ?? true;
        $archivedAt  = $accountData['archived_at'] ?? null;

        $account = ClientAccount::create([
            'control_number'       => $data['booking_ref'],
            'password'             => 'demo123',
            'client_name'          => $data['client_name'],
            'client_email'         => $data['client_email'],
            'client_phone'         => $data['client_phone'],
            'booking_id'           => $booking->id,
            'must_change_password' => $mustChange,
            'last_login_at'        => $mustChange ? null : '2026-08-13 18:30:00',
            'archived_at'          => $archivedAt,
        ]);

        if ($archivedAt) {
            $account->forceFill(['created_at' => '2026-06-10 09:00:00'])->saveQuietly();
        }

        return $booking;
    }

    private function addPayment(
        Booking $booking,
        string $paymentType,
        float $amount,
        string $status,
        ?string $verifiedAt,
        string $submittedAt,
        ?string $rejectionReason = null
    ): Downpayment {
        $downpayment = Downpayment::create([
            'booking_id'      => $booking->id,
            'payment_type'    => $paymentType,
            'amount'          => $amount,
            'payment_proof'   => $paymentType === 'final'
                ? 'final-payment-proofs/demo-final-proof.png'
                : 'downpayment-proofs/demo-downpayment-proof.png',
            'status'          => $status,
            'rejection_reason'=> $rejectionReason,
            'submitted_at'    => $submittedAt,
            'verified_at'     => $verifiedAt,
        ]);

        // Reflect the latest state on the booking
        if ($status === 'verified') {
            $booking->update(['payment_status' => 'downpayment_verified']);

            $totalPaid = $booking->downpayments()->where('status', 'verified')->sum('amount');
            if ((float) $totalPaid >= (float) $booking->total_price) {
                $booking->update(['final_payment_status' => 'paid']);
            }
        } elseif ($status === 'rejected' && !$booking->downpayments()->where('status', 'pending')->exists()) {
            $booking->update(['payment_status' => 'payment_rejected']);
        } elseif ($status === 'pending') {
            $booking->update(['payment_status' => 'payment_submitted']);
        }

        return $downpayment;
    }

    /* ======================================================================
     * STAFF SCHEDULES
     * ==================================================================== */

    private function createSchedules(): void
    {
        $alpha = $this->teams['alpha'];
        $beta  = $this->teams['beta'];
        $gamma = $this->teams['gamma'];

        $bookings = Booking::whereIn('booking_ref', [
            'HMF-2608-APR1', 'HMF-2608-APR2', 'HMF-2608-ONG1', 'HMF-2608-APR3',
            'HMF-2608-APR4', 'HMF-2608-ONG2', 'HMF-2608-CAN3', 'HMF-2608-CAN4',
        ])->get()->keyBy('booking_ref');

        $this->assignTeam($bookings['HMF-2608-APR1'], $alpha, 'assigned');
        $this->assignTeam($bookings['HMF-2608-APR2'], $beta, 'assigned');
        $this->assignTeam($bookings['HMF-2608-ONG1'], $alpha, 'assigned');
        $this->assignTeam($bookings['HMF-2608-APR3'], $alpha, 'assigned');   // conflict with ONG2 (Juan double-booked on 08-23)
        $this->assignTeam($bookings['HMF-2608-APR4'], $beta, 'assigned');
        $this->assignTeam($bookings['HMF-2608-ONG2'], $gamma, 'confirmed');  // Juan double-booked on 08-23
        $this->assignTeam($bookings['HMF-2608-CAN3'], $beta, 'assigned');
        $this->assignTeam($bookings['HMF-2608-CAN4'], $alpha, 'assigned');

        // Completed events
        $completed = Booking::whereIn('booking_ref', [
            'HMF-2608-CMP1', 'HMF-2608-CMP2', 'HMF-2608-DLV1',
        ])->get()->keyBy('booking_ref');

        $this->assignTeam($completed['HMF-2608-CMP1'], $beta, 'completed');
        $this->assignTeam($completed['HMF-2608-CMP2'], $alpha, 'completed');
        $this->assignTeam($completed['HMF-2608-DLV1'], $beta, 'completed');

        // Delivered/archived event
        $arch = Booking::where('booking_ref', 'HMF-2608-ARC1')->first();
        $this->assignTeam($arch, $gamma, 'completed');

        // Cancelled booking kept cancelled schedule rows (for calendar variety)
        $can2 = Booking::where('booking_ref', 'HMF-2608-CAN2')->first();
        $this->assignTeam($can2, $alpha, 'cancelled');
    }

    private function assignTeam(Booking $booking, Team $team, string $scheduleStatus): void
    {
        foreach ($team->members as $member) {
            StaffSchedule::create([
                'staff_id'    => $member->id,
                'booking_id'  => $booking->id,
                'event_date'  => $booking->event_date->format('Y-m-d'),
                'event_time'  => $booking->event_time,
                'status'      => $scheduleStatus,
            ]);
        }
    }

    /* ======================================================================
     * RESCHEDULE REQUESTS
     * ==================================================================== */

    private function createReschedules(): void
    {
        $app1 = Booking::where('booking_ref', 'HMF-2608-APR1')->first();
        $ong1 = Booking::where('booking_ref', 'HMF-2608-ONG1')->first();
        $ong2 = Booking::where('booking_ref', 'HMF-2608-ONG2')->first();

        // Pending reschedule
        RescheduleRequest::create([
            'booking_id'     => $app1->id,
            'requested_date' => '2026-10-03',
            'requested_time' => '09:00',
            'status'         => 'pending',
        ]);

        // Approved reschedule (booking already moved to the new date)
        RescheduleRequest::create([
            'booking_id'     => $ong2->id,
            'requested_date' => '2026-08-23',
            'requested_time' => '15:00',
            'status'         => 'approved',
            'new_team_id'    => $this->teams['gamma']->id,
        ]);

        // Rejected reschedule
        RescheduleRequest::create([
            'booking_id'       => $ong1->id,
            'requested_date'   => '2026-08-30',
            'requested_time'   => '10:00',
            'status'           => 'rejected',
            'rejection_reason' => 'Team Alpha already has a confirmed event on the requested date. You may submit another reschedule request for a different date.',
        ]);
    }

    /* ======================================================================
     * CANCELLATION REQUESTS
     * ==================================================================== */

    private function createCancellations(): void
    {
        $can1 = Booking::where('booking_ref', 'HMF-2608-CAN1')->first(); // zero-paid
        $can2 = Booking::where('booking_ref', 'HMF-2608-CAN2')->first(); // refunded
        $can3 = Booking::where('booking_ref', 'HMF-2608-CAN3')->first(); // rejected
        $can4 = Booking::where('booking_ref', 'HMF-2608-CAN4')->first(); // pending (50%)

        // Zero-paid auto refund
        CancellationRequest::create([
            'booking_id'        => $can1->id,
            'reason'            => 'Change of plans, family decided to postpone the debut.',
            'refund_amount'     => 0,
            'refund_percentage' => 0,
            'status'            => 'refunded',
            'admin_notes'       => 'No payment made — auto-cancelled.',
            'processed_at'      => '2026-08-10 09:00:00',
        ]);

        // Refunded (full refund with reference + proof)
        CancellationRequest::create([
            'booking_id'        => $can2->id,
            'reason'            => 'Emergency family matter, must cancel the wedding.',
            'refund_amount'     => 15000.00,
            'refund_percentage' => 100,
            'status'            => 'refunded',
            'admin_notes'       => 'Full refund approved as the cancellation was made more than 14 days before the event.',
            'refund_reference'  => 'GCASH-REF-240817-X7K2',
            'refund_proof'      => 'refund_proofs/demo-refund-proof-gcash.png',
            'processed_at'      => '2026-08-14 10:30:00',
        ]);

        // Rejected
        CancellationRequest::create([
            'booking_id'        => $can3->id,
            'reason'            => 'Found a cheaper videographer.',
            'refund_amount'     => 4050.00,
            'refund_percentage' => 50,
            'status'            => 'rejected',
            'admin_notes'       => 'The reason provided does not qualify for cancellation. Bookings are non-refundable once within the 7-day window.',
            'processed_at'      => '2026-08-13 15:00:00',
        ]);

        // Pending (50% refund computed per policy)
        CancellationRequest::create([
            'booking_id'        => $can4->id,
            'reason'            => 'The venue cancelled our booking contract.',
            'refund_amount'     => 7500.00,
            'refund_percentage' => 50,
            'status'            => 'pending',
        ]);
    }

    /* ======================================================================
     * POST-PRODUCTION
     * ==================================================================== */

    private function createPostProduction(): void
    {
        $com1 = Booking::where('booking_ref', 'HMF-2608-CMP1')->first();
        $com2 = Booking::where('booking_ref', 'HMF-2608-CMP2')->first();
        $del1 = Booking::where('booking_ref', 'HMF-2608-DLV1')->first();
        $arch = Booking::where('booking_ref', 'HMF-2608-ARC1')->first();

        $jose    = $this->staff['jose@harvymance.com'];
        $maria   = $this->staff['maria@harvymance.com'];
        $rachel  = $this->staff['rachel@harvymance.com'];
        $mark    = $this->staff['mark@harvymance.com'];

        /* ---- COM1: in progress, mixed tasks ---- */
        $pp1 = PostProduction::create([
            'booking_id'              => $com1->id,
            'assigned_staff_id'       => $jose->id,
            'status'                  => 'in_progress',
            'notes'                   => 'Editing the full wedding film and photo set for the Aquino wedding.',
            'expected_completion_date' => '2026-08-28',
            'started_at'              => '2026-08-03 09:00:00',
        ]);

        PostProductionTask::create([
            'post_production_id' => $pp1->id,
            'staff_id'           => $jose->id,
            'outsourced_staff_id'=> null,
            'task_type'          => 'photo_editing',
            'instructions'       => 'Color grade all 800+ photos and retouch key portraits of the couple.',
            'status'             => 'in_progress',
            'admin_review_status'=> 'pending',
        ]);

        PostProductionTask::create([
            'post_production_id' => $pp1->id,
            'staff_id'           => $rachel->id,
            'outsourced_staff_id'=> null,
            'task_type'          => 'video_editing',
            'instructions'       => 'Edit the ceremony and reception highlight reel to 8 minutes.',
            'status'             => 'in_progress',
            'admin_review_status'=> 'pending',
        ]);

        PostProductionTask::create([
            'post_production_id' => $pp1->id,
            'staff_id'           => null,
            'outsourced_staff_id'=> $this->outsourced['sarah']->id,
            'task_type'          => 'both',
            'instructions'       => 'Curate 20 social-media-ready clips and post teasers.',
            'status'             => 'not_started',
            'admin_review_status'=> 'pending',
        ]);

        PostProductionTask::create([
            'post_production_id' => $pp1->id,
            'staff_id'           => $maria->id,
            'outsourced_staff_id'=> null,
            'task_type'          => 'photo_editing',
            'instructions'       => 'Deliver the pre-nuptial album layout.',
            'status'             => 'completed',
            'deliverable_link'   => 'https://drive.google.com/drive/u/0/folders/abCdEfGh',
            'remarks'            => 'Preliminary album draft uploaded for review.',
            'revision_notes'     => 'Please adjust the white balance and skin tones on the pre-nup shots before finalizing.',
            'admin_review_status'=> 'revision_requested',
            'completed_at'       => '2026-08-12 16:00:00',
        ]);

        /* ---- COM2: ready, all tasks approved, fully paid (unlock button) ---- */
        $pp2 = PostProduction::create([
            'booking_id'              => $com2->id,
            'assigned_staff_id'       => $maria->id,
            'status'                  => 'ready',
            'notes'                   => 'All edits approved and waiting for final payment confirmation to release deliverables.',
            'expected_completion_date' => '2026-08-20',
            'started_at'              => '2026-07-27 09:00:00',
        ]);

        $pp2Tasks = [
            ['staff_id' => $maria->id, 'outsourced_staff_id' => null, 'task_type' => 'photo_editing',
             'instructions' => 'Color grade and retouch the full wedding photo set.',
             'deliverable_link' => 'https://drive.google.com/drive/u/0/folders/xYz12345', 'remarks' => 'All 950 photos delivered in full resolution.',
             'completed_at' => '2026-08-05 14:00:00'],
            ['staff_id' => $jose->id, 'outsourced_staff_id' => null, 'task_type' => 'video_editing',
             'instructions' => 'Produce the full-length wedding film and 3-minute highlight reel.',
             'deliverable_link' => 'https://vimeo.com/harvymance/alyssa-wedding', 'remarks' => 'Final cut exported in 4K.',
             'completed_at' => '2026-08-08 17:30:00'],
            ['staff_id' => $mark->id, 'outsourced_staff_id' => null, 'task_type' => 'both',
             'instructions' => 'Deliver 15 social media clips and the 60-second teaser.',
             'deliverable_link' => 'https://drive.google.com/drive/u/0/folders/tEaS7er', 'remarks' => 'Ready for review.',
             'completed_at' => '2026-08-09 11:00:00'],
        ];

        foreach ($pp2Tasks as $i => $task) {
            PostProductionTask::create([
                'post_production_id'  => $pp2->id,
                'staff_id'            => $task['staff_id'],
                'outsourced_staff_id' => $task['outsourced_staff_id'],
                'task_type'           => $task['task_type'],
                'instructions'        => $task['instructions'],
                'status'              => 'completed',
                'deliverable_link'    => $task['deliverable_link'],
                'remarks'             => $task['remarks'],
                'admin_review_status' => 'approved',
                'completed_at'        => $task['completed_at'],
            ]);
        }

        /* ---- DEL1: delivered, all tasks approved, unlocked ---- */
        $pp3 = PostProduction::create([
            'booking_id'              => $del1->id,
            'assigned_staff_id'       => $jose->id,
            'status'                  => 'delivered',
            'notes'                   => 'Deliverables released and download access granted to client.',
            'expected_completion_date' => '2026-08-15',
            'started_at'              => '2026-07-30 09:00:00',
            'completed_at'            => '2026-08-11 15:30:00',
        ]);

        foreach ([
            ['staff_id' => $maria->id, 'task_type' => 'photo_editing',
             'instructions' => 'Retouch wedding album photos.', 'deliverable_link' => 'https://drive.google.com/drive/u/0/folders/dElIveR1'],
            ['staff_id' => $jose->id, 'task_type' => 'video_editing',
             'instructions' => 'Final wedding film edit.', 'deliverable_link' => 'https://vimeo.com/harvymance/trisha-wedding'],
            ['staff_id' => $mark->id, 'task_type' => 'both',
             'instructions' => 'Social media clips and teaser.', 'deliverable_link' => 'https://drive.google.com/drive/u/0/folders/dElIveR2'],
        ] as $task) {
            PostProductionTask::create([
                'post_production_id'  => $pp3->id,
                'staff_id'            => $task['staff_id'],
                'outsourced_staff_id' => null,
                'task_type'           => $task['task_type'],
                'instructions'        => $task['instructions'],
                'status'              => 'completed',
                'deliverable_link'    => $task['deliverable_link'],
                'remarks'             => 'Approved by admin.',
                'admin_review_status' => 'approved',
                'completed_at'        => '2026-08-10 13:00:00',
            ]);
        }

        /* ---- ARCH: delivered + archived client ---- */
        $pp4 = PostProduction::create([
            'booking_id'              => $arch->id,
            'assigned_staff_id'       => $jose->id,
            'status'                  => 'delivered',
            'notes'                   => 'All deliverables released; account archived after the 30-day window.',
            'expected_completion_date' => '2026-07-05',
            'started_at'              => '2026-06-22 09:00:00',
            'completed_at'            => '2026-06-30 14:00:00',
        ]);

        foreach ([
            ['staff_id' => $maria->id, 'task_type' => 'photo_editing'],
            ['staff_id' => $jose->id, 'task_type' => 'video_editing'],
        ] as $task) {
            PostProductionTask::create([
                'post_production_id'  => $pp4->id,
                'staff_id'            => $task['staff_id'],
                'outsourced_staff_id' => null,
                'task_type'           => $task['task_type'],
                'instructions'        => 'Complete final deliverables.',
                'status'              => 'completed',
                'deliverable_link'    => 'https://drive.google.com/drive/u/0/folders/arChIvEd',
                'remarks'             => 'Approved by admin.',
                'admin_review_status' => 'approved',
                'completed_at'        => '2026-06-29 16:00:00',
            ]);
        }

        // Reflect post-production status on bookings
        $com1->update(['post_production_status' => 'in_progress']);
        $com2->update(['post_production_status' => 'ready']);
        $del1->update(['post_production_status' => 'delivered']);
        $arch->update(['post_production_status' => 'delivered']);
    }

    /* ======================================================================
     * ACTIVITY LOGS
     * ==================================================================== */

    private function createActivityLogs(): void
    {
        $bookings = Booking::all()->keyBy('booking_ref');
        $accounts = ClientAccount::all()->keyBy('control_number');

        $admin = 'Harvy Mance';

        $this->log('booking.created', 'Booking HMF-2608-PND1 submitted by Maria Clara Dela Cruz (Church Wedding on Sep 05, 2026).', ['booking_ref' => 'HMF-2608-PND1'], 'client', $accounts['HMF-2608-PND1']->client_name, '2026-08-06 09:12:00');
        $this->log('booking.created', 'Booking HMF-2608-PND2 submitted by Angelica Reyes (Debut Party on Sep 13, 2026).', ['booking_ref' => 'HMF-2608-PND2'], 'client', $accounts['HMF-2608-PND2']->client_name, '2026-08-07 11:30:00');

        $this->log('booking.created', 'Booking HMF-2608-APR1 submitted by Katrina Villanueva.', ['booking_ref' => 'HMF-2608-APR1'], 'client', $accounts['HMF-2608-APR1']->client_name, '2026-08-01 10:05:00');
        $this->log('booking.approved', 'Booking HMF-2608-APR1 approved with team Team Alpha.', ['booking_ref' => 'HMF-2608-APR1', 'team' => 'Team Alpha'], 'admin', $admin, '2026-08-02 14:20:00');

        $this->log('booking.created', 'Booking HMF-2608-APR2 submitted by Paolo & Andrea Ramos.', ['booking_ref' => 'HMF-2608-APR2'], 'client', $accounts['HMF-2608-APR2']->client_name, '2026-08-03 15:40:00');
        $this->log('booking.approved', 'Booking HMF-2608-APR2 approved with team Team Beta.', ['booking_ref' => 'HMF-2608-APR2', 'team' => 'Team Beta'], 'admin', $admin, '2026-08-04 09:10:00');
        $this->log('payment.submitted', 'Downpayment of ₱7,050.00 submitted for booking HMF-2608-APR2.', ['booking_ref' => 'HMF-2608-APR2'], 'client', $accounts['HMF-2608-APR2']->client_name, '2026-08-13 10:15:00');

        $this->log('booking.created', 'Booking HMF-2608-ONG1 submitted by Carmela Santos.', ['booking_ref' => 'HMF-2608-ONG1'], 'client', $accounts['HMF-2608-ONG1']->client_name, '2026-07-28 09:00:00');
        $this->log('booking.approved', 'Booking HMF-2608-ONG1 approved with team Team Alpha.', ['booking_ref' => 'HMF-2608-ONG1', 'team' => 'Team Alpha'], 'admin', $admin, '2026-07-29 11:00:00');
        $this->log('payment.submitted', 'Downpayment of ₱15,900.00 submitted for booking HMF-2608-ONG1.', ['booking_ref' => 'HMF-2608-ONG1'], 'client', $accounts['HMF-2608-ONG1']->client_name, '2026-08-10 11:00:00');
        $this->log('payment.verified', 'Verified downpayment payment of ₱15,900.00 for booking HMF-2608-ONG1.', ['booking_ref' => 'HMF-2608-ONG1', 'payment_type' => 'downpayment'], 'admin', $admin, '2026-08-12 09:30:00');

        $this->log('booking.created', 'Booking HMF-2608-APR3 submitted by Jenny Dela Vega.', ['booking_ref' => 'HMF-2608-APR3'], 'client', $accounts['HMF-2608-APR3']->client_name, '2026-08-04 16:20:00');
        $this->log('booking.approved', 'Booking HMF-2608-APR3 approved with team Team Alpha.', ['booking_ref' => 'HMF-2608-APR3', 'team' => 'Team Alpha'], 'admin', $admin, '2026-08-05 10:00:00');
        $this->log('payment.submitted', 'Downpayment of ₱15,750.00 submitted for booking HMF-2608-APR3.', ['booking_ref' => 'HMF-2608-APR3'], 'client', $accounts['HMF-2608-APR3']->client_name, '2026-08-05 16:40:00');
        $this->log('payment.rejected', 'Rejected downpayment payment of ₱15,750.00 for booking HMF-2608-APR3. The proof of payment was unclear.', ['booking_ref' => 'HMF-2608-APR3', 'payment_type' => 'downpayment'], 'admin', $admin, '2026-08-08 09:00:00');
        $this->log('payment.submitted', 'Downpayment of ₱15,750.00 resubmitted for booking HMF-2608-APR3.', ['booking_ref' => 'HMF-2608-APR3'], 'client', $accounts['HMF-2608-APR3']->client_name, '2026-08-13 14:05:00');

        $this->log('booking.created', 'Booking HMF-2608-APR4 submitted by Danica Lim.', ['booking_ref' => 'HMF-2608-APR4'], 'client', $accounts['HMF-2608-APR4']->client_name, '2026-08-06 13:00:00');
        $this->log('booking.approved', 'Booking HMF-2608-APR4 approved with team Team Beta.', ['booking_ref' => 'HMF-2608-APR4', 'team' => 'Team Beta'], 'admin', $admin, '2026-08-07 10:30:00');
        $this->log('payment.submitted', 'Downpayment of ₱9,750.00 submitted for booking HMF-2608-APR4.', ['booking_ref' => 'HMF-2608-APR4'], 'client', $accounts['HMF-2608-APR4']->client_name, '2026-08-08 13:20:00');
        $this->log('payment.rejected', 'Rejected downpayment payment of ₱9,750.00 for booking HMF-2608-APR4. Amount does not match required downpayment.', ['booking_ref' => 'HMF-2608-APR4', 'payment_type' => 'downpayment'], 'admin', $admin, '2026-08-10 09:15:00');

        $this->log('booking.created', 'Booking HMF-2608-ONG2 submitted by Bea Fernandez.', ['booking_ref' => 'HMF-2608-ONG2'], 'client', $accounts['HMF-2608-ONG2']->client_name, '2026-07-30 10:00:00');
        $this->log('booking.approved', 'Booking HMF-2608-ONG2 approved with team Team Gamma.', ['booking_ref' => 'HMF-2608-ONG2', 'team' => 'Team Gamma'], 'admin', $admin, '2026-07-31 09:00:00');
        $this->log('payment.submitted', 'Downpayment of ₱15,000.00 submitted for booking HMF-2608-ONG2.', ['booking_ref' => 'HMF-2608-ONG2'], 'client', $accounts['HMF-2608-ONG2']->client_name, '2026-08-07 09:45:00');
        $this->log('payment.verified', 'Verified downpayment payment of ₱15,000.00 for booking HMF-2608-ONG2.', ['booking_ref' => 'HMF-2608-ONG2', 'payment_type' => 'downpayment'], 'admin', $admin, '2026-08-09 10:00:00');
        $this->log('reschedule.requested', 'Reschedule requested for booking HMF-2608-ONG2 to Aug 23, 2026 03:00 PM.', ['booking_ref' => 'HMF-2608-ONG2'], 'client', $accounts['HMF-2608-ONG2']->client_name, '2026-08-10 08:30:00');
        $this->log('reschedule.approved', 'Reschedule approved for booking HMF-2608-ONG2. New date Aug 23, 2026.', ['booking_ref' => 'HMF-2608-ONG2'], 'admin', $admin, '2026-08-11 10:00:00');

        $this->log('booking.created', 'Booking HMF-2608-CMP1 submitted by Mariz Aquino.', ['booking_ref' => 'HMF-2608-CMP1'], 'client', $accounts['HMF-2608-CMP1']->client_name, '2026-06-28 09:30:00');
        $this->log('booking.approved', 'Booking HMF-2608-CMP1 approved with team Team Beta.', ['booking_ref' => 'HMF-2608-CMP1', 'team' => 'Team Beta'], 'admin', $admin, '2026-06-29 11:00:00');
        $this->log('payment.verified', 'Verified downpayment payment of ₱15,900.00 for booking HMF-2608-CMP1.', ['booking_ref' => 'HMF-2608-CMP1', 'payment_type' => 'downpayment'], 'admin', $admin, '2026-07-26 11:15:00');
        $this->log('booking.completed', 'Event for booking HMF-2608-CMP1 marked as completed.', ['booking_ref' => 'HMF-2608-CMP1'], 'admin', $admin, '2026-08-01 22:00:00');
        $this->log('post-production.started', 'Post-production started for booking HMF-2608-CMP1 with 4 task(s).', ['booking_ref' => 'HMF-2608-CMP1'], 'admin', $admin, '2026-08-03 09:00:00');
        $this->log('task.revision_requested', 'Revision requested on task photo_editing for booking HMF-2608-CMP1.', ['booking_ref' => 'HMF-2608-CMP1'], 'admin', $admin, '2026-08-13 16:30:00');

        $this->log('booking.created', 'Booking HMF-2608-CMP2 submitted by Alyssa Gomez.', ['booking_ref' => 'HMF-2608-CMP2'], 'client', $accounts['HMF-2608-CMP2']->client_name, '2026-06-20 10:00:00');
        $this->log('booking.approved', 'Booking HMF-2608-CMP2 approved with team Team Alpha.', ['booking_ref' => 'HMF-2608-CMP2', 'team' => 'Team Alpha'], 'admin', $admin, '2026-06-21 10:00:00');
        $this->log('payment.verified', 'Verified downpayment payment of ₱17,400.00 for booking HMF-2608-CMP2.', ['booking_ref' => 'HMF-2608-CMP2', 'payment_type' => 'downpayment'], 'admin', $admin, '2026-07-20 09:20:00');
        $this->log('booking.completed', 'Event for booking HMF-2608-CMP2 marked as completed.', ['booking_ref' => 'HMF-2608-CMP2'], 'admin', $admin, '2026-07-25 22:00:00');
        $this->log('post-production.started', 'Post-production started for booking HMF-2608-CMP2 with 3 task(s).', ['booking_ref' => 'HMF-2608-CMP2'], 'admin', $admin, '2026-07-27 09:00:00');
        $this->log('task.approved', 'Task photo_editing approved for booking HMF-2608-CMP2.', ['booking_ref' => 'HMF-2608-CMP2'], 'admin', $admin, '2026-08-06 10:00:00');
        $this->log('task.approved', 'Task video_editing approved for booking HMF-2608-CMP2.', ['booking_ref' => 'HMF-2608-CMP2'], 'admin', $admin, '2026-08-09 09:30:00');
        $this->log('task.approved', 'Task both approved for booking HMF-2608-CMP2.', ['booking_ref' => 'HMF-2608-CMP2'], 'admin', $admin, '2026-08-10 09:00:00');
        $this->log('payment.submitted', 'Final payment of ₱40,600.00 submitted for booking HMF-2608-CMP2.', ['booking_ref' => 'HMF-2608-CMP2'], 'client', $accounts['HMF-2608-CMP2']->client_name, '2026-08-08 12:30:00');
        $this->log('payment.verified', 'Verified final payment of ₱40,600.00 for booking HMF-2608-CMP2.', ['booking_ref' => 'HMF-2608-CMP2', 'payment_type' => 'final'], 'admin', $admin, '2026-08-10 10:45:00');

        $this->log('booking.created', 'Booking HMF-2608-DLV1 submitted by Trisha Navarro.', ['booking_ref' => 'HMF-2608-DLV1'], 'client', $accounts['HMF-2608-DLV1']->client_name, '2026-06-25 09:00:00');
        $this->log('booking.approved', 'Booking HMF-2608-DLV1 approved with team Team Beta.', ['booking_ref' => 'HMF-2608-DLV1', 'team' => 'Team Beta'], 'admin', $admin, '2026-06-26 10:00:00');
        $this->log('payment.verified', 'Verified final payment of ₱36,400.00 for booking HMF-2608-DLV1.', ['booking_ref' => 'HMF-2608-DLV1', 'payment_type' => 'final'], 'admin', $admin, '2026-08-11 10:00:00');
        $this->log('deliverables.unlocked', 'Deliverables unlocked for booking HMF-2608-DLV1. Client can now download files.', ['booking_ref' => 'HMF-2608-DLV1'], 'admin', $admin, '2026-08-11 15:30:00');

        $this->log('booking.created', 'Booking HMF-2608-REJ1 submitted by Rodel Mercado.', ['booking_ref' => 'HMF-2608-REJ1'], 'client', $accounts['HMF-2608-REJ1']->client_name, '2026-08-08 12:00:00');
        $this->log('booking.rejected', 'Booking HMF-2608-REJ1 rejected. The studio is fully booked for the requested date.', ['booking_ref' => 'HMF-2608-REJ1'], 'admin', $admin, '2026-08-09 09:00:00');

        $this->log('cancellation.submitted', 'Cancellation request submitted for booking HMF-2608-CAN1 (no refund applicable).', ['booking_ref' => 'HMF-2608-CAN1'], 'client', $accounts['HMF-2608-CAN1']->client_name, '2026-08-10 08:30:00');
        $this->log('cancellation.auto_refunded', 'Booking HMF-2608-CAN1 auto-cancelled with no payment made.', ['booking_ref' => 'HMF-2608-CAN1'], 'admin', $admin, '2026-08-10 08:31:00');

        $this->log('booking.created', 'Booking HMF-2608-CAN2 submitted by Hannah Delos Santos.', ['booking_ref' => 'HMF-2608-CAN2'], 'client', $accounts['HMF-2608-CAN2']->client_name, '2026-07-20 09:00:00');
        $this->log('booking.approved', 'Booking HMF-2608-CAN2 approved with team Team Alpha.', ['booking_ref' => 'HMF-2608-CAN2', 'team' => 'Team Alpha'], 'admin', $admin, '2026-07-21 10:00:00');
        $this->log('payment.verified', 'Verified downpayment payment of ₱15,000.00 for booking HMF-2608-CAN2.', ['booking_ref' => 'HMF-2608-CAN2', 'payment_type' => 'downpayment'], 'admin', $admin, '2026-08-06 09:30:00');
        $this->log('cancellation.submitted', 'Cancellation request submitted for booking HMF-2608-CAN2.', ['booking_ref' => 'HMF-2608-CAN2'], 'client', $accounts['HMF-2608-CAN2']->client_name, '2026-08-14 09:00:00');
        $this->log('cancellation.refunded', 'Processed refund of ₱15,000.00 for booking HMF-2608-CAN2.', ['booking_ref' => 'HMF-2608-CAN2', 'refund_amount' => 15000], 'admin', $admin, '2026-08-14 10:30:00');

        $this->log('booking.created', 'Booking HMF-2608-CAN3 submitted by Nicole Aguilar.', ['booking_ref' => 'HMF-2608-CAN3'], 'client', $accounts['HMF-2608-CAN3']->client_name, '2026-08-05 10:00:00');
        $this->log('booking.approved', 'Booking HMF-2608-CAN3 approved with team Team Beta.', ['booking_ref' => 'HMF-2608-CAN3', 'team' => 'Team Beta'], 'admin', $admin, '2026-08-06 10:00:00');
        $this->log('payment.verified', 'Verified downpayment payment of ₱8,100.00 for booking HMF-2608-CAN3.', ['booking_ref' => 'HMF-2608-CAN3', 'payment_type' => 'downpayment'], 'admin', $admin, '2026-08-13 11:40:00');
        $this->log('cancellation.submitted', 'Cancellation request submitted for booking HMF-2608-CAN3.', ['booking_ref' => 'HMF-2608-CAN3'], 'client', $accounts['HMF-2608-CAN3']->client_name, '2026-08-13 14:00:00');
        $this->log('cancellation.rejected', 'Rejected cancellation request for booking HMF-2608-CAN3.', ['booking_ref' => 'HMF-2608-CAN3'], 'admin', $admin, '2026-08-13 15:00:00');

        $this->log('booking.created', 'Booking HMF-2608-CAN4 submitted by Kyla Mendoza.', ['booking_ref' => 'HMF-2608-CAN4'], 'client', $accounts['HMF-2608-CAN4']->client_name, '2026-08-05 14:00:00');
        $this->log('booking.approved', 'Booking HMF-2608-CAN4 approved with team Team Alpha.', ['booking_ref' => 'HMF-2608-CAN4', 'team' => 'Team Alpha'], 'admin', $admin, '2026-08-06 14:00:00');
        $this->log('payment.verified', 'Verified downpayment payment of ₱15,000.00 for booking HMF-2608-CAN4.', ['booking_ref' => 'HMF-2608-CAN4', 'payment_type' => 'downpayment'], 'admin', $admin, '2026-08-12 10:10:00');
        $this->log('cancellation.submitted', 'Cancellation request submitted for booking HMF-2608-CAN4. 50% refund computed.', ['booking_ref' => 'HMF-2608-CAN4'], 'client', $accounts['HMF-2608-CAN4']->client_name, '2026-08-14 11:00:00');

        $this->log('booking.created', 'Booking HMF-2608-ARC1 submitted by Michelle Cuevas.', ['booking_ref' => 'HMF-2608-ARC1'], 'client', $accounts['HMF-2608-ARC1']->client_name, '2026-05-25 09:00:00');
        $this->log('booking.approved', 'Booking HMF-2608-ARC1 approved with team Team Gamma.', ['booking_ref' => 'HMF-2608-ARC1', 'team' => 'Team Gamma'], 'admin', $admin, '2026-05-26 10:00:00');
        $this->log('deliverables.unlocked', 'Deliverables unlocked for booking HMF-2608-ARC1.', ['booking_ref' => 'HMF-2608-ARC1'], 'admin', $admin, '2026-06-30 14:00:00');
        $this->log('client.archived', 'Client account HMF-2608-ARC1 archived after the 30-day access window.', ['booking_ref' => 'HMF-2608-ARC1'], 'admin', $admin, '2026-07-30 09:00:00');
    }

    private function log(
        string $action,
        string $description,
        array $context = [],
        string $userType = 'admin',
        ?string $actor = 'Admin',
        ?string $when = null
    ): void {
        $log = ActivityLog::create([
            'user_id'     => null,
            'user_type'   => $userType,
            'actor_name'  => $actor,
            'action'      => $action,
            'description' => $description,
            'context'     => $context,
        ]);

        if ($when) {
            $log->forceFill(['created_at' => $when])->saveQuietly();
        }
    }

    /* ======================================================================
     * SUMMARY
     * ==================================================================== */

    private function printSummary(): void
    {
        $this->command->newLine();
        $this->command->info('===== DEMO DATA SEEDED =====');

        $bookings = Booking::query()->get()->groupBy('status');
        $rows = [];
        foreach (['pending', 'approved', 'ongoing', 'completed', 'delivered', 'rejected', 'cancelled'] as $status) {
            $rows[] = ['Bookings - ' . $status, ($bookings[$status] ?? collect())->count()];
        }

        $payments = Downpayment::query()->get()->groupBy('status');
        foreach (['pending', 'verified', 'rejected'] as $status) {
            $rows[] = ['Payments - ' . $status, ($payments[$status] ?? collect())->count()];
        }
        $rows[] = ['Payments - final type', Downpayment::where('payment_type', 'final')->count()];
        $rows[] = ['Client accounts', ClientAccount::count()];
        $rows[] = ['Archived client accounts', ClientAccount::whereNotNull('archived_at')->count()];
        $rows[] = ['Staff', Staff::count()];
        $rows[] = ['Teams', Team::count()];
        $rows[] = ['Staff schedules', StaffSchedule::count()];
        $rows[] = ['Reschedule requests', RescheduleRequest::count()];
        $rows[] = ['Cancellation requests', CancellationRequest::count()];
        $rows[] = ['Post-productions', PostProduction::count()];
        $rows[] = ['Post-production tasks', PostProductionTask::count()];
        $rows[] = ['Activity logs', ActivityLog::count()];

        $this->command->table(['Item', 'Count'], $rows);

        $this->command->info('Client login: use control_number (booking_ref) as username, password: demo123');
        $this->command->info('Staff login: email + password (all staff use "password").');
    }
}
