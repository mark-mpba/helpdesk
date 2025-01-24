<?php

namespace mpba\Tickets\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use mpba\Tickets\Models\Ticket;

class TicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }


    /**
     * Creates a random suffix for the call number
     */
    public function randomSuffix($length = 5): string
    {
        $random = '';
        srand((float) microtime() * 1000000);
        $data = '0123456789';
        for ($i = 0; $i < $length; $i++) {
            $random .= substr($data, (rand() % (strlen($data))), 1);
        }

        return $random;
    }

    /**
     * Returns the initials of the user initiating the call
     */
    public function getInitials($string = null): string
    {
        return array_reduce(
            explode(' ', $string),
            function ($initials, $word) {
                return sprintf('%s%s', $initials, substr($word, 0, 1));
            },
            ''
        );
    }

    /**
     * Recovers the latest call number and increments it
     * if there are no calls is set the origin
     */
    private function nextCallNumber(): int
    {
        $referenceSeed = 113211;
        $latestTicket = Ticket::latest()->first();
        if ($latestTicket) {
            // get the latest submission and increment by one
            // to ensure they are sequential

            if (strlen($latestTicket->reference) == 0){
                return $referenceSeed;
            }
            return intval(preg_replace('/[^0-9]/', ' ', $latestTicket->reference)) + 1;
        }
        return $referenceSeed;
    }

    protected function prepareForValidation(): void
    {
        // **********************************************************
        // we can use UUID as opposed to the URL which is a little
        // safer as we are not exposing the db structure.
        // **********************************************************
        $user = Auth::user();
        $this->merge(['reference' => $this->getInitials($user->name).$this->nextCallNumber()]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'subject' => 'required|min:3',
            'content' => 'required|min:6',
            'priority_id' => 'required|exists:tickets_priorities,id',
            'category_id' => 'required|exists:tickets_categories,id',
            'project_id' => 'required|exists:tickets_projects,id',
            'reference' => ['required','string']
        ];
    }
}
