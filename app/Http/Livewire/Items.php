<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Item;
use Livewire\WithPagination;

class Items extends Component
{
    use WithPagination;

    public $active;

    public function render()
    {
        $items = Item::where('user_id', auth()->user()->id)
            ->when($this->active, function( $query) {
                return $query->active();
            })
            ->paginate(10);

        return view('livewire.items', [
            'items' => $items
        ]);
    }

    public function updatingActive() 
    {
        $this->resetPage();
    }
}
