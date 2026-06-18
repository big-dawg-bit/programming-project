<?php

namespace App\Livewire\Student;

use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        $student = auth()->user()?->student;

        // De (laatste) stage van de ingelogde student, met alles wat we nodig
        // hebben om het dashboard echt te laten leven.
        $stage = $student
            ? $student->stages()
                ->with([
                    'company',
                    'mentor.user',
                    'weeklogs.comments.author',
                    'evaluations',
                    'application.reviews',
                    'application.agreement',
                ])
                ->latest()
                ->first()
            : null;

        $data = [
            'student' => $student,
            'stage' => $stage,
            'currentWeek' => null,
            'totalWeeks' => null,
            'ingediend' => 0,
            'deadlineWeek' => null,
            'deadlineDatum' => null,
            'teDoen' => collect(),
            'recente' => collect(),
        ];

        if ($stage) {
            // Voortgang in weken (uit de echte start/eind-datum van de stage).
            if ($stage->start_date && $stage->end_date) {
                $start = Carbon::parse($stage->start_date);
                $eind = Carbon::parse($stage->end_date);
                $data['totalWeeks'] = max(1, (int) ceil($start->diffInDays($eind) / 7));
                $data['currentWeek'] = max(1, min((int) floor($start->diffInDays(now(), false) / 7) + 1, $data['totalWeeks']));
            }

            // Hoeveel weeklogs zijn er al ingediend (niet-draft).
            $data['ingediend'] = $stage->weeklogs->whereNotNull('submitted_at')->count();

            // Deadline: de weeklog van deze week moet uiterlijk zondagavond binnen.
            $current = $data['currentWeek'];
            if ($current) {
                $huidigeLog = $stage->weeklogs->firstWhere('week_number', $current);
                $huidigeIngediend = $huidigeLog && $huidigeLog->submitted_at;

                $data['deadlineWeek'] = $huidigeIngediend
                    ? min($current + 1, $data['totalWeeks'])
                    : $current;
                $data['deadlineDatum'] = $huidigeIngediend
                    ? now()->addWeek()->endOfWeek(Carbon::SUNDAY)
                    : now()->endOfWeek(Carbon::SUNDAY);

                // Te doen: weeklog van deze week invullen (als nog niet ingediend).
                if (! $huidigeIngediend) {
                    $data['teDoen']->push([
                        'titel' => "Weeklog week {$current} invullen",
                        'tekst' => 'Beschrijf je activiteiten en reflecteer op je ervaringen',
                        'deadline' => $data['deadlineDatum'],
                        'route' => route('weeklogs.index'),
                    ]);
                }
            }

            // Te doen: nieuwe evaluatie van begeleider inzien.
            $nieuweEval = $stage->evaluations->where('status', 'submitted')->sortByDesc('submitted_at')->first();
            if ($nieuweEval) {
                $data['teDoen']->push([
                    'titel' => 'Tussentijdse evaluatie inzien',
                    'tekst' => 'Je begeleider heeft een evaluatie ingediend',
                    'badge' => 'Nieuw',
                    'route' => route('student.evaluaties'),
                ]);
            }

            $data['recente'] = $this->recenteActiviteit($stage);
        }

        return view('livewire.student.dashboard', $data);
    }

    /**
     * Bouwt een activiteitenfeed uit echte gebeurtenissen (reviews, weeklogs,
     * reacties, evaluaties, overeenkomst) en sorteert op tijdstip.
     */
    private function recenteActiviteit($stage): \Illuminate\Support\Collection
    {
        $events = collect();

        // Beslissingen van de stagecommissie op de aanvraag.
        foreach ($stage->application?->reviews ?? [] as $review) {
            if (! $review->reviewed_at) {
                continue;
            }
            $map = match ($review->decision) {
                'approved'          => ['check-circle', 'text-green-500', 'Stageaanvraag goedgekeurd', 'De stagecommissie heeft je aanvraag goedgekeurd'],
                'rejected'          => ['x-circle', 'text-red-500', 'Stageaanvraag afgewezen', $review->feedback ?: 'Je aanvraag werd afgewezen'],
                'changes_requested' => ['exclamation-circle', 'text-amber-500', 'Aanpassing gevraagd', $review->feedback ?: 'De commissie vroeg om aanpassingen'],
                default             => null,
            };
            if ($map) {
                $events->push(['icon' => $map[0], 'kleur' => $map[1], 'titel' => $map[2], 'tekst' => $map[3], 'tijd' => $review->reviewed_at]);
            }
        }

        // Weeklogs: ingediend + goedgekeurd + reacties van de begeleider.
        foreach ($stage->weeklogs as $log) {
            if ($log->submitted_at) {
                $events->push(['icon' => 'document-text', 'kleur' => 'text-blue-500', 'titel' => "Weeklog week {$log->week_number} ingediend", 'tekst' => 'Je weeklog is verzonden naar je begeleider', 'tijd' => $log->submitted_at]);
            }
            if (in_array($log->status, ['goedgekeurd', 'gevalideerd'], true)) {
                $events->push(['icon' => 'check-circle', 'kleur' => 'text-green-500', 'titel' => "Weeklog week {$log->week_number} goedgekeurd", 'tekst' => 'Je begeleider heeft je weeklog goedgekeurd', 'tijd' => $log->updated_at]);
            }
            foreach ($log->comments as $comment) {
                $events->push(['icon' => 'chat-bubble-left-right', 'kleur' => 'text-blue-500', 'titel' => "Reactie op weeklog week {$log->week_number}", 'tekst' => ($comment->author?->name ?? 'Je begeleider') . ' gaf een reactie', 'tijd' => $comment->created_at]);
            }
        }

        // Ingediende evaluaties.
        foreach ($stage->evaluations->where('status', 'submitted') as $eval) {
            $events->push(['icon' => 'star', 'kleur' => 'text-green-500', 'titel' => ($eval->type === 'final' ? 'Eindevaluatie' : 'Tussentijdse evaluatie') . ' ontvangen', 'tekst' => 'Je begeleider heeft een evaluatie ingediend', 'tijd' => $eval->submitted_at ?? $eval->updated_at]);
        }

        // Overeenkomst bevestigd.
        if ($stage->application?->agreement?->status === 'bevestigd') {
            $events->push(['icon' => 'check-circle', 'kleur' => 'text-green-500', 'titel' => 'Stageovereenkomst bevestigd', 'tekst' => 'De stagecommissie heeft je overeenkomst bevestigd', 'tijd' => $stage->application->agreement->updated_at]);
        }

        // Normaliseer alle tijdstippen naar Carbon (sommige kolommen komen als string terug).
        return $events
            ->map(function ($e) {
                $e['tijd'] = $e['tijd'] ? Carbon::parse($e['tijd']) : null;

                return $e;
            })
            ->sortByDesc('tijd')
            ->take(5)
            ->values();
    }
}
