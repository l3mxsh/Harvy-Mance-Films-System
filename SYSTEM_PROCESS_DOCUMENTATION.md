# HarvyMance Films — System Process Documentation

Authoritative reference for how the application works. Compiled from the actual code
(controllers, models, migrations, routes, views, JS) on 2026-08-13. Covers booking,
payments, rescheduling, cancellation/refunds, post-production, staff portal, client
portal, and the admin panel.

---

## 1. Overview

HarvyMance Films is a Laravel booking-and-monitoring system for a film/video production
company. It has **four areas**:

1. **Public booking site** — clients book a package (+ add-ons) for an event date/time, verify
   their email with a 6-digit OTP, and track booking status via a booking reference (no login).
2. **Client portal** (`/login`) — after a booking is approved, clients log in with a
   **control number + temporary password**, change their password, submit the downpayment and
   final payment proofs, and request reschedules/cancellations. They can also view deliverables
   once unlocked.
3. **Staff portal** (`/staff/login`) — post-production staff log in, see assigned tasks, submit
   deliverable links, and mark tasks done.
4. **Admin panel** (`/admin/login`) — manage bookings, payments, packages/add-ons/inventory,
   staff/outsourced staff/teams, schedules, post-production, reschedules, cancellations,
   clients, admin users, settings, and activity logs.

**Stack:** Laravel (11-style layout, `bootstrap/app.php`), SQLite, Bootstrap 5.3.3,
Bootstrap Icons 1.11.3, Google Fonts "Inter", vanilla JS (no jQuery), per-page JS files in
`public/js/`, Blade views with inline modals and partials.

---

## 2. Directory map (app-relevant)

```
app/
  Http/Controllers/
    Auth/Login.php                     # admin login (web guard)
    BookingController.php              # public booking + approve/reject + AJAX checks
    ClientAccountController.php        # client login/dashboard/change-password/clients mgmt
    DownpaymentController.php          # payment submissions + admin verify/reject
    OtpController.php                  # OTP generate/verify/resend (JSON)
    RescheduleController.php           # client request + admin approve/reject
    CancellationController.php         # client request + admin approve/reject + refund logic
    PostProductionController.php       # create/show/tasks/unlock/outsourced accounts
    StaffPostProductionController.php  # staff portal login/dashboard/tasks
    StaffController.php                # staff + teams + outsourced staff management
    StaffScheduleController.php        # schedule index/calendar/checkAvailability
    PackageController.php              # packages CRUD
    AddonController.php                # add-ons CRUD
    InventoryController.php            # inventory CRUD + search API
    TeamController.php                 # teams CRUD/toggle
    OutsourcedStaffController.php      # outsourced staff records CRUD
    SettingsController.php             # settings + admin profile/password
    UserManagementController.php       # admin users CRUD
    ActivityLogController.php          # activity log viewer
    DashboardController.php            # admin dashboard
  Mail/ BookingCredentialsEmail.php, BookingRejectedEmail.php, OtpEmail.php, StaffCredentialsEmail.php
  Models/ ... (one per table)
database/migrations/                   # full schema (see §4)
resources/views/
  booking.blade.php, booking-status.blade.php, login.blade.php   # public
  client/ login, dashboard, change-password                      # client portal
  staff/  login, dashboard, task                                 # staff portal
  dashboard/*.blade.php + dashboard/partials/*                   # admin panel
  partials/ status-badge, sidebar, auth-navbar, auth-footer, modals/*
public/js/ admin-bookings.js, schedule.js, staff.js, package.js, inventory.js,
           payment-verification.js, admin-post-production.js, clients.js, users.js,
           activity-logs.js, sidebar.js, dashboard.js, booking.js, client-dashboard.js,
           otp.js, login.js, staff.js (portal), etc.
routes/web.php
```

---

## 3. Authentication

Three independent guards (see `config/auth.php`; the route file defines the `access-admin`
gate).

| Area | Guard | Login page | Credentials | Session table |
|---|---|---|---|---|
| Admin | `web` | `/admin/login` (`Auth\Login`) | email + password (users) | `users` |
| Client | `client` | `/login` (`ClientAccountController`) | control_number + password (client_accounts) | `client_accounts` |
| Staff | `staff` | `/staff/login` (`StaffPostProductionController`) | email + password (staff) | `staff` |

### 3.1 Admin (Auth\Login)
- Validates email + password; `Auth::attempt`.
- Rejects login unless `role === 'admin' && status === 'active'` (logs out + error
  "This account is not active or does not have admin access.").
- 5 attempts/IP soft throttle (message shows remaining attempts; no hard block in code).
- On success: session regenerate, `last_login_at` updated, `ActivityLog::log('auth.login', ...)`,
  redirect `admin.dashboard`.
- Logout logs `auth.logout`.
- **Seed admin:** `GET /admin/seed-admin` route lazily does
  `User::firstOrCreate(['email' => 'admin@gmail.com'], ['name'=>'admin','password'=>Hash::make('admin123'),'role'=>'admin','status'=>'active'])`.
