<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PurchaseRequisitionApproval
{
    public static function configure(
        Schema $schema,
    ): Schema {

        return $schema
            ->components([

				/*
				|--------------------------------------------------------------------------
				| Workflow Information
				|--------------------------------------------------------------------------
				*/

				Section::make('Workflow Information')
				    ->description('Current workflow status of this Material Requisition.')
				    ->icon('heroicon-o-arrow-path')
				    ->collapsible()
				    ->columns(12)
				    ->schema([

				        Placeholder::make('document_status')
				            ->label('Document Status')
				            ->content('Draft')
				            ->columnSpan(3),

				        Placeholder::make('workflow_status')
				            ->label('Workflow Status')
				            ->content('Waiting for Submission')
				            ->columnSpan(3),

				        Placeholder::make('current_step')
				            ->label('Current Step')
				            ->content('Document Preparation')
				            ->columnSpan(3),

				        Placeholder::make('next_step')
				            ->label('Next Step')
				            ->content('Submit Material Requisition')
				            ->columnSpan(3),

				    ])
				    ->columnSpanFull(),

				/*
				|--------------------------------------------------------------------------
				| Approval Information
				|--------------------------------------------------------------------------
				*/

				Section::make('Approval Information')
				    ->description('Current approval assignment and approval result.')
				    ->icon('heroicon-o-check-badge')
				    ->collapsible()
				    ->columns(12)
				    ->schema([

				        Placeholder::make('approval_level')
				            ->label('Approval Level')
				            ->content('Level 1')
				            ->columnSpan(3),

				        Placeholder::make('current_approver')
				            ->label('Current Approver')
				            ->content('Waiting Assignment')
				            ->columnSpan(3),

				        Placeholder::make('approval_result')
				            ->label('Approval Result')
				            ->content('Pending')
				            ->columnSpan(3),

				        Placeholder::make('approval_date')
				            ->label('Approval Date')
				            ->content('-')
				            ->columnSpan(3),

				        Placeholder::make('approval_remarks')
				            ->label('Approval Remarks')
				            ->content('-')
				            ->columnSpanFull(),

				    ])
				    ->columnSpanFull(),

				/*
				|--------------------------------------------------------------------------
				| Approval Timeline
				|--------------------------------------------------------------------------
				*/

				Section::make('Approval Timeline')
				    ->description('Workflow progress of this Material Requisition.')
				    ->icon('heroicon-o-clock')
				    ->collapsible()
				    ->columns(12)
				    ->schema([

				        Placeholder::make('created_step')
				            ->label('Created')
				            ->content('Completed')
				            ->columnSpan(3),

				        Placeholder::make('submitted_step')
				            ->label('Submitted')
				            ->content('Waiting')
				            ->columnSpan(3),

				        Placeholder::make('manager_approval_step')
				            ->label('Manager Approval')
				            ->content('Pending')
				            ->columnSpan(3),

				        Placeholder::make('purchasing_review_step')
				            ->label('Purchasing Review')
				            ->content('Pending')
				            ->columnSpan(3),

				        Placeholder::make('final_approval_step')
				            ->label('Final Approval')
				            ->content('Pending')
				            ->columnSpan(3),

				        Placeholder::make('document_closed_step')
				            ->label('Document Closed')
				            ->content('Pending')
				            ->columnSpan(3),

				    ])
				    ->columnSpanFull(),
				    
				/*
				|--------------------------------------------------------------------------
				| Approval History
				|--------------------------------------------------------------------------
				*/

				Section::make('Approval History')
				    ->description('Approval activity log for this Material Requisition.')
				    ->icon('heroicon-o-document-duplicate')
				    ->collapsible()
				    ->collapsed()
				    ->columns(12)
				    ->schema([

				        Placeholder::make('submitted_by')
				            ->label('Submitted By')
				            ->content('-')
				            ->columnSpan(3),

				        Placeholder::make('submitted_at')
				            ->label('Submitted At')
				            ->content('-')
				            ->columnSpan(3),

				        Placeholder::make('last_action')
				            ->label('Last Action')
				            ->content('Draft')
				            ->columnSpan(3),

				        Placeholder::make('last_action_at')
				            ->label('Last Action At')
				            ->content('-')
				            ->columnSpan(3),

				        Placeholder::make('last_comment')
				            ->label('Latest Comment')
				            ->content('-')
				            ->columnSpanFull(),

				    ])
				    ->columnSpanFull(),

            ]);

    }
}