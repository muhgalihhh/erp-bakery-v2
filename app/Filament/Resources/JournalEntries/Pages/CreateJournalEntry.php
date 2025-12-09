<?php

namespace App\Filament\Resources\JournalEntries\Pages;

use App\Filament\Resources\JournalEntries\JournalEntryResource;
use App\Services\JournalService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateJournalEntry extends CreateRecord
{
    protected static string $resource = JournalEntryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $journalService = new JournalService();

        try {
            // Create using service (as draft - autoPost = false)
            $journalEntry = $journalService->createJournalEntry($data, autoPost: false);

            Notification::make()
                ->success()
                ->title('Journal Entry Created')
                ->body('Journal entry created successfully as draft. You can post it from the list view.')
                ->send();

            return $journalEntry;

        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Failed to Create Journal Entry')
                ->body($e->getMessage())
                ->persistent()
                ->send();

            throw $e;
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