- **Gate:** `Gate::define('access-admin', fn ($user) => $user->role === 'admin' && $user->status === 'active')`
  applied via middleware `auth` + `can:access-admin` on the whole admin route group.

### 3.2 Client (ClientAccountController)
- `showLogin`: already-logged-in users redirect to `client.dashboard`.
- `login`: validate control_number + password. Checks:
  - archived account → error "This account has expired and is no longer active...".
  - bad credentials → error with remaining of 5 attempts/IP (RateLimiter `client-login` throttle).
  - success → `auth('client')->login()`, `last_login_at`, then redirect to
    `client.change-password` **if** `must_change_password`, else `client.dashboard`.
- `logout`: guard logout + session invalidate + regenerate token → `client.login`.
- `changePassword`: current password must match; new password min 6 + confirmed; sets
  `must_change_password = false`.

### 3.3 Staff (StaffPostProductionController)
- `login`: email + password. Hard throttle: `RateLimiter::tooManyAttempts` 5 attempts/60s
  ("Too many login attempts...").
- Rejects if `status !== 'active'`, or if `isExpiredTemp()` (temporary account whose
  `temp_expires_at` has passed).
- Success: clear limiter, log in, `last_login_at`, redirect `staff.dashboard`.

### Password generator helpers
- `ClientAccountController::generateTempPassword()` → 6 chars from `ABCDEFGHJKLMNPQRSTUVWXYZ23456789`
  (no 0/O/1/I to avoid confusion).
- `StaffController::generateTempPassword()` → 8 chars from the same alphabet.

---

## 4. Database schema

Compiled from migrations (run order). All timestamps standard unless noted.

### users
`id`, `name`, `email` unique, `password`, `role` (string, default `admin`; added later),
`status` (`enum active/inactive` default `active`, migration 2026_08_02_000003),
`last_login_at`, `remember_token`, timestamps.

### packages / package_services
- `packages`: `id`, `name`, `description` (text, nullable), `price` (decimal 10,2), `status`
  (default `active`), timestamps.
- `package_services`: `id`, `package_id` FK cascade, `service_name`, timestamps.

### addons / addon_inventory
- `addons`: `id`, `name`, `description`, `price` (decimal 10,2), `status` (`enum active/inactive`), timestamps.
- `addon_inventory`: `id`, `addon_id` FK cascade, `inventory_item_id` FK cascade, `quantity` int default 1, timestamps.

### inventory_items
`id`, `name`, `category` (string; UI uses `equipment`/`material`), `description`, `quantity`
(int), `unit` (string, e.g. pcs/sets), `condition_status` (string: `new|good|maintenance|damaged`),
`availability_status` (string: `available|in_use|reserved|unavailable`), timestamps.

### package_inventory
`id`, `package_id` FK cascade, `inventory_item_id` FK cascade, `quantity` int default 1, timestamps.

### bookings (recreated in 2026_07_22_000009 — status is string, not enum)
`id`, `booking_ref` unique (format `HMF-yymmdd-XXXX`), `package_id` FK nullOnDelete,
`event_type`, `client_name`, `client_email`, `client_phone`, `event_date` (date),
`event_time` (time), `event_venue`, `event_address`, `event_description`,
`total_price` decimal 10,2, `downpayment_amount` decimal 10,2, `status` (string,
default `pending`), `payment_status` (nullable), `final_payment_status` (nullable),
`deliverables_unlocked` boolean default false, `post_production_status` (nullable),
`rejection_reason` (text, nullable), `event_completed_at` (timestamp, nullable),
`delivered_at` (timestamp, nullable), `notes`, `terms_agreed` boolean,
`reschedule_used` boolean default false, `team_id` FK teams nullable nullOnDelete, timestamps.

**Booking statuses:** `pending → approved → ongoing → completed → delivered` (also `rejected`,
`cancelled`).

**payment_status values (set by code):** `payment_submitted`, `payment_rejected`,
`downpayment_verified`. (Client dashboard historically checks for `'verified'`; see §13 quirk.)

**final_payment_status values (set by code):** `paid` (set when fully paid/unlocked). The
client dashboard's `getFinalPaymentDue()` checks `!== 'paid'`.

**post_production_status values (set by code):** `in_progress`, `ready`, `delivered`
(migrations mention `not_started/editing/in_review/completed` but code writes the first three).

### booking_items
`id`, `booking_id` FK cascade, `inventory_item_id` FK cascade, `quantity` int default 1,
`status` string default `reserved` (values: `reserved`, `in_use`, `returned`, `cancelled`), timestamps.

### booking_addons
`id`, `booking_id` FK cascade, `addon_id` FK cascade, `price` decimal 10,2 default 0, timestamps.

### otps
`id`, `email`, `code`, `purpose`, `verified` boolean default false, `expires_at` timestamp nullable, timestamps.

