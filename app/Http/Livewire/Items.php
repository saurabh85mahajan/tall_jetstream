<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Item;
use Livewire\WithPagination;

class Items extends Component
{
    use WithPagination;

    public $active;
    public $q;
    public $sortBy = 'id';
    public $sortAsc = true;

    public $confirmingItemDeletion = false;

    protected $queryString = [
        'active' => ['except' => false],
        'q' => ['except' => ''],
        'sortBy' => ['except' => 'id'],
        'sortAsc' => ['except' => true],
    ];

    public function render()
    {
        $items = Item::where('user_id', auth()->user()->id)
            ->when( $this->q, function($query) {
                return $query->where(function( $query) {
                    $query->where('name', 'like', '%'.$this->q . '%')
                        ->orWhere('price', 'like', '%' . $this->q . '%');
                });
            })
            ->when($this->active, function( $query) {
                return $query->active();
            })
            ->orderBy( $this->sortBy, $this->sortAsc ? 'ASC' : 'DESC');

        $query = $items->toSql();
        $items = $items->paginate(10);

        return view('livewire.items', [
            'items' => $items,
            'query' => $query
        ]);
    }

    public function updatingActive() 
    {
        $this->resetPage();
    }

    public function updatingQ() 
    {
        $this->resetPage();
    }

    public function sortBy( $field) 
    {
        if( $field == $this->sortBy) {
            $this->sortAsc = !$this->sortAsc;
        }
        $this->sortBy = $field;
    }

    public function confirmItemDeletion( $id) 
    {
        // $item->delete();
        $this->confirmingItemDeletion = $id;
    }

    public function deleteItem( Item $item) 
    {
        $item->delete();
        $this->confirmingItemDeletion = false;
    }
}
