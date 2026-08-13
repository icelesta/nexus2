<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Filament\Resources\PurchaseRequisitions\PurchaseRequisitionResource;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ApprovalNotification extends Notification
{
    use Queueable;

    /*
    |--------------------------------------------------------------------------
    | Notification Events
    |--------------------------------------------------------------------------
    */

    public const APPROVAL_REQUIRED = 'APPROVAL_REQUIRED';

    public const LEVEL_APPROVED = 'LEVEL_APPROVED';

    public const FINAL_APPROVED = 'FINAL_APPROVED';

    public const REJECTED = 'REJECTED';

    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct(
        public readonly string $event,
        public readonly string $documentType,
        public readonly int $documentId,
        public readonly string $documentNo,
        public readonly ?int $approvalLevel = null,
        public readonly ?string $roleName = null,
        public readonly ?string $actorName = null,
        public readonly ?string $remarks = null,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Channels
    |--------------------------------------------------------------------------
    */

    public function via(object $notifiable): array
    {
        return [
            'database',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Database
    |--------------------------------------------------------------------------
    */

    public function toDatabase(object $notifiable): array
    {
        return [
            'event' => $this->event,

            'document_type' =>
                $this->documentType,

            'document_id' =>
                $this->documentId,

            'document_no' =>
                $this->documentNo,

            'approval_level' =>
                $this->approvalLevel,

            'role_name' =>
                $this->roleName,

            'actor_name' =>
                $this->actorName,

            'remarks' =>
                $this->remarks,

            'title' =>
                $this->getTitle(),

            'body' =>
                $this->getBody(),

            'url' =>
                $this->getUrl(),

            'icon' =>
                $this->getIcon(),

            'icon_color' =>
                $this->getIconColor(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    */

    protected function getTitle(): string
    {
        return match ($this->event) {

            self::APPROVAL_REQUIRED =>
                'Approval Required',

            self::LEVEL_APPROVED =>
                'Approval Level Completed',

            self::FINAL_APPROVED =>
                'Material Requisition Approved',

            self::REJECTED =>
                'Material Requisition Rejected',

            default =>
                'Approval Notification',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Body
    |--------------------------------------------------------------------------
    */

    protected function getBody(): string
    {
        return match ($this->event) {

            self::APPROVAL_REQUIRED =>
                sprintf(
                    '%s is waiting for Level %d approval by %s.',
                    $this->documentNo,
                    $this->approvalLevel ?? 0,
                    $this->roleName ?? 'Approver',
                ),

            self::LEVEL_APPROVED =>
                sprintf(
                    '%s Level %d has been approved%s.',
                    $this->documentNo,
                    $this->approvalLevel ?? 0,
                    $this->roleName
                        ? " by {$this->roleName}"
                        : '',
                ),

            self::FINAL_APPROVED =>
                sprintf(
                    '%s has completed the approval workflow.',
                    $this->documentNo,
                ),

            self::REJECTED =>
                sprintf(
                    '%s has been rejected%s.',
                    $this->documentNo,
                    $this->actorName
                        ? " by {$this->actorName}"
                        : '',
                ),

            default =>
                $this->documentNo,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | URL
    |--------------------------------------------------------------------------
    */

    protected function getUrl(): string
    {
        if (
            $this->documentType
            === 'MATERIAL_REQUISITION'
        ) {
            return PurchaseRequisitionResource::getUrl(
                'view',
                [
                    'record' => $this->documentId,
                ],
            );
        }

        return '#';
    }

    /*
    |--------------------------------------------------------------------------
    | Icon
    |--------------------------------------------------------------------------
    */

    protected function getIcon(): string
    {
        return match ($this->event) {

            self::APPROVAL_REQUIRED =>
                'heroicon-o-clock',

            self::LEVEL_APPROVED =>
                'heroicon-o-check-circle',

            self::FINAL_APPROVED =>
                'heroicon-o-check-badge',

            self::REJECTED =>
                'heroicon-o-x-circle',

            default =>
                'heroicon-o-bell',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Icon Color
    |--------------------------------------------------------------------------
    */

    protected function getIconColor(): string
    {
        return match ($this->event) {

            self::APPROVAL_REQUIRED =>
                'warning',

            self::LEVEL_APPROVED,
            self::FINAL_APPROVED =>
                'success',

            self::REJECTED =>
                'danger',

            default =>
                'gray',
        };
    }
}