### client_accounts (renamed from customer_accounts, 2026_08_02_000001)
`id`, `control_number`, `password` (hashed), `client_name`, `client_email`, `client_phone`,
`booking_id` FK, `must_change_password` boolean default true, `last_login_at`,
`archived_at` (timestamp nullable, 2026_08_03_000002), `remember_token`, timestamps.

### downpayments
`id`, `booking_id` FK cascade, `payment_type` (string: `downpayment` | `final`),
`amount` decimal 10,2, `payment_proof` string (storage path), `status` string default
`pending` (values: `pending`, `verified`, `rejected`), `rejection_reason` text,
`submitted_at`, `verified_at`, timestamps.

### post_productions
`id`, `booking_id` FK cascade, `assigned_staff_id` FK staff nullable nullOnDelete,
`status` string default `editing` (code sets `in_progress` / `ready` / `delivered`),
`notes` text, `progress_notes` text, `expected_completion_date` date, `started_at`,
`completed_at`, timestamps.

### post_production_tasks
`id`, `post_production_id` FK cascade, `staff_id` FK staff **nullable** (made nullable
2026_07_28_004158), `outsourced_staff_id` FK outsourced_staff nullable nullOnDelete,
`task_type` string (`photo_editing`|`video_editing`|`both`), `instructions` text,
`status` string default `not_started` (`not_started|in_progress|completed`),
`deliverable_link` text, `remarks` text, `revision_notes` text,
`admin_review_status` string default `pending` (`pending|approved|revision_requested`),
`completed_at` timestamp, timestamps.

### staff
`id`, `name`, `email` unique, `password`, `contact_number`, `status` string default
`active`, `last_login_at`, `is_outsourced` boolean default false, `is_temporary` boolean
default false, `temp_expires_at` timestamp nullable, `role` string default
`post_production` (2026_08_03_000005), timestamps.

### teams
`id`, `name`, `description` text, `status` string default `active`, timestamps.

### staff_team
`id`, `staff_id` FK cascade, `team_id` FK cascade, timestamps.

### outsourced_staff_team
`id`, `outsourced_staff_id` FK cascade, `team_id` FK cascade, timestamps.
(Referenced by `Team::outsourcedMembers()`.)

### staff_schedules
`id`, `staff_id` FK cascade, `booking_id` FK cascade, `event_date` (date), `event_time`
(time, nullable), `status` string default `assigned`
(values: `assigned|confirmed|completed|cancelled`), `notes` text, timestamps.

### outsourced_staff
`id`, `name`, `email`, `contact_number`, `notes`, timestamps. **Record-only** — no login.

### reschedule_requests
`id`, `booking_id` FK cascade, `requested_date` (date), `requested_time` (string),
`status` (`enum pending|approved|rejected` default `pending`), `rejection_reason` text,
`new_team_id` FK teams nullable nullOnDelete, timestamps.

### cancellation_requests
`id`, `booking_id` FK cascade, `reason` text, `refund_amount` decimal 10,2 default 0,
`refund_percentage` int default 0, `status`
(`enum pending|approved|rejected|refunded` default `pending`), `admin_notes` text,
`refund_reference` string, `refund_proof` string, `processed_at` timestamp, timestamps.

### settings
`id`, `key` unique, `value` text nullable, timestamps. Seeded: `client_auto_delete_days` = '30'.
Later migration seeds `admin_show_sidebar_badges` = '1'.

### activity_logs
`id`, `user_id` FK users nullable nullOnDelete, `user_type` string (`admin|client|staff|system`),
`actor_name` string, `action` string indexed, `description` text, `context` json nullable, timestamps.

---

## 5. Booking lifecycle (end-to-end)

### 5.1 Public booking form (`GET /` → BookingController@index)
- Loads active packages (with `services`, `inventory`) and active add-ons (with `inventory`).
- The form (JS `booking.js`) calls three availability APIs live and requires OTP verification
  before submit (see §11).

### 5.2 OTP (OtpController — JSON endpoints)
- **generate** (`POST api/otp/generate`): requires email + client_name.
  - If an unverified, unexpired OTP exists for that email: 60-second resend cooldown message.
  - Marks all prior unverified OTPs for that email verified, then creates a new 6-digit code
    (`random_int(100000,999999)`), `purpose = 'booking_verification'`,
    `expires_at = now()+5min`, sends `OtpEmail`. Response includes `otp_code` (exposed so it
    also works in mail-log mode) and `mail_sent`.
- **verify** (`POST api/otp/verify`): email + 6-digit code. Invalid → attempt counter; after 5
  attempts in the last 5 minutes → "Maximum verification attempts reached." Expired → error.
  Success marks the OTP verified.
- **resend** (`POST api/otp/resend`): like generate without the 60s check.

