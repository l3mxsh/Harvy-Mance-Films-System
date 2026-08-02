<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property numeric $price
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InventoryItem> $inventory
 * @property-read int|null $inventory_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Addon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Addon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Addon query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Addon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Addon whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Addon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Addon whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Addon wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Addon whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Addon whereUpdatedAt($value)
 */
	class Addon extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $booking_ref
 * @property int $package_id
 * @property string|null $event_type
 * @property string $client_name
 * @property string|null $client_email
 * @property string|null $client_phone
 * @property \Illuminate\Support\Carbon $event_date
 * @property string|null $event_time
 * @property string|null $event_venue
 * @property string|null $event_address
 * @property string|null $event_description
 * @property numeric $total_price
 * @property numeric $downpayment_amount
 * @property string $status
 * @property string|null $notes
 * @property bool $terms_agreed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $rejection_reason
 * @property int|null $team_id
 * @property string|null $post_production_status
 * @property string|null $payment_status
 * @property \Illuminate\Support\Carbon|null $event_completed_at
 * @property string|null $final_payment_status
 * @property bool $deliverables_unlocked
 * @property \Illuminate\Support\Carbon|null $delivered_at
 * @property bool $reschedule_used
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Addon> $addons
 * @property-read int|null $addons_count
 * @property-read \App\Models\CancellationRequest|null $cancellationRequest
 * @property-read \App\Models\ClientAccount|null $clientAccount
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Downpayment> $downpayments
 * @property-read int|null $downpayments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BookingItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Downpayment|null $latestDownpayment
 * @property-read \App\Models\Package $package
 * @property-read \App\Models\RescheduleRequest|null $pendingReschedule
 * @property-read \App\Models\PostProduction|null $postProduction
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RescheduleRequest> $rescheduleRequests
 * @property-read int|null $reschedule_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StaffSchedule> $staffSchedules
 * @property-read int|null $staff_schedules_count
 * @property-read \App\Models\Team|null $team
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereBookingRef($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereClientEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereClientName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereClientPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereDeliverablesUnlocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereDeliveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereDownpaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereEventAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereEventCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereEventDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereEventDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereEventTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereEventType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereEventVenue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereFinalPaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking wherePostProductionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereRescheduleUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereTermsAgreed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereUpdatedAt($value)
 */
	class Booking extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $booking_id
 * @property int $inventory_item_id
 * @property int $quantity
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Booking $booking
 * @property-read \App\Models\InventoryItem $inventoryItem
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingItem whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingItem whereInventoryItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingItem whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingItem whereUpdatedAt($value)
 */
	class BookingItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $booking_id
 * @property string|null $reason
 * @property numeric $refund_amount
 * @property int $refund_percentage
 * @property string $status
 * @property string|null $admin_notes
 * @property string|null $refund_reference
 * @property string|null $refund_proof
 * @property \Illuminate\Support\Carbon|null $processed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Booking $booking
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CancellationRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CancellationRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CancellationRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CancellationRequest whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CancellationRequest whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CancellationRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CancellationRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CancellationRequest whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CancellationRequest whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CancellationRequest whereRefundAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CancellationRequest whereRefundPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CancellationRequest whereRefundProof($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CancellationRequest whereRefundReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CancellationRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CancellationRequest whereUpdatedAt($value)
 */
	class CancellationRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $control_number
 * @property string $password
 * @property string $client_name
 * @property string $client_email
 * @property string|null $client_phone
 * @property int $booking_id
 * @property bool $must_change_password
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $archived_at
 * @property-read \App\Models\Booking $booking
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAccount active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAccount whereArchivedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAccount whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAccount whereClientEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAccount whereClientName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAccount whereClientPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAccount whereControlNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAccount whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAccount whereMustChangePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAccount wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAccount whereUpdatedAt($value)
 */
	class ClientAccount extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $booking_id
 * @property numeric $amount
 * @property string|null $payment_proof
 * @property string $status
 * @property string|null $rejection_reason
 * @property \Illuminate\Support\Carbon|null $submitted_at
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $payment_type
 * @property-read \App\Models\Booking $booking
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Downpayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Downpayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Downpayment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Downpayment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Downpayment whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Downpayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Downpayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Downpayment wherePaymentProof($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Downpayment wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Downpayment whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Downpayment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Downpayment whereSubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Downpayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Downpayment whereVerifiedAt($value)
 */
	class Downpayment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $category
 * @property string|null $description
 * @property int $quantity
 * @property string $unit
 * @property string $condition_status
 * @property string $availability_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Addon> $addons
 * @property-read int|null $addons_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Package> $packages
 * @property-read int|null $packages_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem whereAvailabilityStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem whereConditionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem whereUpdatedAt($value)
 */
	class InventoryItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $email
 * @property string $code
 * @property string $purpose
 * @property \Illuminate\Support\Carbon $expires_at
 * @property bool $verified
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp wherePurpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp whereVerified($value)
 */
	class Otp extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $contact_number
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $email
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PostProductionTask> $tasks
 * @property-read int|null $tasks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Team> $teams
 * @property-read int|null $teams_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutsourcedStaff newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutsourcedStaff newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutsourcedStaff query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutsourcedStaff whereContactNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutsourcedStaff whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutsourcedStaff whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutsourcedStaff whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutsourcedStaff whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutsourcedStaff whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutsourcedStaff whereUpdatedAt($value)
 */
	class OutsourcedStaff extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property numeric $price
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InventoryItem> $inventory
 * @property-read int|null $inventory_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PackageService> $services
 * @property-read int|null $services_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package whereUpdatedAt($value)
 */
	class Package extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $package_id
 * @property string $service_name
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Package $package
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageService query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageService wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageService whereServiceName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageService whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageService whereUpdatedAt($value)
 */
	class PackageService extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $booking_id
 * @property int|null $assigned_staff_id
 * @property string $status
 * @property string|null $notes
 * @property string|null $progress_notes
 * @property \Illuminate\Support\Carbon|null $expected_completion_date
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Staff|null $assignedStaff
 * @property-read \App\Models\Booking $booking
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PostProductionTask> $tasks
 * @property-read int|null $tasks_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProduction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProduction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProduction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProduction whereAssignedStaffId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProduction whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProduction whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProduction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProduction whereExpectedCompletionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProduction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProduction whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProduction whereProgressNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProduction whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProduction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProduction whereUpdatedAt($value)
 */
	class PostProduction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $post_production_id
 * @property int|null $staff_id
 * @property string $task_type
 * @property string|null $instructions
 * @property string $status
 * @property string|null $deliverable_link
 * @property string|null $remarks
 * @property string|null $revision_notes
 * @property string $admin_review_status
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $outsourced_staff_id
 * @property-read \App\Models\OutsourcedStaff|null $outsourcedStaff
 * @property-read \App\Models\PostProduction $postProduction
 * @property-read \App\Models\Staff|null $staff
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProductionTask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProductionTask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProductionTask query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProductionTask whereAdminReviewStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProductionTask whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProductionTask whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProductionTask whereDeliverableLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProductionTask whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProductionTask whereInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProductionTask whereOutsourcedStaffId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProductionTask wherePostProductionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProductionTask whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProductionTask whereRevisionNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProductionTask whereStaffId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProductionTask whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProductionTask whereTaskType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostProductionTask whereUpdatedAt($value)
 */
	class PostProductionTask extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $booking_id
 * @property \Illuminate\Support\Carbon $requested_date
 * @property string $requested_time
 * @property string $status
 * @property string|null $rejection_reason
 * @property int|null $new_team_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Booking $booking
 * @property-read \App\Models\Team|null $newTeam
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RescheduleRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RescheduleRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RescheduleRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RescheduleRequest whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RescheduleRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RescheduleRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RescheduleRequest whereNewTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RescheduleRequest whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RescheduleRequest whereRequestedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RescheduleRequest whereRequestedTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RescheduleRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RescheduleRequest whereUpdatedAt($value)
 */
	class RescheduleRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereValue($value)
 */
	class Setting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $contact_number
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $is_outsourced
 * @property bool $is_temporary
 * @property \Illuminate\Support\Carbon|null $temp_expires_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PostProductionTask> $postProductionTasks
 * @property-read int|null $post_production_tasks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StaffSchedule> $schedules
 * @property-read int|null $schedules_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Team> $teams
 * @property-read int|null $teams_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Staff newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Staff newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Staff query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Staff whereContactNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Staff whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Staff whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Staff whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Staff whereIsOutsourced($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Staff whereIsTemporary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Staff whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Staff whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Staff wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Staff whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Staff whereTempExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Staff whereUpdatedAt($value)
 */
	class Staff extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $staff_id
 * @property int $booking_id
 * @property \Illuminate\Support\Carbon $event_date
 * @property string $event_time
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Booking $booking
 * @property-read \App\Models\Staff $staff
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffSchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffSchedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffSchedule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffSchedule whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffSchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffSchedule whereEventDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffSchedule whereEventTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffSchedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffSchedule whereStaffId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffSchedule whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffSchedule whereUpdatedAt($value)
 */
	class StaffSchedule extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Staff> $members
 * @property-read int|null $members_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OutsourcedStaff> $outsourcedMembers
 * @property-read int|null $outsourced_members_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereUpdatedAt($value)
 */
	class Team extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $role
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $status
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User active()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

