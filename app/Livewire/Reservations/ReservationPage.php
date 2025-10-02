<?php

namespace App\Livewire\Reservations;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Kreait\Laravel\Firebase\Facades\Firebase;

#[Layout('layouts.app')]
class ReservationPage extends Component
{
    public string $panel = 'create';

    public string $name = '';
    public string $contact = '';
    public ?string $date = null;
    public ?string $time = null;

    public string $today;
    public array $myReservations = [];
    public bool $submitting = false;

    public function mount(): void
    {
        $this->today = now()->toDateString();

        if (Auth::user()?->name) {
            $this->name = Auth::user()->name;
        }

        $this->loadReservations();
    }

    protected function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:100'],
            'contact' => ['required', 'string', 'min:7', 'max:20'],
            'date'    => ['required', 'date', 'after_or_equal:today'],
            'time'    => ['required'],
        ];
    }

    public function save(): void
    {
        $this->validate();
        $this->submitting = true;

        $db = Firebase::database();
        $userId = (string) Auth::id();

        // Save reservation under user’s node
        $db->getReference("reservations/{$userId}")
            ->push([
                'name'       => $this->name,
                'contact'    => $this->contact,
                'date'       => $this->date,
                'time'       => $this->time,
                'status'     => 'pending',
                'created_at' => now()->toDateTimeString(),
            ]);

        // Reset time only
        $this->time = null;

        $this->loadReservations();
        $this->panel = 'list';

        $this->dispatch('notify', message: 'Reservation added!');
        $this->submitting = false;
    }

    public function cancel(string $reservationId): void
    {
        $db = Firebase::database();
        $userId = (string) Auth::id();

        $ref = $db->getReference("reservations/{$userId}/{$reservationId}");
        $snapshot = $ref->getValue();

        if (!$snapshot) {
            $this->dispatch('notify', message: 'Reservation not found.');
            return;
        }

        if (($snapshot['status'] ?? '') !== 'pending') {
            $this->dispatch('notify', message: 'Only pending reservations can be canceled.');
            return;
        }

        $ref->update([
            'status'        => 'canceled_by_user',
            'canceled_at'   => now()->toDateTimeString(),
            'canceled_reason' => 'User canceled',
        ]);

        $this->loadReservations();
        $this->dispatch('notify', message: 'Reservation canceled.');
    }

    public function switchPanel(string $panel): void
    {
        $this->panel = in_array($panel, ['create', 'list']) ? $panel : 'create';
        if ($this->panel === 'list') {
            $this->loadReservations();
        }
    }

    public function loadReservations(): void
    {
        $db = Firebase::database();
        $userId = (string) Auth::id();

        $reservations = $db->getReference("reservations/{$userId}")->getValue();

        $this->myReservations = [];

        if ($reservations) {
            foreach ($reservations as $id => $data) {
                $this->myReservations[] = [
                    'id'      => $id,
                    'date'    => $data['date'] ?? '',
                    'time'    => $data['time'] ?? '',
                    'status'  => $data['status'] ?? 'pending',
                    'name'    => $data['name'] ?? '',
                    'contact' => $data['contact'] ?? '',
                ];
            }

            // Sort by date/time
            usort($this->myReservations, function ($a, $b) {
                return strcmp($a['date'] . $a['time'], $b['date'] . $b['time']);
            });
        }
    }

    public function render()
    {
        return view('livewire.reservations.page');
    }
}