### 5.3 Submit booking (`POST /booking` → BookingController@store)
Validation:
```
package_id      required|exists:packages,id
addon_ids       nullable|array, addon_ids.* exists:addons,id
client_name     required|string|max:255
client_email    required|email|max:255
client_phone    required, regex /^09\d{2}-\d{3}-\d{4}$/   (e.g. 0912-345-6789)
event_type      required|string|max:100
event_date      required|date|after:today
event_time      required|string
event_venue     required|string|max:255
event_address   nullable|string|max:500
event_description nullable|string|max:1000
total_price     required|numeric|min:0    (hidden field, recomputed server-side anyway)
notes           nullable|string|max:1000
otp_verified    accepted                  (set by the form after successful OTP verify)
terms_agreed    accepted
```
Server-side steps, in order:
1. **OTP gate:** looks up an OTP for `client_email` with `purpose=booking_verification`,
   `verified=true`, created within the last 30 minutes. Missing → error
   "Email verification is required. Please verify your email first."
2. **Email usage gate** (`emailUsage()`): blocked if the email already has an **active**
   (`archived_at` null) client account ("Please log in instead of making a new booking") OR a
   **pending** booking ("You already have a booking in review...").
3. Loads package + selected add-ons.
4. **Pricing:** `total_price = package.price + Σ addon.price`;
   `downpayment_amount = round(total_price * 0.30, 2)` (**30%**).
5. **Creates Booking:** `booking_ref = HMF-yymmdd-XXXX` (unique via loop), `status='pending'`,
   `terms_agreed=true`.
6. **Creates booking_items** (status `reserved`) for every package inventory item using its
   pivot `quantity`. For each add-on: attach `booking_addons` row (pivot `price`), then add its
   inventory quantities — if the same inventory item already exists for this booking, the
   quantity is **merged** instead of duplicated.
7. **Redirect:** `route('booking.status', $booking->booking_ref)` with success
   "Booking submitted successfully! Your booking is now pending admin approval."

> **Note:** `store()` performs **no** server-side date/inventory conflict checks — availability
> is enforced client-side through `api/booking/check-date` and `api/booking/check-inventory`
> before the form is allowed to submit.

### 5.4 Public status lookup (`GET /booking/status/{bookingRef}` → BookingController@status)
Public page showing package, services, add-ons, items, status for a booking ref. No login.

### 5.5 Admin booking list (`GET /admin/booking` → BookingController@adminIndex)
- `$bookings` (paginated 15, newest first) with package.services, addons, team,
  `latestDownpayment` (a downpayment-typed payment via `latestOfMany`), postProduction.
- `$availableTeams` — active teams with members + outsourcedMembers.
- `$rescheduleRequests` (with booking.team + newTeam), pending-first ordering,
  `$pendingRescheduleCount`.
- Two tabs (query string): "All Bookings" and "Reschedule Requests".

### 5.6 Approve (`POST /admin/booking/{booking}/approve` → BookingController@approve)
- Guard: booking must be `pending` (422 JSON or flash error otherwise).
- Requires `team_id` (exists:teams).
- **Member availability:** for each team member, checks `staff_schedules` with
  `staff_id`, `event_date = booking.event_date`, status in `['assigned','confirmed']`,
  linked to a booking whose status is **not** cancelled/rejected. Any conflict aborts with
  "Cannot approve: {names} already have a booking on {date}."
- Creates a `staff_schedules` row (status `assigned`) for each team member.
- **Creates ClientAccount:** `control_number = booking.booking_ref` (the same string),
  6-char temp password, `must_change_password=true`, linked to the booking.
- Updates booking: `status='approved'`, `team_id`.
- Logs `booking.approved` (context: booking_ref, team, event_date).
- Sends `BookingCredentialsEmail` (subject "Booking Approved - Your Monitoring Credentials")
  with control number + temp password. Mail failures are swallowed.
- Flash: "Booking approved. {team} assigned. Login credentials sent to {email}."

### 5.7 Reject (`POST /admin/booking/{booking}/reject` → BookingController@reject)
- Guard: booking must be `pending`.
- Requires `rejection_reason` (max 1000).
- Sets `status='rejected'`, `rejection_reason`; booking_items → `cancelled`.
- Logs `booking.rejected`; sends `BookingRejectedEmail` ("Your Booking Was Rejected").

### 5.8 Mark event complete (`POST /admin/booking/{booking}/complete` →
PostProductionController@markEventComplete)
- Guard: booking must be `ongoing`.
- Sets `status='completed'`, `event_completed_at=now()`; staff_schedules → `completed`.
- Flash "Event ... has been marked as completed."

---

## 6. Client portal

### 6.1 Dashboard (`GET /dashboard` → ClientAccountController@dashboard)
Loads booking with package, addons, bookingItems.inventoryItem, downpayments, postProduction,
team. Computes:
- `totalPaid` = Σ verified downpayment amounts.
- `finalPaymentInfo` (remaining = `total_price − downpayment_amount − totalPaid`; due if
  remaining > 0 and `final_payment_status !== 'paid'`).
- `statusConfig` labels for every status.
- **activeSection** decides which panel/section to highlight:
  - `pending` → pending
  - `approved` & `payment_status !== 'verified'` → downpayment
  - `approved` & `payment_status === 'verified'` → final_payment
  - `ongoing` & `post_production_status === 'not_started'` → event_completed
  - `ongoing` → post_production
  - `completed` & `!deliverables_unlocked` → final_payment
  - `completed` & `deliverables_unlocked` & `payment_status === 'verified'` → deliverables
  - `completed` & `deliverables_unlocked` → completed
  - `delivered` → delivered
  - `cancelled` / `rejected` → corresponding section

> Quirk: `adminVerify` sets `payment_status='downpayment_verified'` (never `'verified'`), so the
> `=== 'verified'` branches in the client dashboard are effectively unreachable via the current
> flow. The `downpayment`/`final_payment` sections are instead driven by booking status.

### 6.2 Payments (DownpaymentController)
- **submit** (`POST /downpayment`): booking must exist for the account. Validates
  `payment_proof` (image jpeg/png/jpg/gif ≤ 5 MB) + `amount` (> 0). Stores proof under
  `downpayment-proofs/` on the `public` disk. Creates a Downpayment
  (`payment_type='downpayment'`, `status='pending'`, `submitted_at=now()`). Sets
  `booking.payment_status='payment_submitted'`. Flash "Payment proof submitted successfully!"
- **resubmit** (`POST /downpayment/{downpayment}/resubmit`): 403 if not owned by the booking;
  only if `status==='rejected'`. Deletes the old proof file, then **creates a new** pending
  Downpayment row (old record is left rejected) and resets `payment_status='payment_submitted'`.
- **submitFinalPayment** (`POST /final-payment`): only when booking status is
  `['completed','ongoing']` (else error "Final payment is only available after event
  completion."). Proof stored under `final-payment-proofs/`; creates Downpayment
  (`payment_type='final'`, pending). (Does **not** update `final_payment_status` here.)
- **resubmitFinalPayment**: same guards as resubmit plus `payment_type==='final'`.

### 6.3 Admin verification (DownpaymentController@adminIndex / adminVerify / adminReject)
- **adminIndex** (`GET /admin/payment-verification`): paginated 15, filter by status; eager-loads
  booking.package.services, booking.addons, booking.team. Stats: total, downpayment_total,
  final_total, pending, verified, rejected.
- **adminVerify** (`POST /admin/payment-verification/{downpayment}/verify`): sets
  `status='verified'`, `verified_at=now()`. Recomputes total verified paid:
  - if `totalPaid >= total_price` → `final_payment_status='paid'`;
  - if booking still `approved` → `payment_status='downpayment_verified'` **and**
    `status='ongoing'`.
  Logs `payment.verified`. Flash "Payment has been verified successfully."
- **adminReject** (`POST /admin/payment-verification/{downpayment}/reject`): requires
  `rejection_reason`. Sets downpayment `status='rejected'` + reason; booking
  `payment_status='payment_rejected'`. Logs `payment.rejected`.

---

## 7. Rescheduling (RescheduleController)

### 7.1 Client request (`POST /reschedule` → store)
- Booking status must be in `['pending','approved','ongoing']`.
- Lead-time rule: `now()->diffInDays(event_date, false) < leadTime` where
  `leadTime = setting reschedule_lead_time_days (default 5)` → error "Reschedule must be
  requested at least {n} days before the event date."
- Validates `requested_date` (after:today) + `requested_time`.
- New-date conflict: another approved/ongoing booking on that date → error.
- Inventory check (`checkInventoryConflict`): for each booking item, reserved-on-date quantity
  (status `reserved`, booking approved/ongoing, excluding self) vs item quantity. Any shortfall
  → error listing the unavailable item names.
- Deletes any previous **pending** request for the booking, then creates a new pending request.
- Flash "Reschedule request submitted. Waiting for admin approval."

### 7.2 Admin approve (`POST /admin/reschedule/{request}/approve`)
- Request must be `pending`; requires `team_id`.
- Checks every member of the selected team for a `staff_schedules` conflict on the new date
  (status assigned/confirmed, other booking) → error per member.
- Deletes the booking's old staff schedules; creates new ones (assigned) for the new team.
- Request → `approved`, `new_team_id`.
- Booking → `event_date = requested_date`, `event_time = requested_time`, `team_id = new team`,
  `reschedule_used = false`.
- Flash "Reschedule approved. Booking {ref} moved to {date}."

### 7.3 Admin reject (`POST /admin/reschedule/{request}/reject`)
- Requires `rejection_reason`; request → `rejected`. Booking unchanged.

---

## 8. Cancellation & refunds (CancellationController)

### 8.1 Refund policy
- `computeRefundPercentage(Booking)`: days until event (`diffInDays(event_date, false)`);
  policy from `setting refund_policy` (JSON array of `{days, percent}`), default
  `[14→100%, 7→50%, 0→0%]`. Tiers sorted descending by days; first tier where
  `daysUntil >= tier.days` wins.

### 8.2 Client request (`POST /cancel` → store)
- Booking status must be in `['pending','approved','ongoing']`.
- No existing pending/approved cancellation request.
- Validates `reason` (optional, max 1000).
- `amountPaid = Σ verified downpayments`.
- **Zero paid:** cancels immediately — creates request `status='refunded'`, refund 0,
  `processed_at=now()`; booking `status='cancelled'`; items → `cancelled`;
  staff_schedules deleted. Flash "Booking cancelled successfully."
- **Paid:** creates request `status='pending'` with computed `refund_percentage` and
  `refund_amount = round(amountPaid × pct/100, 2)`. Flash either a refund amount or
  "No refund is applicable based on the current policy."

### 8.3 Admin list (`GET /admin/cancellations` → adminIndex)
Pending-first, filter by status, search by booking_ref/client_name/client_email. Summary
counts (total, pending, refunded, rejected). AJAX re-render support.

### 8.4 Admin approve (→ `refunded`)
- Validates optional `refund_reference`, `refund_proof` (jpg/jpeg/png/pdf ≤ 5 MB stored under
  `refund_proofs/`), `admin_notes`.
- Request → `status='refunded'`, `processed_at=now()`.
- Booking → `status='cancelled'`; items → `cancelled`; staff_schedules deleted.
- Logs `cancellation.refunded`. Flash "Refund marked as processed..."

### 8.5 Admin reject
- Requires `admin_notes`. Request → `status='rejected'`, `processed_at=now()`.
- Booking **stays active**. Logs `cancellation.rejected`.

---

## 9. Post-production

### 9.1 Start (`GET /admin/post-production/create/{booking}` + `POST /admin/post-production/{booking}`)
- Guards: booking status must be `completed`; no existing postProduction record.
- Create form: `expected_completion_date` (after:today), `notes`, and dynamic task rows:
  - `tasks[i][assignee_type]` ∈ `inhouse|outsourced|admin`
  - `tasks[i][staff_id]` (inhouse), `tasks[i][outsourced_staff_id]` (outsourced)
  - `tasks[i][task_type]` ∈ `photo_editing|video_editing|both`
  - `tasks[i][instructions]` (optional)
- Creates PostProduction (`status='in_progress'`, `started_at=now()`, expected date, notes).
- For each task: `staff_id` set for inhouse, `outsourced_staff_id` for outsourced, neither for
  admin. Task `status` = `in_progress` for admin-assigned tasks, else `not_started`;
  `admin_review_status='pending'`.
- Booking → `post_production_status='in_progress'`. Redirect to the detail page.

### 9.2 Detail page (`GET /admin/post-production/{postProduction}` → show)
Loads booking.package/addons/clientAccount, tasks.staff, tasks.outsourcedStaff. Computes
`allApproved` (every task `admin_review_status === 'approved'`), `totalPaid`, `isFullyPaid`
(≥ total_price), `remainingBalance`. Admin actions per task:
- **Approve** (`approveTask`): `admin_review_status='approved'`, clears revision notes. After
  any approval, `checkAllApproved()` sets PP `status='ready'` and booking
  `post_production_status='ready'` when every task is approved.
- **Request revision** (`requestRevision`): requires `revision_notes`; sets
  `admin_review_status='revision_requested'`, `status='in_progress'`, clears deliverable_link /
  remarks / completed_at.
- **Save link manually** (`adminUpdateTaskLink`): `deliverable_link` (url) + optional `remarks`;
  sets `status='completed'`, `completed_at=now()`, `admin_review_status='pending'`.
- **Create temp account** (`createOutsourcedAccount`): validates name/email. Reuses an existing
  `staff` row with matching email, else creates one (`is_outsourced=true`, `is_temporary=true`,
  `status='active'`), assigns an 8-char temp password, links the task (`staff_id`), and emails
  `StaffCredentialsEmail`. If the mail fails, the plaintext password is shown in the flash
  message.
- **Update notes** (`updateNotes`): free-text notes on the PP record.
- **Unlock deliverables** (`unlockDeliverables`, booking-scoped): requires all tasks approved
  AND fully paid. Sets booking `deliverables_unlocked=true`, `final_payment_status='paid'`,
  `status='completed'`, `post_production_status='delivered'`, `delivered_at=now()`; staff
  schedules → completed; PP `status='delivered'`, `completed_at=now()`.

### 9.3 Staff portal
- **Dashboard** (`GET /staff/dashboard`): tasks for the logged-in staff (with
  postProduction.booking), stats total/not_started/in_progress/completed.
- **Task** (`GET /staff/task/{task}`): ownership enforced (`task.staff_id === staff.id`, else 403).
- **Update** (`PUT /staff/task/{task}` → updateTask): validates `status`
  (`not_started|in_progress|completed`), `deliverable_link` (url), `remarks`.
  - Marking `completed` **requires** a deliverable link (else error).
  - On complete: `completed_at=now()`, `admin_review_status='pending'`.
  - **Temp-account auto-expiry:** if the staff is temporary and has no other non-completed
    tasks, sets `temp_expires_at=now()` and `status='inactive'`.

---

## 10. Admin modules

### 10.1 Dashboard (DashboardController@index)
Summary cards: totalBookings, pendingBookings, activeBookings (approved/ongoing),
completedBookings (completed/delivered), todayEvents, totalClients (active client accounts),
pendingPayments (pending downpayments), monthlyRevenue (Σ verified downpayments this month).
Lists upcoming events (6), recent bookings (8), pending payments/cancellations/reschedules
(5 each), recent activity (8).

### 10.2 Packages / Add-ons (PackageController, AddonController)
- Full CRUD. Package edit/update syncs `package_services` and `package_inventory`
  (quantities). Add-ons sync `addon_inventory`. Inventory pickers use
  `GET /api/inventory/search` (InventoryController@search).
- `GET /admin/team` renders the staff management page (`team.index` closure in routes).
- Package/Addon `show` also exists (JSON-ish detail used by edit pages).

### 10.3 Inventory (InventoryController)
- CRUD on inventory_items. `show` returns JSON for the edit/view modal.
- Availability/category/condition filters + search, AJAX partial rendering.
- `search` (`GET /api/inventory/search?query=`) used by package/addon forms.

### 10.4 Staff management (StaffController + OutsourcedStaffController + TeamController)
- **Staff** create (unique email, password confirmed), update (name/email/contact, optional
  new password, optional "email new password" notify), resetPassword, generatePassword (only
  for outsourced staff, 8-char temp password + email), toggleStatus, destroy (permanent).
- **Outsourced staff records** — record-only (no login): store/update/destroy.
- **Teams** — name/description/status; members via `staff_team`, outsourced via
  `outsourced_staff_team`; toggleStatus/destroy.
- Staff page tabs: All / In-House / Outsourced / Teams, with search + status filters (AJAX).

### 10.5 Schedules (StaffScheduleController)
- `index` (`GET /admin/schedule`): feeds the full-page calendar (JSON `$events` built from
  staff_schedules + bookings, staff list, event types; driven by `js/schedule.js`).
- `calendar` (`GET /admin/schedule/calendar`): JSON endpoint returning schedule events for the
  given date range.
- `update` (`PUT /admin/schedule/{schedule}`): update a schedule row's status (used by the
  schedule UI / admin).
- `checkAvailability` (`POST /api/staff-schedule/check-availability`): given team_id +
  event_date, returns each member with availability + any conflicts (used by the booking
  approve / reschedule approve modals).

### 10.6 Clients (ClientAccountController@adminIndex / restore)
- Active accounts (archived_at null), search by control number/name/email/phone/booking_ref.
- Archived accounts tab; Upcoming Archiving tab computed from delivered bookings
  (`status='completed'`, `deliverables_unlocked`, `delivered_at` set, active client account)
  → `delete_at = delivered_at + client_auto_delete_days`, with days remaining and urgency
  badges (≤3 danger, ≤7 warning).
- `restore` (`POST /admin/clients/{account}/restore`): clears `archived_at`.

### 10.7 Admin users (UserManagementController)
- CRUD on `users`. Create/update admin accounts; toggle status and delete are guarded against
  the currently logged-in user and against removing the **last** active/last admin
  (`isLastActiveAdmin`/`isLastAdmin`).

### 10.8 Settings (SettingsController)
- **General** (`update`): `client_auto_delete_days` ∈ {7,14,30},
  `reschedule_lead_time_days` ∈ {5,6,7}, and a `refund_policy` JSON rebuilt from
  `refund_tiers[]` (days/percent, sorted descending by days).
- **Profile** (`updateProfile`): name, email (unique), `admin_show_sidebar_badges` toggle.
- **Password** (`updatePassword`): current password verified via closure, new password
  min 6 + confirmed.

### 10.9 Activity logs (ActivityLogController@index)
Paginated, filters (search, action, user_type, date range), AJAX re-render. Actions recorded
via `ActivityLog::log()` with actor auto-detected from admin/client/staff guards.

---

## 11. AJAX / API endpoints (all POST unless noted)

| Method | URI | Name | Purpose |
|---|---|---|---|
| GET | `api/booking/check-date?date=` | `booking.checkDate` | Returns `{available, message, conflicts}` for a date |
| GET | `api/booking/check-email?email=` | `booking.checkEmail` | Blocks existing active account or pending booking for the email |
| GET | `api/booking/check-inventory?package_id=&addon_ids[]=&event_date=` | `booking.checkInventory` | Sums required inventory (merging package+addon), compares to reserved-on-date, returns per-item availability |
| POST | `api/otp/generate` | `otp.generate` | Send 6-digit OTP (5-min expiry, 60s cooldown) |
| POST | `api/otp/verify` | `otp.verify` | Validate OTP (max 5 attempts/5min) |
| POST | `api/otp/resend` | `otp.resend` | New OTP without cooldown |
| GET | `api/inventory/search?query=` | `inventory.search` | Inventory picker for package/addon forms |
| POST | `api/staff-schedule/check-availability` | `staff-schedule.checkAvailability` | Team member availability for a date (approve / reschedule modals) |

---

## 12. Emails

| Mailable | When | Subject | Contents |
|---|---|---|---|
| `OtpEmail` | OTP generate/resend | "Your Verification Code - HarvyMance Films" | 6-digit code, 5-min expiry |
| `BookingCredentialsEmail` | Booking approved | "Booking Approved - Your Monitoring Credentials - HarvyMance Films" | Control number (= booking ref), temp password, login URL `/login` |
| `BookingRejectedEmail` | Booking rejected | "Your Booking Was Rejected - HarvyMance Films" | Rejection reason, "Book Again" link |
| `StaffCredentialsEmail` | Staff create/update/reset + outsourced temp account | "Your Staff Account Access - HarvyMance Films" | Email, temp password, `/staff/login`; optionally booking ref |

All are HTML-string mails (no Blade templates). Mail-sending failures are swallowed in
controllers; the OTP/staff-temp flows surface a note in the response when the mail fails.

---

## 13. Status & field reference

### Booking.status
`pending` (created) → `approved` (approve) → `ongoing` (downpayment verified) →
`completed` (event complete / unlock) → `delivered` (unlock sets status completed +
post_production_status delivered; delivered shown in statusConfig). Also `rejected`, `cancelled`.

### Booking.payment_status
`payment_submitted` (downpayment submitted), `downpayment_verified` (verified while approved),
`payment_rejected` (downpayment rejected).

### Booking.final_payment_status
`paid` (set when verified totals reach total_price, or at unlock). Client dashboard treats
anything `!== 'paid'` as balance due.

### booking_items.status
`reserved` (on booking) → `cancelled` (on reject/cancel); `in_use`/`returned` reserved in the
original schema but not set by current code.

### staff_schedules.status
`assigned` (approve/reschedule) → `completed` (event complete / unlock), `cancelled` (cancel).
`confirmed` referenced in conflict queries.

### downpayments.payment_type / status
`downpayment` | `final`. Status `pending` → `verified` | `rejected`.

### post_production.status
`in_progress` (start) → `ready` (all tasks approved) → `delivered` (unlock).
Booking `post_production_status` mirrors: `in_progress` → `ready` → `delivered`.

### post_production_tasks
`status`: `not_started` | `in_progress` | `completed`.
`admin_review_status`: `pending` | `approved` | `revision_requested`.

### cancellation_requests.status
`pending` → `refunded` (approve) | `rejected`. Zero-paid requests are created `refunded`.

### Team / Staff / Users status
`active` | `inactive`.

### Status badge color map (partials/status-badge.blade.php)
- warning: `pending`, `submitted`, `in_progress`, `reserved`, `maintenance`
- info: `awaiting`, `awaiting_payment`, `awaiting_review`, `ready`, `good`
- success: `approved`, `verified`, `completed`, `delivered`, `refunded`, `active`, `available`, `new`, `paid`, `fully_paid`, `unlocked`
- primary: `confirmed`, `ongoing`
- danger: `revision_requested`, `revision`, `rejected`, `cancelled`, `damaged`, `unpaid`, `balance_due`
- secondary: `not_yet_submitted`, `inactive`, `unavailable`, `locked`, `assigned`(warning in admin)

---

## 14. Known quirks / inconsistencies (worth flagging)

1. **No server-side availability check in `BookingController@store`** — conflict/inventory
   checks live in the JS/API layer only. A crafted request can create overlapping bookings.
2. **Client dashboard `activeSection`** checks `payment_status === 'verified'` but the code
   writes `'downpayment_verified'`; the affected branches are effectively dead.
3. **`downpayment_amount` is 30%** of the total at booking time (recomputed only as
   `round(total * 0.2, 2)`? — no: 30% in store; approve does not recompute it). Confirm the
   intended downpayment rate before quoting to clients.
4. **ClientAccount control_number = booking_ref** (created at approval). The
   `generateControlNumber()` helper is unused for that path.
5. **`resubmit` creates a new Downpayment row** rather than updating the rejected one, so the
   client may see multiple payment rows.
6. **`delivered` booking status** is referenced in UI/statusConfig but `unlockDeliverables`
   sets booking status to `completed` (delivery is expressed via `post_production_status` and
   `delivered_at`).
7. Staff `role` column defaults to `post_production` but the code path does not branch on it.
8. Several routes reference closures or views that could 500 if data is inconsistent (e.g.,
   booking status page for a booking with no package).

---

*End of document.*
